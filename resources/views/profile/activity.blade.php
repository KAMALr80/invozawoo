@extends('layouts.profile')

@section('profile-section-title', 'Account Activity Log')

@section('profile-section-content')
    <div class="activity-log-premium">
        <!-- Login History -->
        <div class="log-section">
            <h3 class="log-group-title">Session Activity</h3>
            <div class="log-item-premium">
                <div class="log-icon-box">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="log-details">
                    <div class="log-main-text">Last Successful Login</div>
                    <div class="log-sub-text">
                        {{ $lastLoginAt ? \Carbon\Carbon::parse($lastLoginAt)->format('F d, Y • h:i A') : 'No login record found' }}
                    </div>
                </div>
                <div class="log-meta">
                    <span class="ip-badge"><i class="fas fa-network-wired"></i> {{ $lastLoginIp ?? '0.0.0.0' }}</span>
                </div>
            </div>
        </div>

        <!-- Account Milestones -->
        <div class="log-section" style="margin-top: 30px;">
            <h3 class="log-group-title">Account Milestones</h3>
            <div class="log-timeline-premium">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Account Created</div>
                        <div class="timeline-date">{{ auth()->user()->created_at->format('F d, Y • h:i A') }}</div>
                        <div class="timeline-desc">Official onboarding to INVOZA One ERP platform.</div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-dot {{ auth()->user()->two_factor_enabled ? 'success' : '' }}"></div>
                    <div class="timeline-content">
                        <div class="timeline-title">Two-Factor Authentication</div>
                        <div class="timeline-date">Current Status: {{ auth()->user()->two_factor_enabled ? 'Active' : 'Not Configured' }}</div>
                        <div class="timeline-desc">
                            {{ auth()->user()->two_factor_enabled ? 'Your account is currently secured with hardware/software MFA.' : 'MFA is recommended for administrative accounts.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .log-section {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .log-group-title {
            font-size: 13px;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--text-muted);
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .log-item-premium {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: var(--bg-light);
            border-radius: 16px;
            border: 1px solid var(--border);
        }

        .log-icon-box {
            width: 44px;
            height: 44px;
            background: var(--bg-white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 18px;
            box-shadow: var(--shadow-sm);
        }

        .log-main-text {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
        }

        .log-sub-text {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .log-meta { margin-left: auto; }

        .ip-badge {
            padding: 4px 10px;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
        }

        /* Timeline Styles */
        .log-timeline-premium {
            position: relative;
            padding-left: 30px;
        }

        .log-timeline-premium::before {
            content: '';
            position: absolute;
            left: 7px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 30px;
        }

        .timeline-dot {
            position: absolute;
            left: -30px;
            width: 16px;
            height: 16px;
            background: var(--bg-white);
            border: 3px solid var(--border);
            border-radius: 50%;
            z-index: 1;
        }

        .timeline-dot.success {
            border-color: #10b981;
            background: #10b981;
        }

        .timeline-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-main);
        }

        .timeline-date {
            font-size: 12px;
            color: var(--primary);
            font-weight: 700;
            margin-top: 2px;
        }

        .timeline-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 8px;
            line-height: 1.5;
        }
    </style>
@endsection
