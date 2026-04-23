@extends('layouts.profile')

@section('profile-section-title', 'Personal Information')

@section('profile-section-content')
    <div class="personal-info-grid">
        <div class="info-group">
            <label>Full Name</label>
            <div class="val-text">{{ auth()->user()->name }}</div>
        </div>
        <div class="info-group">
            <label>Email Address</label>
            <div class="val-text">{{ auth()->user()->email }}</div>
        </div>
        <div class="info-group">
            <label>Mobile Number</label>
            <div class="val-text">{{ auth()->user()->mobile ?? 'Not provided' }}</div>
        </div>
        <div class="info-group">
            <label>Department</label>
            <div class="val-text">{{ ucfirst(auth()->user()->role) }}</div>
        </div>
        <div class="info-group">
            <label>Member Since</label>
            <div class="val-text">{{ auth()->user()->created_at->format('F d, Y') }}</div>
        </div>
    </div>
    
    <div style="margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border);">
        <a href="{{ route('profile.edit') }}" class="btn-premium-action">
            <i class="fas fa-edit"></i> Edit Personal Details
        </a>
    </div>

    <style>
        .personal-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        .info-group label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--text-muted);
            letter-spacing: 1px;
            margin-bottom: 8px;
        }
        .info-group .val-text {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }
        .btn-premium-action {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn-premium-action:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
        }
    </style>
@endsection
