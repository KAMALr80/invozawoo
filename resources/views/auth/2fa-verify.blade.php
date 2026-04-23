<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>2FA Verification - {{ config('app.name') }}</title>
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

        .verify-container {
            max-width: 480px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .verify-content {
            padding: 48px;
            text-align: center;
        }

        .shield-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }

        .shield-icon i {
            font-size: 48px;
            color: white;
        }

        h2 {
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .subtitle {
            color: #9ca3af;
            font-size: 14px;
            margin-bottom: 32px;
        }

        .input-group {
            margin-bottom: 28px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 12px;
            text-align: left;
        }

        .otp-input-container {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-bottom: 10px;
        }

        .otp-digit {
            width: 55px;
            height: 70px;
            background: #1a1d26;
            border: 2px solid #2a2f3c;
            border-radius: 16px;
            font-size: 28px;
            font-weight: 700;
            color: white;
            text-align: center;
            font-family: 'Courier New', monospace;
            transition: all 0.3s ease;
        }

        .otp-digit:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .hidden-otp {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            width: 1px;
            height: 1px;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3b82f6, #1e40af);
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .recovery-link {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .recovery-link:hover {
            color: #3b82f6;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
            padding: 12px 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #f87171;
            font-size: 13px;
            text-align: left;
        }

        .resend-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #2a2f3c;
            font-size: 12px;
            color: #6b7280;
        }

        .resend-info i {
            margin-right: 6px;
        }

        @media (max-width: 480px) {
            .verify-content {
                padding: 32px 24px;
            }

            .otp-digit {
                width: 45px;
                height: 60px;
                font-size: 24px;
            }

            h2 {
                font-size: 24px;
            }

            .shield-icon {
                width: 70px;
                height: 70px;
            }

            .shield-icon i {
                font-size: 36px;
            }
        }
    </style>
</head>

<body>
    <div class="verify-container">
        <div class="verify-content">
            <div class="shield-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>Two-Factor Authentication</h2>
            <p class="subtitle">Enter the 6-digit code from your authenticator app</p>

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify.post') }}" id="verifyForm">
                @csrf
                <div class="input-group">
                    <label class="input-label">Verification Code</label>
                    <div class="otp-input-container">
                        <input type="text" class="otp-digit" maxlength="1" data-index="0" autofocus>
                        <input type="text" class="otp-digit" maxlength="1" data-index="1">
                        <input type="text" class="otp-digit" maxlength="1" data-index="2">
                        <input type="text" class="otp-digit" maxlength="1" data-index="3">
                        <input type="text" class="otp-digit" maxlength="1" data-index="4">
                        <input type="text" class="otp-digit" maxlength="1" data-index="5">
                    </div>
                    <input type="hidden" name="otp" id="otpHidden" value="">
                </div>

                <button type="submit" class="btn-primary" id="submitBtn">
                    <i class="fas fa-check-circle"></i> Verify & Continue
                </button>
            </form>

            <a href="{{ route('2fa.recovery') }}" class="recovery-link">
                <i class="fas fa-key"></i> Lost your device? Use a recovery code
            </a>

            <div class="resend-info">
                <i class="fas fa-clock"></i> The code expires in 30 seconds
            </div>
        </div>
    </div>

    <script>
        // OTP Input Handling
        const digits = document.querySelectorAll('.otp-digit');
        const hiddenInput = document.getElementById('otpHidden');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('verifyForm');

        function updateHiddenInput() {
            let otp = '';
            digits.forEach(digit => {
                otp += digit.value;
            });
            hiddenInput.value = otp;

            // Enable/disable submit button
            submitBtn.disabled = otp.length !== 6;
        }

        digits.forEach((digit, index) => {
            digit.addEventListener('input', function(e) {
                // Allow only numbers
                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value.length === 1 && index < 5) {
                    digits[index + 1].focus();
                }

                updateHiddenInput();
            });

            digit.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value.length === 0 && index > 0) {
                    digits[index - 1].focus();
                    digits[index - 1].value = '';
                    updateHiddenInput();
                }
            });

            digit.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const numbers = pastedData.replace(/[^0-9]/g, '').slice(0, 6);

                for (let i = 0; i < numbers.length; i++) {
                    if (digits[i]) {
                        digits[i].value = numbers[i];
                    }
                }

                if (numbers.length === 6) {
                    digits[5].focus();
                } else if (numbers.length > 0) {
                    digits[numbers.length - 1].focus();
                }

                updateHiddenInput();
            });
        });

        // Prevent form submission on Enter if OTP incomplete
        form.addEventListener('submit', function(e) {
            if (hiddenInput.value.length !== 6) {
                e.preventDefault();
                alert('Please enter the complete 6-digit verification code');
            }
        });
    </script>
</body>

</html>
