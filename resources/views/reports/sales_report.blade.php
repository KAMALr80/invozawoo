@extends('layouts.app')

@section('page-title', 'Sales Report')

@section('content')
    <style>
        /* ================= PROFESSIONAL SALES REPORT STYLES ================= */
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

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-paid {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-partial {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-emi {
            background: #e0e7ff;
            color: #3730a3;
        }

        .amount {
            font-weight: 600;
        }

        .amount-positive {
            color: var(--success);
        }

        .amount-negative {
            color: var(--danger);
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

        .btn-sm {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .action-buttons {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
        }

        /* Status Summary Row Styles */
        .status-summary-row {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-top: 2px solid var(--border);
        }

        .status-summary-row td {
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .status-summary-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 20px;
            padding: 4px 12px;
            border-radius: 20px;
            background: white;
            box-shadow: var(--shadow-sm);
        }

        .status-summary-badge {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-summary-badge.paid {
            background: var(--success);
        }

        .status-summary-badge.partial {
            background: var(--warning);
        }

        .status-summary-badge.unpaid {
            background: var(--danger);
        }

        .status-summary-badge.emi {
            background: var(--purple);
        }

        .status-summary-count {
            font-weight: 700;
            font-size: 1rem;
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

            .status-summary-item {
                margin-right: 10px;
                margin-bottom: 5px;
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
            <!-- Loading Overlay -->
            <div id="loadingOverlay" class="loading-overlay">
                <div class="spinner"></div>
                <div class="loading-text">Generating PDF...</div>
            </div>

            <!-- Header -->
            <div class="report-header">
                <div class="header-title">
                    <h1>📊 Sales Report</h1>
                    <p>Complete sales analytics and transaction summary</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value positive">₹{{ number_format($stats['total_revenue'], 2) }}</div>
                    <div class="stat-sub">From {{ $stats['total_orders'] }} orders</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Discount</div>
                    <div class="stat-value">-₹{{ number_format($stats['total_discount'], 2) }}</div>
                    <div class="stat-sub">Saved by customers</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Total Tax</div>
                    <div class="stat-value">₹{{ number_format($stats['total_tax'], 2) }}</div>
                    <div class="stat-sub">GST collected</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Avg Order Value</div>
                    <div class="stat-value warning">₹{{ number_format($stats['avg_order_value'], 2) }}</div>
                    <div class="stat-sub">Per transaction</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Collection Rate</div>
                    <div class="stat-value">{{ $stats['collection_rate'] }}%</div>
                    <div class="stat-sub">{{ $stats['paid_count'] }} paid invoices</div>
                </div>
            </div>

            <!-- Status Breakdown Cards (Keep as is) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Paid</div>
                    <div class="stat-value positive">₹{{ number_format($stats['paid_amount'], 2) }}</div>
                    <div class="stat-sub">{{ $stats['paid_count'] }} invoices</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Partial</div>
                    <div class="stat-value warning">₹{{ number_format($stats['partial_amount'], 2) }}</div>
                    <div class="stat-sub">{{ $stats['partial_count'] }} invoices</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Unpaid</div>
                    <div class="stat-value negative">₹{{ number_format($stats['unpaid_amount'], 2) }}</div>
                    <div class="stat-sub">{{ $stats['unpaid_count'] }} invoices</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">EMI</div>
                    <div class="stat-value">₹{{ number_format($stats['emi_amount'], 2) }}</div>
                    <div class="stat-sub">{{ $stats['emi_count'] }} invoices</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="{{ route('reports.sales') }}" class="filter-form" id="filterForm">
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
                            <option value="paid" {{ $filters['status'] == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ $filters['status'] == 'partial' ? 'selected' : '' }}>Partial
                            </option>
                            <option value="unpaid" {{ $filters['status'] == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="emi" {{ $filters['status'] == 'emi' ? 'selected' : '' }}>EMI</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Customer</label>
                        <select name="customer_id">
                            <option value="">All Customers</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ $filters['customer_id'] == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                        <a href="{{ route('reports.sales') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Sales Table -->
            <div class="table-container">
                <div class="table-header">
                    <h2>
                        <span>📋</span>
                        Sales Transactions
                        <span style="font-weight: normal; color: var(--text-muted);">
                            ({{ $sales->total() }} records)
                        </span>
                    </h2>
                    <div class="header-actions">
                        <button onclick="exportReport('csv')" class="btn btn-success" id="exportCsvBtn">
                            📥 Export CSV
                        </button>
                        <button onclick="exportReport('pdf')" class="btn btn-primary" id="exportPdfBtn">
                            📄 Export PDF
                        </button>
                        <button onclick="window.print()" class="btn btn-secondary">
                            🖨️ Print
                        </button>
                    </div>
                    <div>
                        <input type="text" id="tableSearch" placeholder="Search in table..."
                            style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                    </div>
                </div>



                <div class="table-responsive">
                    <table class="data-table" id="salesTable">
                        <thead>
                            32
                            <th class="serial-cell">#</th>
                            <th>Invoice #</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th class="text-right">Sub Total</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Tax</th>
                            <th class="text-right">Grand Total</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $index => $sale)
                                @php
                                    $statusClass = match ($sale->payment_status) {
                                        'paid' => 'badge-paid',
                                        'partial' => 'badge-partial',
                                        'unpaid' => 'badge-unpaid',
                                        'emi' => 'badge-emi',
                                        default => 'badge-unpaid',
                                    };

                                    $statusDisplay = match ($sale->payment_status) {
                                        'paid' => 'Paid',
                                        'partial' => 'Partial',
                                        'unpaid' => 'Unpaid',
                                        'emi' => 'EMI',
                                        default => ucfirst($sale->payment_status),
                                    };

                                    // Serial number calculation (considering pagination)
                                    $serial = ($sales->currentPage() - 1) * $sales->perPage() + $index + 1;
                                @endphp
                                <tr>
                                    <td class="serial-cell">{{ $serial }}</td>
                                    <td>
                                        <strong>#{{ $sale->invoice_no }}</strong>
                                    </td>
                                    <td>{{ $sale->sale_date->format('d M Y') }}</td>
                                    <td>
                                        <div>{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                                        <div class="customer-meta" style="font-size: 0.75rem; color: var(--text-muted);">
                                            {{ $sale->customer->mobile ?? 'No contact' }}
                                        </div>
                                    </td>
                                    <td class="text-right">₹{{ number_format($sale->sub_total, 2) }}</td>
                                    <td class="text-right">-₹{{ number_format($sale->discount, 2) }}</td>
                                    <td class="text-right">+₹{{ number_format($sale->tax_amount, 2) }}</td>
                                    <td class="text-right amount amount-positive">
                                        ₹{{ number_format($sale->grand_total, 2) }}
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusDisplay }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="{{ route('sales.show', $sale->id) }}" class="btn-sm"
                                                style="background: #e0f2fe; border-radius: 4px; text-decoration: none; color: #0369a1;"
                                                title="View">
                                                👁️
                                            </a>
                                            <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank"
                                                class="btn-sm"
                                                style="background: #dcfce7; border-radius: 4px; text-decoration: none; color: #166534;"
                                                title="PDF">
                                                📄
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
                                        <div class="empty-state">
                                            <div class="empty-icon">📭</div>
                                            <div class="empty-title">No sales found</div>
                                            <div class="empty-text">Try adjusting your filters or create a new sale</div>
                                            <a href="{{ route('sales.create') }}" class="btn btn-primary"
                                                style="margin-top: 1rem;">
                                                + Create New Sale
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($sales->count() > 0)
                            <!-- Footer with Total Amounts -->
                            <tfoot>
                                <tr style="background: var(--bg-light); font-weight: 600;">
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td class="text-right">
                                        <strong>₹{{ number_format($stats['total_revenue'] + $stats['total_discount'], 2) }}</strong>
                                    </td>
                                    <td class="text-right">
                                        <strong>-₹{{ number_format($stats['total_discount'], 2) }}</strong>
                                    </td>
                                    <td class="text-right"><strong>+₹{{ number_format($stats['total_tax'], 2) }}</strong>
                                    </td>
                                    <td class="text-right amount-positive">
                                        <strong>₹{{ number_format($stats['total_revenue'], 2) }}</strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                <!-- Status Summary Row - Shows invoice counts by status -->
                                <tr class="status-summary-row">
                                    <td colspan="10" style="padding: 12px 1rem;">
                                        <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                                            <span style="font-weight: 600; color: var(--text-muted);">📊 Status
                                                Summary:</span>
                                            <div class="status-summary-item">
                                                <span class="status-summary-badge paid"></span>
                                                <span>Paid:</span>
                                                <span class="status-summary-count"
                                                    style="color: var(--success);">{{ $stats['paid_count'] }}</span>
                                                <span>invoices</span>
                                            </div>
                                            <div class="status-summary-item">
                                                <span class="status-summary-badge partial"></span>
                                                <span>Partial:</span>
                                                <span class="status-summary-count"
                                                    style="color: var(--warning);">{{ $stats['partial_count'] }}</span>
                                                <span>invoices</span>
                                            </div>
                                            <div class="status-summary-item">
                                                <span class="status-summary-badge unpaid"></span>
                                                <span>Unpaid:</span>
                                                <span class="status-summary-count"
                                                    style="color: var(--danger);">{{ $stats['unpaid_count'] }}</span>
                                                <span>invoices</span>
                                            </div>
                                            <div class="status-summary-item">
                                                <span class="status-summary-badge emi"></span>
                                                <span>EMI:</span>
                                                <span class="status-summary-count"
                                                    style="color: var(--purple);">{{ $stats['emi_count'] }}</span>
                                                <span>invoices</span>
                                            </div>
                                            <div style="margin-left: auto;">
                                                <span style="font-size: 0.75rem; color: var(--text-muted);">Total:
                                                    {{ $stats['total_orders'] }} invoices</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                @if ($sales->hasPages())
                    <div class="pagination-wrapper">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Get current filter parameters
        function getFilterParams() {
            const params = new URLSearchParams();
            const startDate = document.querySelector('input[name="start_date"]')?.value || '';
            const endDate = document.querySelector('input[name="end_date"]')?.value || '';
            const status = document.querySelector('select[name="status"]')?.value || 'all';
            const customerId = document.querySelector('select[name="customer_id"]')?.value || '';

            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (status !== 'all') params.append('status', status);
            if (customerId) params.append('customer_id', customerId);

            return params.toString();
        }

        // Export Report Function
        function exportReport(type) {
            const params = getFilterParams();
            let url = '';

            if (type === 'csv') {
                url = '{{ route('reports.sales.excel') }}?' + params;
                window.location.href = url;
                showToast('CSV export started!', 'success');
            } else if (type === 'pdf') {
                const loadingOverlay = document.getElementById('loadingOverlay');
                loadingOverlay.classList.add('active');

                url = '{{ route('reports.sales.pdf') }}?' + params;
                window.open(url, '_blank');

                setTimeout(() => {
                    loadingOverlay.classList.remove('active');
                    showToast('PDF generated successfully!', 'success');
                }, 2000);
            }
        }

        // Table search functionality
        document.getElementById('tableSearch')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('salesTable');
            const rows = table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');

            if (!rows) return;

            for (let row of rows) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            }
        });

        // Toast notification
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

        // Check for session messages
        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif

        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    </script>
@endsection
