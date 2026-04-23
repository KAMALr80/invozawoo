@extends('layouts.app')

@section('page-title', 'Staff Dashboard')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --success: #16a34a;
        --success-dark: #15803d;
        --danger: #dc2626;
        --danger-dark: #991b1b;
        --warning: #f97316;
        --warning-dark: #c2410c;
        --purple: #7c3aed;
        --text-main: #111827;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-white: #ffffff;
        --bg-light: #f3f4f6;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.08);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 18px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f9fafb;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .staff-dashboard {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
        width: 100%;
        min-height: 100vh;
    }

    /* ================= HEADER ================= */
    .dashboard-header {
        margin-bottom: 30px;
    }

    .header-title {
        margin: 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 800;
        color: var(--text-main);
        word-break: break-word;
    }

    .header-subtitle {
        margin-top: 6px;
        color: var(--text-muted);
        font-size: clamp(13px, 2.5vw, 15px);
        word-break: break-word;
    }

    /* ================= STATS GRID ================= */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 22px;
        margin-bottom: 40px;
        width: 100%;
    }

    .stat-card {
        padding: clamp(18px, 4vw, 22px);
        border-radius: var(--radius-xl);
        color: white;
        transition: all 0.3s ease;
        width: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .stat-card.blue {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    }

    .stat-card.green {
        background: linear-gradient(135deg, var(--success), var(--success-dark));
    }

    .stat-card.red {
        background: linear-gradient(135deg, var(--danger), var(--danger-dark));
    }

    .stat-card.orange {
        background: linear-gradient(135deg, var(--warning), var(--warning-dark));
    }

    .stat-label {
        font-size: clamp(12px, 2.5vw, 14px);
        opacity: 0.9;
        margin-bottom: 8px;
        word-break: break-word;
    }

    .stat-value {
        font-size: clamp(28px, 6vw, 34px);
        font-weight: 800;
        line-height: 1.2;
        word-break: break-word;
    }

    /* ================= QUICK ACTIONS GRID ================= */
    .actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 22px;
        margin-bottom: 40px;
        width: 100%;
    }

    .action-card {
        background: var(--bg-white);
        padding: clamp(18px, 4vw, 22px);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        width: 100%;
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .action-title {
        margin: 0;
        font-size: clamp(16px, 3vw, 18px);
        font-weight: 700;
        word-break: break-word;
    }

    .action-title.blue {
        color: var(--primary);
    }

    .action-title.green {
        color: var(--success);
    }

    .action-title.orange {
        color: var(--warning);
    }

    .action-title.purple {
        color: var(--purple);
    }

    .action-desc {
        margin-top: 6px;
        color: var(--text-muted);
        font-size: clamp(12px, 2.5vw, 14px);
        word-break: break-word;
    }

    /* ================= RECENT ATTENDANCE CARD ================= */
    .attendance-card {
        background: var(--bg-white);
        padding: clamp(20px, 5vw, 26px);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        width: 100%;
    }

    .card-title {
        margin: 0 0 15px 0;
        font-size: clamp(16px, 3vw, 18px);
        font-weight: 700;
        color: var(--text-main);
        word-break: break-word;
    }

    /* ================= TABLE ================= */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 500px;
    }

    .data-table th {
        background: var(--bg-light);
        padding: 10px;
        text-align: left;
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .data-table td {
        padding: 10px;
        border-bottom: 1px solid var(--border);
        font-size: clamp(12px, 2.5vw, 14px);
        white-space: nowrap;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .status-present {
        color: var(--success);
        font-weight: 600;
    }

    .status-absent {
        color: var(--danger);
        font-weight: 600;
    }

    .status-late {
        color: var(--warning);
        font-weight: 600;
    }

    /* ================= EMPTY STATE ================= */
    .empty-state {
        color: var(--text-muted);
        font-size: clamp(13px, 2.5vw, 15px);
        padding: 20px;
        text-align: center;
        word-break: break-word;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .actions-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .actions-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .staff-dashboard {
            padding: 15px;
        }

        .stats-grid {
            gap: 18px;
            margin-bottom: 30px;
        }

        .actions-grid {
            gap: 18px;
            margin-bottom: 30px;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .staff-dashboard {
            padding: 12px;
        }

        .stats-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .actions-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .stat-card {
            padding: 18px;
        }

        .action-card {
            padding: 18px;
        }

        .attendance-card {
            padding: 18px;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .staff-dashboard {
            padding: 10px;
        }

        .data-table {
            min-width: 400px;
        }

        .data-table th,
        .data-table td {
            padding: 8px;
            font-size: 12px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .staff-dashboard {
            padding: 8px;
        }

        .header-title {
            font-size: 22px;
        }

        .stat-value {
            font-size: 26px;
        }

        .data-table {
            min-width: 350px;
        }

        .data-table th,
        .data-table td {
            padding: 6px;
            font-size: 11px;
        }
    }

    /* Print Styles */
    @media print {
        .action-card,
        .stat-card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid #000;
        }

        .stat-card {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="staff-dashboard">
    {{-- PAGE HEADER --}}
    <div class="dashboard-header">
        <h2 class="header-title">
            👨‍💼 Staff Dashboard
        </h2>
        <p class="header-subtitle">
            Your daily overview & activities
        </p>
    </div>

    {{-- ================= STAFF STATS ================= --}}
    <div class="stats-grid">
        {{-- MY ATTENDANCE --}}
        <div class="stat-card blue">
            <div class="stat-label">Today's Attendance</div>
            <div class="stat-value">
                {{ $myAttendanceStatus ?? 'N/A' }}
            </div>
        </div>

        {{-- PRESENT DAYS --}}
        <div class="stat-card green">
            <div class="stat-label">Present Days</div>
            <div class="stat-value">
                {{ $presentCount ?? 0 }}
            </div>
        </div>

        {{-- ABSENT DAYS --}}
        <div class="stat-card red">
            <div class="stat-label">Absent Days</div>
            <div class="stat-value">
                {{ $absentCount ?? 0 }}
            </div>
        </div>

        {{-- TOTAL LEAVES --}}
        <div class="stat-card orange">
            <div class="stat-label">My Leaves</div>
            <div class="stat-value">
                {{ $leaveCount ?? 0 }}
            </div>
        </div>
    </div>

    {{-- ================= QUICK ACTIONS ================= --}}
    <div class="actions-grid">
        <a href="{{ route('attendance.my') }}" class="action-card">
            <h3 class="action-title blue">🕒 My Attendance</h3>
            <p class="action-desc">
                View check-in, check-out & history
            </p>
        </a>

        <a href="{{ route('leaves.my') }}" class="action-card">
            <h3 class="action-title green">📝 My Leaves</h3>
            <p class="action-desc">
                Apply & track leave requests
            </p>
        </a>

        <a href="{{ route('sales.index') }}" class="action-card">
            <h3 class="action-title orange">💰 Sales</h3>
            <p class="action-desc">
                Create & manage invoices
            </p>
        </a>

        <a href="{{ route('customers.index') }}" class="action-card">
            <h3 class="action-title purple">👤 Customers</h3>
            <p class="action-desc">
                View & add customers
            </p>
        </a>
    </div>

    {{-- ================= RECENT ATTENDANCE ================= --}}
    <div class="attendance-card">
        <h3 class="card-title">
            📅 Recent Attendance
        </h3>

        @if (isset($recentAttendance) && $recentAttendance->count())
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentAttendance as $a)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($a->attendance_date)->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="
                                        @if($a->status === 'Present' || $a->status === 'present') status-present
                                        @elseif($a->status === 'Absent' || $a->status === 'absent') status-absent
                                        @elseif($a->status === 'Late' || $a->status === 'late') status-late
                                        @endif
                                    ">
                                        {{ $a->status }}
                                    </span>
                                </td>
                                <td>
                                    {{ $a->check_in ?? '-' }}
                                </td>
                                <td>
                                    {{ $a->check_out ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="empty-state">
                No attendance records found
            </p>
        @endif
    </div>
</div>
@endsection