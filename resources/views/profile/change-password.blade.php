@extends('layouts.profile')

@section('profile-section-title', 'Change Account Password')

@section('profile-section-content')
    <div style="max-width: 500px;">
        @if (session('status') === 'password-updated')
            <div class="alert-premium success">
                <i class="fas fa-check-circle"></i> Password updated successfully!
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-premium danger">
                @foreach ($errors->all() as $error)
                    <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update-password') }}" class="premium-form">
            @csrf
            @method('PUT')

            <div class="form-group-premium">
                <label>Current Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="current_password" required placeholder="Enter current password">
                </div>
            </div>

            <div class="form-group-premium">
                <label>New Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" required placeholder="Min. 8 characters">
                </div>
                <p class="input-hint">Use a combination of letters, numbers, and symbols.</p>
            </div>

            <div class="form-group-premium">
                <label>Confirm New Password</label>
                <div class="input-with-icon">
                    <i class="fas fa-shield-alt"></i>
                    <input type="password" name="password_confirmation" required placeholder="Repeat new password">
                </div>
            </div>

            <div class="form-actions-premium">
                <button type="submit" class="btn-premium-save">
                    <i class="fas fa-sync-alt"></i> Update Password
                </button>
                <a href="{{ route('profile.index') }}" class="btn-premium-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <style>
        .alert-premium {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .alert-premium.success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
        .alert-premium.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

        .premium-form { display: flex; flex-direction: column; gap: 20px; }

        .form-group-premium label {
            display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-icon i {
            position: absolute;
            left: 18px;
            color: var(--text-muted);
            font-size: 14px;
        }

        .input-with-icon input {
            width: 100%;
            padding: 14px 18px 14px 45px;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: var(--bg-light);
            color: var(--text-main);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg-white);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .input-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            font-weight: 500;
        }

        .form-actions-premium {
            display: flex; gap: 15px; margin-top: 20px; padding-top: 25px; border-top: 1px solid var(--border);
        }

        .btn-premium-save {
            flex: 1.5; padding: 14px; background: var(--primary); color: white; border: none; border-radius: 12px;
            font-weight: 700; font-size: 15px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
        }

        .btn-premium-cancel {
            flex: 1; padding: 14px; background: var(--bg-light); color: var(--text-muted); text-align: center; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 15px;
        }
    </style>
@endsection
