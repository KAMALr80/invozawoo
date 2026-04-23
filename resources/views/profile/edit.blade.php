@extends('layouts.profile')

@section('profile-section-title', 'Edit Profile Details')

@section('profile-section-content')
    @if (session('status') === 'profile-updated')
        <div class="alert-premium success">
            <i class="fas fa-check-circle"></i> Profile updated successfully!
        </div>
    @endif

    @if ($errors->any())
        <div class="alert-premium danger">
            @foreach ($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="premium-form">
        @csrf
        @method('PATCH')

        <div class="form-group-premium">
            <label>Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Enter your full name">
        </div>

        <div class="form-group-premium">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Enter your email">
        </div>

        <div class="form-group-premium">
            <label>Mobile Number</label>
            <input type="text" name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="e.g. +91 9876543210">
        </div>

        <div class="form-actions-premium">
            <button type="submit" class="btn-premium-save">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('profile.index') }}" class="btn-premium-cancel">
                Cancel
            </a>
        </div>
    </form>

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
        .alert-premium.success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .alert-premium.danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .premium-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group-premium label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .form-group-premium input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: var(--bg-light);
            color: var(--text-main);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-group-premium input:focus {
            outline: none;
            border-color: var(--primary);
            background: var(--bg-white);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .form-actions-premium {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
        }

        .btn-premium-save {
            flex: 1;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-premium-save:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
        }

        .btn-premium-cancel {
            flex: 1;
            padding: 14px;
            background: var(--bg-light);
            color: var(--text-muted);
            text-align: center;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-premium-cancel:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }
    </style>
@endsection
