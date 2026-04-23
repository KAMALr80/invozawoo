<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'INVOZA One') }} - Authentication</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ================= PROFESSIONAL AUTH DESIGN SYSTEM ================= */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #8b5cf6;
            --success: #10b981;
            --danger: #ef4444;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-light: #f3f4f6;
            --bg-white: #ffffff;
            --bg-dark: #111827;
            --border: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --font-sans: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* ================= ANIMATED BACKGROUND ================= */
        .auth-background {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: -1;
            overflow: hidden;
        }

        .auth-background::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            animation: rotate 30s linear infinite;
        }

        .auth-background::after {
            content: '';
            position: absolute;
            bottom: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 70%);
            animation: rotate 20s linear infinite reverse;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        /* ================= MAIN CONTAINER ================= */
        .auth-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
            z-index: 10;
        }

        /* ================= LOGO SECTION ================= */
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeInDown 0.6s ease;
        }

        .logo-wrapper {
            display: inline-block;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 12px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .logo-wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: white;
            font-size: 30px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4);
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .logo-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        /* ================= AUTH CARD ================= */
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeInUp 0.6s ease;
            transition: all 0.3s ease;
        }

        .auth-card:hover {
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.4);
            transform: translateY(-5px);
        }

        /* ================= FORM STYLES ================= */
        .auth-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            text-align: center;
        }

        .auth-subtitle {
            color: var(--text-muted);
            font-size: 14px;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-main);
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
            pointer-events: none;
            z-index: 1;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 15px;
            color: var(--text-main);
            background: white;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .form-input.error {
            border-color: var(--danger);
        }

        .error-message {
            color: var(--danger);
            font-size: 13px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ================= BUTTONS ================= */
        .auth-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            margin-top: 10px;
        }

        .auth-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }

        .auth-btn:active {
            transform: translateY(0);
        }

        /* ================= LINKS ================= */
        .auth-links {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
        }

        .auth-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .auth-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .auth-divider {
            color: var(--text-muted);
            margin: 0 10px;
        }

        /* ================= ANIMATIONS ================= */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */
        
        /* Large Desktop (1200px and above) */
        @media (min-width: 1200px) {
            .auth-container {
                max-width: 500px;
            }
        }

        /* Desktop (992px to 1199px) */
        @media (max-width: 1199px) {
            .auth-container {
                max-width: 450px;
            }
            
            .auth-card {
                padding: 35px;
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            .auth-container {
                max-width: 400px;
            }
            
            .logo-text {
                font-size: 26px;
            }
            
            .auth-card {
                padding: 30px;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            body {
                padding: 15px;
            }

            .auth-container {
                max-width: 100%;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .logo-text {
                font-size: 24px;
            }

            .logo-subtitle {
                font-size: 13px;
            }

            .auth-card {
                padding: 25px;
            }

            .auth-title {
                font-size: 22px;
            }

            .auth-subtitle {
                font-size: 13px;
            }

            .form-input {
                padding: 12px 14px 12px 44px;
                font-size: 14px;
            }

            .auth-btn {
                padding: 14px;
                font-size: 15px;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            body {
                padding: 10px;
            }

            .logo-wrapper {
                padding: 8px 20px;
            }

            .logo-icon {
                width: 45px;
                height: 45px;
                font-size: 22px;
            }

            .logo-text {
                font-size: 22px;
            }

            .logo-subtitle {
                font-size: 12px;
            }

            .auth-card {
                padding: 20px;
            }

            .auth-title {
                font-size: 20px;
            }

            .auth-subtitle {
                font-size: 12px;
                margin-bottom: 20px;
            }

            .form-group {
                margin-bottom: 15px;
            }

            .form-label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .form-input {
                padding: 10px 12px 10px 40px;
                font-size: 13px;
                border-radius: 12px;
            }

            .input-icon {
                left: 12px;
                font-size: 14px;
            }

            .auth-btn {
                padding: 12px;
                font-size: 14px;
                border-radius: 12px;
            }

            .auth-links {
                margin-top: 20px;
                font-size: 13px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .logo-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }

            .logo-text {
                font-size: 20px;
            }

            .logo-subtitle {
                font-size: 11px;
            }

            .auth-card {
                padding: 15px;
            }

            .auth-title {
                font-size: 18px;
            }

            .auth-subtitle {
                font-size: 11px;
            }

            .form-input {
                padding: 8px 10px 8px 35px;
                font-size: 12px;
            }

            .input-icon {
                left: 10px;
                font-size: 12px;
            }

            .auth-btn {
                padding: 10px;
                font-size: 13px;
            }

            .auth-links {
                font-size: 12px;
            }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            }

            .auth-card {
                background: rgba(30, 41, 59, 0.95);
                border-color: rgba(255, 255, 255, 0.1);
            }

            .auth-title {
                color: white;
            }

            .auth-subtitle {
                color: #94a3b8;
            }

            .form-label {
                color: #e2e8f0;
            }

            .form-input {
                background: #1e293b;
                border-color: #334155;
                color: white;
            }

            .form-input:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            }

            .input-icon {
                color: #94a3b8;
            }

            .auth-links {
                color: #94a3b8;
            }

            .auth-divider {
                color: #475569;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .auth-background {
                display: none;
            }

            .auth-card {
                box-shadow: none;
                border: 1px solid #000;
            }

            .auth-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="auth-background"></div>

    <!-- Main Container -->
    <div class="auth-container">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo-wrapper">
                <div class="logo-icon">
                    {{-- <x-application-logo class="w-8 h-8 fill-current text-white" /> --}}
                    <span>⚡</span>
                </div>
                <h1 class="logo-text">{{ config('app.name', 'INVOZA One') }}</h1>
                <p class="logo-subtitle">Business Intelligence System</p>
            </div>
        </div>

        <!-- Auth Card -->
        <div class="auth-card">
            {{ $slot }}
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; color: rgba(255,255,255,0.7); font-size: 12px;">
            &copy; {{ date('Y') }} {{ config('app.name', 'INVOZA One') }}. All rights reserved.
        </div>
    </div>

    <!-- JavaScript for Auto-focus and Validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus first input
            const firstInput = document.querySelector('input');
            if (firstInput) {
                firstInput.focus();
            }

            // Add animation classes to form elements
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.animation = `fadeInUp 0.6s ease ${index * 0.1}s both`;
            });

            const button = document.querySelector('.auth-btn');
            if (button) {
                button.style.animation = 'fadeInUp 0.6s ease 0.4s both';
            }

            const links = document.querySelector('.auth-links');
            if (links) {
                links.style.animation = 'fadeIn 0.6s ease 0.5s both';
            }
        });

        // Prevent form double submission
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Processing...';
                }
            });
        });
    </script>
</body>
</html>