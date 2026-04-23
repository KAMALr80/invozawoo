<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Show registration form
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle registration - Send OTP first
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mobile' => 'nullable|digits:10',
        ]);

        // Generate secure OTP
        $otp = sprintf("%06d", random_int(0, 999999));

        // Create user with pending status
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'mobile' => $request->mobile,
            'status' => 'pending',
            'register_otp' => $otp,
            'register_otp_expires_at' => now()->addMinutes(5),
            'otp_last_sent_at' => now(),
            'password_updated_at' => now(),
        ]);

        // Store in session for OTP verification
        Session::put('otp_user_id', $user->id);
        Session::put('otp_purpose', 'registration');
        Session::put('otp_email', $user->email);
        Session::put('otp_sent_at', now()->timestamp);
        Session::put('otp_expires_at', now()->addMinutes(5)->timestamp);

        // Send OTP via Email
        try {
            Mail::send('emails.otp', [
                'otp' => $otp,
                'purpose' => 'Registration',
                'name' => $user->name,
                'email' => $user->email,
                'year' => date('Y')
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('🔐 ERP Registration OTP Verification');
            });

            Log::info('Registration OTP sent', ['user_id' => $user->id]);
        } catch (\Exception $e) {
            Log::error('Registration email failed: ' . $e->getMessage());

            // Fallback to raw email
            try {
                Mail::raw(
                    "Hello {$user->name},\n\n" .
                    "Your ERP Registration OTP is: {$otp}\n\n" .
                    "This OTP is valid for 5 minutes.\n\n" .
                    "© " . date('Y') . " ERP System",
                    function ($message) use ($user) {
                        $message->to($user->email)
                                ->subject("ERP Registration OTP");
                    }
                );
            } catch (\Exception $ex) {
                Log::error('Fallback email failed', ['error' => $ex->getMessage()]);
            }
        }

        return redirect()->route('register.otp.show')
            ->with('status', 'OTP sent to your email. Please verify to complete registration.');
    }
}
