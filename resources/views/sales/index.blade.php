@extends('layouts.app')

@section('page-title', 'Sales Management')

@section('content')
<style>
    /* ================= PREMIUM PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #4f46e5;
        --primary-rgb: 79, 70, 229;
        --primary-dark: #4338ca;
        --success: #10b981;
        --success-rgb: 16, 185, 129;
        --danger: #f43f5e;
        --danger-rgb: 244, 63, 94;
        --warning: #f59e0b;
        --warning-rgb: 245, 158, 11;
        --text-main: #09090b;
        --text-muted: #71717a;
        --border: #e4e4e7;
        --bg-light: #fafafa;
        --bg-white: rgba(255, 255, 255, 0.95);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --glass-border: rgba(255, 255, 255, 0.6);
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-premium: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 24px;
        --font-sans: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
    }

    [data-theme="dark"] {
        --text-main: #fafafa;
        --text-muted: #a1a1aa;
        --border: #27272a;
        --bg-light: #09090b;
        --bg-white: rgba(24, 24, 27, 0.9);
        --glass-bg: rgba(9, 9, 11, 0.85);
        --glass-border: rgba(255, 255, 255, 0.05);
        --shadow-premium: 0 20px 40px -10px rgba(0, 0, 0, 0.7);
    }

    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    ::selection {
        background: rgba(var(--primary-rgb), 0.2);
        color: var(--primary-dark);
    }

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: var(--bg-light);
    }
    ::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-font-smoothing: antialiased;
    }

    body {
        background: var(--bg-light);
        font-family: var(--font-sans);
        color: var(--text-main);
        line-height: 1.6;
        overflow-x: hidden;
    }

    /* Background Orbs */
    .bg-orbs {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: -1;
        overflow: hidden;
        background: var(--bg-light);
        transition: background 0.5s ease;
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(120px);
        opacity: 0.15;
        animation: float 25s infinite alternate ease-in-out;
    }

    .orb-1 {
        width: 600px;
        height: 600px;
        background: var(--primary);
        top: -250px;
        right: -150px;
    }

    .orb-2 {
        width: 500px;
        height: 500px;
        background: #8b5cf6;
        bottom: -200px;
        left: -150px;
        animation-delay: -5s;
    }

    .orb-3 {
        width: 400px;
        height: 400px;
        background: var(--success);
        top: 30%;
        left: 15%;
        opacity: 0.1;
        animation-delay: -10s;
    }

    @keyframes float {
        0% { transform: translate(0, 0) rotate(0deg) scale(1); }
        33% { transform: translate(40px, 60px) rotate(5deg) scale(1.1); }
        66% { transform: translate(-30px, 30px) rotate(-5deg) scale(0.9); }
        100% { transform: translate(0, 0) rotate(0deg) scale(1); }
    }

    /* Main Container */
    .sales-page {
        min-height: 100vh;
        padding: clamp(20px, 5vw, 40px);
        width: 100%;
        position: relative;
    }

    .container {
        max-width: 1600px;
        margin: 0 auto;
        width: 100%;
    }

    /* Sales Dashboard */
    .sales-dashboard {
        background: var(--bg-white);
        backdrop-filter: blur(25px) saturate(180%);
        -webkit-backdrop-filter: blur(25px) saturate(180%);
        padding: clamp(24px, 5vw, 48px);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-premium);
        border: 1px solid var(--glass-border);
        width: 100%;
        position: relative;
        z-index: 1;
    }

    /* Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 48px;
        padding-bottom: 32px;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 24px;
    }

    .header-left {
        display: flex;
        align-items: flex-start;
        gap: 24px;
    }

    .header-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(145deg, var(--primary), var(--primary-dark));
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 20px 40px -10px rgba(var(--primary-rgb), 0.4);
        position: relative;
    }

    .header-icon::after {
        content: '';
        position: absolute;
        inset: -6px;
        border-radius: inherit;
        background: var(--primary);
        z-index: -1;
        opacity: 0.15;
        filter: blur(12px);
    }

    .header-icon span {
        font-size: 36px;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }

    .header-content h1 {
        margin: 0;
        font-size: clamp(32px, 5vw, 42px);
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -1.5px;
        line-height: 1;
    }

    .header-content p {
        margin: 10px 0 0;
        color: var(--text-muted);
        font-size: 17px;
        font-weight: 500;
    }

    /* Buttons */
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .btn-primary {
        background: var(--text-main);
        color: white;
        padding: 16px 36px;
        border-radius: var(--radius-md);
        border: 1px solid rgba(255,255,255,0.1);
        font-weight: 700;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.2);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -10px rgba(var(--primary-rgb), 0.5);
    }

    .btn-secondary {
        background: var(--bg-white);
        color: var(--text-main);
        padding: 16px 32px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        font-weight: 700;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
    }

    .btn-secondary:hover {
        background: var(--bg-light);
        border-color: var(--text-main);
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 48px;
    }

    .stat-card {
        background: var(--bg-white);
        padding: 24px;
        border-radius: var(--radius-lg);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid var(--border);
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary), var(--success), var(--warning));
        opacity: 0;
        transition: opacity 0.3s;
    }

    .stat-card:hover::before {
        opacity: 1;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-premium);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: rgba(var(--primary-rgb), 0.1);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .stat-text h3 {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-main);
        letter-spacing: -1px;
        margin: 0;
    }

    .stat-text p {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 8px 0 0;
    }

    /* Search & Filters */
    .search-box {
        position: relative;
        margin-bottom: 32px;
    }

    .search-input {
        width: 100%;
        padding: 20px 24px 20px 64px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        font-size: 16px;
        font-weight: 600;
        color: var(--text-main);
        background: var(--bg-white);
        transition: all 0.3s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
        transform: translateY(-2px);
    }

    .search-icon {
        position: absolute;
        left: 24px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        color: var(--text-muted);
        pointer-events: none;
    }

    .search-clear {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--text-muted);
        display: none;
        transition: color 0.2s;
    }

    .search-clear:hover {
        color: var(--text-main);
    }

    /* Advanced Filter Bar */
    .advanced-filter-bar {
        background: var(--bg-white);
        padding: 24px 32px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        margin-bottom: 32px;
    }

    .filter-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-title {
        font-size: 16px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--text-main);
    }

    .filter-toggle {
        background: var(--bg-light);
        border: 1px solid var(--border);
        padding: 8px 16px;
        border-radius: var(--radius-sm);
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-muted);
    }

    .filter-toggle:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .filter-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .filter-input {
        padding: 12px 16px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        font-size: 14px;
        font-weight: 500;
        background: var(--bg-light);
        width: 100%;
        transition: all 0.2s;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(var(--primary-rgb), 0.1);
    }

    .date-range-picker {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .date-range-picker .filter-input {
        flex: 1;
    }

    .date-separator {
        color: var(--text-muted);
        font-weight: 600;
    }

    .filter-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--border);
    }

    /* Bulk Actions */
    .bulk-actions {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 24px;
        background: linear-gradient(135deg, rgba(var(--primary-rgb), 0.05), rgba(var(--success-rgb), 0.05));
        border-radius: var(--radius-md);
        margin-bottom: 24px;
        flex-wrap: wrap;
        border: 1px solid var(--border);
    }

    .bulk-actions.show {
        display: flex;
    }

    .bulk-select-all {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
    }

    .bulk-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .bulk-btn {
        padding: 10px 18px;
        border-radius: var(--radius-md);
        border: none;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-white);
        color: var(--text-main);
        border: 1px solid var(--border);
    }

    .bulk-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    .bulk-download {
        background: var(--warning);
        color: white;
        border: none;
    }

    .bulk-download:hover {
        background: #d97706;
    }

    .bulk-print {
        background: #1e293b;
        color: white;
        border: none;
    }

    .bulk-export {
        background: var(--success);
        color: white;
        border: none;
    }

    .bulk-email {
        background: #0ea5e9;
        color: white;
        border: none;
    }

    .bulk-delete {
        background: var(--danger);
        color: white;
        border: none;
    }

    /* Data Table */
    .datatable-container {
        overflow-x: auto;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--bg-white);
    }

    .datatable {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .datatable thead {
        background: rgba(var(--primary-rgb), 0.04);
        border-bottom: 1px solid var(--border);
    }

    .datatable th {
        padding: 18px 20px;
        text-align: left;
        font-weight: 700;
        color: var(--text-muted);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sortable {
        cursor: pointer;
        transition: color 0.2s;
    }

    .sortable:hover {
        color: var(--primary);
    }

    .sort-icon {
        margin-left: 5px;
        font-size: 10px;
        opacity: 0.5;
    }

    .datatable tbody tr {
        transition: background 0.2s ease;
        border-bottom: 1px solid var(--border);
    }

    .datatable tbody tr:hover {
        background: rgba(var(--primary-rgb), 0.02);
    }

    .datatable td {
        padding: 16px 20px;
        color: var(--text-main);
        vertical-align: middle;
    }

    /* Checkbox */
    .table-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 2px solid var(--border);
        cursor: pointer;
        transition: all 0.2s;
        accent-color: var(--primary);
    }

    /* Invoice Cell */
    .invoice-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .invoice-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        font-weight: 700;
        font-size: 14px;
    }

    .invoice-details {
        display: flex;
        flex-direction: column;
    }

    .invoice-number {
        font-weight: 800;
        color: var(--text-main);
    }

    .invoice-type {
        font-size: 11px;
        color: var(--text-muted);
    }

    /* Customer Cell */
    .customer-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
        background: linear-gradient(135deg, var(--warning), #d97706);
        color: white;
        flex-shrink: 0;
    }

    .customer-info {
        display: flex;
        flex-direction: column;
    }

    .customer-name {
        font-weight: 700;
        color: var(--text-main);
    }

    .customer-mobile {
        font-size: 12px;
        color: var(--text-muted);
    }

    /* Date Cell */
    .date-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-weight: 500;
    }

    /* Status Badges */
    .status-badge {
        padding: 6px 14px;
        border-radius: 40px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        width: fit-content;
    }

    .status-paid {
        background: rgba(var(--success-rgb), 0.12);
        color: var(--success);
    }

    .status-pending {
        background: rgba(var(--warning-rgb), 0.12);
        color: var(--warning);
    }

    .status-overdue {
        background: rgba(var(--danger-rgb), 0.12);
        color: var(--danger);
    }

    .status-draft {
        background: rgba(100, 116, 139, 0.12);
        color: var(--text-muted);
    }

    /* Amount Cell */
    .amount-cell {
        font-weight: 800;
        font-size: 16px;
    }

    .amount-positive {
        color: var(--success);
    }

    .amount-negative {
        color: var(--danger);
    }

    /* Action Cell */
    .action-cell {
        position: relative;
    }

    .action-container {
        position: relative;
    }

    .action-btn {
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: var(--bg-light);
        color: var(--text-main);
        border: 1px solid var(--border);
        width: auto;
    }

    .action-btn:hover, .action-btn.active {
        background: var(--primary);
        color: white;
        border-color: transparent;
        transform: translateY(-1px);
    }

    .action-menu {
        display: none;
        position: absolute;
        background: var(--bg-white);
        backdrop-filter: blur(20px);
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        min-width: 200px;
        top: 100%;
        right: 0;
        margin-top: 8px;
        overflow: hidden;
        animation: menuFadeIn 0.2s ease;
    }

    @keyframes menuFadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .action-menu.show {
        display: block;
    }

    .action-menu-item {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-main);
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        background: transparent;
        width: 100%;
        text-align: left;
        border: none;
    }

    .action-menu-item:last-child {
        border-bottom: none;
    }

    .action-menu-item:hover {
        background: rgba(var(--primary-rgb), 0.08);
        color: var(--primary);
        padding-left: 24px;
    }

    .action-menu-item.delete:hover {
        background: rgba(var(--danger-rgb), 0.08);
        color: var(--danger);
    }

    /* Pagination */
    .datatable-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        background: var(--bg-white);
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 15px;
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 14px;
        font-weight: 500;
    }

    .pagination {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .pagination-btn, .pagination-btn-disabled {
        padding: 10px 16px;
        border-radius: var(--radius-md);
        background: var(--bg-white);
        border: 1px solid var(--border);
        color: var(--text-main);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }

    .pagination-btn:hover:not(:disabled) {
        background: var(--primary);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
    }

    .pagination-btn.active {
        background: var(--primary);
        color: white;
        border-color: transparent;
    }

    .pagination-btn:disabled, .pagination-btn-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-ellipsis {
        padding: 10px 8px;
        color: var(--text-muted);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px !important;
    }

    .empty-content {
        max-width: 400px;
        margin: 0 auto;
    }

    .empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-title {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 10px;
    }

    .empty-description {
        color: var(--text-muted);
        font-size: 14px;
        margin-bottom: 25px;
    }

    /* Alerts */
    .success-alert, .error-alert {
        padding: 14px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 24px;
        font-weight: 600;
        animation: slideInUp 0.4s ease;
    }

    .success-alert {
        background: rgba(var(--success-rgb), 0.1);
        color: var(--success);
        border-left: 4px solid var(--success);
    }

    .error-alert {
        background: rgba(var(--danger-rgb), 0.1);
        color: var(--danger);
        border-left: 4px solid var(--danger);
    }

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }

    .loading-overlay.show {
        display: flex;
    }

    .export-progress {
        background: var(--bg-white);
        padding: 32px;
        border-radius: var(--radius-xl);
        text-align: center;
        max-width: 400px;
        width: 90%;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-premium);
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--border);
        border-radius: 3px;
        margin: 20px 0;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--success));
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 3px;
    }

    .progress-text {
        font-size: 14px;
        color: var(--text-muted);
        margin-top: 10px;
    }

    /* Toast Notification */
    .toast-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 14px 24px;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 600;
        z-index: 10001;
        display: none;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        animation: toastSlideIn 0.3s ease;
        font-size: 14px;
    }

    @keyframes toastSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Export Dropdown */
    .export-menu {
        position: relative;
        display: inline-block;
    }

    .export-dropdown {
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 8px;
        background: var(--bg-white);
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-premium);
        min-width: 200px;
        z-index: 100;
        display: none;
        overflow: hidden;
    }

    .export-dropdown.show {
        display: block;
        animation: menuFadeIn 0.2s ease;
    }

    .export-option {
        padding: 12px 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: all 0.2s;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
        font-weight: 600;
    }

    .export-option:last-child {
        border-bottom: none;
    }

    .export-option:hover {
        background: rgba(var(--primary-rgb), 0.08);
        color: var(--primary);
        padding-left: 24px;
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card, .sales-dashboard, .advanced-filter-bar {
        animation: slideInUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .stat-card:nth-child(1) { animation-delay: 0.05s; }
    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.15s; }
    .stat-card:nth-child(4) { animation-delay: 0.2s; }
    .stat-card:nth-child(5) { animation-delay: 0.25s; }

    /* Responsive */
    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: stretch;
        }
        .action-buttons {
            justify-content: flex-start;
        }
        .filter-content {
            grid-template-columns: 1fr;
        }
        .bulk-actions {
            flex-direction: column;
            align-items: stretch;
        }
        .bulk-buttons {
            justify-content: center;
        }
        .datatable-footer {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }
        .pagination {
            justify-content: center;
        }
    }

    @media print {
        .action-buttons, .action-cell, .bulk-actions, .search-box, .advanced-filter-bar,
        .export-menu, .pagination, .filter-actions, .table-checkbox {
            display: none !important;
        }
        .status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="sales-page">
    <div class="container">
        <div class="sales-dashboard" id="salesDashboard">
            <!-- Loading Overlay -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="export-progress">
                    <div class="loading-spinner"></div>
                    <h3 style="margin: 20px 0 10px; color: var(--text-main);">Processing Request</h3>
                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <div class="progress-text" id="progressText">Please wait...</div>
                </div>
            </div>

            <!-- Toast Notification -->
            <div id="toastNotification" class="toast-notification"></div>

            <!-- Header -->
            <div class="dashboard-header">
                <div class="header-left">
                    <div class="header-icon">
                        <span>💼</span>
                    </div>
                    <div class="header-content">
                        <h1>Sales Ledger</h1>
                        <p>Real-time oversight of enterprise sales performance</p>
                    </div>
                </div>
                <div class="action-buttons">
                    @if(auth()->user()->hasPermission('create_sales'))
                        <a href="{{ route('sales.create') }}" class="btn-primary">
                            <span style="font-size: 20px;">+</span>
                            New Sale
                        </a>
                    @endif
                    @if(auth()->user()->hasPermission('export_sales'))
                        <div class="export-menu">
                            <button class="btn-secondary" id="exportBtn">
                                <span>📤</span>
                                Export
                                <span>▼</span>
                            </button>
                            <div class="export-dropdown" id="exportDropdown">
                                <div class="export-option" data-format="csv">
                                    <span>📁</span>
                                    Export as CSV
                                </div>
                                <div class="export-option" data-format="excel">
                                    <span>📊</span>
                                    Export as Excel
                                </div>
                                <div class="export-option" data-format="pdf">
                                    <span>📄</span>
                                    Export as PDF
                                </div>
                                <div class="export-option" onclick="window.print()">
                                    <span>🖨️</span>
                                    Print List
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--primary);">📊</div>
                    <div class="stat-text">
                        <h3>{{ $sales->total() }}</h3>
                        <p>Total Invoices</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--success);">💰</div>
                    <div class="stat-text">
                        <h3>₹{{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: #6366f1;">👥</div>
                    <div class="stat-text">
                        <h3>{{ $customersCount ?? '0' }}</h3>
                        <p>Active Customers</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--warning);">📈</div>
                    <div class="stat-text">
                        <h3>₹{{ number_format($sales->avg('grand_total') ?? 0, 2) }}</h3>
                        <p>Average Value</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--danger);">🔄</div>
                    <div class="stat-text">
                        <h3>₹{{ number_format($stats['total_returns'] ?? 0, 2) }}</h3>
                        <p>Total Returns</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            @if (session('success'))
                <div class="success-alert" id="successAlert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="error-alert" id="errorAlert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Search Bar -->
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-input"
                    placeholder="Search by invoice number, customer name, amount...">
                <button class="search-clear" id="searchClear" title="Clear search">×</button>
            </div>

            <!-- Advanced Filters -->
            <div class="advanced-filter-bar">
                <div class="filter-header">
                    <div class="filter-title">
                        <span>⚙️</span>
                        Advanced Filters
                    </div>
                    <button class="filter-toggle" id="toggleFilters">
                        <span>▼</span>
                        Show Filters
                    </button>
                </div>
                <div class="filter-content" id="filterContent" style="display: none;">
                    <div class="filter-group">
                        <label class="filter-label">Date Range</label>
                        <div class="date-range-picker">
                            <input type="date" id="startDate" class="filter-input">
                            <span class="date-separator">to</span>
                            <input type="date" id="endDate" class="filter-input">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="filter-input" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Customer</label>
                        <select class="filter-input" id="customerFilter">
                            <option value="">All Customers</option>
                            @foreach ($customers ?? [] as $customer)
                                @if(is_object($customer))
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Amount Range</label>
                        <div class="date-range-picker">
                            <input type="number" id="minAmount" class="filter-input" placeholder="Min ₹">
                            <span class="date-separator">to</span>
                            <input type="number" id="maxAmount" class="filter-input" placeholder="Max ₹">
                        </div>
                    </div>
                </div>
                <div class="filter-actions" id="filterActions" style="display: none;">
                    <button class="btn-secondary" id="resetFilters">Reset All</button>
                    <button class="btn-primary" id="applyFilters">Apply Filters</button>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div class="bulk-actions" id="bulkActions">
                <div class="bulk-select-all">
                    <input type="checkbox" id="selectAllBulk" class="table-checkbox">
                    <span id="selectedCount">0 items selected</span>
                </div>
                <div class="bulk-buttons">
                    @if(auth()->user()->hasPermission('export_sales'))
                        <button class="bulk-btn bulk-download" id="bulkDownloadZip">
                            <span>📦</span>
                            Bulk Invoice Download
                        </button>
                    @endif
                    @if(auth()->user()->hasPermission('view_sales'))
                        <button class="bulk-btn bulk-print" id="bulkPrint">
                            <span>🖨️</span>
                            Print
                        </button>
                    @endif
                    @if(auth()->user()->hasPermission('export_sales'))
                        <button class="bulk-btn bulk-export" id="bulkExportBtn">
                            <span>📤</span>
                            Export
                        </button>
                    @endif
                    @if(auth()->user()->hasPermission('view_sales'))
                        <button class="bulk-btn bulk-email" id="bulkEmailBtn">
                            <span>📧</span>
                            Email
                        </button>
                    @endif
                    @if(auth()->user()->hasPermission('delete_sales'))
                        <button class="bulk-btn bulk-delete" id="bulkDelete">
                            <span>🗑️</span>
                            Delete
                        </button>
                    @endif
                </div>
            </div>

            <!-- Data Table -->
            <div class="datatable-container">
                <table class="datatable" id="salesTable">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="table-checkbox">
                            </th>
                            <th width="140">Actions</th>
                            <th class="sortable" data-sort="invoice_no">Invoice # <span class="sort-icon">↓</span></th>
                            <th class="sortable" data-sort="sale_date">Date <span class="sort-icon">↓</span></th>
                            <th class="sortable" data-sort="customer_name">Customer <span class="sort-icon">↓</span></th>
                            <th class="sortable" data-sort="status">Status <span class="sort-icon">↓</span></th>
                            <th class="sortable" data-sort="amount">Amount <span class="sort-icon">↓</span></th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody">
                        @if (count($sales) > 0)
                            @foreach ($sales as $sale)
                                <tr data-id="{{ $sale->id }}" data-invoice="{{ $sale->invoice_no }}"
                                    data-customer="{{ $sale->customer->name ?? 'Walk-in Customer' }}"
                                    data-status="{{ $sale->payment_status }}"
                                    data-amount="{{ $sale->grand_total }}" data-date="{{ $sale->sale_date }}"
                                    data-customer-id="{{ $sale->customer_id }}"
                                    data-customer-email="{{ $sale->customer->email ?? '' }}">
                                    <td>
                                        <input type="checkbox" class="row-checkbox table-checkbox">
                                    </td>
                                    <td class="action-cell">
                                        <div class="action-container">
                                            <button type="button" class="action-btn">
                                                <span>⚡</span>
                                                Actions
                                                <span style="font-size: 12px;">▼</span>
                                            </button>
                                            <div class="action-menu">
                                                @if(auth()->user()->hasPermission('view_sales'))
                                                    <a href="{{ route('sales.show', $sale->id) }}" class="action-menu-item">
                                                        <span>👁️</span> View Details
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermission('edit_sales'))
                                                    <a href="{{ route('sales.edit', $sale->id) }}" class="action-menu-item">
                                                        <span>✏️</span> Edit Sale
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermission('create_sales'))
                                                    <a href="{{ route('credit-memos.create', ['sale' => $sale->id]) }}" class="action-menu-item">
                                                        <span>🔄</span> Return / Credit Memo
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermission('view_sales'))
                                                    <a href="{{ route('sales.invoice', $sale->id) }}" class="action-menu-item" download>
                                                        <span>📥</span> Download Invoice
                                                    </a>
                                                @endif
                                                @if(auth()->user()->hasPermission('export_sales'))
                                                    @if ($sale->customer && $sale->customer->email)
                                                        <a href="#" onclick="sendSingleEmail('{{ $sale->id }}', '{{ $sale->customer->email }}', '{{ $sale->invoice_no }}'); return false;" class="action-menu-item">
                                                            <span>📧</span> Email Invoice
                                                        </a>
                                                    @else
                                                        <span class="action-menu-item" style="opacity:0.5; cursor:not-allowed;" title="No customer email available">
                                                            <span>📧</span> Email Invoice
                                                        </span>
                                                    @endif
                                                @endif
                                                @if(auth()->user()->hasPermission('delete_sales'))
                                                    <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="delete-form" style="margin:0;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-menu-item delete" onclick="return confirm('Are you sure you want to delete this sale?')">
                                                            <span>🗑️</span> Delete Sale
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="invoice-cell">
                                            <div class="invoice-icon">
                                                {{ substr($sale->invoice_no, -3) }}
                                            </div>
                                            <div class="invoice-details">
                                                <div class="invoice-number">{{ $sale->invoice_no }}</div>
                                                <div class="invoice-type">{{ $sale->invoice_type ?? 'Sale' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <span>📅</span>
                                            {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="customer-cell">
                                            @php
                                                $custName = optional($sale->customer)->name ?? 'Walk-in Customer';
                                                $initials = strtoupper(substr($custName, 0, 1));
                                                $colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'];
                                                $colorIndex = abs(crc32($custName) % count($colors));
                                                $bgColor = $colors[$colorIndex];
                                            @endphp
                                            <div class="customer-avatar" style="background: {{ $bgColor }};">
                                                {{ $initials }}
                                            </div>
                                            <div class="customer-info">
                                                <div class="customer-name">{{ $custName }}</div>
                                                <div class="customer-mobile">
                                                    {{ $sale->customer->mobile ?? 'No contact' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = 'status-paid';
                                            $statusIcon = '✓';
                                            if ($sale->payment_status == 'pending') {
                                                $statusClass = 'status-pending';
                                                $statusIcon = '⏱️';
                                            } elseif ($sale->payment_status == 'overdue') {
                                                $statusClass = 'status-overdue';
                                                $statusIcon = '⚠️';
                                            } elseif ($sale->payment_status == 'draft') {
                                                $statusClass = 'status-draft';
                                                $statusIcon = '📝';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ ucfirst($sale->payment_status ?? 'paid') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="amount-cell {{ $sale->grand_total >= 0 ? 'amount-positive' : 'amount-negative' }}">
                                            ₹{{ number_format($sale->grand_total, 2) }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <div class="empty-content">
                                        <div class="empty-icon">📊</div>
                                        <div class="empty-title">No Sales Records Found</div>
                                        <div class="empty-description">
                                            Start by creating your first sales invoice. All your sales transactions will
                                            appear here for tracking and analysis.
                                        </div>
                                        <a href="{{ route('sales.create') }}" class="btn-primary" style="display: inline-flex;">
                                            <span>+</span>
                                            Create Your First Sale
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($sales->hasPages())
                <div class="datatable-footer">
                    <div class="pagination-info">
                        Showing <span id="startCount">{{ $sales->firstItem() ?? 0 }}</span> to
                        <span id="endCount">{{ $sales->lastItem() ?? 0 }}</span> of
                        <span id="totalCount">{{ $sales->total() }}</span> entries
                    </div>
                    <div class="pagination">
                        @if ($sales->onFirstPage())
                            <span class="pagination-btn-disabled">
                                <span>←</span>
                                Previous
                            </span>
                        @else
                            <a href="{{ $sales->previousPageUrl() }}" class="pagination-btn">
                                <span>←</span>
                                Previous
                            </a>
                        @endif

                        @php
                            $current = $sales->currentPage();
                            $last = $sales->lastPage();
                            $start = max($current - 2, 1);
                            $end = min($current + 2, $last);
                        @endphp

                        @if ($start > 1)
                            <a href="{{ $sales->url(1) }}" class="pagination-btn {{ 1 == $current ? 'active' : '' }}">1</a>
                            @if ($start > 2)
                                <span class="pagination-ellipsis">...</span>
                            @endif
                        @endif

                        @for ($i = $start; $i <= $end; $i++)
                            <a href="{{ $sales->url($i) }}" class="pagination-btn {{ $i == $current ? 'active' : '' }}">{{ $i }}</a>
                        @endfor

                        @if ($end < $last)
                            @if ($end < $last - 1)
                                <span class="pagination-ellipsis">...</span>
                            @endif
                            <a href="{{ $sales->url($last) }}" class="pagination-btn {{ $last == $current ? 'active' : '' }}">{{ $last }}</a>
                        @endif

                        @if ($sales->hasMorePages())
                            <a href="{{ $sales->nextPageUrl() }}" class="pagination-btn">
                                Next
                                <span>→</span>
                            </a>
                        @else
                            <span class="pagination-btn-disabled">
                                Next
                                <span>→</span>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toastNotification');
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        toast.style.background = colors[type] || colors.info;
        toast.textContent = message;
        toast.style.display = 'block';
        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);
    }

    // Send single email
    function sendSingleEmail(saleId, customerEmail, invoiceNo) {
        if (!customerEmail) {
            showToast('Cannot send email: Customer email not available', 'error');
            return;
        }

        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('show');
            document.getElementById('progressText').textContent = 'Sending email...';
        }

        fetch('{{ route('sales.send-invoice') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                sale_id: saleId,
                recipient_email: customerEmail,
                email_subject: `Invoice #${invoiceNo} from {{ config('app.name') }}`,
                email_body: `Dear Customer,\n\nPlease find attached the invoice #${invoiceNo} for your recent purchase.\n\nThank you for your business!`
            })
        })
        .then(response => response.json())
        .then(data => {
            loadingOverlay.classList.remove('show');
            if (data.success) {
                showToast(`✅ Email sent to ${customerEmail}`, 'success');
            } else {
                showToast(`❌ Error: ${data.message}`, 'error');
            }
        })
        .catch(error => {
            loadingOverlay.classList.remove('show');
            showToast('❌ Error sending email', 'error');
            console.error('Error:', error);
        });
    }

    // Get selected IDs
    function getSelectedIds() {
        const selectedIds = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(checkbox => {
            const row = checkbox.closest('tr');
            if (row && row.dataset.id) {
                selectedIds.push(row.dataset.id);
            }
        });
        return selectedIds;
    }

    // Update bulk actions visibility
    function updateBulkActions() {
        const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;
        const selectedCountElement = document.getElementById('selectedCount');
        const bulkActions = document.getElementById('bulkActions');
        const selectAllBulk = document.getElementById('selectAllBulk');

        if (selectedCountElement) {
            selectedCountElement.textContent = `${selectedCount} item${selectedCount !== 1 ? 's' : ''} selected`;
        }

        if (bulkActions) {
            if (selectedCount > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
                if (selectAllBulk) selectAllBulk.checked = false;
            }
        }
    }

    // Bulk download as ZIP
    async function bulkDownloadAsZip() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            showToast('Please select at least one invoice to download', 'warning');
            return;
        }

        const loadingOverlay = document.getElementById('loadingOverlay');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');

        loadingOverlay.classList.add('show');
        progressFill.style.width = '0%';
        progressText.textContent = `Preparing to download ${selectedIds.length} invoices...`;

        try {
            const zip = new JSZip();
            let completed = 0;
            let failed = 0;

            for (const saleId of selectedIds) {
                const row = document.querySelector(`tr[data-id="${saleId}"]`);
                const invoiceNo = row?.dataset.invoice || saleId;

                try {
                    const response = await fetch(`/sales/${saleId}/invoice`, {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (response.ok) {
                        const blob = await response.blob();
                        zip.file(`Invoice_${invoiceNo}.pdf`, blob);
                        completed++;
                    } else {
                        failed++;
                    }
                } catch (error) {
                    failed++;
                }

                const progress = ((completed + failed) / selectedIds.length) * 100;
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `Downloaded ${completed} of ${selectedIds.length} invoices...`;
            }

            if (completed > 0) {
                progressText.textContent = 'Creating ZIP file...';
                const zipBlob = await zip.generateAsync({ type: 'blob' });
                const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
                saveAs(zipBlob, `Invoices_${timestamp}.zip`);
                showToast(`✅ Downloaded ${completed} invoices${failed > 0 ? ` (${failed} failed)` : ''}`, failed > 0 ? 'warning' : 'success');
            } else {
                showToast('❌ Failed to download any invoices', 'error');
            }
        } catch (error) {
            console.error('Bulk download error:', error);
            showToast('❌ Error creating ZIP file', 'error');
        } finally {
            loadingOverlay.classList.remove('show');
            progressFill.style.width = '0%';
        }
    }

    // Bulk email
    function sendBulkEmail() {
        const selectedIds = getSelectedIds();
        if (selectedIds.length === 0) {
            showToast('Please select at least one invoice', 'warning');
            return;
        }

        const rowsWithEmail = [];
        const rowsWithoutEmail = [];

        selectedIds.forEach(id => {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            const customerEmail = row?.dataset.customerEmail;
            const invoiceNo = row?.dataset.invoice;

            if (customerEmail) {
                rowsWithEmail.push({ id, email: customerEmail, invoice: invoiceNo });
            } else {
                rowsWithoutEmail.push(invoiceNo || id);
            }
        });

        if (rowsWithEmail.length === 0) {
            showToast('None of the selected invoices have customer email addresses', 'warning');
            return;
        }

        if (rowsWithoutEmail.length > 0) {
            showToast(`${rowsWithEmail.length} emails sending, ${rowsWithoutEmail.length} skipped (no email)`, 'info');
        }

        const loadingOverlay = document.getElementById('loadingOverlay');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');

        loadingOverlay.classList.add('show');
        progressFill.style.width = '0%';
        progressText.textContent = `Sending ${rowsWithEmail.length} emails...`;

        let sentCount = 0;
        let failedCount = 0;

        const promises = rowsWithEmail.map((item, index) => {
            return fetch('{{ route('sales.send-invoice') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    sale_id: item.id,
                    recipient_email: item.email,
                    email_subject: `Invoice #${item.invoice} from {{ config('app.name') }}`,
                    email_body: `Dear Customer,\n\nPlease find attached the invoice #${item.invoice} for your recent purchase.\n\nThank you for your business!`
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) sentCount++;
                else failedCount++;
                const progress = ((sentCount + failedCount) / rowsWithEmail.length) * 100;
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `Sent ${sentCount} of ${rowsWithEmail.length} emails...`;
            })
            .catch(() => {
                failedCount++;
                const progress = ((sentCount + failedCount) / rowsWithEmail.length) * 100;
                progressFill.style.width = `${progress}%`;
                progressText.textContent = `Sent ${sentCount} of ${rowsWithEmail.length} emails...`;
            });
        });

        Promise.all(promises).then(() => {
            loadingOverlay.classList.remove('show');
            showToast(`✅ Emails sent: ${sentCount} successful, ${failedCount} failed`, failedCount > 0 ? 'warning' : 'success');
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        });
    }

    // Filter table
    function filterTable() {
        const searchValue = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const startDate = document.getElementById('startDate')?.value;
        const endDate = document.getElementById('endDate')?.value;
        const status = document.getElementById('statusFilter')?.value;
        const customerId = document.getElementById('customerFilter')?.value;
        const minAmount = parseFloat(document.getElementById('minAmount')?.value) || 0;
        const maxAmount = parseFloat(document.getElementById('maxAmount')?.value) || Infinity;

        const rows = document.querySelectorAll('#salesTableBody tr[data-id]');
        let visibleCount = 0;

        rows.forEach(row => {
            let showRow = true;

            // Search
            if (searchValue) {
                const invoice = row.dataset.invoice?.toLowerCase() || '';
                const customer = row.dataset.customer?.toLowerCase() || '';
                const amount = row.dataset.amount || '';
                if (!invoice.includes(searchValue) && !customer.includes(searchValue) && !amount.includes(searchValue)) {
                    showRow = false;
                }
            }

            // Date
            if (showRow && startDate && endDate && row.dataset.date) {
                const rowDate = new Date(row.dataset.date);
                const start = new Date(startDate);
                const end = new Date(endDate);
                end.setHours(23, 59, 59, 999);
                if (rowDate < start || rowDate > end) showRow = false;
            }

            // Status
            if (showRow && status && row.dataset.status !== status) showRow = false;

            // Customer
            if (showRow && customerId && row.dataset.customerId !== customerId) showRow = false;

            // Amount
            if (showRow) {
                const amount = parseFloat(row.dataset.amount) || 0;
                if (minAmount > 0 && amount < minAmount) showRow = false;
                if (maxAmount < Infinity && amount > maxAmount) showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
            if (showRow) visibleCount++;
        });
    }

    // Sorting
    let currentSort = { column: null, direction: 'asc' };

    function sortTable(column) {
        const tbody = document.getElementById('salesTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr[data-id]'));
        
        if (currentSort.column === column) {
            currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
        } else {
            currentSort.column = column;
            currentSort.direction = 'asc';
        }

        rows.sort((a, b) => {
            let aVal, bVal;
            switch (column) {
                case 'invoice_no':
                    aVal = a.dataset.invoice || '';
                    bVal = b.dataset.invoice || '';
                    break;
                case 'sale_date':
                    aVal = new Date(a.dataset.date || 0);
                    bVal = new Date(b.dataset.date || 0);
                    break;
                case 'customer_name':
                    aVal = a.dataset.customer || '';
                    bVal = b.dataset.customer || '';
                    break;
                case 'status':
                    aVal = a.dataset.status || '';
                    bVal = b.dataset.status || '';
                    break;
                case 'amount':
                    aVal = parseFloat(a.dataset.amount) || 0;
                    bVal = parseFloat(b.dataset.amount) || 0;
                    break;
                default:
                    return 0;
            }

            if (aVal < bVal) return currentSort.direction === 'asc' ? -1 : 1;
            if (aVal > bVal) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });

        rows.forEach(row => tbody.appendChild(row));
        
        // Update sort icons
        document.querySelectorAll('.sortable .sort-icon').forEach(icon => {
            icon.textContent = '↓';
            icon.style.opacity = '0.5';
        });
        const activeHeader = document.querySelector(`.sortable[data-sort="${column}"] .sort-icon`);
        if (activeHeader) {
            activeHeader.textContent = currentSort.direction === 'asc' ? '↓' : '↑';
            activeHeader.style.opacity = '1';
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Date defaults
        const today = new Date().toISOString().split('T')[0];
        const lastMonth = new Date();
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        
        if (document.getElementById('startDate')) {
            document.getElementById('startDate').value = lastMonth.toISOString().split('T')[0];
        }
        if (document.getElementById('endDate')) {
            document.getElementById('endDate').value = today;
        }

        // Filter toggle
        const toggleFilters = document.getElementById('toggleFilters');
        const filterContent = document.getElementById('filterContent');
        const filterActions = document.getElementById('filterActions');
        
        if (toggleFilters) {
            toggleFilters.addEventListener('click', function() {
                const isVisible = filterContent.style.display === 'grid';
                filterContent.style.display = isVisible ? 'none' : 'grid';
                filterActions.style.display = isVisible ? 'none' : 'flex';
                toggleFilters.innerHTML = isVisible ?
                    '<span>▼</span> Show Filters' :
                    '<span>▲</span> Hide Filters';
            });
        }

        // Export dropdown
        const exportBtn = document.getElementById('exportBtn');
        const exportDropdown = document.getElementById('exportDropdown');
        
        if (exportBtn) {
            exportBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('show');
            });
        }

        // Close dropdowns on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.export-menu')) {
                if (exportDropdown) exportDropdown.classList.remove('show');
            }
            if (!e.target.closest('.action-container')) {
                document.querySelectorAll('.action-menu.show').forEach(menu => {
                    menu.classList.remove('show');
                });
                document.querySelectorAll('.action-btn.active').forEach(btn => {
                    btn.classList.remove('active');
                });
            }
        });

        // Action menu toggles (using event delegation for DataTables compatibility)
        $(document).on('click', '.action-btn', function(e) {
            e.stopPropagation();
            const container = $(this).closest('.action-container');
            const menu = container.find('.action-menu');
            const isOpen = menu.hasClass('show');
            
            $('.action-menu.show').not(menu).removeClass('show');
            $('.action-btn.active').not(this).removeClass('active');
            
            menu.toggleClass('show');
            $(this).toggleClass('active');
        });

        // Search
        const searchInput = document.getElementById('searchInput');
        const searchClear = document.getElementById('searchClear');
        
        if (searchInput) {
            let timeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                if (searchClear) searchClear.style.display = this.value ? 'block' : 'none';
                timeout = setTimeout(filterTable, 300);
            });
        }
        
        if (searchClear) {
            searchClear.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                this.style.display = 'none';
                filterTable();
            });
        }

        // Apply filters
        const applyFilters = document.getElementById('applyFilters');
        if (applyFilters) {
            applyFilters.addEventListener('click', filterTable);
        }

        // Reset filters
        const resetFilters = document.getElementById('resetFilters');
        if (resetFilters) {
            resetFilters.addEventListener('click', function() {
                if (document.getElementById('startDate')) {
                    document.getElementById('startDate').value = lastMonth.toISOString().split('T')[0];
                }
                if (document.getElementById('endDate')) {
                    document.getElementById('endDate').value = today;
                }
                if (document.getElementById('statusFilter')) document.getElementById('statusFilter').value = '';
                if (document.getElementById('customerFilter')) document.getElementById('customerFilter').value = '';
                if (document.getElementById('minAmount')) document.getElementById('minAmount').value = '';
                if (document.getElementById('maxAmount')) document.getElementById('maxAmount').value = '';
                filterTable();
            });
        }

        // Sorting
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', function() {
                const sort = this.dataset.sort;
                if (sort) sortTable(sort);
            });
        });

        // Select all
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const isChecked = this.checked;
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = isChecked);
                updateBulkActions();
            });
        }

        // Row checkbox changes
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateBulkActions();
                if (selectAll) {
                    const allCheckboxes = document.querySelectorAll('.row-checkbox');
                    const checkedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
                    selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
                }
            }
        });

        // Bulk actions
        const bulkDownloadZip = document.getElementById('bulkDownloadZip');
        if (bulkDownloadZip) bulkDownloadZip.addEventListener('click', bulkDownloadAsZip);
        
        const bulkPrint = document.getElementById('bulkPrint');
        if (bulkPrint) {
            bulkPrint.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length > 0) {
                    selectedIds.forEach(id => window.open(`/sales/${id}/invoice`, '_blank'));
                } else {
                    showToast('Please select at least one invoice', 'warning');
                }
            });
        }
        
        const bulkEmailBtn = document.getElementById('bulkEmailBtn');
        if (bulkEmailBtn) bulkEmailBtn.addEventListener('click', sendBulkEmail);
        
        const bulkDelete = document.getElementById('bulkDelete');
        if (bulkDelete) {
            bulkDelete.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length > 0) {
                    if (confirm(`Delete ${selectedIds.length} selected items?`)) {
                        showToast('Delete functionality would be implemented here', 'info');
                    }
                } else {
                    showToast('Please select at least one item', 'warning');
                }
            });
        }
        
        const bulkExportBtn = document.getElementById('bulkExportBtn');
        if (bulkExportBtn) {
            bulkExportBtn.addEventListener('click', function() {
                const selectedIds = getSelectedIds();
                if (selectedIds.length > 0) {
                    showToast(`Export ${selectedIds.length} items`, 'info');
                } else {
                    showToast('Please select at least one item', 'warning');
                }
            });
        }

        // Export options
        if (exportDropdown) {
            exportDropdown.querySelectorAll('.export-option').forEach(option => {
                option.addEventListener('click', function() {
                    const format = this.dataset.format;
                    if (format) {
                        showToast(`Export as ${format.toUpperCase()} triggered`, 'success');
                    }
                    exportDropdown.classList.remove('show');
                });
            });
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.success-alert, .error-alert').forEach(el => {
                el.style.display = 'none';
            });
        }, 5000);

        // --- OFFLINE INVOICE INJECTION ---
        if (typeof DataService !== 'undefined') {
            const invoices = DataService.getAllInvoices();
            const tbody = document.getElementById('salesTableBody');
            
            // Find unsynced ones
            const unsynced = Object.values(invoices).filter(inv => !inv.synced);
            
            if (unsynced.length > 0 && tbody) {
                // Remove empty state if present
                const emptyStateRow = tbody.querySelector('.empty-state')?.closest('tr');
                if (emptyStateRow) emptyStateRow.remove();
                
                unsynced.forEach(inv => {
                    const row = document.createElement('tr');
                    row.className = 'offline-row';
                    row.style.background = '#fffbeb'; // Light amber for offline
                    row.style.borderLeft = '4px solid #f59e0b';
                    
                    row.innerHTML = `
                        <td><input type="checkbox" disabled class="table-checkbox"></td>
                        <td class="action-cell">
                            <a href="/sales/${inv.invoice_token}" class="btn-sm btn-primary" style="padding: 6px 12px; font-size: 11px; text-decoration: none; border-radius: 4px;">View & Sync</a>
                        </td>
                        <td>
                            <div class="invoice-cell">
                                <div class="invoice-icon" style="background: #f59e0b; color: white; border-radius: 4px; padding: 4px 6px; font-weight: 800; font-size: 10px;">OFF</div>
                                <div class="invoice-details">
                                    <div class="invoice-number" style="font-weight: 700;">${inv.invoice_no || 'OFFLINE'}</div>
                                    <div class="invoice-type" style="color: #d97706; font-size: 10px; font-weight: 800;">[OFFLINE DRAFT]</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="date-cell" style="font-size: 13px;"><span>📅</span> ${new Date(inv.created_at || Date.now()).toLocaleDateString('en-GB', {day:'2-digit', month:'short', year:'numeric'})}</div>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <div class="customer-avatar" style="background: #f59e0b; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">${(inv.customer?.name || 'W').charAt(0)}</div>
                                <div class="customer-info" style="margin-left: 10px;">
                                    <div class="customer-name" style="font-weight: 600;">${inv.customer?.name || 'Walk-in Customer'}</div>
                                    <div class="customer-mobile" style="font-size: 11px; color: #92400e;">Offline Saved</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge" style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;">⚠️ NOT SYNCED</span>
                        </td>
                        <td>
                            <div class="amount-cell amount-positive" style="font-weight: 700; color: #059669;">₹${(inv.totals?.grand_total || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                        </td>
                    `;
                    tbody.prepend(row);
                });
            }
        }

        // Initialize
        updateBulkActions();
    });
</script>
<script src="{{ asset('js/pos-data-service.js') }}"></script>
@endsection