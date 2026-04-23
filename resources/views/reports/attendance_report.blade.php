@extends('layouts.app')

@section('page-title', 'Attendance Report')

@section('content')
    <style>
        /* ================= PROFESSIONAL ATTENDANCE REPORT STYLES ================= */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple: #8b5cf6;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
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
        }

        .report-wrapper {
            padding: 2rem 1rem;
            min-height: 100vh;
            width: 100%;
        }

        .report-container {
            max-width: 1600px;
            margin: 0 auto;
            width: 100%;
        }

        .report-header {
            background: var(--bg-white);
            border-radius: var(--radius-xl);
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 0.25rem;
            color: var(--text-main);
        }

        .header-title p {
            color: var(--text-muted);
            font-size: 0.875rem;
            margin: 0;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-main);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: var(--border);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .stat-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .stat-value.positive {
            color: var(--success);
        }

        .stat-value.negative {
            color: var(--danger);
        }

        .stat-value.warning {
            color: var(--warning);
        }

        .filter-section {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
        }

        .filter-group {
            flex: 1;
            min-width: 150px;
        }

        .filter-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
        }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-Present {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-Absent {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-Late {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-Half-Day {
            background: #f3e8ff;
            color: #5b21b6;
        }

        .badge-Leave {
            background: #e0e7ff;
            color: #3730a3;
        }

        .table-container {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .table-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            background: var(--bg-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h2 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
            min-width: 1000px;
        }

        .data-table thead th {
            background: var(--bg-light);
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .data-table tbody tr:hover {
            background: var(--bg-light);
        }

        .serial-cell {
            width: 60px;
            text-align: center;
            font-weight: 600;
            color: var(--text-muted);
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .pagination-wrapper {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
        }

        .pagination {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item .page-link {
            display: inline-block;
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            color: var(--text-main);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            background: white;
        }

        .page-item .page-link:hover {
            border-color: var(--primary);
            background: var(--bg-light);
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .chart-card {
            background: var(--bg-white);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }

        .chart-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-container {
            height: 250px;
            position: relative;
        }

        .employee-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .employee-stat-card {
            background: var(--bg-light);
            border-radius: var(--radius-md);
            padding: 0.75rem;
            border: 1px solid var(--border);
        }

        .employee-name {
            font-weight: 600;
            color: var(--text-main);
            font-size: 0.9rem;
        }

        .employee-code {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        .employee-rate {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--primary);
        }

        .progress-bar-sm {
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill-sm {
            height: 100%;
            background: var(--success);
            width: 0%;
        }

        .toast-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-md);
            background: white;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 1000;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }

        .toast-notification.success {
            border-left-color: var(--success);
        }

        .toast-notification.error {
            border-left-color: var(--danger);
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

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--border);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .report-wrapper {
                padding: 1rem;
            }

            .report-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
            }

            .btn {
                flex: 1;
                justify-content: center;
            }

            .filter-form {
                flex-direction: column;
            }

            .filter-group {
                width: 100%;
            }

            .filter-actions {
                width: 100%;
            }

            .filter-actions button {
                flex: 1;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media print {

            .header-actions,
            .filter-section,
            .pagination-wrapper,
            .btn {
                display: none !important;
            }
        }
    </style>

    <div class="report-wrapper">
        <div class="report-container">
            <div id="loadingOverlay" class="loading-overlay">
                <div class="spinner"></div>
                <div class="loading-text">Generating PDF...</div>
            </div>

            <div class="report-header">
                <div class="header-title">
                    <h1>🕒 Attendance Report</h1>
                    <p>Complete attendance analytics and employee summary</p>
                </div>
                <div class="header-actions">
                    <button onclick="exportReport('csv')" class="btn btn-success">
                        📥 Export CSV
                    </button>
                    <button onclick="exportReport('pdf')" class="btn btn-primary">
                        📄 Export PDF
                    </button>
                    <button onclick="window.print()" class="btn btn-secondary">
                        🖨️ Print
                    </button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Records</div>
                    <div class="stat-value">{{ number_format($stats['total_records']) }}</div>
                    <div class="stat-sub">Attendance entries</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Present</div>
                    <div class="stat-value positive">{{ number_format($stats['present']) }}</div>
                    <div class="stat-sub">{{ $stats['avg_attendance_rate'] }}% rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Absent</div>
                    <div class="stat-value negative">{{ number_format($stats['absent']) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Late</div>
                    <div class="stat-value warning">{{ number_format($stats['late']) }}</div>
                    <div class="stat-sub">{{ $stats['avg_late_rate'] }}% rate</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Half Day</div>
                    <div class="stat-value">{{ number_format($stats['half_day']) }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Leave</div>
                    <div class="stat-value">{{ number_format($stats['leave']) }}</div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Employees</div>
                    <div class="stat-value">{{ number_format($stats['total_employees']) }}</div>
                    <div class="stat-sub">Active employees</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Days</div>
                    <div class="stat-value">{{ number_format($stats['total_days']) }}</div>
                    <div class="stat-sub">In selected period</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Avg Working Hours</div>
                    <div class="stat-value">{{ $stats['avg_working_hours'] }}</div>
                    <div class="stat-sub">Per day average</div>
                </div>
            </div>

            <div class="filter-section">
                <form method="GET" action="{{ route('reports.attendance') }}" class="filter-form" id="filterForm">
                    <div class="filter-group">
                        <label>From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}">
                    </div>

                    <div class="filter-group">
                        <label>To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}">
                    </div>

                    <div class="filter-group">
                        <label>Employee</label>
                        <select name="employee_id">
                            <option value="">All Employees</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}"
                                    {{ $filters['employee_id'] == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="Present" {{ $filters['status'] == 'Present' ? 'selected' : '' }}>Present
                            </option>
                            <option value="Absent" {{ $filters['status'] == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Late" {{ $filters['status'] == 'Late' ? 'selected' : '' }}>Late</option>
                            <option value="Half Day" {{ $filters['status'] == 'Half Day' ? 'selected' : '' }}>Half Day
                            </option>
                            <option value="Leave" {{ $filters['status'] == 'Leave' ? 'selected' : '' }}>Leave</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Department</label>
                        <select name="department">
                            <option value="all" {{ $filters['department'] == 'all' ? 'selected' : '' }}>All Departments
                            </option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}"
                                    {{ $filters['department'] == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="{{ route('reports.attendance') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-title">
                        <span>📈</span> Daily Attendance Trend
                    </div>
                    <div class="chart-container">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-title">
                        <span>🥧</span> Status Distribution
                    </div>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>

            @if (isset($employeeStats) && is_array($employeeStats) && count($employeeStats) > 0)
                <h4 style="margin: 0 0 10px 0; font-size: 1rem;">📊 Employee Performance</h4>
                <div class="employee-stats-grid">
                    @php $counter = 0; @endphp
                    @foreach ($employeeStats as $empStat)
                        @if ($counter < 6)
                            <div class="employee-stat-card">
                                <div class="employee-name">{{ $empStat['name'] }}</div>
                                <div class="employee-code">{{ $empStat['employee_code'] }} | {{ $empStat['department'] }}
                                </div>
                                <div class="employee-rate">{{ $empStat['attendance_rate'] }}%</div>
                                <div class="progress-bar-sm">
                                    <div class="progress-fill-sm" style="width: {{ $empStat['attendance_rate'] }}%">
                                    </div>
                                </div>
                                <div style="display: flex; gap: 8px; margin-top: 8px; font-size: 0.7rem;">
                                    <span>✅ {{ $empStat['present'] }}</span>
                                    <span>❌ {{ $empStat['absent'] }}</span>
                                    <span>⏰ {{ $empStat['late'] }}</span>
                                </div>
                            </div>
                            @php $counter++; @endphp
                        @endif
                    @endforeach
                </div>
            @endif

            <div class="table-container">
                <div class="table-header">
                    <h2>
                        <span>📋</span>
                        Attendance List
                        <span style="font-weight: normal; color: var(--text-muted);">
                            ({{ $attendances->total() }} records)
                        </span>
                    </h2>
                    <div>
                        <input type="text" id="tableSearch" placeholder="Search in table..."
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table" id="attendanceTable">
                        <thead>
                            <tr>
                                <th class="serial-cell">#</th>
                                <th>Employee</th>
                                <th>Employee Code</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Working Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $serial = ($attendances->currentPage() - 1) * $attendances->perPage(); @endphp
                            @forelse($attendances as $index => $attendance)
                                @php
                                    $serialNum = $serial + $index + 1;
                                    $statusClass = 'badge-' . str_replace(' ', '-', $attendance->status);
                                    $employeeName = isset($attendance->employee) ? $attendance->employee->name : 'N/A';
                                    $employeeCode = isset($attendance->employee)
                                        ? $attendance->employee->employee_code
                                        : 'N/A';
                                    $employeeDept =
                                        isset($attendance->employee) && isset($attendance->employee->department)
                                            ? $attendance->employee->department
                                            : 'N/A';
                                    $checkIn = isset($attendance->check_in) ? $attendance->check_in : '-';
                                    $checkOut = isset($attendance->check_out) ? $attendance->check_out : '-';
                                    $workingHours = isset($attendance->working_hours)
                                        ? $attendance->working_hours
                                        : '-';
                                @endphp
                                <tr>
                                    <td class="serial-cell">{{ $serialNum }}
                </div>
                </td>
                <td>
                    <div>{{ $employeeName }}</div>
                </td>
                <td>
                    <div>{{ $employeeCode }}</div>
                </td>
                <td>
                    <div>{{ $employeeDept }}</div>
                </td>
                <td>{{ $attendance->attendance_date->format('d M Y') }}
            </div>
            </td>
            <td><span class="badge {{ $statusClass }}">{{ $attendance->status }}</span>
        </div>
        </td>
        <td>{{ $checkIn }}
    </div>
    </td>
    <td>{{ $checkOut }}</div>
    </td>
    <td>{{ $workingHours }}</div>
    </td>
    </tr>
@empty
    <tr>
        <td colspan="9">
            <div class="empty-state">
                <div class="empty-icon">📭</div>
                <div class="empty-title">No attendance records found</div>
                <div class="empty-text">Try adjusting your filters</div>
            </div>
            </div>
        </td>
    </tr>
    @endforelse
    </tbody>
    </table>
    </div>

    @if ($attendances->hasPages())
        <div class="pagination-wrapper">
            {{ $attendances->links() }}
        </div>
    @endif
    </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var dailyTrendData = @json($dailyTrend);
        var statusBreakdown = @json($statusBreakdown);

        var trendChart, statusChart;

        function initCharts() {
            // Daily Attendance Trend Chart
            var trendCtx = document.getElementById('attendanceTrendChart');
            if (trendCtx && dailyTrendData && dailyTrendData.length > 0) {
                var labels = [];
                var presentData = [];
                var lateData = [];
                var absentData = [];

                for (var i = 0; i < dailyTrendData.length; i++) {
                    labels.push(dailyTrendData[i].date);
                    presentData.push(dailyTrendData[i].present);
                    lateData.push(dailyTrendData[i].late);
                    absentData.push(dailyTrendData[i].absent);
                }

                trendChart = new Chart(trendCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                                label: 'Present',
                                data: presentData,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: 'white',
                                pointBorderWidth: 2,
                                pointRadius: 3
                            },
                            {
                                label: 'Late',
                                data: lateData,
                                borderColor: '#f59e0b',
                                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#f59e0b',
                                pointBorderColor: 'white',
                                pointBorderWidth: 2,
                                pointRadius: 3
                            },
                            {
                                label: 'Absent',
                                data: absentData,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true,
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: 'white',
                                pointBorderWidth: 2,
                                pointRadius: 3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // Status Distribution Chart
            var statusCtx = document.getElementById('statusChart');
            if (statusCtx && statusBreakdown && statusBreakdown.length > 0) {
                var statusLabels = [];
                var statusCounts = [];
                var statusColors = [];

                for (var i = 0; i < statusBreakdown.length; i++) {
                    statusLabels.push(statusBreakdown[i].status);
                    statusCounts.push(statusBreakdown[i].count);
                    if (statusBreakdown[i].status == 'Present') {
                        statusColors.push('#10b981');
                    } else if (statusBreakdown[i].status == 'Absent') {
                        statusColors.push('#ef4444');
                    } else if (statusBreakdown[i].status == 'Late') {
                        statusColors.push('#f59e0b');
                    } else if (statusBreakdown[i].status == 'Half Day') {
                        statusColors.push('#8b5cf6');
                    } else {
                        statusColors.push('#3b82f6');
                    }
                }

                statusChart = new Chart(statusCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusCounts,
                            backgroundColor: statusColors,
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: {
                                        size: 10
                                    }
                                }
                            }
                        },
                        cutout: '60%'
                    }
                });
            }
        }

        function getFilterParams() {
            var params = new URLSearchParams();
            var startDate = document.querySelector('input[name="start_date"]') ? document.querySelector(
                'input[name="start_date"]').value : '';
            var endDate = document.querySelector('input[name="end_date"]') ? document.querySelector(
                'input[name="end_date"]').value : '';
            var employeeId = document.querySelector('select[name="employee_id"]') ? document.querySelector(
                'select[name="employee_id"]').value : '';
            var status = document.querySelector('select[name="status"]') ? document.querySelector('select[name="status"]')
                .value : 'all';
            var department = document.querySelector('select[name="department"]') ? document.querySelector(
                'select[name="department"]').value : 'all';

            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (employeeId) params.append('employee_id', employeeId);
            if (status !== 'all') params.append('status', status);
            if (department !== 'all') params.append('department', department);

            return params.toString();
        }

        function exportReport(type) {
            var params = getFilterParams();
            var url = '';

            if (type === 'csv') {
                url = '{{ route('reports.attendance.excel') }}?' + params;
                window.location.href = url;
                showToast('CSV export started!', 'success');
            } else if (type === 'pdf') {
                var loadingOverlay = document.getElementById('loadingOverlay');
                loadingOverlay.classList.add('active');
                url = '{{ route('reports.attendance.pdf') }}?' + params;
                window.open(url, '_blank');
                setTimeout(function() {
                    loadingOverlay.classList.remove('active');
                    showToast('PDF generated successfully!', 'success');
                }, 2000);
            }
        }

        var searchInput = document.getElementById('tableSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                var searchTerm = this.value.toLowerCase();
                var table = document.getElementById('attendanceTable');
                var rows = table.getElementsByTagName('tbody')[0] ? table.getElementsByTagName('tbody')[0]
                    .getElementsByTagName('tr') : [];
                if (!rows) return;
                for (var i = 0; i < rows.length; i++) {
                    var text = rows[i].textContent.toLowerCase();
                    rows[i].style.display = text.indexOf(searchTerm) !== -1 ? '' : 'none';
                }
            });
        }

        function showToast(message, type) {
            type = type || 'success';
            var existingToast = document.querySelector('.toast-notification');
            if (existingToast) existingToast.remove();
            var toast = document.createElement('div');
            toast.className = 'toast-notification ' + type;
            var icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : '⚠️');
            toast.innerHTML = '<span>' + icon + '</span><span>' + message + '</span>';
            document.body.appendChild(toast);
            setTimeout(function() {
                toast.remove();
            }, 3000);
        }

        document.addEventListener('DOMContentLoaded', initCharts);

        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
@endsection
