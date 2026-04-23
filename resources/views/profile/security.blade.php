@extends('layouts.profile')

@section('profile-section-title', 'Security Settings')

@section('profile-section-content')
    <div class="security-sections-premium">
        <!-- 2FA Section -->
        <div class="security-card-premium">
            <div class="security-icon-box {{ $twoFactorEnabled ? 'success' : 'warning' }}">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="security-info">
                <h4>Two-Factor Authentication</h4>
                <p>Add an extra layer of security to your account by requiring a verification code from your phone.</p>
                <div class="status-badge {{ $twoFactorEnabled ? 'active' : 'inactive' }}">
                    {{ $twoFactorEnabled ? 'Currently Enabled' : 'Currently Disabled' }}
                </div>
            </div>
            <div class="security-action">
                <a href="{{ route('2fa.setup') }}" class="btn-premium-action {{ $twoFactorEnabled ? 'secondary' : 'primary' }}">
                    {{ $twoFactorEnabled ? 'Manage 2FA' : 'Enable 2FA' }}
                </a>
            </div>
        </div>

        @if ($twoFactorEnabled && $remainingRecoveryCodes > 0)
            <div class="recovery-alert-premium">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 20px;"></i>
                    <div>
                        <div style="font-weight: 700; color: #92400e; font-size: 14px;">Recovery Codes Remaining: {{ $remainingRecoveryCodes }}</div>
                        <div style="color: #b45309; font-size: 13px;">Ensure you have these codes saved in a safe place.</div>
                    </div>
                </div>
                <a href="{{ route('2fa.recovery.generate') }}" class="btn-refresh-mini">
                    <i class="fas fa-sync-alt"></i> Regenerate
                </a>
            </div>
        @endif

        <div class="security-card-premium">
            <div class="security-icon-box info">
                <i class="fas fa-key"></i>
            </div>
            <div class="security-info">
                <h4>Change Password</h4>
                <p>It's a good idea to use a strong password that you're not using elsewhere.</p>
            </div>
            <div class="security-action">
                <a href="{{ route('profile.change-password') }}" class="btn-premium-action outline">
                    Update Password
                </a>
            </div>
        </div>

        <div class="security-card-premium">
            <div class="security-icon-box secondary">
                <i class="fas fa-history"></i>
            </div>
            <div class="security-info">
                <h4>Active Sessions</h4>
                <p>View and manage your active login sessions across different devices and browsers.</p>
            </div>
            <div class="security-action">
                <a href="{{ route('profile.activity') }}" class="btn-premium-action outline">
                    Review Activity
                </a>
            </div>
        </div>
    </div>

    <style>
        .security-sections-premium {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .security-card-premium {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 25px;
            background: var(--bg-light);
            border-radius: 16px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .security-card-premium:hover {
            border-color: var(--primary);
            background: var(--bg-white);
            box-shadow: var(--shadow-sm);
        }

        .security-icon-box {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        .security-icon-box.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .security-icon-box.warning { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .security-icon-box.info { background: rgba(14, 165, 233, 0.1); color: var(--primary); }
        .security-icon-box.secondary { background: rgba(99, 102, 241, 0.1); color: var(--secondary); }

        .security-info h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
        }

        .security-info p {
            margin: 0;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.5;
            max-width: 400px;
        }

        .status-badge {
            display: inline-block;
            margin-top: 10px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .status-badge.active { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-badge.inactive { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

        .security-action {
            margin-left: auto;
        }

        .recovery-alert-premium {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            padding: 20px;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-premium-action {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: inline-block;
        }
        .btn-premium-action.primary { background: var(--primary); color: white; }
        .btn-premium-action.secondary { background: var(--secondary); color: white; }
        .btn-premium-action.outline { background: transparent; border: 2px solid var(--border); color: var(--text-muted); }
        .btn-premium-action.outline:hover { border-color: var(--primary); color: var(--primary); }

        .btn-refresh-mini {
            padding: 8px 12px;
            background: #f59e0b;
            color: white;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            display: flex; align-items: center; gap: 8px;
        }

        @media (max-width: 767px) {
            .security-card-premium { flex-direction: column; text-align: center; }
            .security-action { margin-left: 0; width: 100%; }
            .btn-premium-action { width: 100%; }
        }
    </style>
@endsection
