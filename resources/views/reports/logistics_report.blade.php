@extends('layouts.app')

@section('page-title', 'Logistics Report')

@section('content')
<style>
    /* ================= PROFESSIONAL LOGISTICS REPORT STYLES ================= */
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
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-picked {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-in_transit {
        background: #ede9fe;
        color: #5b21b6;
    }

    .badge-out_for_delivery {
        background: #dcfce7;
        color: #166534;
    }

    .badge-delivered {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-failed {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-returned {
        background: #f1f5f9;
        color: #475569;
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
        min-width: 1200px;
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

    .amount {
        font-weight: 600;
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
                <h1>🚚 Logistics Report</h1>
                <p>Complete shipment analytics and delivery performance</p>
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
                <div class="stat-label">Total Shipments</div>
                <div class="stat-value">{{ number_format($stats['total_shipments']) }}</div>
                <div class="stat-sub">In selected period</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Delivered</div>
                <div class="stat-value positive">{{ number_format($stats['delivered']) }}</div>
                <div class="stat-sub">{{ $stats['delivery_rate'] }}% success rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">In Transit</div>
                <div class="stat-value warning">{{ number_format($stats['in_transit']) }}</div>
                <div class="stat-sub">On the way</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ number_format($stats['pending']) }}</div>
                <div class="stat-sub">Awaiting pickup</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Failed/Returned</div>
                <div class="stat-value negative">{{ number_format($stats['failed']) }}</div>
                <div class="stat-sub">Failed deliveries</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value positive">₹{{ number_format($stats['total_revenue'], 2) }}</div>
                <div class="stat-sub">Shipping charges</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Weight</div>
                <div class="stat-value">{{ number_format($stats['total_weight'], 2) }} kg</div>
                <div class="stat-sub">Total package weight</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg Delivery Time</div>
                <div class="stat-value">{{ $stats['avg_delivery_time'] }} hrs</div>
                <div class="stat-sub">From pickup to delivery</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">On-Time Delivery</div>
                <div class="stat-value">{{ $stats['on_time_delivery_rate'] }}%</div>
                <div class="stat-sub">Within estimated date</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">COD Shipments</div>
                <div class="stat-value">{{ number_format($stats['cod_shipments']) }}</div>
                <div class="stat-sub">Cash on Delivery</div>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('reports.logistics') }}" class="filter-form" id="filterForm">
                <div class="filter-group">
                    <label>From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}">
                </div>

                <div class="filter-group">
                    <label>To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}">
                </div>

                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="picked" {{ $filters['status'] == 'picked' ? 'selected' : '' }}>Picked Up</option>
                        <option value="in_transit" {{ $filters['status'] == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="out_for_delivery" {{ $filters['status'] == 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                        <option value="delivered" {{ $filters['status'] == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="failed" {{ $filters['status'] == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Delivery Agent</label>
                    <select name="agent_id">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ $filters['agent_id'] == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label>City</label>
                    <input type="text" name="city" value="{{ $filters['city'] }}" placeholder="Filter by city...">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('reports.logistics') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">
                    <span>📊</span> Daily Shipment Trend
                </div>
                <div class="chart-container">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-title">
                    <span>📈</span> Status Distribution
                </div>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2>
                    <span>📋</span>
                    Shipment List
                    <span style="font-weight: normal; color: var(--text-muted);">
                        ({{ $shipments->total() }} records)
                    </span>
                </h2>
                <div>
                    <input type="text" id="tableSearch" placeholder="Search in table..."
                        style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="shipmentsTable">
                    <thead>
                        <tr>
                            <th class="serial-cell">#</th>
                            <th>Shipment #</th>
                            <th>Tracking #</th>
                            <th>Receiver</th>
                            <th>City</th>
                            <th>Status</th>
                            <th>Courier</th>
                            <th class="text-right">Weight (kg)</th>
                            <th class="text-right">Value (₹)</th>
                            <th class="text-right">Charge (₹)</th>
                            <th>Assigned Agent</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $index => $shipment)
                            @php
                                $serial = ($shipments->currentPage() - 1) * $shipments->perPage() + $index + 1;
                                $statusClass = match($shipment->status) {
                                    'pending' => 'badge-pending',
                                    'picked' => 'badge-picked',
                                    'in_transit' => 'badge-in_transit',
                                    'out_for_delivery' => 'badge-out_for_delivery',
                                    'delivered' => 'badge-delivered',
                                    'failed' => 'badge-failed',
                                    'returned' => 'badge-returned',
                                    default => 'badge-pending',
                                };
                                $statusDisplay = match($shipment->status) {
                                    'pending' => 'Pending',
                                    'picked' => 'Picked Up',
                                    'in_transit' => 'In Transit',
                                    'out_for_delivery' => 'Out for Delivery',
                                    'delivered' => 'Delivered',
                                    'failed' => 'Failed',
                                    'returned' => 'Returned',
                                    default => ucfirst($shipment->status),
                                };
                            @endphp
                            <tr>
                                <td class="serial-cell">{{ $serial }}</td>
                                <td><strong>{{ $shipment->shipment_number }}</strong></td>
                                <td>{{ $shipment->tracking_number ?? 'N/A' }}</td>
                                <td>
                                    <div>{{ $shipment->receiver_name }}</div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">{{ $shipment->receiver_phone }}</div>
                                </td>
                                <td>{{ $shipment->city }}, {{ $shipment->state }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }}">{{ $statusDisplay }}</span>
                                </td>
                                <td>{{ $shipment->courier_partner ?? 'N/A' }}</td>
                                <td class="text-right">{{ number_format($shipment->weight ?? 0, 2) }}</td>
                                <td class="text-right">₹{{ number_format($shipment->declared_value ?? 0, 2) }}</td>
                                <td class="text-right amount">₹{{ number_format($shipment->total_charge ?? 0, 2) }}</td>
                                <td>{{ $shipment->deliveryAgent->name ?? 'Unassigned' }}</td>
                                <td>{{ $shipment->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12">
                                    <div class="empty-state">
                                        <div class="empty-icon">📦</div>
                                        <div class="empty-title">No shipments found</div>
                                        <div class="empty-text">Try adjusting your filters</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($shipments->count() > 0)
                    <tfoot>
                        <tr style="background: var(--bg-light); font-weight: 600;">
                            <td colspan="8" class="text-right"><strong>Total:</strong></td>
                            <td class="text-right"><strong>₹{{ number_format($stats['total_revenue'], 2) }}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            @if($shipments->hasPages())
                <div class="pagination-wrapper">
                    {{ $shipments->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from PHP
    const dailyTrendData = @json($dailyTrend);
    const statusBreakdown = @json($statusBreakdown);

    let dailyChart, statusChart;

    function initCharts() {
        // Daily Trend Chart
        const dailyCtx = document.getElementById('dailyTrendChart')?.getContext('2d');
        if (dailyCtx && dailyTrendData.length) {
            dailyChart = new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyTrendData.map(d => d.date),
                    datasets: [{
                        label: 'Shipments',
                        data: dailyTrendData.map(d => d.total),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2563eb',
                        pointBorderColor: 'white',
                        pointBorderWidth: 2,
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.raw} shipments` } }
                    },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart')?.getContext('2d');
        if (statusCtx && statusBreakdown.length) {
            const statusLabels = statusBreakdown.map(s => s.status.replace('_', ' ').toUpperCase());
            const statusData = statusBreakdown.map(s => s.count);
            const statusColors = {
                pending: '#f59e0b', picked: '#3b82f6', in_transit: '#8b5cf6',
                out_for_delivery: '#10b981', delivered: '#10b981', failed: '#ef4444', returned: '#6b7280'
            };

            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: statusBreakdown.map(s => statusColors[s.status] || '#94a3b8'),
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                        tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${ctx.raw} shipments` } }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    function getFilterParams() {
        const params = new URLSearchParams();
        const startDate = document.querySelector('input[name="start_date"]')?.value || '';
        const endDate = document.querySelector('input[name="end_date"]')?.value || '';
        const status = document.querySelector('select[name="status"]')?.value || 'all';
        const agentId = document.querySelector('select[name="agent_id"]')?.value || '';
        const city = document.querySelector('input[name="city"]')?.value || '';

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (status !== 'all') params.append('status', status);
        if (agentId) params.append('agent_id', agentId);
        if (city) params.append('city', city);

        return params.toString();
    }

    function exportReport(type) {
        const params = getFilterParams();
        let url = '';

        if (type === 'csv') {
            url = '{{ route('reports.logistics.excel') }}?' + params;
            window.location.href = url;
            showToast('CSV export started!', 'success');
        } else if (type === 'pdf') {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            url = '{{ route('reports.logistics.pdf') }}?' + params;
            window.open(url, '_blank');
            setTimeout(() => {
                loadingOverlay.classList.remove('active');
                showToast('PDF generated successfully!', 'success');
            }, 2000);
        }
    }

    document.getElementById('tableSearch')?.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const table = document.getElementById('shipmentsTable');
        const rows = table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');
        if (!rows) return;
        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        }
    });

    function showToast(message, type = 'success') {
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) existingToast.remove();
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        const icon = type === 'success' ? '✅' : (type === 'error' ? '❌' : '⚠️');
        toast.innerHTML = `<span>${icon}</span><span>${message}</span>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    document.addEventListener('DOMContentLoaded', initCharts);

    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif
    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
</script>
@endsection
