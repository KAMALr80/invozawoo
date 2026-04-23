@extends('layouts.app')

@section('page-title', 'HR Dashboard')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --success: #10b981;
        --success-dark: #059669;
        --danger: #ef4444;
        --danger-dark: #dc2626;
        --warning: #f59e0b;
        --warning-dark: #d97706;
        --info: #0ea5e9;
        --purple: #a855f7;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 24px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f3f4f6;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .hr-dashboard {
        max-width: 1600px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
        min-height: 100vh;
        background: #f3f4f6;
    }

    /* ================= HEADER ================= */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        flex: 1;
        min-width: 280px;
    }

    .header-title {
        margin: 0;
        font-size: clamp(24px, 4vw, 28px);
        color: var(--text-main);
        font-weight: 800;
        word-break: break-word;
    }

    .header-subtitle {
        margin: 8px 0 0 0;
        color: var(--text-muted);
        font-size: clamp(13px, 2vw, 15px);
        word-break: break-word;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: clamp(13px, 2vw, 14px);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: var(--success);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: clamp(13px, 2vw, 14px);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-success:hover {
        background: var(--success-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= STATS GRID ================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
        width: 100%;
    }

    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: clamp(20px, 3vw, 25px);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        transition: all 0.2s ease;
        width: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary);
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .stat-info {
        flex: 1;
    }

    .stat-label {
        font-size: clamp(12px, 2vw, 14px);
        color: var(--text-muted);
        margin-bottom: 5px;
        font-weight: 500;
        word-break: break-word;
    }

    .stat-value {
        font-size: clamp(30px, 5vw, 36px);
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
        word-break: break-word;
    }

    .stat-icon {
        width: clamp(50px, 8vw, 60px);
        height: clamp(50px, 8vw, 60px);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 3vw, 24px);
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .stat-icon.green {
        background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
    }

    .stat-icon.red {
        background: linear-gradient(135deg, var(--danger) 0%, var(--danger-dark) 100%);
    }

    .stat-icon.orange {
        background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);
    }

    .stat-footer {
        margin-top: 15px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .stat-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s ease;
    }

    .stat-link:hover {
        gap: 8px;
    }

    .stat-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .stat-badge.green {
        background: #d1fae5;
        color: var(--success-dark);
    }

    .stat-badge.red {
        background: #fee2e2;
        color: var(--danger-dark);
    }

    /* ================= TWO COLUMN LAYOUT ================= */
    .two-column-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
        width: 100%;
    }

    .card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: clamp(20px, 3vw, 25px);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        width: 100%;
        overflow: hidden;
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .card-title {
        margin: 0;
        font-size: clamp(18px, 3vw, 20px);
        color: var(--text-main);
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        word-break: break-word;
    }

    .card-action {
        background: #eef2ff;
        color: var(--primary);
        text-decoration: none;
        font-size: clamp(12px, 2vw, 14px);
        font-weight: 600;
        padding: 6px 12px;
        border-radius: var(--radius-md);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .card-action:hover {
        background: var(--primary);
        color: white;
    }

    /* ================= TABLES ================= */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 600px;
    }

    .data-table th {
        text-align: left;
        padding: 12px 0;
        color: var(--text-muted);
        font-weight: 600;
        font-size: clamp(12px, 2vw, 13px);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }

    .data-table td {
        padding: 12px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: clamp(12px, 2vw, 14px);
        white-space: nowrap;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .employee-cell {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .employee-avatar {
        width: clamp(35px, 5vw, 40px);
        height: clamp(35px, 5vw, 40px);
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, var(--purple) 0%, var(--primary) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: clamp(14px, 2vw, 16px);
        flex-shrink: 0;
    }

    .employee-name {
        font-weight: 600;
        color: var(--text-main);
        word-break: break-word;
    }

    /* ================= STATUS BADGES ================= */
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: clamp(11px, 2vw, 12px);
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
    }

    .status-present {
        background: #d1fae5;
        color: var(--success-dark);
    }

    .status-absent {
        background: #fee2e2;
        color: var(--danger-dark);
    }

    .status-late {
        background: #fef3c7;
        color: var(--warning-dark);
    }

    .status-leave {
        background: #fef3c7;
        color: var(--warning-dark);
    }

    /* ================= ACTION BUTTONS ================= */
    .action-group {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 4px 10px;
        border-radius: var(--radius-sm);
        font-size: clamp(11px, 2vw, 12px);
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-edit {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background: #0284c7;
        color: white;
    }

    .btn-approve {
        background: #d1fae5;
        color: var(--success-dark);
    }

    .btn-approve:hover {
        background: var(--success);
        color: white;
    }

    .btn-reject {
        background: #fee2e2;
        color: var(--danger-dark);
    }

    .btn-reject:hover {
        background: var(--danger);
        color: white;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        text-align: center;
        padding: clamp(30px, 8vw, 40px) clamp(10px, 3vw, 20px);
    }

    .empty-icon {
        font-size: clamp(36px, 8vw, 48px);
        margin-bottom: 10px;
    }

    .empty-title {
        color: var(--text-muted);
        margin-bottom: 5px;
        font-size: clamp(14px, 2.5vw, 16px);
        word-break: break-word;
    }

    .empty-text {
        color: #9ca3af;
        font-size: clamp(12px, 2vw, 13px);
        margin-bottom: 15px;
        word-break: break-word;
    }

    .empty-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
        font-size: clamp(13px, 2vw, 14px);
    }

    /* ================= FOOTER SECTION ================= */
    .footer-section {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: clamp(20px, 3vw, 25px);
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        margin-bottom: 30px;
        width: 100%;
    }

    .department-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        width: 100%;
    }

    .department-card {
        border-radius: var(--radius-lg);
        padding: clamp(15px, 2.5vw, 20px);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.2s ease;
        background: var(--bg-light);
        border: 1px solid var(--border);
    }

    .department-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .department-icon {
        width: clamp(40px, 6vw, 50px);
        height: clamp(40px, 6vw, 50px);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 3vw, 24px);
        color: white;
        flex-shrink: 0;
    }

    .department-info {
        flex: 1;
    }

    .department-count {
        font-size: clamp(20px, 4vw, 24px);
        font-weight: 800;
        color: var(--text-main);
        line-height: 1.2;
    }

    .department-name {
        font-size: clamp(13px, 2vw, 14px);
        color: var(--text-muted);
        font-weight: 600;
        word-break: break-word;
    }

    .department-percentage {
        font-size: clamp(11px, 1.5vw, 12px);
        color: #9ca3af;
        margin-top: 4px;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .two-column-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .department-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .hr-dashboard {
            padding: 15px;
        }

        .dashboard-header {
            margin-bottom: 20px;
        }

        .header-actions {
            width: 100%;
        }

        .btn-primary,
        .btn-success {
            flex: 1;
            justify-content: center;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .two-column-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .hr-dashboard {
            padding: 12px;
        }

        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            flex-direction: column;
        }

        .btn-primary,
        .btn-success {
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-header {
            flex-direction: row;
        }

        .stat-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .stat-link {
            width: 100%;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .card-action {
            width: 100%;
            text-align: center;
        }

        .employee-cell {
            flex-direction: column;
            align-items: flex-start;
        }

        .action-group {
            flex-direction: column;
        }

        .btn-sm {
            width: 100%;
            text-align: center;
        }

        .department-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .hr-dashboard {
            padding: 10px;
        }

        .data-table {
            min-width: 500px;
        }

        .data-table th,
        .data-table td {
            padding: 8px 0;
        }

        .employee-avatar {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }

        .status-badge {
            padding: 3px 8px;
        }

        .department-card {
            padding: 12px;
        }

        .department-icon {
            width: 35px;
            height: 35px;
            font-size: 18px;
        }

        .department-count {
            font-size: 20px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .hr-dashboard {
            padding: 8px;
        }

        .stat-value {
            font-size: 28px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 20px;
        }

        .data-table {
            min-width: 400px;
        }

        .data-table th,
        .data-table td {
            font-size: 11px;
            padding: 6px 0;
        }

        .btn-sm {
            padding: 3px 8px;
            font-size: 10px;
        }

        .status-badge {
            padding: 2px 6px;
            font-size: 10px;
        }

        .department-grid {
            gap: 10px;
        }
    }

    /* Print Styles */
    @media print {
        .btn-primary,
        .btn-success,
        .card-action,
        .btn-sm,
        .stat-link,
        .header-actions {
            display: none !important;
        }

        .stat-card,
        .card,
        .footer-section {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #000;
        }

        .stat-icon {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="hr-dashboard">
    <!-- Page Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h1 class="header-title">HR Dashboard</h1>
            <p class="header-subtitle">
                {{ now()->format('l, F j, Y') }} • Welcome, {{ auth()->user()->name }}
            </p>
        </div>
        <div class="header-actions">
            <button onclick="window.location.href='{{ route('employees.index') }}'" class="btn-primary">
                👥 Manage Employees
            </button>
            <button onclick="window.location.href='{{ route('attendance.mark') }}'" class="btn-success">
                📝 Mark Attendance
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Total Employees -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">Total Employees</div>
                    <div class="stat-value">{{ $totalEmployees ?? 0 }}</div>
                </div>
                <div class="stat-icon blue">👥</div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('employees.index') }}" class="stat-link">
                    <span>→</span> <span>View all employees</span>
                </a>
            </div>
        </div>

        <!-- Present Today -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">Present Today</div>
                    <div class="stat-value">{{ $presentToday ?? 0 }}</div>
                </div>
                <div class="stat-icon green">✅</div>
            </div>
            <div class="stat-footer">
                <span class="stat-badge green">
                    {{ $totalEmployees > 0 ? round(($presentToday / $totalEmployees) * 100, 1) : 0 }}% attendance
                </span>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">Absent Today</div>
                    <div class="stat-value">{{ $absentToday ?? 0 }}</div>
                </div>
                <div class="stat-icon red">⚠️</div>
            </div>
            <div class="stat-footer">
                @if ($employeesWithoutAttendance->count() > 0)
                    <span class="stat-badge red">
                        {{ $employeesWithoutAttendance->count() }} pending
                    </span>
                @endif
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <div class="stat-label">Pending Leaves</div>
                    <div class="stat-value">{{ $pendingLeaves ?? 0 }}</div>
                </div>
                <div class="stat-icon orange">📝</div>
            </div>
            <div class="stat-footer">
                <a href="{{ route('leaves.manage') }}" class="stat-link">
                    <span>→</span> <span>Manage leaves</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="two-column-grid">
        <!-- Left: Attendance Summary -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <span>🕒</span> Today's Attendance ({{ $todayAttendance->count() }})
                </h2>
                <a href="{{ route('attendance.mark') }}" class="card-action">
                    Mark Attendance
                </a>
            </div>

            @if ($todayAttendance->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Status</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todayAttendance as $attendance)
                                <tr>
                                    <td>
                                        <div class="employee-cell">
                                            <div class="employee-avatar">
                                                {{ strtoupper(substr($attendance->employee->name ?? 'E', 0, 1)) }}
                                            </div>
                                            <div class="employee-name">
                                                {{ $attendance->employee->name ?? 'Unknown' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if (in_array($attendance->status, ['present', 'Present']))
                                            <span class="status-badge status-present">✅ Present</span>
                                        @elseif(in_array($attendance->status, ['absent', 'Absent']))
                                            <span class="status-badge status-absent">❌ Absent</span>
                                        @elseif(in_array($attendance->status, ['late', 'Late']))
                                            <span class="status-badge status-late">⏰ Late</span>
                                        @else
                                            <span class="status-badge">{{ $attendance->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $attendance->check_in ? Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--:--' }}
                                    </td>
                                    <td>
                                        <button onclick="editAttendance({{ $attendance->id }})" class="btn-sm btn-edit">
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($employeesWithoutAttendance->count() > 0)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border);">
                        <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 10px;">
                            ⚠️ {{ $employeesWithoutAttendance->count() }} employees without attendance
                        </div>
                        <a href="{{ route('attendance.mark') }}" style="color: var(--danger); text-decoration: none; font-size: 13px; font-weight: 600;">
                            Mark attendance for missing employees →
                        </a>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">🕒</div>
                    <p class="empty-title">No attendance marked today</p>
                    <a href="{{ route('attendance.mark') }}" class="empty-link">
                        Mark today's attendance →
                    </a>
                </div>
            @endif
        </div>

        <!-- Right: Pending Leaves -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <span>📝</span> Pending Leave Requests ({{ $pendingLeaves }})
                </h2>
                <a href="{{ route('leaves.manage') }}" class="card-action">
                    Manage All
                </a>
            </div>

            @if ($recentLeaves->count() > 0)
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentLeaves as $leave)
                                <tr>
                                    <td>
                                        <div class="employee-cell">
                                            <div class="employee-avatar" style="background: linear-gradient(135deg, var(--warning) 0%, var(--warning-dark) 100%);">
                                                {{ strtoupper(substr($leave->employee->name ?? 'E', 0, 1)) }}
                                            </div>
                                            <div class="employee-name">
                                                {{ $leave->employee->name ?? 'Unknown' }}
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge status-leave">
                                            {{ $leave->type ?? 'Leave' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if (isset($leave->from_date) && isset($leave->to_date))
                                            {{ Carbon\Carbon::parse($leave->from_date)->format('d M') }} -
                                            {{ Carbon\Carbon::parse($leave->to_date)->format('d M') }}
                                        @else
                                            {{ $leave->created_at->format('d M, Y') }}
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <button onclick="approveLeave({{ $leave->id }})" class="btn-sm btn-approve">
                                                Approve
                                            </button>
                                            <button onclick="rejectLeave({{ $leave->id }})" class="btn-sm btn-reject">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($pendingLeaves > 5)
                    <div style="text-align: center; padding-top: 15px; border-top: 1px solid var(--border);">
                        <a href="{{ route('leaves.manage') }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px;">
                            View all {{ $pendingLeaves }} pending requests →
                        </a>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-icon">✅</div>
                    <p class="empty-title">No pending leave requests</p>
                    <p class="empty-text">All leave requests have been processed</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Bottom Row: Charts and Quick Actions -->
    <div class="two-column-grid">
        <!-- Attendance Chart -->
        <div class="card">
            <h2 class="card-title" style="margin-bottom: 20px;">
                <span>📈</span> Weekly Attendance Trend
            </h2>
            <div style="height: 250px; width: 100%;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <h2 class="card-title" style="margin-bottom: 20px;">
                <span>🚀</span> Quick Actions
            </h2>
            <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
                <a href="{{ route('employees.create') }}" style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 12px; padding: 20px; text-decoration: none; display: flex; align-items: center; gap: 15px; transition: all 0.2s ease;">
                    <div style="width: clamp(45px, 6vw, 50px); height: clamp(45px, 6vw, 50px); border-radius: 12px; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: clamp(20px, 3vw, 24px); color: white; flex-shrink: 0;">
                        ➕
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main);">Add New Employee</div>
                        <div style="font-size: clamp(12px, 2vw, 13px); color: var(--text-muted);">Register new employee in system</div>
                    </div>
                </a>

                <a href="{{ route('attendance.mark') }}" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 20px; text-decoration: none; display: flex; align-items: center; gap: 15px; transition: all 0.2s ease;">
                    <div style="width: clamp(45px, 6vw, 50px); height: clamp(45px, 6vw, 50px); border-radius: 12px; background: var(--info); display: flex; align-items: center; justify-content: center; font-size: clamp(20px, 3vw, 24px); color: white; flex-shrink: 0;">
                        🕒
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main);">Mark Bulk Attendance</div>
                        <div style="font-size: clamp(12px, 2vw, 13px); color: var(--text-muted);">Mark attendance for multiple employees</div>
                    </div>
                </a>

                <a href="{{ route('hr.dashboard') }}" style="background: #f3e8ff; border: 1px solid #e9d5ff; border-radius: 12px; padding: 20px; text-decoration: none; display: flex; align-items: center; gap: 15px; transition: all 0.2s ease;">
                    <div style="width: clamp(45px, 6vw, 50px); height: clamp(45px, 6vw, 50px); border-radius: 12px; background: var(--purple); display: flex; align-items: center; justify-content: center; font-size: clamp(20px, 3vw, 24px); color: white; flex-shrink: 0;">
                        📊
                    </div>
                    <div>
                        <div style="font-weight: 700; color: var(--text-main);">HR Analytics</div>
                        <div style="font-size: clamp(12px, 2vw, 13px); color: var(--text-muted);">Detailed reports and analytics</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Department Stats -->
    <div class="footer-section">
        <h2 class="card-title" style="margin-bottom: 20px;">
            <span>🏢</span> Department Statistics
        </h2>

        @if (count($departmentStats) > 0)
            <div class="department-grid">
                @foreach ($departmentStats as $dept)
                    <div class="department-card">
                        <div class="department-icon" style="background: {{ $dept['color'] }};">
                            {{ $dept['icon'] ?? '🏢' }}
                        </div>
                        <div class="department-info">
                            <div class="department-count">{{ $dept['count'] }}</div>
                            <div class="department-name">{{ $dept['name'] }}</div>
                            <div class="department-percentage">
                                {{ $totalEmployees > 0 ? round(($dept['count'] / $totalEmployees) * 100, 1) : 0 }}% of total
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">🏢</div>
                <p class="empty-title">No department data available</p>
                <p class="empty-text">Add departments to employee records</p>
            </div>
        @endif
    </div>
</div>

<!-- JavaScript for Charts and Actions -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle responsive chart resizing
        let chart;
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        
        // Chart options based on screen size
        const isMobile = window.innerWidth < 768;
        
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($attendanceTrend['labels'] ?? []),
                datasets: [{
                    label: 'Present',
                    data: @json($attendanceTrend['present'] ?? []),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: isMobile ? 2 : 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: isMobile ? 2 : 3,
                    pointHoverRadius: isMobile ? 4 : 5
                }, {
                    label: 'Absent',
                    data: @json($attendanceTrend['absent'] ?? []),
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: isMobile ? 2 : 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: isMobile ? 2 : 3,
                    pointHoverRadius: isMobile ? 4 : 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: isMobile ? 11 : 13
                            },
                            boxWidth: isMobile ? 12 : 15,
                            padding: isMobile ? 10 : 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        titleColor: '#1f2937',
                        bodyColor: '#4b5563',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: isMobile ? 8 : 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(229, 231, 235, 0.5)'
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 10 : 12
                            },
                            callback: function(value) {
                                return value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: isMobile ? 9 : 11
                            },
                            maxRotation: isMobile ? 45 : 30
                        }
                    }
                }
            }
        });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                if (window.innerWidth < 768) {
                    chart.options.plugins.legend.labels.font.size = 11;
                    chart.options.scales.y.ticks.font.size = 10;
                    chart.options.scales.x.ticks.font.size = 9;
                    chart.data.datasets[0].borderWidth = 2;
                    chart.data.datasets[0].pointRadius = 2;
                    chart.data.datasets[1].borderWidth = 2;
                    chart.data.datasets[1].pointRadius = 2;
                } else {
                    chart.options.plugins.legend.labels.font.size = 13;
                    chart.options.scales.y.ticks.font.size = 12;
                    chart.options.scales.x.ticks.font.size = 11;
                    chart.data.datasets[0].borderWidth = 3;
                    chart.data.datasets[0].pointRadius = 3;
                    chart.data.datasets[1].borderWidth = 3;
                    chart.data.datasets[1].pointRadius = 3;
                }
                chart.update();
            }, 250);
        });
    });

    // Leave Actions
    function approveLeave(leaveId) {
        if (confirm('Approve this leave request?')) {
            fetch(`/leaves/${leaveId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Leave approved successfully');
                        location.reload();
                    } else {
                        alert('Error approving leave');
                    }
                })
                .catch(error => {
                    alert('Error approving leave');
                });
        }
    }

    function rejectLeave(leaveId) {
        if (confirm('Reject this leave request?')) {
            fetch(`/leaves/${leaveId}/reject`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Leave rejected successfully');
                        location.reload();
                    } else {
                        alert('Error rejecting leave');
                    }
                })
                .catch(error => {
                    alert('Error rejecting leave');
                });
        }
    }

    function editAttendance(attendanceId) {
        window.location.href = `/attendance/${attendanceId}/edit`;
    }
</script>
@endsection