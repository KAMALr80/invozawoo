@extends('layouts.app')

@section('page-title', 'User Profile')

@section('content')
<div class="profile-container-premium">
    <!-- Profile Sidebar -->
    <div class="profile-sidebar-wrapper">
        <div class="profile-card-mini">
            <div class="profile-avatar-large">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="avatar">
                @else
                    {{ substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <h3 class="profile-name-title">{{ auth()->user()->name }}</h3>
            <p class="profile-role-subtitle">{{ ucfirst(auth()->user()->role) }}</p>
        </div>

        <nav class="profile-nav-vertical">
            <a href="{{ route('profile.index') }}" class="profile-nav-link {{ request()->routeIs('profile.index') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i>
                <span>Personal Information</span>
            </a>
            <a href="{{ route('profile.change-password') }}" class="profile-nav-link {{ request()->routeIs('profile.change-password') ? 'active' : '' }}">
                <i class="fas fa-key"></i>
                <span>Change Password</span>
            </a>
            <a href="{{ route('profile.security') }}" class="profile-nav-link {{ request()->routeIs('profile.security') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i>
                <span>Security & 2FA</span>
            </a>
            <a href="{{ route('profile.activity') }}" class="profile-nav-link {{ request()->routeIs('profile.activity') ? 'active' : '' }}">
                <i class="fas fa-history"></i>
                <span>Activity Log</span>
            </a>
        </nav>
    </div>

    <!-- Profile Content Area -->
    <div class="profile-content-wrapper">
        <div class="profile-glass-card">
            <div class="glass-header">
                <h2 class="glass-title">@yield('profile-section-title')</h2>
                <div class="glass-actions">
                    @yield('profile-section-actions')
                </div>
            </div>
            
            <div class="glass-body">
                @yield('profile-section-content')
            </div>
        </div>
    </div>
</div>

<style>
    .profile-container-premium {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 30px;
        align-items: start;
    }

    .profile-sidebar-wrapper {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        padding: 30px 20px;
        box-shadow: var(--shadow-sm);
    }

    .profile-card-mini {
        text-align: center;
        margin-bottom: 40px;
    }

    .profile-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 24px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        display: flex;
        align-items: center; justify-content: center;
        font-size: 42px; font-weight: 800;
        margin: 0 auto 15px;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.25);
        overflow: hidden;
    }

    .profile-avatar-large img { width: 100%; height: 100%; object-fit: cover; }

    .profile-name-title { font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0; }
    .profile-role-subtitle { font-size: 13px; color: var(--text-muted); font-weight: 600; margin-top: 5px; }

    .profile-nav-vertical { display: flex; flex-direction: column; gap: 8px; }

    .profile-nav-link {
        display: flex; align-items: center; gap: 15px; padding: 14px 20px; border-radius: 12px;
        text-decoration: none; color: var(--text-muted); font-weight: 700; font-size: 14px;
        transition: all 0.3s ease; border: 1px solid transparent;
    }

    .profile-nav-link:hover { background: var(--bg-light); color: var(--primary); }
    .profile-nav-link.active {
        background: rgba(14, 165, 233, 0.08); color: var(--primary); border-color: rgba(14, 165, 233, 0.1);
    }

    .profile-glass-card {
        background: var(--bg-white); border-radius: var(--radius-xl); border: 1px solid var(--border);
        box-shadow: var(--shadow-md); overflow: hidden;
    }

    .glass-header { padding: 25px 35px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .glass-title { font-size: 20px; font-weight: 800; color: var(--text-main); margin: 0; }
    .glass-body { padding: 35px; }

    @media (max-width: 991px) {
        .profile-container-premium { grid-template-columns: 1fr; }
        .profile-sidebar-wrapper { margin-bottom: 20px; }
    }
</style>
@endsection
