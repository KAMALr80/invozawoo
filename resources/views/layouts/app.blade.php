<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Add these BEFORE your content -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <meta name="google-maps-key" content="{{ config('services.google.maps_api_key') }}">
    <meta charset="UTF-8">
    <title>INVOZA One - @yield('page-title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --secondary: #6366f1;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --info: #0ea5e9;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #f1f5f9;
            --bg-light: #f8fafc;
            --bg-white: #ffffff;
            --bg-sidebar: #ffffff;
            --card-bg: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
            --radius-sm: 8px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 18px;
            --radius-2xl: 24px;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 0px;
            --header-height: 80px;
        }

        [data-theme="dark"] {
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #1e293b;
            --bg-light: #0f172a;
            --bg-white: #1e293b;
            --bg-sidebar: #1e293b;
            --card-bg: #1e293b;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background: var(--bg-light);
            color: var(--text-main);
            line-height: 1.5;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ================= SIDEBAR OVERLAY (Mobile) ================= */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* ================= MAIN SIDEBAR ================= */
        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--bg-sidebar);
            color: var(--text-main);
            padding: 25px 15px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #f1f5f9;
            z-index: 1000;
            overflow-y: auto;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop - Sidebar always visible */
        @media (min-width: 992px) {
            #sidebar {
                transform: translateX(0);
            }
        }

        /* Mobile/Tablet - Sidebar hidden by default */
        @media (max-width: 991px) {
            #sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            #sidebar.active {
                transform: translateX(0);
                box-shadow: 4px 0 30px rgba(0, 0, 0, 0.3);
            }
        }

        /* Custom scrollbar for Webkit browsers */
        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: #1f2937;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 3px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* ========== SIDEBAR CLOSE BUTTON (Mobile Only) ========== */
        .sidebar-close {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            color: white;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 1001;
        }

        .sidebar-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        @media (max-width: 991px) {
            .sidebar-close {
                display: flex;
            }
        }

        /* Logo Section */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding: 10px 15px;
            background: linear-gradient(to right, var(--bg-light), transparent);
            border-radius: var(--radius-md);
        }

        .logo-icon {
            background: var(--primary);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
        }

        .logo-text {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #0f172a 0%, #334155 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Premium User Profile in Sidebar */
        .user-info-sidebar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            background: var(--bg-light);
            border-radius: var(--radius-lg);
            margin-bottom: 25px;
            border: 1px solid var(--border);
        }

        .user-avatar-sm {
            width: 42px;
            height: 42px;
            background: var(--bg-white);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            border: 2px solid var(--bg-white);
        }
        
        .user-avatar-sm img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-meta-sm {
            flex: 1;
        }

        .user-name-sm {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .user-role-sm {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* User Info */
        .user-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--radius-lg);
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease;
        }

        .user-info:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.08);
        }

        .user-details {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            width: 45px;
            height: 45px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .user-name {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .user-role {
            font-size: 12px;
            color: #cbd5e1;
            word-break: break-word;
        }

        /* Navigation Menu */
        .nav-menu {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 20px;
        }

        /* Dropdown Styles */
        .nav-item {
            width: 100%;
        }

        .nav-link {
            color: #64748b;
            text-decoration: none;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
            margin-bottom: 4px;
            width: 100%;
            background: transparent;
            border: none;
            cursor: pointer;
            text-align: left;
        }

        .nav-link:hover {
            background: #f8fafc;
            color: var(--primary);
        }

        .nav-link.active {
            background: var(--primary);
            color: white !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
        }

        .nav-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 20px 0 10px 16px;
        }

        .nav-icon {
            font-size: 18px;
            width: 20px;
            display: flex;
            justify-content: center;
        }

        .dropdown-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .dropdown-icon.rotate {
            transform: rotate(90deg);
        }

        .dropdown-menu {
            list-style: none;
            padding-left: 20px;
            margin-left: 25px;
            margin-top: 4px;
            margin-bottom: 8px;
            display: none;
            animation: slideDown 0.3s ease;
            border-left: 1px solid #f1f5f9;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            color: #475569;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 2px;
        }

        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: var(--primary);
            transform: translateX(5px);
        }

        .dropdown-item.active {
            background: #f1f5f9;
            color: var(--primary);
            font-weight: 700;
        }

        /* Badges & Counters */
        .badge-count {
            background: var(--danger);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: auto;
            margin-right: 8px;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }

        .badge-dot-red {
            background: #fee2e2;
            color: #ef4444;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
        }

        .d-flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .align-center { align-items: center; }

        /* Premium Logout Button */
        .logout-btn-premium {
            width: 100%;
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #ffe4e6;
            padding: 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn-premium:hover {
            background: #e11d48;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.2);
        }

        /* Sidebar Footer */
        .sidebar-footer-premium {
            margin-top: auto;
            padding: 20px 15px;
            font-size: 11px;
            color: #94a3b8;
            font-weight: 600;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }

        /* ================= TOP NAVBAR ================= */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: var(--bg-white);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 998;
            transition: left 0.3s ease;
        }

        /* Desktop - Navbar starts after sidebar */
        @media (min-width: 992px) {
            .top-navbar {
                left: var(--sidebar-width);
            }
        }

        /* Navbar left section */
        .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Menu Toggle Button (Mobile/Tablet only) */
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
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .menu-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
        }

        @media (max-width: 991px) {
            .menu-toggle {
                display: flex;
            }
        }

        .page-title {
            font-size: clamp(18px, 3vw, 22px);
            font-weight: 700;
            color: var(--text-main);
            word-break: break-word;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-detail {
            text-align: right;
        }

        .user-fullname {
            font-weight: 600;
            color: var(--text-main);
            font-size: 15px;
            word-break: break-word;
        }

        .user-badge {
            font-size: 13px;
            color: var(--text-muted);
            background: var(--bg-light);
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            word-break: break-word;
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
            white-space: nowrap;
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.3);
        }

        /* ================= MAIN CONTENT ================= */
        .main-content {
            flex: 1;
            margin-top: var(--header-height);
            min-height: calc(100vh - var(--header-height));
            padding: 30px;
            background: var(--bg-light);
            overflow-y: auto;
            transition: margin-left 0.3s ease;
        }

        /* Desktop - Content starts after sidebar */
        @media (min-width: 992px) {
            .main-content {
                margin-left: var(--sidebar-width);
            }
        }

        /* ================= TOAST NOTIFICATION ================= */
        .toast-notification {
            position: fixed;
            top: 90px;
            right: 30px;
            background: var(--success);
            color: #fff;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            animation: slideIn 0.3s ease;
            max-width: 90%;
            word-break: break-word;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* ================= ANIMATIONS ================= */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-link,
        .dropdown-item,
        .user-info {
            animation: fadeIn 0.5s ease;
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */

        /* Large Desktop (1200px and above) */
        @media (min-width: 1200px) {
            .main-content {
                padding: 30px;
            }
        }

        /* Desktop (992px to 1199px) */
        @media (min-width: 992px) and (max-width: 1199px) {
            .main-content {
                padding: 25px;
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            .top-navbar {
                padding: 0 20px;
            }

            .main-content {
                padding: 20px;
            }

            .page-title {
                font-size: 18px;
            }

            .user-detail {
                display: none;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            .top-navbar {
                padding: 0 15px;
            }

            .main-content {
                padding: 15px;
            }

            .user-section {
                gap: 10px;
            }

            .logout-btn {
                padding: 8px 15px;
                font-size: 13px;
            }

            .logout-btn span {
                font-size: 16px;
            }

            .toast-notification {
                top: 80px;
                right: 15px;
                left: 15px;
                max-width: none;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .top-navbar {
                padding: 0 12px;
            }

            .page-title {
                font-size: 16px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 12px;
            }

            .logout-btn span {
                font-size: 14px;
            }

            .main-content {
                padding: 12px;
            }

            #sidebar {
                width: 260px;
            }

            .dropdown-menu {
                padding-left: 46px;
            }

            .dropdown-item {
                padding: 8px 12px;
                font-size: 13px;
            }

            .nav-link {
                padding: 12px 14px;
                font-size: 14px;
            }

            .nav-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .top-navbar {
                padding: 0 8px;
            }

            .page-title {
                font-size: 14px;
            }

            .logout-btn {
                padding: 4px 8px;
                font-size: 11px;
            }

            .logout-btn span {
                font-size: 12px;
            }

            .main-content {
                padding: 8px;
            }

            #sidebar {
                width: 240px;
                padding: 20px 15px;
            }

            .nav-link {
                padding: 10px 12px;
                font-size: 13px;
            }

            .nav-icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
            }

            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .user-name {
                font-size: 14px;
            }

            .user-role {
                font-size: 11px;
            }
        }

        /* Print Styles */
        @media print {

            #sidebar,
            .top-navbar,
            .toast-notification,
            .menu-toggle,
            .sidebar-close,
            .sidebar-overlay {
                display: none !important;
            }

            .main-content {
                margin-left: 0;
                margin-top: 0;
                padding: 0;
            }
        }

        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid var(--border) !important;
            border-radius: var(--radius-md) !important;
            padding: 8px 12px !important;
            margin-left: 10px !important;
        }

        .dataTables_wrapper .dataTables_length select {
            border: 2px solid var(--border) !important;
            border-radius: var(--radius-md) !important;
            padding: 6px 10px !important;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 10px;
        }

        .dt-button {
            background: var(--primary) !important;
            color: white !important;
            border: none !important;
            border-radius: var(--radius-sm) !important;
            padding: 8px 16px !important;
            margin-right: 5px !important;
            cursor: pointer !important;
        }

        .dt-button:hover {
            background: var(--primary-dark) !important;
        }

        @media (max-width: 767px) {
            .dataTables_wrapper .dt-buttons {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .dt-button {
                width: 100%;
                margin-right: 0 !important;
            }
        }
        /* ================= PREMIUM PROFILE DROPDOWN ================= */
        .profile-dropdown-container {
            position: relative;
            z-index: 1001;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 8px;
            padding-right: 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 1px solid transparent;
            background: rgba(0,0,0,0.02);
        }

        .profile-trigger:hover {
            background: var(--bg-light);
            border-color: var(--border);
        }

        .user-meta-header {
            text-align: right;
            display: flex;
            flex-direction: column;
        }

        .user-name-header {
            font-weight: 700;
            font-size: 14px;
            color: var(--text-main);
            line-height: 1.2;
        }

        .user-role-header {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .user-avatar-header {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            overflow: hidden;
            background: var(--primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
            box-shadow: 0 4px 10px rgba(14, 165, 233, 0.2);
        }

        .user-avatar-header img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .top-nav-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 220px;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            padding: 8px;
            display: none;
            flex-direction: column;
            animation: slideUp 0.3s ease forwards;
        }

        .top-nav-dropdown.active {
            display: flex;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-header-premium {
            padding: 10px 12px;
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .top-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-main);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .top-nav-item:hover {
            background: var(--bg-light);
            color: var(--primary);
        }

        .top-nav-item i {
            width: 20px;
            font-size: 16px;
            color: var(--text-muted);
        }

        .top-nav-item:hover i {
            color: var(--primary);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 8px 12px;
        }

        .logout-link:hover {
            background: rgba(239, 68, 68, 0.05);
            color: var(--danger);
        }

        .logout-link:hover i {
            color: var(--danger);
        }

        @media (max-width: 767px) {
            .user-meta-header { display: none; }
            .top-nav-dropdown { width: 180px; }
        }
        /* ================= PREMIUM OMNI-SEARCH (COMMAND PALETTE) ================= */
        .omni-search-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: none;
            align-items: flex-start;
            justify-content: center;
            padding-top: 15vh;
            z-index: 9999;
            transition: all 0.3s ease;
        }

        .omni-search-modal.active {
            display: flex;
        }

        .omni-search-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .omni-search-modal.active .omni-search-backdrop {
            opacity: 1;
        }

        .omni-search-box {
            width: 100%;
            max-width: 650px;
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border);
            overflow: hidden;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .omni-search-modal.active .omni-search-box {
            transform: translateY(0);
            opacity: 1;
        }

        .omni-search-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 15px;
            background: var(--bg-light);
        }

        .omni-search-header i {
            font-size: 20px;
            color: var(--primary);
        }

        .omni-search-header input {
            flex: 1;
            background: transparent;
            border: none;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-main);
            outline: none;
        }

        .omni-search-body {
            max-height: 400px;
            overflow-y: auto;
            padding: 15px;
        }

        .omni-search-footer {
            padding: 12px 20px;
            background: var(--bg-light);
            border-top: 1px solid var(--border);
            display: flex;
            gap: 20px;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 700;
        }

        .omni-search-footer span kbd {
            background: var(--bg-white);
            border: 1px solid var(--border);
            padding: 2px 6px;
            border-radius: 4px;
            margin-right: 4px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .search-group {
            margin-bottom: 15px;
        }

        .search-group-title {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 10px 0 10px 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-group-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .search-item-premium {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .search-item-premium:hover, .search-item-premium.selected {
            background: var(--bg-light);
            transform: translateX(5px);
        }

        .search-item-premium i {
            width: 32px;
            height: 32px;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: var(--text-muted);
            transition: all 0.2s ease;
        }

        .search-item-premium:hover i, .search-item-premium.selected i {
            color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.1);
        }

        .search-item-info {
            display: flex;
            flex-direction: column;
        }

        .search-item-name {
            font-weight: 700;
            font-size: 14px;
        }

        .search-item-path {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        #main-area.blur-active {
            filter: blur(5px) brightness(0.8);
            pointer-events: none;
        }
        /* ================= NOTIFICATION SYSTEM ================= */
        .notification-dropdown-container {
            position: relative;
        }

        .notification-trigger {
            position: relative;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 20px;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .notification-trigger:hover {
            background: var(--bg-light);
            color: var(--primary);
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 2px 5px;
            border-radius: 10px;
            border: 2px solid var(--bg-white);
            min-width: 18px;
            height: 18px;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .notification-badge.active {
            display: flex;
        }

        .notification-dropdown {
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            width: 350px;
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
            z-index: 1000;
            display: none;
            flex-direction: column;
            overflow: hidden;
            animation: dropdownIn 0.3s ease;
        }

        .notification-dropdown.active {
            display: flex;
        }

        .notification-header {
            padding: 15px 20px;
            background: var(--bg-light);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-header h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
        }

        .mark-all-read-btn {
            font-size: 11px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .notification-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            gap: 15px;
            padding: 15px 20px;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.2s ease;
            background: #fff;
        }

        .notification-item:hover {
            background: var(--bg-light);
        }

        .notification-item.unread {
            background: rgba(14, 165, 233, 0.03);
            border-left: 3px solid var(--primary);
        }

        .noti-icon {
            width: 40px;
            height: 40px;
            background: var(--bg-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 16px;
            flex-shrink: 0;
        }

        .noti-content {
            flex: 1;
        }

        .noti-title {
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 3px;
            display: block;
        }

        .noti-message {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.4;
            display: block;
        }

        .noti-time {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 5px;
            font-weight: 600;
        }

        .notification-footer {
            padding: 12px;
            text-align: center;
            background: var(--bg-light);
            border-top: 1px solid var(--border);
        }

        .notification-footer a {
            font-size: 12px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 700;
        }

        .no-notifications {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
        }
        /* ================= ELITE PRO STYLES ================= */
        .elite-nav-item {
            position: relative;
            overflow: hidden;
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.05) 0%, transparent 100%) !important;
            border-left: 3px solid transparent;
            transition: all 0.3s ease !important;
        }
        .elite-nav-item.active {
            border-left-color: var(--primary);
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.1) 0%, transparent 100%) !important;
        }
        .elite-icon-box {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .elite-pulse {
            position: absolute;
            top: -2px; right: -2px;
            width: 8px; height: 8px;
            background: #10b981;
            border-radius: 50%;
            border: 2px solid var(--bg-sidebar);
        }
        .elite-pro-badge {
            margin-left: auto;
            font-size: 9px;
            font-weight: 900;
            padding: 2px 6px;
            border-radius: 6px;
            background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
            color: white;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
        }
        .elite-nav-item:hover .elite-pro-badge {
            transform: scale(1.1);
            filter: brightness(1.1);
        }
    </style>

    @stack('styles')
</head>

<body>
    {{-- Impersonation Alert Banner --}}
    @if(session()->has('impersonator_id'))
        <div class="impersonation-banner animate__animated animate__fadeInDown">
            <div class="banner-content">
                <i class="fas fa-user-secret me-2"></i>
                <span>Viewing as <strong>{{ auth()->user()->name }}</strong> (Staff Mode)</span>
                <a href="{{ route('admin.users.access.stop') }}" class="btn-return-admin">
                    <i class="fas fa-undo-alt me-1"></i> Return to Admin Control
                </a>
            </div>
        </div>
        <style>
            .impersonation-banner {
                background: linear-gradient(90deg, #1e293b 0%, #334155 100%);
                color: white; padding: 10px 0; text-align: center;
                border-bottom: 2px solid #0ea5e9; position: sticky; top: 0; z-index: 9999;
            }
            .banner-content { display: flex; align-items: center; justify-content: center; gap: 15px; font-size: 14px; }
            .btn-return-admin {
                background: #0ea5e9; color: white; padding: 4px 12px; border-radius: 6px;
                text-decoration: none; font-weight: 800; font-size: 12px;
                transition: 0.3s; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.3);
            }
            .btn-return-admin:hover { background: #0284c7; transform: translateY(-1px); color: white; }
        </style>
    @endif

    {{-- ================= OMNI-SEARCH MODAL ================= --}}
    <div id="omniSearchModal" class="omni-search-modal">
        <div class="omni-search-backdrop" onclick="closeOmniSearch()"></div>
        <div class="omni-search-box">
            <div class="omni-search-header">
                <i class="fas fa-search"></i>
                <input type="text" id="omniSearchInput" placeholder="Search modules, pages, or tools..." autocomplete="off">
            </div>
            <div class="omni-search-body" id="omniSearchResults">
                <!-- Dynamic Results -->
            </div>
            <div class="omni-search-footer">
                <span><kbd>↑↓</kbd> Navigate</span>
                <span><kbd>Enter</kbd> Select</span>
                <span><kbd>Esc</kbd> Close</span>
                <span style="margin-left: auto;">Shortcut: <kbd>Ctrl</kbd> + <kbd>/</kbd></span>
            </div>
        </div>
    </div>

    {{-- ================= MAIN SIDEBAR ================= --}}
    {{-- ================= MAIN SIDEBAR ================= --}}
    <div id="sidebar">
        {{-- Close Button (Mobile only) --}}
        <button class="sidebar-close" id="sidebarClose" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>

        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-icon"><i class="fas fa-bolt"></i></div>
            <h1 class="logo-text">INVOZA One</h1>
        </div>

        {{-- User Info (Premium Style) --}}
        <div class="user-info-sidebar">
            <div class="user-avatar-sm">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <div class="user-meta-sm">
                <div class="user-name-sm">{{ auth()->user()->name }}</div>
                <div class="user-role-sm">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
        </div>

        <nav class="nav-menu">
            {{-- 1. Dashboard --}}
            @if(auth()->user()->hasPermission('view_dashboard'))
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie nav-icon"></i>
                    <span>Dashboard</span>
                </a>
            @endif



            {{-- 3. Logistics Dropdown --}}
            @if (\Illuminate\Support\Facades\Cache::get('logistics_system_enabled', true) && auth()->user()->hasPermission('view_logistics'))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('logisticsDropdown')" id="logisticsBtn">
                        <i class="fas fa-truck-loading nav-icon"></i>
                        <span>Logistics</span>
                        <i class="fas fa-chevron-right dropdown-icon" id="logisticsIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="logisticsDropdown">
                        <a href="{{ route('logistics.shipments.index') }}" class="dropdown-item">
                            <i class="fas fa-box"></i> All Shipments
                        </a>
                        <a href="{{ route('logistics.shipments.create') }}" class="dropdown-item">
                            <i class="fas fa-plus-circle"></i> New Shipment
                        </a>
                        <a href="{{ route('logistics.agents.index') }}" class="dropdown-item">
                            <i class="fas fa-users-cog"></i> Delivery Agents
                        </a>
                        <a href="{{ route('logistics.agents.create') }}" class="dropdown-item">
                            <i class="fas fa-user-plus"></i> Add Agent
                        </a>
                        <a href="{{ route('logistics.service-areas') }}" class="dropdown-item">
                            <i class="fas fa-map-marked-alt"></i> Service Areas
                        </a>
                        <a href="{{ route('logistics.route-planner') }}" class="dropdown-item">
                            <i class="fas fa-route"></i> Route Planner
                        </a>
                        <a href="{{ route('logistics.reports') }}" class="dropdown-item">
                            <i class="fas fa-file-invoice"></i> Reports
                        </a>
                    </ul>
                </div>
            @endif

            {{-- 4. Agent Specific Menu --}}
            @if (auth()->user()->role === 'delivery_agent')
                <a href="{{ route('agent.dashboard') }}" class="nav-link {{ request()->routeIs('agent.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-motorcycle nav-icon"></i>
                    <span>Delivery Dashboard</span>
                </a>
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('deliveryDropdown')" id="deliveryBtn">
                        <i class="fas fa-shipping-fast nav-icon"></i>
                        <span>Deliveries</span>
                        <i class="fas fa-chevron-right dropdown-icon" id="deliveryIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="deliveryDropdown">
                        <a href="{{ route('agent.deliveries.active') }}" class="dropdown-item">
                            <i class="fas fa-play-circle"></i> Active Deliveries
                        </a>
                        <a href="{{ route('agent.deliveries.history') }}" class="dropdown-item">
                            <i class="fas fa-history"></i> History
                        </a>
                        <a href="{{ route('agent.deliveries.assigned') }}" class="dropdown-item">
                            <i class="fas fa-list-ul"></i> Assigned
                        </a>
                    </ul>
                </div>
                <a href="{{ route('agent.performance.index') }}" class="nav-link"><i class="fas fa-tachometer-alt nav-icon"></i> Performance</a>
                <a href="{{ route('agent.earnings') }}" class="nav-link"><i class="fas fa-wallet nav-icon"></i> Earnings</a>
            @endif

            {{-- 5. Reports Dropdown --}}
            @if (auth()->user()->hasPermission('view_reports'))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('reportsDropdown')" id="reportsBtn">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <span>Reports Center</span>
                        <i class="fas fa-chevron-right dropdown-icon" id="reportsIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="reportsDropdown">

                        @if (auth()->user()->hasPermission('view_sales_reports'))
                            <a href="{{ route('reports.sales') }}" class="dropdown-item"><i class="fas fa-chart-line"></i> Sales</a>
                        @endif
                        @if (auth()->user()->hasPermission('view_customers_reports'))
                            <a href="{{ route('reports.customers') }}" class="dropdown-item"><i class="fas fa-users"></i> Customers</a>
                        @endif
                        @if (auth()->user()->hasPermission('view_inventory_reports'))
                            <a href="{{ route('reports.inventory') }}" class="dropdown-item"><i class="fas fa-boxes"></i> Inventory</a>
                        @endif
                        @if (auth()->user()->hasPermission('view_employee_reports'))
                            <a href="{{ route('reports.employees') }}" class="dropdown-item"><i class="fas fa-user-clock"></i> Employees</a>
                        @endif
                        @if (auth()->user()->hasPermission('view_logistics_reports'))
                            <a href="{{ route('reports.logistics') }}" class="dropdown-item"><i class="fas fa-truck-moving"></i> Logistics</a>
                        @endif
                        @if (auth()->user()->hasPermission('view_purchase_reports'))
                            <a href="{{ route('reports.purchases') }}" class="dropdown-item"><i class="fas fa-shopping-cart"></i> Purchases</a>
                        @endif
                        @if (auth()->user()->hasPermission('view_attendance_reports'))
                            <a href="{{ route('reports.attendance') }}" class="dropdown-item"><i class="fas fa-calendar-check"></i> Attendance</a>
                        @endif
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('reports.financial') }}" class="dropdown-item"><i class="fas fa-file-invoice-dollar"></i> Financial</a>
                        @endif
                    </ul>
                </div>
            @endif

            {{-- 6. Approvals Dropdown --}}
            @if (auth()->user()->hasPermission('view_approvals'))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('approvalDropdown')" id="approvalBtn">
                        <i class="fas fa-check-double nav-icon"></i>
                        <span>Approvals</span>
                        @php $totalPending = ($sidebarPendingStaff ?? 0) + ($sidebarPendingAgent ?? 0) + ($sidebarPendingHr ?? 0); @endphp
                        @if($totalPending > 0)
                            <span class="badge-count">{{ $totalPending }}</span>
                        @endif
                        <i class="fas fa-chevron-right dropdown-icon" id="approvalIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="approvalDropdown">
                        <a href="{{ route('admin.staff.approval') }}" class="dropdown-item d-flex justify-between">
                            Staff Approvals @if(($sidebarPendingStaff ?? 0) > 0)<span class="badge-dot-red">{{ $sidebarPendingStaff }}</span>@endif
                        </a>
                        <a href="{{ route('admin.hr.approval') }}" class="dropdown-item d-flex justify-between">
                            HR Approvals @if(($sidebarPendingHr ?? 0) > 0)<span class="badge-dot-red">{{ $sidebarPendingHr }}</span>@endif
                        </a>
                        <a href="{{ route('admin.agent.approvals') }}" class="dropdown-item d-flex justify-between">
                            Agent Approvals @if(($sidebarPendingAgent ?? 0) > 0)<span class="badge-dot-red">{{ $sidebarPendingAgent }}</span>@endif
                        </a>
                    </ul>
                </div>
            @endif


            {{-- 7. Operational Pulse (Direct Link) --}}
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('dashboard.operations') }}" class="nav-link {{ request()->routeIs('dashboard.operations') ? 'active' : '' }}">
                    <i class="fas fa-microchip nav-icon"></i>
                    <span>Operational Pulse</span>
                </a>
            @endif

            {{-- 8-16. Individual Modules --}}
            @if (auth()->user()->hasPermission('view_employees'))
                <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees*') ? 'active' : '' }}">
                    <i class="fas fa-user-friends nav-icon"></i> <span>Employees</span>
                </a>
            @endif

            {{-- 8. Inventory Dropdown --}}
            @if (auth()->user()->hasPermission('view_inventory'))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('inventoryDropdown')" id="inventoryBtn">
                        <i class="fas fa-warehouse nav-icon"></i>
                        <span>Inventory</span>
                        <i class="fas fa-chevron-right dropdown-icon" id="inventoryIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="inventoryDropdown">
                        <a href="{{ route('inventory.index') }}" class="dropdown-item">
                            <i class="fas fa-boxes"></i> All Products
                        </a>
                        <a href="{{ route('inventory.create') }}" class="dropdown-item">
                            <i class="fas fa-plus-circle"></i> Add Product
                        </a>
                        <a href="{{ route('inventory.index') }}?stock=low" class="dropdown-item">
                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                        </a>
                    </ul>
                </div>
            @endif

            {{-- 9. Customers Dropdown --}}
            @if (auth()->user()->hasPermission('view_customers'))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('customersDropdown')" id="customersBtn">
                        <i class="fas fa-address-book nav-icon"></i>
                        <span>Customers</span>
                        <i class="fas fa-chevron-right dropdown-icon" id="customersIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="customersDropdown">
                        <a href="{{ route('customers.index') }}" class="dropdown-item">
                            <i class="fas fa-users"></i> All Customers
                        </a>
                        <a href="{{ route('customers.create') }}" class="dropdown-item">
                            <i class="fas fa-user-plus"></i> Add Customer
                        </a>
                    </ul>
                </div>
            @endif

            {{-- 10. Sales Dropdown --}}
            @if (auth()->user()->hasPermission('view_sales'))
                <div class="nav-item">
                    <button class="nav-link" onclick="toggleDropdown('salesDropdown')" id="salesBtn">
                        <i class="fas fa-hand-holding-usd nav-icon"></i>
                        <span>Sales Hub</span>
                        <i class="fas fa-chevron-right dropdown-icon" id="salesIcon"></i>
                    </button>
                    <ul class="dropdown-menu" id="salesDropdown">
                        <a href="{{ route('sales.index') }}" class="dropdown-item">
                            <i class="fas fa-list-ul"></i> All Sales
                        </a>
                        <a href="{{ route('sales.create') }}" class="dropdown-item">
                            <i class="fas fa-cash-register"></i> New POS Sale
                        </a>
                        <a href="{{ route('sales.index') }}?status=pending" class="dropdown-item">
                            <i class="fas fa-hourglass-half"></i> Pending Orders
                        </a>
                    </ul>
                </div>
            @endif

            @if (auth()->user()->hasPermission('view_attendance'))
                <a href="{{ route('attendance.manage') }}" class="nav-link {{ request()->routeIs('attendance.manage') ? 'active' : '' }}">
                    <i class="fas fa-clock nav-icon"></i> <span>Attendance</span>
                </a>
                @if (auth()->user()->role !== 'admin')
                    <a href="{{ route('attendance.my') }}" class="nav-link {{ request()->routeIs('attendance.my') ? 'active' : '' }}">
                        <i class="fas fa-user-clock nav-icon"></i> <span>My Attendance</span>
                    </a>
                @endif
            @endif

            @if (auth()->user()->hasPermission('view_leaves'))
                <a href="{{ route('leaves.manage') }}" class="nav-link {{ request()->routeIs('leaves.manage') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check nav-icon"></i> <span>Manage Leaves</span>
                </a>
                @if (auth()->user()->role !== 'admin')
                    <a href="{{ route('leaves.my') }}" class="nav-link {{ request()->routeIs('leaves.my') ? 'active' : '' }}">
                        <i class="fas fa-calendar-day nav-icon"></i> <span>My Leaves</span>
                    </a>
                @endif
            @endif


            {{-- 16-19. Admin & System Tools --}}
            @if (auth()->user()->hasPermission('manage_roles'))
                <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag nav-icon"></i> <span>Role Management</span>
                </a>
            @endif

            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.users.access') }}" class="nav-link {{ request()->routeIs('admin.users.access*') ? 'active' : '' }}">
                    <i class="fas fa-key nav-icon"></i> <span>User Login Details</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="nav-link {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt nav-icon"></i> <span>System Audit Logs</span>
                </a>
            @endif

            @if (auth()->user()->role === 'admin' || auth()->user()->hasPermission('woocommerce_access'))
                <a href="{{ route('woocommerce.index') }}" class="nav-link {{ request()->routeIs('woocommerce*') ? 'active' : '' }} elite-nav-item">
                    <div class="elite-icon-box">
                        <i class="fas fa-globe nav-icon"></i>
                        <span class="elite-pulse"></span>
                    </div>
                    <span>WooCommerce</span>
                    <span class="elite-pro-badge">PRO</span>
                </a>
            @endif

            @if (\Illuminate\Support\Facades\Cache::get('logistics_system_enabled', true) && (auth()->user()->hasPermission('manage_logistics') || auth()->user()->role === 'admin'))
                <a href="{{ route('admin.tracking.agents') }}" class="nav-link {{ request()->routeIs('admin.tracking.agents') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt nav-icon"></i> <span>Track All Agents</span>
                </a>
            @endif


        </nav>

        <div class="sidebar-footer-premium">
            INVOZA v4.2 • Premium ERP
        </div>
    </div>

    <!-- Main Content Area -->
    <div id="main-area">
        <header class="top-navbar">
            <div class="navbar-left">
                <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <h2 class="page-title">@yield('page-title', 'Dashboard')</h2>
                
                <div class="search-trigger-premium" onclick="openOmniSearch()" style="margin-left: 40px; width: 350px; background: var(--bg-light); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border); cursor: pointer; display: flex; align-items: center; gap: 12px; color: var(--text-muted); transition: all 0.3s ease;">
                    <i class="fas fa-search" style="font-size: 14px;"></i>
                    <span style="font-size: 14px; font-weight: 500;">Search anything...</span>
                    <span style="margin-left: auto; font-size: 10px; font-weight: 800; background: var(--bg-white); padding: 2px 6px; border-radius: 4px; border: 1px solid var(--border);">Ctrl + /</span>
                </div>
            </div>

            <div class="user-section" style="display: flex; align-items: center; gap: 20px;">
                @if (auth()->user()->role === 'admin')
                    @php
                        $logisticsEnabled = \Illuminate\Support\Facades\Cache::get('logistics_system_enabled', true);
                    @endphp
                    <div id="logisticsSystemToggle" onclick="toggleLogisticsSystem()" style="display: flex; align-items: center; gap: 8px; background: {{ $logisticsEnabled ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; padding: 6px 12px; border-radius: 30px; border: 1px solid {{ $logisticsEnabled ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }}; cursor: pointer; transition: all 0.3s ease;" title="Global Logistics System: {{ $logisticsEnabled ? 'ON' : 'OFF' }}">
                        <i class="fas fa-truck-moving" style="color: {{ $logisticsEnabled ? '#10b981' : '#ef4444' }}; font-size: 14px;"></i>
                        <span style="font-size: 11px; font-weight: 700; color: {{ $logisticsEnabled ? '#10b981' : '#ef4444' }};">Logistics: {{ $logisticsEnabled ? 'ON' : 'OFF' }}</span>
                    </div>
                @endif

                <div id="themeToggle" onclick="toggleTheme()" style="display: flex; align-items: center; gap: 10px; background: var(--bg-light); padding: 6px; border-radius: 30px; border: 1px solid var(--border); cursor: pointer;">
                   <div style="width: 34px; height: 34px; background: var(--bg-white); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); color: var(--text-muted); font-size: 11px; font-weight: 700;">Light</div>
                   <div style="color: var(--text-muted); font-size: 14px; padding-right: 10px;"><i class="fas fa-moon"></i></div>
                </div>

                <div class="notification-dropdown-container">
                    <div class="notification-trigger" onclick="toggleNotifications()">
                        <i class="far fa-bell"></i>
                        <span id="notificationBadge" class="notification-badge">0</span>
                    </div>
                    <div id="notificationDropdown" class="notification-dropdown">
                        <div class="notification-header">
                            <h4>Notifications</h4>
                            <a href="#" class="mark-all-read-btn" onclick="markAllNotificationsAsRead(event)">Mark all as read</a>
                        </div>
                        <div id="notificationList" class="notification-list">
                            <div class="no-notifications">Loading notifications...</div>
                        </div>
                        <div class="notification-footer">
                            <a href="#">View All Activity</a>
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown-container">
                    <div class="profile-trigger" onclick="toggleTopDropdown('userMenu')">
                        <div class="user-meta-header">
                            <div class="user-name-header">{{ auth()->user()->name }}</div>
                            <div class="user-role-header">{{ ucfirst(auth()->user()->role) }}</div>
                        </div>
                        <div class="user-avatar-header">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="avatar">
                            @else
                                {{ substr(auth()->user()->name, 0, 1) }}
                            @endif
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 10px; color: var(--text-muted); margin-left: 5px;"></i>
                    </div>

                    <div id="userMenu" class="top-nav-dropdown">
                        <div class="dropdown-header-premium">User Account</div>
                        <a href="{{ route('profile.index') }}" class="top-nav-item">
                            <i class="fas fa-user-circle"></i>
                            <span>My Profile</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="top-nav-item logout-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Log Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    {{-- ================= TOAST NOTIFICATION ================= --}}
    @if (session('success'))
        <div class="toast-notification">
            ✅ {{ session('success') }}
        </div>
        <script>
            setTimeout(() => {
                document.querySelector('.toast-notification')?.remove();
            }, 3000);
        </script>
    @endif

    {{-- ================= JAVASCRIPT LIBRARIES ================= --}}
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- DataTables Core -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- DataTables Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <!-- DataTables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- Chart.js (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    {{-- ================= CUSTOM JAVASCRIPT ================= --}}
    <script>
        // Theme Toggle Logic
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeToggleButton(newTheme);
            
            // Dispatch event for other components (like charts) to update
            window.dispatchEvent(new Event('theme-changed'));
        }

        function updateThemeToggleButton(theme) {
            const toggle = document.getElementById('themeToggle');
            if (!toggle) return;
            const lightLabel = toggle.querySelector('div:first-child');
            const icon = toggle.querySelector('i');
            
            if (theme === 'dark') {
                lightLabel.innerText = 'Dark';
                lightLabel.style.color = '#fff';
                lightLabel.parentElement.style.background = '#0f172a';
                icon.className = 'fas fa-sun';
                icon.parentElement.style.color = '#f59e0b';
            } else {
                lightLabel.innerText = 'Light';
                lightLabel.style.color = '#64748b';
                lightLabel.parentElement.style.background = '#f1f5f9';
                icon.className = 'fas fa-moon';
                icon.parentElement.style.color = '#94a3b8';
            }
        }

        // Initialize Theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.addEventListener('DOMContentLoaded', () => updateThemeToggleButton(savedTheme));

        // Global Logistics Toggle Function
        async function toggleLogisticsSystem() {
            const toggleBtn = document.getElementById('logisticsSystemToggle');
            if (!toggleBtn) return;
            
            toggleBtn.style.opacity = '0.5';
            toggleBtn.style.pointerEvents = 'none';

            try {
                const response = await fetch("{{ route('admin.logistics.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Refresh page to update sidebar and other layout elements
                    window.location.reload();
                } else {
                    alert('Failed to update system status.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                toggleBtn.style.opacity = '1';
                toggleBtn.style.pointerEvents = 'auto';
            }
        }

        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        // Function to open sidebar
        function openSidebar() {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        // Compatibility function for onclick="toggleSidebar()"
        function toggleSidebar() {
            if (sidebar.classList.contains('active')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }

        // Function to close sidebar
        function closeSidebar() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }

        // Toggle sidebar on menu button click
        if (menuToggle) {
            menuToggle.addEventListener('click', openSidebar);
        }

        // Close sidebar on close button click
        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }

        // Close sidebar on overlay click
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar on escape key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 991) {
                    // Desktop view - sidebar always visible
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }, 250);
        });

        // Dropdown toggle function
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const icon = document.getElementById(id.replace('Dropdown', 'Icon'));

            if (dropdown) {
                dropdown.classList.toggle('show');
            }

            if (icon) {
                icon.classList.toggle('rotate');
            }

            // Close other dropdowns
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            allDropdowns.forEach(d => {
                if (d.id !== id && d.classList.contains('show')) {
                    d.classList.remove('show');
                    const otherIcon = document.getElementById(d.id.replace('Dropdown', 'Icon'));
                    if (otherIcon) {
                        otherIcon.classList.remove('rotate');
                    }
                }
            });
        }

        // Top Navigation Dropdown
        function toggleTopDropdown(id) {
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('active');
            }
            
            // Close other dropdowns if any
            document.querySelectorAll('.top-nav-dropdown').forEach(d => {
                if (d.id !== id) d.classList.remove('active');
            });
        }

        // Close dropdowns on outside click
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.profile-dropdown-container')) {
                document.querySelectorAll('.top-nav-dropdown').forEach(d => d.classList.remove('active'));
            }
        });

        // Auto-open dropdown if any child is active
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = ['hr', 'inventory', 'sales', 'logistics', 'admin'];
            
            dropdowns.forEach(id => {
                const dropdown = document.getElementById(id + 'Dropdown');
                const icon = document.getElementById(id + 'Icon');
                
                if (dropdown) {
                    const activeItems = dropdown.querySelectorAll('.active');
                    if (activeItems.length > 0) {
                        dropdown.classList.add('show');
                        if (icon) {
                            icon.classList.add('rotate');
                        }
                    }
                }
            });

            // Close sidebar when clicking a link on mobile
            if (window.innerWidth <= 991) {
                document.querySelectorAll('#sidebar a, #sidebar button').forEach(link => {
                    link.addEventListener('click', function() {
                        setTimeout(closeSidebar, 200);
                    });
                });
            }
        });

        // Basic DataTable Initialization (will be overridden by specific page scripts)
        document.addEventListener('DOMContentLoaded', function() {
            if ($.fn.DataTable && $('.datatable').length) {
                $('.datatable').DataTable({
                    pageLength: 10,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search..."
                    }
                });
            }
        });
        // ================= OMNI-SEARCH (COMMAND PALETTE) LOGIC =================
        function openOmniSearch() {
            const modal = document.getElementById('omniSearchModal');
            const input = document.getElementById('omniSearchInput');
            const mainArea = document.getElementById('main-area');
            
            modal.classList.add('active');
            mainArea.classList.add('blur-active');
            setTimeout(() => input.focus(), 50);
            renderOmniResults('');
        }

        function closeOmniSearch() {
            const modal = document.getElementById('omniSearchModal');
            const mainArea = document.getElementById('main-area');
            const input = document.getElementById('omniSearchInput');
            
            modal.classList.remove('active');
            mainArea.classList.remove('blur-active');
            input.value = '';
        }

        let moduleIndex = [];
        let selectedIndex = -1;

        function buildModuleIndex() {
            const items = [];
            // Scrape sidebar for links
            document.querySelectorAll('#sidebar .nav-link, #sidebar .dropdown-item').forEach(el => {
                if (el.href && !el.href.includes('#') && !el.href.includes('logout')) {
                    const text = el.querySelector('span')?.innerText || el.innerText.trim();
                    const icon = el.querySelector('i')?.className || 'fas fa-link';
                    
                    // Determine path/category
                    let path = 'Navigation';
                    const parentDropdown = el.closest('.dropdown-menu');
                    if (parentDropdown) {
                        const parentBtn = document.getElementById(parentDropdown.id.replace('Dropdown', 'Btn'));
                        path = parentBtn?.querySelector('span')?.innerText || 'Modules';
                    }

                    items.push({ name: text, url: el.href, icon: icon, path: path });
                }
            });
            
            // Add top nav actions
            items.push({ name: 'My Profile', url: "{{ route('profile.index') }}", icon: 'fas fa-user-circle', path: 'Account' });
            items.push({ name: 'Security Settings', url: "{{ route('profile.security') }}", icon: 'fas fa-shield-alt', path: 'Account' });
            
            moduleIndex = items;
        }

        function renderOmniResults(query) {
            const container = document.getElementById('omniSearchResults');
            const filtered = moduleIndex.filter(item => 
                item.name.toLowerCase().includes(query.toLowerCase()) || 
                item.path.toLowerCase().includes(query.toLowerCase())
            );

            if (filtered.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding: 40px; color: var(--text-muted);">No results found for "${query}"</div>`;
                return;
            }

            // Group by path
            const groups = {};
            filtered.forEach(item => {
                if (!groups[item.path]) groups[item.path] = [];
                groups[item.path].push(item);
            });

            let html = '';
            for (const [path, items] of Object.entries(groups)) {
                html += `<div class="search-group"><div class="search-group-title">${path}</div>`;
                items.forEach(item => {
                    html += `
                        <a href="${item.url}" class="search-item-premium">
                            <i class="${item.icon}"></i>
                            <div class="search-item-info">
                                <span class="search-item-name">${item.name}</span>
                                <span class="search-item-path">${item.path}</span>
                            </div>
                        </a>
                    `;
                });
                html += `</div>`;
            }

            container.innerHTML = html;
            selectedIndex = -1;
        }

        document.addEventListener('DOMContentLoaded', () => {
            buildModuleIndex();
            const input = document.getElementById('omniSearchInput');

            input.addEventListener('input', (e) => renderOmniResults(e.target.value));

            input.addEventListener('keydown', (e) => {
                const items = document.querySelectorAll('.search-item-premium');
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    updateOmniHighlight(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, 0);
                    updateOmniHighlight(items);
                } else if (e.key === 'Enter') {
                    if (selectedIndex > -1) items[selectedIndex].click();
                } else if (e.key === 'Escape') {
                    closeOmniSearch();
                }
            });

            document.addEventListener('keydown', (e) => {
                // Trigger: Ctrl + / (Robust check for multiple layouts/browsers)
                const isSlash = (e.key === '/' || e.code === 'Slash' || e.keyCode === 191);
                
                if (e.ctrlKey && isSlash) {
                    // Don't trigger if user is typing in a form
                    if (document.activeElement.tagName === 'INPUT' || 
                        document.activeElement.tagName === 'TEXTAREA' || 
                        document.activeElement.isContentEditable) {
                        return;
                    }
                    
                    e.preventDefault();
                    openOmniSearch();
                }
            });
        });

        function updateOmniHighlight(items) {
            if (!items) return;
            items.forEach((item, i) => {
                if (item && item.classList) {
                    item.classList.toggle('selected', i === selectedIndex);
                    if (i === selectedIndex) item.scrollIntoView({ block: 'nearest' });
                }
            });
        }
        // ================= NOTIFICATION SYSTEM LOGIC =================
        let lastUnreadCount = 0;
        const notificationAudio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');

        async function fetchNotifications() {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 10000); // 10s timeout

                const response = await fetch("{{ route('notifications.unread') }}", {
                    signal: controller.signal,
                    headers: { 'Accept': 'application/json' }
                });
                
                clearTimeout(timeoutId);
                
                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                
                const data = await response.json();
                updateNotificationUI(data);
                
                // Play sound if new notifications arrived
                if (data && data.count > lastUnreadCount) {
                    notificationAudio.play().catch(e => {}); // Silent fail for audio
                }
                lastUnreadCount = data?.count || 0;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.log('Pulse Monitor: Notification sync standby.');
                }
            }
        }

        function updateNotificationUI(data) {
            if (!data) return;
            
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationList');
            
            // Update Badge
            if (badge) {
                const count = parseInt(data.count || 0);
                if (count > 0) {
                    badge.innerText = count;
                    badge.classList.add('active');
                } else {
                    badge.classList.remove('active');
                }
            }

            // Update List
            if (list) {
                const notifications = data.notifications || [];
                if (notifications.length === 0) {
                    list.innerHTML = `<div class="no-notifications">No new notifications</div>`;
                    return;
                }

                let html = '';
                notifications.forEach(n => {
                    if (n && n.id) {
                        html += `
                            <a href="${n.url || '#'}" class="notification-item unread" onclick="markNotificationAsRead(event, '${n.id}', '${n.url || '#'}')">
                                <div class="noti-icon">
                                    <i class="${n.icon || 'fas fa-bell'}"></i>
                                </div>
                                <div class="noti-content">
                                    <span class="noti-title">${n.title || 'System Alert'}</span>
                                    <span class="noti-message">${n.message || ''}</span>
                                    <span class="noti-time">${n.time || ''}</span>
                                </div>
                            </a>
                        `;
                    }
                });
                list.innerHTML = html;
            }
        }

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) {
                dropdown.classList.toggle('active');
                
                // Close other dropdowns
                document.querySelectorAll('.top-nav-dropdown').forEach(d => {
                    if (d !== dropdown) d.classList.remove('active');
                });
                
                if (dropdown.classList.contains('active')) {
                    fetchNotifications();
                }
            }
        }

        async function markNotificationAsRead(event, id, url) {
            event.preventDefault();
            try {
                await fetch(`/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                window.location.href = url;
            } catch (error) {
                console.error('Mark read error:', error);
                window.location.href = url;
            }
        }

        async function markAllNotificationsAsRead(event) {
            event.preventDefault();
            try {
                await fetch("{{ route('notifications.mark-all-read') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                fetchNotifications();
            } catch (error) {
                console.error('Mark all read error:', error);
            }
        }

        // Close notification dropdown on outside click
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.notification-dropdown-container')) {
                document.getElementById('notificationDropdown')?.classList.remove('active');
            }
        });

        // Poll for notifications every 30 seconds
        setInterval(fetchNotifications, 30000);
        document.addEventListener('DOMContentLoaded', fetchNotifications);
    </script>

    {{-- ================= PAGE SPECIFIC SCRIPTS ================= --}}
    @stack('scripts')
</body>

</html>
