<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\TwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        Log::info('Profile updated', ['user_id' => $request->user()->id]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Show change password form
     */
    public function showChangePassword(Request $request): View
    {
        return view('profile.change-password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update user's password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Current password is incorrect.');
                }
            }],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->password);
        $user->password_updated_at = now();
        $user->save();

        Log::info('Password changed', ['user_id' => $user->id]);

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Show security settings page (2FA management)
     */
    public function showSecurity(Request $request): View
    {
        $user = $request->user();
        $twoFactorEnabled = $user->two_factor_enabled;
        $remainingRecoveryCodes = $this->twoFactorService->getRemainingRecoveryCodesCount($user);

        return view('profile.security', compact('twoFactorEnabled', 'remainingRecoveryCodes'));
    }

    /**
     * Show activity log page
     */
    public function activityLog(Request $request): View
    {
        $user = $request->user();
        $lastLoginAt = $user->last_login_at;
        $lastLoginIp = $user->last_login_ip;

        return view('profile.activity', compact('lastLoginAt', 'lastLoginIp'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log before deletion
        Log::warning('Account deleted', ['user_id' => $user->id, 'email' => $user->email]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
