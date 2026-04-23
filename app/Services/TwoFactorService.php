<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class TwoFactorService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Generate a new 2FA secret for a user
     */
    public function generateSecret(User $user): string
    {
        $secret = $this->google2fa->generateSecretKey(32);

        // Encrypt and store the secret
        $user->two_factor_secret = Crypt::encryptString($secret);
        $user->save();

        return $secret;
    }

    /**
     * Get the decrypted 2FA secret for a user
     */
    public function getSecret(User $user): ?string
    {
        if (!$user->two_factor_secret) {
            return null;
        }

        try {
            return Crypt::decryptString($user->two_factor_secret);
        } catch (\Exception $e) {
            Log::error('Failed to decrypt 2FA secret', ['user_id' => $user->id]);
            return null;
        }
    }

    /**
     * Generate QR Code URL for Google/Microsoft Authenticator
     */
    public function getQRCodeUrl(User $user, string $secret): string
    {
        $company = config('app.name', 'ERP System');
        $email = $user->email;

        // Format: otpauth://totp/COMPANY:EMAIL?secret=SECRET&issuer=COMPANY
        return $this->google2fa->getQRCodeUrl($company, $email, $secret);
    }

    /**
     * Verify OTP code
     */
    public function verifyCode(User $user, string $code): bool
    {
        $secret = $this->getSecret($user);

        if (!$secret) {
            return false;
        }

        // Check if it's a recovery code first
        if ($this->verifyRecoveryCode($user, $code)) {
            return true;
        }

        // Verify TOTP code
        return $this->google2fa->verifyKey($secret, $code, 2); // 2 windows = 30 seconds
    }

    /**
     * Generate recovery codes for backup access
     */
    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = $this->generateRecoveryCode();
        }
        return $codes;
    }

    /**
     * Generate a single recovery code
     */
    protected function generateRecoveryCode(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            if ($i < 3) $code .= '-';
        }
        return $code;
    }

    /**
     * Store recovery codes (hashed)
     */
    public function storeRecoveryCodes(User $user, array $codes): void
    {
        $hashedCodes = array_map(function($code) {
            return password_hash($code, PASSWORD_BCRYPT);
        }, $codes);

        $user->two_factor_recovery_codes = json_encode($hashedCodes);
        $user->save();
    }

    /**
     * Verify a recovery code
     */
    protected function verifyRecoveryCode(User $user, string $code): bool
    {
        if (!$user->two_factor_recovery_codes) {
            return false;
        }

        $storedCodes = json_decode($user->two_factor_recovery_codes, true);

        foreach ($storedCodes as $index => $hashedCode) {
            if (password_verify($code, $hashedCode)) {
                // Remove used recovery code
                unset($storedCodes[$index]);
                $user->two_factor_recovery_codes = json_encode(array_values($storedCodes));
                $user->save();
                return true;
            }
        }

        return false;
    }

    /**
     * Get remaining recovery codes count
     */
    public function getRemainingRecoveryCodesCount(User $user): int
    {
        if (!$user->two_factor_recovery_codes) {
            return 0;
        }

        return count(json_decode($user->two_factor_recovery_codes, true));
    }

    /**
     * Enable 2FA for user
     */
    public function enableTwoFactor(User $user): void
    {
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();
    }

    /**
     * Disable 2FA for user
     */
    public function disableTwoFactor(User $user): void
    {
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();
    }
    /**
 * Generate QR Code as base64 image using Endroid QR Code
 */
/**
 * Generate QR Code as base64 image using Endroid QR Code
 */
public function getQRCodeBase64(User $user, string $secret): ?string
{
    try {
        $qrCodeUrl = $this->getQRCodeUrl($user, $secret);

        // Endroid QR Code v6.x syntax - set size in constructor
        $qrCode = new \Endroid\QrCode\QrCode($qrCodeUrl);

        // Create writer and get result
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        $base64 = base64_encode($result->getString());

        return 'data:image/png;base64,' . $base64;
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Failed to generate QR code: ' . $e->getMessage());
        return null;
    }
}
}
