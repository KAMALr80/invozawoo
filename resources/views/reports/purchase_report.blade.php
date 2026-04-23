@extends('layouts.app')

@section('page-title', 'Purchase Report')

@section('content')
<style>
    /* ================= PROFESSIONAL PURCHASE REPORT STYLES ================= */
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

    .badge-completed {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-paid {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-overdue {
        background: #fee2e2;
        color: #991b1b;
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
        min-width: 1100px;
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

    .supplier-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .supplier-card {
        background: var(--bg-light);
        border-radius: var(--radius-md);
        padding: 0.75rem;
        text-align: center;
        border: 1px solid var(--border);
    }

    .supplier-name {
        font-weight: 600;
        color: var(--text-main);
        font-size: 0.85rem;
        word-break: break-word;
    }

    .supplier-amount {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--primary);
    }

    .supplier-count {
        font-size: 0.7rem;
        color: var(--text-muted);
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

        .supplier-grid {
            grid-template-columns: repeat(2, 1fr);
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
                <h1>🛒 Purchase Report</h1>
                <p>Complete purchase analytics and supplier summary</p>
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
                <div class="stat-label">Total Purchases</div>
                <div class="stat-value">{{ number_format($stats['total_purchases']) }}</div>
                <div class="stat-sub">Purchase orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Spent</div>
                <div class="stat-value positive">₹{{ number_format($stats['total_spent'], 2) }}</div>
                <div class="stat-sub">Total purchase value</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Quantity</div>
                <div class="stat-value">{{ number_format($stats['total_quantity']) }}</div>
                <div class="stat-sub">Items purchased</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Avg Purchase Value</div>
                <div class="stat-value warning">₹{{ number_format($stats['avg_purchase_value'], 2) }}</div>
                <div class="stat-sub">Per order average</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unique Suppliers</div>
                <div class="stat-value">{{ number_format($stats['unique_suppliers']) }}</div>
                <div class="stat-sub">Active suppliers</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value positive">{{ number_format($stats['completed']) }}</div>
                <div class="stat-sub">{{ $stats['completion_rate'] }}% rate</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value warning">{{ number_format($stats['pending']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Cancelled</div>
                <div class="stat-value negative">{{ number_format($stats['cancelled']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Paid</div>
                <div class="stat-value positive">{{ number_format($stats['paid']) }}</div>
                <div class="stat-sub">{{ $stats['payment_rate'] }}% paid</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Payment Pending</div>
                <div class="stat-value warning">{{ number_format($stats['payment_pending']) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Overdue</div>
                <div class="stat-value negative">{{ number_format($stats['overdue']) }}</div>
            </div>
        </div>

        <div class="filter-section">
            <form method="GET" action="{{ route('reports.purchases') }}" class="filter-form" id="filterForm">
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
                        <option value="completed" {{ $filters['status'] == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ $filters['status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="cancelled" {{ $filters['status'] == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <option value="all" {{ $filters['payment_status'] == 'all' ? 'selected' : '' }}>All Payment</option>
                        <option value="paid" {{ $filters['payment_status'] == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ $filters['payment_status'] == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="overdue" {{ $filters['payment_status'] == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Supplier</label>
                    <input type="text" name="supplier" value="{{ $filters['supplier'] }}" placeholder="Supplier name...">
                </div>

                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Invoice, Product...">
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('reports.purchases') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-title">
                    <span>📈</span> Purchase Trend
                </div>
                <div class="chart-container">
                    <canvas id="purchaseTrendChart"></canvas>
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

        @if(isset($supplierStats) && count($supplierStats) > 0)
        <h4 style="margin: 0 0 10px 0; font-size: 1rem;">🏭 Top Suppliers</h4>
        <div class="supplier-grid">
            @foreach($supplierStats as $supplier)
            <div class="supplier-card">
                <div class="supplier-name">{{ $supplier['name'] }}</div>
                <div class="supplier-amount">₹{{ number_format($supplier['total_amount'], 2) }}</div>
                <div class="supplier-count">{{ $supplier['total_purchases'] }} purchases</div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="table-container">
            <div class="table-header">
                <h2>
                    <span>📋</span>
                    Purchase List
                    <span style="font-weight: normal; color: var(--text-muted);">
                        ({{ $purchases->total() }} records)
                    </span>
                </h2>
                <div>
                    <input type="text" id="tableSearch" placeholder="Search in table..."
                        style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="purchaseTable">
                    <thead>
                        <tr>
                            <th class="serial-cell">#</th>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Supplier</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $index => $purchase)
                            @php
                                $serial = ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1;
                                $statusClass = '';
                                if ($purchase->status == 'completed') {
                                    $statusClass = 'badge-completed';
                                } elseif ($purchase->status == 'pending') {
                                    $statusClass = 'badge-pending';
                                } elseif ($purchase->status == 'cancelled') {
                                    $statusClass = 'badge-cancelled';
                                }
                                $paymentClass = '';
                                if ($purchase->payment_status == 'paid') {
                                    $paymentClass = 'badge-paid';
                                } elseif ($purchase->payment_status == 'pending') {
                                    $paymentClass = 'badge-pending';
                                } elseif ($purchase->payment_status == 'overdue') {
                                    $paymentClass = 'badge-overdue';
                                }
                            @endphp
                            <tr>
                                <td class="serial-cell">{{ $serial }}</td>
                                <td><strong>{{ $purchase->invoice_number }}</strong></td>
                                <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                                <td>
                                    <div>{{ isset($purchase->product) ? $purchase->product->name : 'N/A' }}</div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted);">Code: {{ isset($purchase->product) && isset($purchase->product->product_code) ? $purchase->product->product_code : 'N/A' }}</div>
                                </td>
                                <td>{{ isset($purchase->supplier_name) ? $purchase->supplier_name : 'N/A' }}</td>
                                <td class="text-right">{{ number_format($purchase->quantity) }}</td>
                                <td class="text-right">₹{{ number_format($purchase->price, 2) }}</td>
                                <td class="text-right amount amount-positive">₹{{ number_format($purchase->grand_total, 2) }}</td>
                                <td><span class="badge {{ $statusClass }}">{{ ucfirst($purchase->status) }}</span></td>
                                <td><span class="badge {{ $paymentClass }}">{{ ucfirst($purchase->payment_status) }}</span></td>
                                <td class="text-center">
                                    <div class="action-buttons" style="display: flex; gap: 0.25rem; justify-content: center;">
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn-sm"
                                            style="padding: 0.25rem 0.5rem; background: #e0f2fe; border-radius: 4px; text-decoration: none; color: #0369a1;"
                                            title="View">
                                            👁️
                                        </a>
                                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-sm"
                                            style="padding: 0.25rem 0.5rem; background: #fef3c7; border-radius: 4px; text-decoration: none; color: #92400e;"
                                            title="Edit">
                                            ✏️
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11">
                                    <div class="empty-state">
                                        <div class="empty-icon">🛒</div>
                                        <div class="empty-title">No purchases found</div>
                                        <div class="empty-text">Try adjusting your filters</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($purchases->count() > 0)
                    <tfoot>
                        <tr style="background: var(--bg-light); font-weight: 600;">
                            <td colspan="7" class="text-right"><strong>Total:</strong></td>
                            <td class="text-right amount-positive"><strong>₹{{ number_format($stats['total_spent'], 2) }}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>

            @if($purchases->hasPages())
                <div class="pagination-wrapper">
                    {{ $purchases->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var monthlyTrendData = @json($monthlyTrend);
    var statusBreakdown = @json($statusBreakdown);

    var trendChart, statusChart;

    function initCharts() {
        // Purchase Trend Chart
        var trendCtx = document.getElementById('purchaseTrendChart');
        if (trendCtx && monthlyTrendData && monthlyTrendData.length > 0) {
            trendChart = new Chart(trendCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: monthlyTrendData.map(function(d) { return d.date; }),
                    datasets: [
                        {
                            label: 'Purchase Count',
                            data: monthlyTrendData.map(function(d) { return d.total; }),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#2563eb',
                            pointBorderColor: 'white',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: 'Amount (₹)',
                            data: monthlyTrendData.map(function(d) { return d.amount; }),
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: 'white',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Amount (₹)') {
                                        return context.dataset.label + ': ₹' + context.raw.toLocaleString();
                                    }
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Number of Purchases', font: { size: 9 } } },
                        y1: { position: 'right', beginAtZero: true, title: { display: true, text: 'Amount (₹)', font: { size: 9 } } }
                    }
                }
            });
        }

        // Status Distribution Chart
        var statusCtx = document.getElementById('statusChart');
        if (statusCtx && statusBreakdown && statusBreakdown.length > 0) {
            var statusLabels = [];
            var statusAmounts = [];
            var statusColors = [];

            for (var i = 0; i < statusBreakdown.length; i++) {
                statusLabels.push(statusBreakdown[i].status.toUpperCase());
                statusAmounts.push(statusBreakdown[i].amount);
                if (statusBreakdown[i].status == 'completed') {
                    statusColors.push('#10b981');
                } else if (statusBreakdown[i].status == 'pending') {
                    statusColors.push('#f59e0b');
                } else if (statusBreakdown[i].status == 'cancelled') {
                    statusColors.push('#ef4444');
                }
            }

            statusChart = new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusAmounts,
                        backgroundColor: statusColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ₹' + context.raw.toLocaleString();
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
        var startDate = document.querySelector('input[name="start_date"]') ? document.querySelector('input[name="start_date"]').value : '';
        var endDate = document.querySelector('input[name="end_date"]') ? document.querySelector('input[name="end_date"]').value : '';
        var status = document.querySelector('select[name="status"]') ? document.querySelector('select[name="status"]').value : 'all';
        var paymentStatus = document.querySelector('select[name="payment_status"]') ? document.querySelector('select[name="payment_status"]').value : 'all';
        var supplier = document.querySelector('input[name="supplier"]') ? document.querySelector('input[name="supplier"]').value : '';
        var search = document.querySelector('input[name="search"]') ? document.querySelector('input[name="search"]').value : '';

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (status !== 'all') params.append('status', status);
        if (paymentStatus !== 'all') params.append('payment_status', paymentStatus);
        if (supplier) params.append('supplier', supplier);
        if (search) params.append('search', search);

        return params.toString();
    }

    function exportReport(type) {
        var params = getFilterParams();
        var url = '';

        if (type === 'csv') {
            url = '{{ route('reports.purchases.excel') }}?' + params;
            window.location.href = url;
            showToast('CSV export started!', 'success');
        } else if (type === 'pdf') {
            var loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            url = '{{ route('reports.purchases.pdf') }}?' + params;
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
            var table = document.getElementById('purchaseTable');
            var rows = table.getElementsByTagName('tbody')[0] ? table.getElementsByTagName('tbody')[0].getElementsByTagName('tr') : [];
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
        setTimeout(function() { toast.remove(); }, 3000);
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
