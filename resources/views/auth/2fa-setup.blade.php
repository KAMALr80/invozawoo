<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>2FA Setup - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0c10 0%, #12151c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .setup-container {
            max-width: 700px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .setup-content {
            padding: 48px;
        }

        .brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            margin: 0 auto 16px;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        }

        .brand h1 {
            font-size: 28px;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff, #a8b3cf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand p {
            color: #6b7280;
            font-size: 14px;
            margin-top: 8px;
        }

        /* Steps */
        .step {
            background: #1a1d26;
            border-radius: 24px;
            padding: 28px;
            margin-bottom: 24px;
            border: 1px solid #2a2f3c;
            transition: all 0.3s ease;
        }

        .step:hover {
            border-color: #3b82f6;
            box-shadow: 0 8px 25px -10px rgba(59, 130, 246, 0.2);
        }

        .step-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 24px;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            color: white;
        }

        .step-title {
            font-size: 20px;
            font-weight: 600;
            color: white;
        }

        /* QR Code */
        .qr-container {
            text-align: center;
            padding: 24px;
            background: white;
            border-radius: 20px;
            margin-bottom: 20px;
        }

        .qr-code {
            max-width: 200px;
            width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
        }

        .secret-key {
            background: #0f1117;
            padding: 14px 20px;
            border-radius: 14px;
            font-family: 'Courier New', monospace;
            font-size: 18px;
            letter-spacing: 2px;
            text-align: center;
            color: #3b82f6;
            border: 1px solid #2a2f3c;
            margin-top: 16px;
            word-break: break-all;
        }

        .manual-entry {
            color: #9ca3af;
            font-size: 12px;
            text-align: center;
            margin-top: 12px;
        }

        /* App Buttons */
        .app-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .app-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            background: #0f1117;
            border: 1px solid #2a2f3c;
            border-radius: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .app-btn:hover {
            border-color: #3b82f6;
            transform: translateY(-2px);
        }

        .app-btn i {
            font-size: 24px;
        }

        .app-btn span {
            color: white;
            font-weight: 500;
            font-size: 14px;
        }

        .fa-google { color: #3b82f6; }
        .fa-microsoft { color: #3b82f6; }

        /* Recovery Codes */
        .recovery-codes {
            background: #0f1117;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #2a2f3c;
        }

        .warning-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }

        .warning-box i {
            color: #f59e0b;
            font-size: 20px;
            flex-shrink: 0;
        }

        .warning-box p {
            color: #fcd34d;
            font-size: 13px;
            line-height: 1.5;
        }

        .recovery-code-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin: 20px 0;
        }

        .recovery-code-item {
            background: #1a1d26;
            padding: 12px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            text-align: center;
            color: #4ade80;
            border: 1px solid #2a2f3c;
            letter-spacing: 1px;
        }

        /* Form Styles */
        .input-group {
            margin-bottom: 24px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .input-field input {
            width: 100%;
            padding: 16px;
            background: #1a1d26;
            border: 1.5px solid #2a2f3c;
            border-radius: 14px;
            font-size: 20px;
            color: white;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 8px;
            transition: all 0.3s ease;
        }

        .input-field input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Buttons */
        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: transparent;
            border: 1.5px solid #2a2f3c;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 500;
            color: #9ca3af;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-danger {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.4);
        }

        /* Messages */
        .success-message {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4ade80;
            font-size: 13px;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f87171;
            font-size: 13px;
        }

        .info-box {
            background: rgba(59, 130, 246, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }

        .info-box i {
            color: #3b82f6;
            font-size: 20px;
        }

        .info-box p {
            color: #9ca3af;
            font-size: 13px;
            line-height: 1.5;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }

        .back-link a:hover {
            color: #3b82f6;
        }

        hr {
            border-color: #2a2f3c;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            .setup-content {
                padding: 32px 24px;
            }

            .recovery-code-list {
                grid-template-columns: 1fr;
            }

            .step {
                padding: 20px;
            }

            .step-title {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>
    <div class="setup-container">
        <div class="setup-content">
            <div class="brand">
                <div class="logo-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>Two-Factor Authentication</h1>
                <p>Secure your account with an extra layer of protection</p>
            </div>

            @if (session('success'))
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (!$twoFactorEnabled)
                <!-- Info Box -->
                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>Two-factor authentication adds an extra layer of security to your account. After enabling 2FA, you'll need to enter a verification code from your authenticator app every time you log in.</p>
                </div>

                <!-- Step 1: Install Authenticator App -->
                <div class="step">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <div class="step-title">Install Authenticator App</div>
                    </div>
                    <p style="color: #9ca3af; margin-bottom: 16px; font-size: 14px;">
                        Download one of these apps on your smartphone:
                    </p>
                    <div class="app-buttons">
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="app-btn">
                            <i class="fab fa-google fa-google"></i>
                            <span>Google Authenticator</span>
                        </a>
                        <a href="https://www.microsoft.com/en-us/security/mobile-authenticator-app" target="_blank" class="app-btn">
                            <i class="fab fa-microsoft"></i>
                            <span>Microsoft Authenticator</span>
                        </a>
                    </div>
                </div>

                <!-- Step 2: Scan QR Code -->
                <div class="step">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <div class="step-title">Scan QR Code</div>
                    </div>
                    <div class="qr-container">
                        @if($qrCodeBase64)
                            <img src="{{ $qrCodeBase64 }}" alt="QR Code" class="qr-code">
                        @else
                            <div style="padding: 20px; text-align: center;">
                                <i class="fas fa-qrcode" style="font-size: 48px; color: #3b82f6;"></i>
                                <p style="margin-top: 10px;">QR code generation failed. Please use manual entry.</p>
                            </div>
                        @endif
                    </div>
                    <div class="secret-key">
                        {{ $secret }}
                    </div>
                    <div class="manual-entry">
                        <i class="fas fa-keyboard"></i> Can't scan? Manually enter the secret key in your authenticator app
                    </div>
                </div>

                <!-- Step 3: Verify Code -->
                <div class="step">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <div class="step-title">Verify & Enable</div>
                    </div>
                    <form method="POST" action="{{ route('2fa.enable') }}">
                        @csrf
                        <div class="input-group">
                            <label class="input-label">Enter 6-digit code from authenticator</label>
                            <div class="input-field">
                                <input type="text" name="otp" maxlength="6" placeholder="000000" autocomplete="off" autofocus>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check-circle"></i> Enable 2FA
                        </button>
                    </form>
                    <button type="button" class="btn-secondary" onclick="location.href='{{ route('2fa.recovery.generate') }}'">
                        <i class="fas fa-sync-alt"></i> Generate New Recovery Codes
                    </button>
                </div>

                <!-- Recovery Codes Section -->
                @if (count($recoveryCodes) > 0)
                    <div class="step">
                        <div class="step-header">
                            <div class="step-number">⚠️</div>
                            <div class="step-title">Save Your Recovery Codes</div>
                        </div>
                        <div class="recovery-codes">
                            <div class="warning-box">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p><strong>Important!</strong> Save these codes in a safe place. Each code can only be used once. If you lose your authenticator app, you can use these codes to access your account.</p>
                            </div>
                            <div class="recovery-code-list">
                                @foreach ($recoveryCodes as $code)
                                    <div class="recovery-code-item">{{ $code }}</div>
                                @endforeach
                            </div>
                            <div class="warning-box" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3);">
                                <i class="fas fa-download" style="color: #4ade80;"></i>
                                <p style="color: #4ade80;">Make sure to download or print these codes before enabling 2FA. You won't be able to see them again!</p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- 2FA is already enabled -->
                <div class="step">
                    <div class="step-header">
                        <div class="step-number">✓</div>
                        <div class="step-title" style="color: #4ade80;">2FA is Enabled</div>
                    </div>
                    <div class="info-box" style="background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3);">
                        <i class="fas fa-shield-alt" style="color: #4ade80;"></i>
                        <p style="color: #4ade80;">Your account is protected with two-factor authentication. This adds an extra layer of security to your account.</p>
                    </div>

                    @if($remainingRecoveryCodes > 0)
                        <div class="warning-box" style="margin-bottom: 20px;">
                            <i class="fas fa-key"></i>
                            <p>You have <strong>{{ $remainingRecoveryCodes }}</strong> recovery codes remaining. Generate new codes if you're running low.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('2fa.disable') }}">
                        @csrf
                        <div class="input-group">
                            <label class="input-label">Enter 6-digit code to disable 2FA</label>
                            <div class="input-field">
                                <input type="text" name="otp" maxlength="6" placeholder="000000" autocomplete="off">
                            </div>
                        </div>
                        <button type="submit" class="btn-danger">
                            <i class="fas fa-trash-alt"></i> Disable 2FA
                        </button>
                    </form>

                    <button type="button" class="btn-secondary" onclick="location.href='{{ route('2fa.recovery.generate') }}'">
                        <i class="fas fa-sync-alt"></i> Generate New Recovery Codes
                    </button>
                </div>
            @endif

            <hr>

            <div class="back-link">
                <a href="{{ route('profile.security') }}">
                    <i class="fas fa-arrow-left"></i> Back to Security Settings
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-format OTP input
        const otpInput = document.querySelector('input[name="otp"]');
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
            });
        }
    </script>
</body>

</html>
