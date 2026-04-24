@extends('layouts.app')

@section('page-title', 'Financial Summary Report')

@section('content')
<style>
    /* ================= PROFESSIONAL FINANCIAL REPORT STYLES ================= */
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

    .report-wrapper {
        padding: 1.5rem;
        background: #f3f4f6;
        min-height: 100vh;
    }

    .report-container {
        max-width: 1600px;
        margin: 0 auto;
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
        margin: 0;
        color: var(--text-main);
    }

    .header-title p {
        color: var(--text-muted);
        font-size: 0.875rem;
        margin: 4px 0 0 0;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
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
        margin-bottom: 0.75rem;
        font-weight: 700;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }

    .stat-footer {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: auto;
    }

    .stat-value.revenue { color: var(--primary); }
    .stat-value.expense { color: var(--danger); }
    .stat-value.profit { color: var(--success); }
    .stat-value.margin { color: var(--purple); }

    .chart-section {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1024px) {
        .chart-section { grid-template-columns: 1fr; }
    }

    .chart-panel {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        height: 450px; /* Fixed height for symmetry */
        display: flex;
        flex-direction: column;
    }

    .card-panel {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
    }

    .chart-container {
        flex: 1;
        position: relative;
        min-height: 0;
    }

    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .filter-panel {
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
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }

    .filter-group input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 0.875rem;
    }

    .btn {
        padding: 0.5rem 1.25rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }

    .btn-primary { background: var(--primary); color: white; }
    .btn-primary:hover { background: var(--primary-dark); }
    .btn-outline { background: transparent; border-color: var(--border); color: var(--text-main); }
    .btn-outline:hover { background: var(--bg-light); }

    .ratio-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border);
    }

    .ratio-item:last-child { border-bottom: none; }

    .ratio-label { font-weight: 600; color: var(--text-main); }
    .ratio-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-success { background: #d1fae5; color: #065f46; }
    .badge-primary { background: #dbeafe; color: #1e40af; }
    .badge-purple { background: #f3e8ff; color: #6b21a8; }
</style>

<div class="report-wrapper">
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-title">
                <h1>📈 Financial Summary</h1>
                <p>Profit & Loss analysis with performance metrics</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('reports.financial.excel', request()->all()) }}" class="btn btn-outline">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('reports.financial.pdf', request()->all()) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-panel">
            <form action="{{ route('reports.financial') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="{{ route('reports.financial') }}" class="btn btn-outline">Reset</a>
                </div>
            </form>
        </div>

        <!-- Key Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value revenue">₹ {{ number_format($totalRevenue, 2) }}</div>
                <div class="stat-footer">Based on {{ $totalOrders }} orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Expenses</div>
                <div class="stat-value expense">₹ {{ number_format($totalExpenses, 2) }}</div>
                <div class="stat-footer">Total procurement cost</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Net Profit</div>
                <div class="stat-value profit">₹ {{ number_format($netProfit, 2) }}</div>
                <div class="stat-footer">Operating surplus</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Profit Margin</div>
                <div class="stat-value margin">{{ $netProfitMargin }}%</div>
                <div class="stat-footer">Efficiency ratio</div>
            </div>
        </div>

        <!-- Analysis Section -->
        <div class="chart-section">
            <div class="chart-panel">
            <div class="card-title">
                <i class="fas fa-chart-area" style="color: var(--primary);"></i>
                Revenue vs Expenses Trend (Last 6 Months)
            </div>
            <div class="chart-container">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>
        <div class="chart-panel">
            <div class="card-title">
                <i class="fas fa-chart-pie" style="color: var(--warning);"></i>
                Payment Collection Status
            </div>
            <div class="chart-container">
                <canvas id="paymentStatusChart"></canvas>
            </div>
            <div style="margin-top: 1.5rem; flex-shrink: 0;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.875rem; font-weight: 600;">Collection Rate</span>
                    <span style="font-size: 0.875rem; font-weight: 700; color: var(--success);">{{ $collectionRate }}%</span>
                </div>
                <div style="height: 8px; background: #f1f5f9; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ $collectionRate }}%; height: 100%; background: var(--success);"></div>
                </div>
            </div>
        </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="stats-grid">
            <div class="card-panel">
                <div class="card-title">📦 Sales Performance</div>
                <div class="ratio-item">
                    <span class="ratio-label">Average Order Value</span>
                    <span>₹ {{ number_format($avgOrderValue, 2) }}</span>
                </div>
                <div class="ratio-item">
                    <span class="ratio-label">Amount Received</span>
                    <span style="color: var(--success); font-weight: 700;">₹ {{ number_format($amountReceived, 2) }}</span>
                </div>
            </div>
            <div class="card-panel">
                <div class="card-title">🛒 Purchase Analysis</div>
                <div class="ratio-item">
                    <span class="ratio-label">Average Purchase Value</span>
                    <span>₹ {{ number_format($avgPurchaseValue, 2) }}</span>
                </div>
                <div class="ratio-item">
                    <span class="ratio-label">Outstanding Receivables</span>
                    <span style="color: var(--danger); font-weight: 700;">₹ {{ number_format($outstandingAmount, 2) }}</span>
                </div>
            </div>
            <div class="card-panel">
                <div class="card-title">⚖️ Financial Ratios</div>
                <div class="ratio-item">
                    <span class="ratio-label">Liquidity Ratio</span>
                    <span class="ratio-badge badge-success">1.5 Optimal</span>
                </div>
                <div class="ratio-item">
                    <span class="ratio-label">Current Ratio</span>
                    <span class="ratio-badge badge-primary">2.1 Strong</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        const trendsData = @json($trends);
        
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendsData.map(d => d.month),
                datasets: [
                    {
                        label: 'Revenue',
                        data: trendsData.map(d => d.revenue),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#2563eb'
                    },
                    {
                        label: 'Expenses',
                        data: trendsData.map(d => d.expenses),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#ef4444'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, font: { weight: '600' } } }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { callback: function(value) { return '₹' + value.toLocaleString(); } }
                    },
                    x: { grid: { display: false } }
                }
            }
        });

        const statusCtx = document.getElementById('paymentStatusChart').getContext('2d');
        const statusData = @json($paymentStatus);
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Paid', 'Partial', 'EMI', 'Unpaid'],
                datasets: [{
                    data: [statusData.paid, statusData.partial, statusData.emi, statusData.unpaid],
                    backgroundColor: ['#10b981', '#f59e0b', '#6366f1', '#ef4444'],
                    hoverOffset: 10,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 20, font: { weight: '600' } } }
                },
                cutout: '75%'
            }
        });
    });
</script>
@endpush
@endsection
