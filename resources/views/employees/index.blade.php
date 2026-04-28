@extends('layouts.app')

@section('page-title', 'Staff Intelligence Dashboard')

@section('content')
<div class="perf-snap-wrapper">

    <!-- Header & AI Insight Pulse -->
    <div class="header-ai-banner">
        <div class="header-main-info">
            <h1 class="premium-page-title">Workforce Intelligence</h1>
            <p class="premium-page-subtitle">Strategic employee management & operational analytics</p>
        </div>
        <div class="ai-insight-pulse" id="hrInsightPulse">
            <div class="pulse-icon">🧠</div>
            <div class="pulse-text">
                <span class="label">AI HR INSIGHT</span>
                <span class="val" id="dynamicInsight">Analyzing workforce momentum...</span>
            </div>
        </div>
    </div>

    <!-- Row 1: High-Velocity Stat Cards -->
    <div class="perf-grid-row row-stats">
        <div class="perf-card-glass stat-card-premium">
            <div class="card-header-mini">
                <span class="card-label">Total Workforce</span>
                <div class="card-icon-box blue"><i class="fas fa-users"></i></div>
            </div>
            <div class="card-main">
                <h2 class="card-value">{{ $totalEmployees ?? 0 }}</h2>
                <div class="sparkline-container"><canvas id="sparkline1"></canvas></div>
            </div>
            <p class="card-subtext"><span class="highlight">+{{ $recentJoiningsCount }}</span> New this month</p>
        </div>

        <div class="perf-card-glass stat-card-premium">
            <div class="card-header-mini">
                <span class="card-label">Operational Capacity</span>
                <div class="card-icon-box green"><i class="fas fa-bolt"></i></div>
            </div>
            <div class="card-main">
                <h2 class="card-value">{{ number_format(($activeEmployees / ($totalEmployees ?: 1)) * 100, 1) }}%</h2>
                <div class="sparkline-container"><canvas id="sparkline2"></canvas></div>
            </div>
            <p class="card-subtext"><span class="highlight">{{ $activeEmployees }}</span> Active personnel</p>
        </div>

        <div class="perf-card-glass stat-card-premium">
            <div class="card-header-mini">
                <span class="card-label">Org Structure</span>
                <div class="card-icon-box purple"><i class="fas fa-sitemap"></i></div>
            </div>
            <div class="card-main">
                <h2 class="card-value">{{ $departmentsCount ?? 0 }}</h2>
                <div class="sparkline-container"><canvas id="sparkline3"></canvas></div>
            </div>
            <p class="card-subtext">Functional Departments</p>
        </div>

        <div class="perf-card-glass stat-card-premium">
            <div class="card-header-mini">
                <span class="card-label">Hiring Velocity</span>
                <div class="card-icon-box orange"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="card-main">
                <h2 class="card-value">{{ number_format($recentJoiningsCount / 4, 1) }}</h2>
                <div class="sparkline-container"><canvas id="sparkline4"></canvas></div>
            </div>
            <p class="card-subtext">Average joinings / week</p>
        </div>
    </div>

    <!-- Row 2: Deep Analytics & Trends -->
    <div class="perf-grid-row row-main">
        <!-- Hiring Trend Line Chart -->
        <div class="perf-card-glass chart-card-lg">
            <div class="card-header-main">
                <div>
                    <h3 class="card-title">Hiring Momentum</h3>
                    <p class="card-subtitle">Last 6 months registration trend</p>
                </div>
                <div class="chart-legend-premium">
                    <span class="legend-item"><span class="dot blue"></span> Growth</span>
                </div>
            </div>
            <div class="chart-area-premium">
                <canvas id="hiringTrendChart"></canvas>
            </div>
        </div>

        <!-- Department Breakdown (Doughnut) -->
        <div class="perf-card-glass chart-card-sm">
            <div class="card-header-main">
                <div>
                    <h3 class="card-title">Department Allocation</h3>
                    <p class="card-subtitle">Workforce distribution</p>
                </div>
            </div>
            <div class="donut-container-premium">
                <canvas id="deptDonutChart"></canvas>
                <div class="donut-overlay-text">
                    <span class="big">{{ $totalEmployees }}</span>
                    <span class="small">STAFF</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Advanced Data Management (DataTables) -->
    <div class="perf-grid-row">
        <div class="perf-card-glass table-card-premium" style="grid-column: span 4;">
            <div class="table-header-advanced">
                <div class="left">
                    <h3 class="card-title">Staff Management Console</h3>
                    <div class="table-actions-quick">
                        @if (auth()->user()->hasPermission('create_employees'))
                            <a href="{{ route('employees.create') }}" class="btn-action-premium primary">
                                <i class="fas fa-user-plus"></i> Add New Employee
                            </a>
                        @endif
                    </div>
                </div>
                <div class="right">
                    <div id="tableButtons" class="dt-buttons-container"></div>
                    <div class="custom-search-premium">
                        <i class="fas fa-search"></i>
                        <input type="text" id="dtSearchInput" placeholder="Instant lookup...">
                    </div>
                </div>
            </div>

            <div class="advanced-table-wrapper">
                <table id="employeesTable" class="premium-datatable">
                    <thead>
                        <tr>
                            <th>STAFF MEMBER</th>
                            <th>ID CODE</th>
                            <th>DEPARTMENT</th>
                            <th>CONTACT</th>
                            <th>STATUS</th>
                            <th class="no-export">OPERATIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $emp)
                        <tr>
                            <td>
                                <div class="member-profile">
                                    <div class="member-avatar">
                                        {{ strtoupper(substr($emp->name, 0, 1)) }}
                                    </div>
                                    <div class="member-info">
                                        <div class="name">{{ $emp->name }}</div>
                                        <div class="joined">Joined {{ \Carbon\Carbon::parse($emp->joining_date)->format('M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="id-badge">{{ $emp->employee_code }}</span></td>
                            <td><span class="dept-pill">{{ $emp->department }}</span></td>
                            <td>
                                <div class="contact-links">
                                    <a href="mailto:{{ $emp->email }}" title="{{ $emp->email }}"><i class="fas fa-envelope"></i></a>
                                    <a href="tel:{{ $emp->phone }}" title="{{ $emp->phone }}"><i class="fas fa-phone"></i></a>
                                </div>
                            </td>
                            <td>
                                @if($emp->status == 1)
                                    <div class="status-indicator active">
                                        <span class="dot"></span> Active
                                    </div>
                                @else
                                    <div class="status-indicator inactive">
                                        <span class="dot"></span> Inactive
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="ops-group">
                                    @if(auth()->user()->hasPermission('view_employees'))
                                        <a href="{{ route('employees.show', $emp->id) }}" class="op-btn view"><i class="fas fa-eye"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('edit_employees'))
                                        <a href="{{ route('employees.edit', $emp->id) }}" class="op-btn edit"><i class="fas fa-edit"></i></a>
                                    @endif
                                    @if(auth()->user()->hasPermission('delete_employees'))
                                        <form action="{{ route('employees.destroy', $emp->id) }}" method="POST" class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="button" class="op-btn delete" onclick="confirmDelete(this)"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Styles: Professional Dashboard System -->
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --accent-blue: #6366f1;
        --accent-green: #10b981;
        --accent-purple: #8b5cf6;
        --accent-orange: #f59e0b;
        --text-dark: #1e293b;
        --text-soft: #64748b;
        --radius-premium: 20px;
    }

    [data-theme="dark"] {
        --glass-bg: rgba(30, 41, 59, 0.7);
        --glass-border: rgba(255, 255, 255, 0.05);
        --text-dark: #f1f5f9;
        --text-soft: #94a3b8;
    }

    .perf-snap-wrapper { 
        padding: 24px; display: flex; flex-direction: column; gap: 30px; 
        background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
                    radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        min-height: calc(100vh - 70px);
    }
    .perf-grid-row { display: grid; gap: 25px; }
    
    /* Header & AI Banner - Enhanced */
    .header-ai-banner {
        display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; flex-wrap: wrap; gap: 20px;
        background: linear-gradient(135deg, rgba(255,255,255,0.6), rgba(255,255,255,0.2));
        backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border); padding: 24px 30px; border-radius: var(--radius-premium);
        box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.4);
    }
    [data-theme="dark"] .header-ai-banner { background: linear-gradient(135deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.4)); box-shadow: 0 10px 30px -5px rgba(0,0,0,0.2); }
    .header-main-info { position: relative; }
    .premium-page-title {
        font-size: 36px; font-weight: 900; margin: 0; letter-spacing: -1.2px; line-height: 1.1;
        background: linear-gradient(135deg, var(--text-dark) 0%, #6366f1 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .premium-page-subtitle { color: var(--text-soft); font-size: 15px; margin: 8px 0 0; font-weight: 600; letter-spacing: 0.5px; }
    .ai-insight-pulse {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.15) 100%);
        border: 1px solid rgba(99, 102, 241, 0.3); padding: 14px 28px; border-radius: 100px;
        display: flex; align-items: center; gap: 16px; box-shadow: 0 0 20px rgba(99, 102, 241, 0.15);
        animation: pulse-glow-premium 3s infinite alternate;
    }
    @keyframes pulse-glow-premium { 0% { box-shadow: 0 0 10px rgba(99, 102, 241, 0.1); } 100% { box-shadow: 0 0 25px rgba(139, 92, 246, 0.3); } }
    .pulse-icon { font-size: 26px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }
    .pulse-text { display: flex; flex-direction: column; }
    .pulse-text .label { font-size: 11px; font-weight: 900; color: var(--accent-blue); text-transform: uppercase; letter-spacing: 1.5px; }
    .pulse-text .val { font-size: 14px; font-weight: 700; color: var(--text-dark); transition: opacity 0.5s ease; }

    .row-stats { grid-template-columns: repeat(4, 1fr); }
    .row-main { grid-template-columns: 2.5fr 1.5fr; }

    /* High-End Glassmorphism Cards */
    .perf-card-glass {
        background: linear-gradient(135deg, var(--glass-bg), rgba(255,255,255,0.3));
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-premium);
        padding: 28px;
        box-shadow: 
            0 10px 15px -3px rgba(0, 0, 0, 0.05),
            0 4px 6px -2px rgba(0, 0, 0, 0.02),
            inset 0 0 0 1px rgba(255, 255, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        overflow: hidden;
        position: relative;
    }

    [data-theme="dark"] .perf-card-glass {
        background: linear-gradient(135deg, var(--glass-bg), rgba(30, 41, 59, 0.5));
    }

    .perf-card-glass::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(30deg);
        pointer-events: none;
        transition: all 0.6s ease;
    }

    .perf-card-glass:hover {
        box-shadow: 
            0 15px 30px -5px rgba(0, 0, 0, 0.08),
            0 0 15px 2px rgba(99, 102, 241, 0.05);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .perf-card-glass:hover::before {
        left: -30%;
        top: -30%;
    }

    /* Stat Cards Specific Enhancements */
    .stat-card-premium {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .card-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.4s ease;
        box-shadow: 0 8px 16px rgba(0,0,0,0.05);
    }

    .stat-card-premium:hover .card-icon-box {
        transform: scale(1.02);
    }

    .card-icon-box.blue { background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%); color: white; }
    .card-icon-box.green { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; }
    .card-icon-box.purple { background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%); color: white; }
    .card-icon-box.orange { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); color: white; }

    .card-value {
        font-size: 32px;
        font-weight: 900;
        letter-spacing: -1px;
        background: linear-gradient(135deg, var(--text-dark) 0%, var(--text-soft) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 5px 0;
    }
    .sparkline-container { height: 40px; width: 100%; margin-top: 5px; }
    .highlight { color: var(--accent-green); font-weight: 800; }

    /* Charts Area */
    .chart-area-premium { height: 300px; width: 100%; margin-top: 20px; }
    .donut-container-premium { position: relative; height: 250px; display: flex; align-items: center; justify-content: center; margin-top: 20px; }
    .donut-overlay-text { position: absolute; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .donut-overlay-text .big { font-size: 36px; font-weight: 900; color: var(--text-dark); line-height: 1; }
    .donut-overlay-text .small { font-size: 12px; font-weight: 800; color: var(--text-soft); text-transform: uppercase; letter-spacing: 1px; }

    /* Advanced Table Section */
    .table-header-advanced { 
        display: flex; justify-content: space-between; align-items: center; 
        padding-bottom: 25px; border-bottom: 2px dashed var(--glass-border); 
        margin-bottom: 25px; flex-wrap: wrap; gap: 20px; 
    }
    
    .btn-action-premium { 
        padding: 12px 24px; border-radius: 14px; font-weight: 800; text-decoration: none; 
        display: inline-flex; align-items: center; gap: 10px; font-size: 14px; 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    .btn-action-premium.primary { 
        background: linear-gradient(135deg, var(--accent-blue) 0%, #818cf8 100%); 
        color: white; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25), inset 0 1px 1px rgba(255,255,255,0.3); 
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-action-premium:hover { box-shadow: 0 12px 25px rgba(99, 102, 241, 0.35); filter: brightness(1.05); transform: translateY(-2px); }

    .custom-search-premium { position: relative; width: 320px; }
    .custom-search-premium i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: var(--text-soft); font-size: 16px; }
    .custom-search-premium input { 
        width: 100%; padding: 14px 14px 14px 50px; border-radius: 100px; 
        border: 1px solid rgba(99, 102, 241, 0.2); background: rgba(255, 255, 255, 0.5); 
        color: var(--text-dark); font-size: 15px; font-weight: 600;
        outline: none; transition: all 0.4s ease; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    [data-theme="dark"] .custom-search-premium input { background: rgba(15, 23, 42, 0.5); }
    .custom-search-premium input:focus { 
        border-color: var(--accent-blue); background: var(--glass-bg); 
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); 
    }

    /* DataTables Overrides - Ultra Premium */
    .premium-datatable { width: 100% !important; border-collapse: separate !important; border-spacing: 0 12px !important; margin-top: -10px !important; }
    .premium-datatable thead th { 
        background: transparent; color: var(--text-soft); font-size: 12px; 
        font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; 
        padding: 15px 25px; border: none !important; border-bottom: 1px solid var(--glass-border) !important;
    }
    .premium-datatable tbody tr { 
        background: linear-gradient(90deg, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0.2) 100%); 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), inset 0 1px 0 rgba(255,255,255,0.4);
        transition: all 0.3s ease; 
    }
    [data-theme="dark"] .premium-datatable tbody tr {
        background: linear-gradient(90deg, rgba(30, 41, 59, 0.6) 0%, rgba(15, 23, 42, 0.3) 100%); 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.05);
    }
    .premium-datatable tbody tr:hover { 
        background: linear-gradient(90deg, rgba(255,255,255,0.7) 0%, rgba(255,255,255,0.4) 100%);
        box-shadow: 0 8px 15px -3px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.6);
    }
    [data-theme="dark"] .premium-datatable tbody tr:hover {
        background: linear-gradient(90deg, rgba(30, 41, 59, 0.8) 0%, rgba(15, 23, 42, 0.5) 100%); 
    }
    .premium-datatable td { padding: 18px 25px; border: none !important; vertical-align: middle; }
    .premium-datatable td:first-child { border-top-left-radius: 16px; border-bottom-left-radius: 16px; border-left: 1px solid var(--glass-border); }
    .premium-datatable td:last-child { border-top-right-radius: 16px; border-bottom-right-radius: 16px; border-right: 1px solid var(--glass-border); }

    /* Profiles & Badges Refinement */
    .member-avatar { 
        width: 48px; height: 48px; border-radius: 16px; 
        background: linear-gradient(135deg, var(--accent-blue) 0%, #a855f7 100%); 
        color: white; display: flex; align-items: center; justify-content: center; 
        font-weight: 900; font-size: 20px; 
        box-shadow: 0 6px 12px rgba(99, 102, 241, 0.25), inset 0 1px 1px rgba(255,255,255,0.3);
    }
    .member-info .name { font-weight: 800; color: var(--text-dark); font-size: 16px; letter-spacing: -0.3px; }
    .member-info .joined { font-size: 12px; color: var(--text-soft); font-weight: 600; margin-top: 2px; }

    .id-badge { 
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.02) 100%);
        color: var(--accent-blue); padding: 6px 14px; border-radius: 8px; 
        font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 13px;
        border: 1px solid rgba(99, 102, 241, 0.2);
    }
    .dept-pill { 
        background: rgba(255,255,255,0.6); border: 1px solid var(--glass-border); 
        color: var(--text-dark); padding: 6px 16px; border-radius: 100px; 
        font-size: 13px; font-weight: 800; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    [data-theme="dark"] .dept-pill { background: rgba(0,0,0,0.2); color: var(--text-soft); }

    .contact-links { display: flex; gap: 10px; }
    .contact-links a { width: 36px; height: 36px; border-radius: 12px; background: rgba(255,255,255,0.5); display: flex; align-items: center; justify-content: center; color: var(--text-soft); transition: all 0.3s; border: 1px solid var(--glass-border); }
    [data-theme="dark"] .contact-links a { background: rgba(0,0,0,0.2); }
    .contact-links a:hover { background: var(--accent-blue); color: white; border-color: var(--accent-blue); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(99,102,241,0.2); }

    .status-indicator { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-indicator .dot { width: 10px; height: 10px; border-radius: 50%; }
    .status-indicator.active { color: var(--accent-green); }
    .status-indicator.active .dot { background: var(--accent-green); box-shadow: 0 0 12px var(--accent-green); }
    .status-indicator.inactive { color: var(--text-soft); }
    .status-indicator.inactive .dot { background: var(--text-soft); }

    /* Action Buttons (Ops) */
    .ops-group { display: flex; gap: 8px; }
    .op-btn { 
        width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        background: rgba(255,255,255,0.5); border: 1px solid var(--glass-border); color: var(--text-soft); 
        font-size: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }
    [data-theme="dark"] .op-btn { background: rgba(0,0,0,0.2); }
    .op-btn:hover { transform: translateY(-3px); box-shadow: 0 5px 10px rgba(0,0,0,0.05); }
    .op-btn.view:hover { color: #fff; background: var(--accent-blue); border-color: var(--accent-blue); }
    .op-btn.edit:hover { color: #fff; background: var(--accent-orange); border-color: var(--accent-orange); }
    .op-btn.delete:hover { color: #fff; background: #ef4444; border-color: #ef4444; }

    /* DT Customization & Export Buttons */
    .dt-buttons-container .dt-btn-premium {
        background: rgba(255,255,255,0.6); border: 1px solid var(--glass-border); color: var(--text-dark);
        padding: 10px 20px; border-radius: 12px; font-weight: 800; font-size: 13px; margin-right: 10px;
        transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02); backdrop-filter: blur(5px);
    }
    [data-theme="dark"] .dt-buttons-container .dt-btn-premium { background: rgba(0,0,0,0.3); color: var(--text-soft); }
    .dt-buttons-container .dt-btn-premium:hover { background: var(--accent-blue); color: #fff; border-color: var(--accent-blue); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(99,102,241,0.25); }

    .dataTables_wrapper .dataTables_paginate .paginate_button { 
        border-radius: 12px !important; border: 1px solid var(--glass-border) !important; 
        background: rgba(255,255,255,0.5) !important; font-weight: 700 !important; 
        color: var(--text-dark) !important; padding: 8px 18px !important; 
        margin: 0 4px !important; transition: all 0.3s ease;
    }
    [data-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button { background: rgba(0,0,0,0.3) !important; color: var(--text-soft) !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: rgba(255,255,255,0.9) !important; border-color: var(--accent-blue) !important; color: var(--accent-blue) !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { 
        background: var(--accent-blue) !important; color: white !important; 
        border-color: var(--accent-blue) !important; box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
    }
    .dataTables_info { color: var(--text-soft) !important; font-size: 14px !important; font-weight: 600 !important; padding-top: 15px !important; }

    @media (max-width: 1200px) { .row-stats { grid-template-columns: repeat(2, 1fr); } .row-main { grid-template-columns: 1fr; } }
    @media (max-width: 768px) { .row-stats { grid-template-columns: 1fr; } .header-ai-banner { flex-direction: column; align-items: flex-start; } }
</style>

<!-- Scripts: Intel & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.02)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    // 1. Initialize DataTables
    const table = $('#employeesTable').DataTable({
        dom: 'Brtip',
        pageLength: 10,
        buttons: [
            { extend: 'excel', text: '<i class="fas fa-file-excel"></i> Excel', className: 'dt-btn-premium' },
            { extend: 'pdf', text: '<i class="fas fa-file-pdf"></i> PDF', className: 'dt-btn-premium' },
            { extend: 'print', text: '<i class="fas fa-print"></i> Print', className: 'dt-btn-premium' }
        ],
        language: {
            paginate: { previous: '<i class="fas fa-chevron-left"></i>', next: '<i class="fas fa-chevron-right"></i>' }
        },
        columnDefs: [ { targets: 'no-sort', orderable: false } ]
    });

    // Custom Search
    $('#dtSearchInput').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Move buttons to custom container
    table.buttons().container().appendTo('#tableButtons');

    // 2. Hiring Trend Chart
    const trendCtx = document.getElementById('hiringTrendChart').getContext('2d');
    const trendData = {!! json_encode($hiringTrend) !!};
    
    const gradBlue = trendCtx.createLinearGradient(0, 0, 0, 400);
    gradBlue.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
    gradBlue.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.map(d => d.month),
            datasets: [{
                label: 'New Joinings',
                data: trendData.map(d => d.count),
                borderColor: '#6366f1',
                borderWidth: 4,
                tension: 0.4,
                fill: true,
                backgroundColor: gradBlue,
                pointRadius: 6,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: gridColor }, ticks: { color: textColor, font: { weight: '700' } } },
                x: { grid: { display: false }, ticks: { color: textColor, font: { weight: '700' } } }
            }
        }
    });

    // 3. Department Donut Chart
    const donutCtx = document.getElementById('deptDonutChart').getContext('2d');
    const deptStats = {!! json_encode($departmentStats) !!};

    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: deptStats.map(d => d.department),
            datasets: [{
                data: deptStats.map(d => d.count),
                backgroundColor: ['#6366f1', '#10b981', '#8b5cf6', '#f59e0b', '#ec4899', '#3b82f6'],
                borderWidth: 0,
                cutout: '80%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 4. Sparklines (Mini Analytics)
    const sparkOptions = {
        type: 'line',
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: { x: { display: false }, y: { display: false } },
            elements: { point: { radius: 0 }, line: { tension: 0.4, borderWidth: 2 } }
        }
    };

    ['1','2','3','4'].forEach(id => {
        const ctx = document.getElementById('sparkline'+id).getContext('2d');
        new Chart(ctx, {
            ...sparkOptions,
            data: {
                labels: [1,2,3,4,5,6],
                datasets: [{ 
                    data: [Math.random()*10, Math.random()*10, Math.random()*10, Math.random()*10, Math.random()*10, Math.random()*10], 
                    borderColor: id == '2' ? '#10b981' : '#6366f1' 
                }]
            }
        });
    });

    // AI Insight Generator
    const insights = [
        "Workforce capacity is currently optimal at {{ number_format(($activeEmployees / ($totalEmployees ?: 1)) * 100, 1) }}%",
        "{{ $recentJoiningsCount }} new members joined our mission this month. Momentum is high!",
        "The {{ $departmentStats->sortByDesc('count')->first()->department ?? 'N/A' }} department has the strongest presence.",
        "System health is excellent. All HR operational nodes are synchronized."
    ];
    let insightIdx = 0;
    const insightEl = document.getElementById('dynamicInsight');
    
    function rotateInsight() {
        insightEl.style.opacity = '0';
        setTimeout(() => {
            insightEl.textContent = insights[insightIdx];
            insightEl.style.opacity = '1';
            insightIdx = (insightIdx + 1) % insights.length;
        }, 500);
    }
    setInterval(rotateInsight, 5000);
    rotateInsight();
});

function confirmDelete(btn) {
    if (confirm('Critical: This will permanently remove the employee record. Proceed?')) {
        btn.closest('form').submit();
    }
}
</script>
@endsection