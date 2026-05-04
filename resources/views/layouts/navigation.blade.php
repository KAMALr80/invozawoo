@php
    $logisticsEnabled = \Illuminate\Support\Facades\Cache::get('logistics_system_enabled', true);
@endphp

<style>
    /* ================= PREMIUM NAVIGATION COMPONENT STYLES ================= */
    .top-navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: var(--header-height);
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(15px) saturate(180%);
        -webkit-backdrop-filter: blur(15px) saturate(180%);
        border-bottom: 1px solid rgba(241, 245, 249, 0.5);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 clamp(15px, 3vw, 30px);
        z-index: 998;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-theme="dark"] .top-navbar {
        background: rgba(30, 41, 59, 0.7);
        border-bottom-color: rgba(51, 65, 85, 0.5);
    }

    @media (min-width: 992px) {
        .top-navbar {
            left: var(--sidebar-width);
        }
    }

    .navbar-left {
        display: flex;
        align-items: center;
        gap: clamp(10px, 2vw, 20px);
    }

    .menu-toggle {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border: none;
        border-radius: var(--radius-md);
        color: white;
        font-size: 20px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }

    .menu-toggle:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
    }

    @media (max-width: 991px) {
        .menu-toggle {
            display: flex;
        }
    }

    .page-title {
        font-size: clamp(16px, 2.5vw, 22px);
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Connectivity Status */
    .status-badge-offline {
        background: #fee2e2;
        color: #ef4444;
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #fecaca;
        animation: pulse-red 2s infinite;
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Premium Omni-Search Trigger */
    .search-trigger-premium {
        background: rgba(248, 250, 252, 0.8);
        padding: 10px 20px;
        border-radius: 14px;
        border: 1px solid var(--border);
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-muted);
        transition: all 0.3s ease;
        width: clamp(200px, 25vw, 400px);
    }

    [data-theme="dark"] .search-trigger-premium {
        background: rgba(15, 23, 42, 0.6);
    }

    .search-trigger-premium:hover {
        background: var(--bg-white);
        border-color: var(--primary);
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .search-kbd {
        margin-left: auto;
        font-size: 10px;
        font-weight: 800;
        background: var(--bg-white);
        padding: 2px 6px;
        border-radius: 6px;
        border: 1px solid var(--border);
        color: var(--text-muted);
    }

    /* Quick Actions */
    .quick-create-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    @media (max-width: 1200px) {
        .search-trigger-premium { width: auto; padding: 12px; }
        .search-trigger-premium span { display: none; }
        .search-kbd { display: none; }
    }

    @media (max-width: 768px) {
        .quick-create-actions { display: none; }
    }

    .btn-quick-create {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        color: white;
    }

    .btn-quick-create:hover {
        transform: translateY(-3px) scale(1.02);
        filter: brightness(1.1);
    }

    .btn-qc-sales { background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%); box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3); }
    .btn-qc-employee { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); box-shadow: 0 6px 15px rgba(14, 165, 233, 0.3); }

    /* Right Section */
    .user-section {
        display: flex;
        align-items: center;
        gap: clamp(10px, 1.5vw, 20px);
    }

    .nav-icon-btn {
        width: 42px;
        height: 42px;
        background: var(--bg-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 18px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        position: relative;
    }

    .nav-icon-btn:hover {
        background: var(--bg-white);
        color: var(--primary);
        border-color: var(--border);
        transform: translateY(-2px);
    }

    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: var(--danger);
        color: white;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 20px;
        border: 2px solid var(--bg-white);
        min-width: 20px;
        height: 20px;
        display: none;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
    }

    .notification-badge.active { display: flex; }

    /* Profile Trigger */
    .profile-trigger {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 5px 10px;
        background: rgba(14, 165, 233, 0.05);
        border-radius: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .profile-trigger:hover {
        background: rgba(14, 165, 233, 0.1);
        border-color: rgba(14, 165, 233, 0.2);
        transform: translateY(-1px);
    }

    .user-meta-header {
        text-align: right;
    }

    .user-name-header {
        font-weight: 800;
        font-size: 14px;
        color: var(--text-main);
        line-height: 1.1;
    }

    .user-role-header {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .user-avatar-header {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        box-shadow: 0 4px 10px rgba(14, 165, 233, 0.2);
        overflow: hidden;
    }

    .user-avatar-header img { width: 100%; height: 100%; object-fit: cover; }

    /* Dropdowns */
    .top-nav-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: 260px;
        background: var(--bg-white);
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid var(--border);
        padding: 10px;
        display: none;
        flex-direction: column;
        z-index: 1000;
        animation: dropdownIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes dropdownIn {
        from { opacity: 0; transform: translateY(15px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .top-nav-dropdown.active { display: flex; }

    .dropdown-header-premium {
        padding: 12px 15px;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-bottom: 1px solid var(--border);
        margin-bottom: 8px;
    }

    .top-nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        border-radius: 10px;
        color: var(--text-main);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .top-nav-item:hover {
        background: var(--bg-light);
        color: var(--primary);
        transform: translateX(5px);
    }

    .top-nav-item i { width: 20px; font-size: 16px; color: var(--text-muted); text-align: center; }
    .top-nav-item:hover i { color: var(--primary); }

    .logout-item:hover { background: #fff1f2; color: var(--danger); }
    .logout-item:hover i { color: var(--danger); }

    /* Mobile Adaptations */
    @media (max-width: 600px) {
        .page-title span:not(.status-badge-offline) { display: none; }
        .user-meta-header { display: none; }
        .search-trigger-premium { padding: 10px; }
    }
</style>

<header class="top-navbar">
    <div class="navbar-left">
        <button class="menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="page-title">
            <span>@php $navTitle = trim($__env->yieldContent('page-title')); @endphp {{ $navTitle ?: 'Dashboard' }}</span>
            <span id="connectivity-status" class="status-badge-offline" style="display: none;">
                <i class="fas fa-wifi-slash"></i> Offline
            </span>
        </div>
        
        <div class="search-trigger-premium" onclick="openOmniSearch()">
            <i class="fas fa-search"></i>
            <span>Search anything...</span>
            <div class="search-kbd">Ctrl + K</div>
        </div>

        <div class="quick-create-actions">
            @if (auth()->user()->hasPermission('view_sales'))
                <a href="{{ route('sales.create') }}" class="btn-quick-create btn-qc-sales">
                    <i class="fas fa-cash-register"></i> <span>POS</span>
                </a>
            @endif
            @if (auth()->user()->hasPermission('view_customers'))
                <a href="{{ route('customers.create') }}" class="btn-quick-create btn-qc-employee" style="background: linear-gradient(135deg, var(--secondary) 0%, #4f46e5 100%);">
                    <i class="fas fa-user-plus"></i> <span>Customer</span>
                </a>
            @endif
        </div>
    </div>

    <div class="user-section">
        @if (auth()->user()->role === 'admin')
            <div class="nav-icon-btn" onclick="toggleLogisticsSystem()" title="Logistics System">
                <i class="fas fa-truck-moving" style="color: {{ $logisticsEnabled ? 'var(--success)' : 'var(--danger)' }}"></i>
            </div>
        @endif

        <div class="nav-icon-btn" onclick="toggleTheme()" title="Switch Theme">
            <i class="fas fa-moon dark-icon"></i>
            <i class="fas fa-sun light-icon" style="display: none;"></i>
        </div>

        <div class="notification-dropdown-container" style="position: relative;">
            <div class="nav-icon-btn" onclick="toggleNotifications()">
                <i class="far fa-bell"></i>
                <span id="notificationBadge" class="notification-badge">0</span>
            </div>
            
            <div id="notificationDropdown" class="top-nav-dropdown" style="width: 350px;">
                <div class="dropdown-header-premium d-flex justify-between align-center">
                    <span>Notifications</span>
                    <a href="#" onclick="markAllNotificationsAsRead(event)" style="color: var(--primary); font-size: 10px;">Mark read</a>
                </div>
                <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                    <div style="padding: 20px; text-align: center; color: var(--text-muted); font-size: 13px;">
                        Loading notifications...
                    </div>
                </div>
                <div style="padding: 10px; border-top: 1px solid var(--border); text-align: center;">
                    <a href="#" style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-decoration: none;">View All Activity</a>
                </div>
            </div>
        </div>

        <div class="profile-dropdown-container" style="position: relative;">
            <div class="profile-trigger" onclick="toggleTopDropdown('userMenu')">
                <div class="user-meta-header">
                    <div class="user-name-header">{{ auth()->user()->name }}</div>
                    <div class="user-role-header">{{ auth()->user()->role }}</div>
                </div>
                <div class="user-avatar-header">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="avatar">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted);"></i>
            </div>

            <div id="userMenu" class="top-nav-dropdown">
                <div class="dropdown-header-premium">Account Settings</div>
                <a href="{{ route('profile.index') }}" class="top-nav-item">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
                </a>
                <a href="{{ route('dashboard') }}" class="top-nav-item">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
                <div style="height: 1px; background: var(--border); margin: 8px 12px;"></div>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="top-nav-item logout-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sign Out</span>
                </a>
            </div>
        </div>
    </div>
</header>