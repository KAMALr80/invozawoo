<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Show 2FA setup page
     */
    public function showSetup()
    {
        $user = Auth::user();

        // Generate new secret if not exists
        $secret = $this->twoFactorService->getSecret($user);
        if (!$secret) {
            $secret = $this->twoFactorService->generateSecret($user);
        }

        // Get QR code as base64 image
        $qrCodeBase64 = $this->twoFactorService->getQRCodeBase64($user, $secret);
        $recoveryCodes = Session::get('temp_recovery_codes', []);
        $twoFactorEnabled = $user->two_factor_enabled;
        $remainingRecoveryCodes = $this->twoFactorService->getRemainingRecoveryCodesCount($user);

        return view('auth.2fa-setup', compact('qrCodeBase64', 'secret', 'recoveryCodes', 'twoFactorEnabled', 'remainingRecoveryCodes'));
    }

    /**
     * Generate new recovery codes
     */
    public function generateRecoveryCodes()
    {
        $user = Auth::user();
        $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();

        // Store temporarily in session for display
        Session::put('temp_recovery_codes', $recoveryCodes);

        return redirect()->route('2fa.setup');
    }

    /**
     * Verify OTP and enable 2FA
     */
    public function enable(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$this->twoFactorService->verifyCode($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid verification code. Please try again.']);
        }

        // Store recovery codes permanently
        $recoveryCodes = Session::get('temp_recovery_codes', []);
        if (empty($recoveryCodes)) {
            $recoveryCodes = $this->twoFactorService->generateRecoveryCodes();
        }

        $this->twoFactorService->storeRecoveryCodes($user, $recoveryCodes);
        $this->twoFactorService->enableTwoFactor($user);

        Session::forget('temp_recovery_codes');

        return redirect()->route('2fa.setup')->with('success', 'Two-factor authentication has been enabled successfully!');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$this->twoFactorService->verifyCode($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid verification code.']);
        }

        $this->twoFactorService->disableTwoFactor($user);

        return redirect()->route('2fa.setup')->with('success', 'Two-factor authentication has been disabled.');
    }

    /**
     * Show 2FA verification page after login
     */
    public function showVerify()
    {
        // Check if user needs 2FA verification
        if (!Session::has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa-verify');
    }

    /**
     * Verify OTP after login and complete the authentication
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $userId = Session::get('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);

        if (!$user || !$this->twoFactorService->verifyCode($user, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid verification code. Please try again.']);
        }

        // Get remember preference from session
        $remember = Session::get('2fa:remember', false);

        // Clear the temporary session data
        Session::forget('2fa:user:id');
        Session::forget('2fa:remember');
        Session::forget('2fa:login:attempt');

        // Mark that 2FA was verified (for logging purposes)
        Session::put('2fa:verified', true);

        // Complete the login process
        return $this->completeLogin($user, $request, $remember);
    }

    /**
     * Show recovery code verification page
     */
    public function showRecovery()
    {
        if (!Session::has('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.2fa-recovery');
    }

    /**
     * Verify recovery code
     */
    public function verifyRecovery(Request $request)
    {
        $request->validate([
            'recovery_code' => 'required|string',
        ]);

        $userId = Session::get('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);

        if (!$user || !$this->twoFactorService->verifyCode($user, $request->recovery_code)) {
            return back()->withErrors(['recovery_code' => 'Invalid recovery code.']);
        }

        // Get remember preference from session
        $remember = Session::get('2fa:remember', false);

        // Clear the temporary session
        Session::forget('2fa:user:id');
        Session::forget('2fa:remember');
        Session::forget('2fa:login:attempt');

        // Mark that 2FA was verified
        Session::put('2fa:verified', true);

        // Complete the login process
        return $this->completeLogin($user, $request, $remember)->with('warning', 'You used a recovery code. Please setup a new device for 2FA.');
    }

    /**
     * Complete the login process by calling the AuthenticatedSessionController
     */
    private function completeLogin($user, Request $request, $remember = false): \Illuminate\Http\RedirectResponse
    {
        // Update login tracking
        $user->last_login_at = now();
        $user->last_login_ip = $request->ip();
        $user->login_attempts = 0;
        $user->save();

        // Login the user
        Auth::login($user, $remember);
        $request->session()->regenerate();

        Log::info('Login successful (with 2FA)', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip' => $request->ip()
        ]);

        // Clear 2FA session data
        Session::forget('2fa:verified');

        // Check password age (optional)
        if ($user->password_updated_at && $user->password_updated_at->diffInDays(now()) > 90) {
            return redirect()->route('password.request')
                ->with('warning', 'Your password is over 90 days old. Please update for security.');
        }

        // ==================== ROLE-BASED REDIRECT ====================

        // Admin Dashboard
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')
                ->with('success', "Welcome back, Admin {$user->name}!");
        }

        // Delivery Agent Dashboard
        if ($user->role === 'delivery_agent') {
            $agent = \App\Models\DeliveryAgent::where('user_id', $user->id)->first();

            if (!$agent) {
                Log::warning('Delivery agent login but no agent profile found', ['user_id' => $user->id]);
                return redirect()->route('login')->withErrors([
                    'email' => 'Agent profile not found. Please contact admin.'
                ]);
            }

            // Update agent online status
            $agent->is_online = true;
            $agent->last_active_at = now();
            $agent->save();

            return redirect()->route('agent.dashboard')
                ->with('success', "Welcome back, {$agent->name}!");
        }

        // HR Dashboard
        if ($user->role === 'hr') {
            return redirect()->route('hr.dashboard')
                ->with('success', "Welcome back, HR {$user->name}!");
        }

        // Staff Dashboard
        if ($user->role === 'staff') {
            return redirect()->route('staff.dashboard')
                ->with('success', "Welcome back, {$user->name}!");
        }

        // Default fallback
        return redirect()->intended(route('dashboard'))
            ->with('success', "Welcome back, {$user->name}!");
    }
}
