<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>2FA Recovery - {{ config('app.name') }}</title>
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

        .recovery-container {
            max-width: 520px;
            width: 100%;
            background: #0f1117;
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .recovery-content {
            padding: 48px;
            text-align: center;
        }

        .key-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .key-icon i {
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

        .warning-box {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid rgba(245, 158, 11, 0.3);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 28px;
            display: flex;
            gap: 14px;
            text-align: left;
        }

        .warning-box i {
            color: #f59e0b;
            font-size: 22px;
            flex-shrink: 0;
        }

        .warning-box p {
            color: #fcd34d;
            font-size: 13px;
            line-height: 1.5;
        }

        .warning-box p strong {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 28px;
        }

        .input-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #9ca3af;
            margin-bottom: 10px;
            text-align: left;
        }

        .input-field input {
            width: 100%;
            padding: 16px 20px;
            background: #1a1d26;
            border: 1.5px solid #2a2f3c;
            border-radius: 16px;
            font-size: 16px;
            color: white;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 2px;
            transition: all 0.3s ease;
        }

        .input-field input:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }

        .input-field input::placeholder {
            color: #4b5563;
            letter-spacing: normal;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
        }

        .btn-primary {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.4);
        }

        .back-link {
            color: #9ca3af;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
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

        .format-example {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
            text-align: left;
        }

        .format-example code {
            background: #1a1d26;
            padding: 4px 8px;
            border-radius: 6px;
            font-family: monospace;
            color: #f59e0b;
        }

        hr {
            border-color: #2a2f3c;
            margin: 20px 0;
        }

        @media (max-width: 480px) {
            .recovery-content {
                padding: 32px 24px;
            }

            h2 {
                font-size: 24px;
            }

            .key-icon {
                width: 70px;
                height: 70px;
            }

            .key-icon i {
                font-size: 36px;
            }

            .warning-box {
                padding: 16px;
            }
        }
    </style>
</head>

<body>
    <div class="recovery-container">
        <div class="recovery-content">
            <div class="key-icon">
                <i class="fas fa-key"></i>
            </div>
            <h2>Recovery Code</h2>
            <p class="subtitle">Enter one of your backup recovery codes</p>

            <div class="warning-box">
                <i class="fas fa-exclamation-triangle"></i>
                <p>
                    <strong>⚠️ Important Notice</strong>
                    Each recovery code can only be used once. After using a recovery code, you should set up a new device for 2FA as soon as possible.
                </p>
            </div>

            @if ($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.recovery.verify') }}">
                @csrf
                <div class="input-group">
                    <label class="input-label">Recovery Code</label>
                    <div class="input-field">
                        <input type="text" name="recovery_code" placeholder="XXXX-XXXX-XXXX-XXXX" autocomplete="off" autofocus>
                    </div>
                    <div class="format-example">
                        <i class="fas fa-info-circle"></i> Format example: <code>ABCD-1234-EFGH-5678</code>
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check-circle"></i> Verify Recovery Code
                </button>
            </form>

            <hr>

            <a href="{{ route('2fa.verify') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to code verification
            </a>
        </div>
    </div>

    <script>
        // Auto-format recovery code input
        const recoveryInput = document.querySelector('input[name="recovery_code"]');
        if (recoveryInput) {
            recoveryInput.addEventListener('input', function(e) {
                let value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

                // Add hyphens every 4 characters
                let formatted = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formatted += '-';
                    }
                    formatted += value[i];
                }

                this.value = formatted.slice(0, 19); // Max length: 16 chars + 3 hyphens = 19
            });
        }
    </script>
</body>

</html>
