@extends('layouts.app')

@section('page-title', 'Customer Report')

@section('content')
<style>
    /* ================= CUSTOMER REPORT STYLES ================= */
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

    /* Container */
    .report-wrapper {
        padding: 2rem 1rem;
        min-height: 100vh;
        width: 100%;
    }

    .report-container {
        max-width: 1440px;
        margin: 0 auto;
        width: 100%;
    }

    /* Header */
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

    .btn-secondary {
        background: var(--bg-light);
        color: var(--text-main);
        border-color: var(--border);
    }

    .btn-secondary:hover {
        background: var(--border);
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

    /* Filter Section */
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

    /* Table */
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

    /* Customer Info */
    .customer-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .customer-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .customer-details {
        line-height: 1.3;
    }

    .customer-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .customer-meta {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    /* Amount Styles */
    .amount {
        font-weight: 600;
    }

    .amount-positive {
        color: var(--success);
    }

    .amount-negative {
        color: var(--danger);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .action-btn {
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius-sm);
        font-size: 0.75rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .action-btn-view {
        background: #e0f2fe;
        color: #0369a1;
    }

    .action-btn-view:hover {
        background: #bae6fd;
    }

    .action-btn-sales {
        background: #d1fae5;
        color: #065f46;
    }

    .action-btn-sales:hover {
        background: #a7f3d0;
    }

    /* Pagination */
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

    /* Empty State */
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

    /* Toast */
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

    /* Responsive */
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

        .action-buttons {
            flex-direction: column;
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

        .toast-notification {
            left: 10px;
            right: 10px;
            bottom: 10px;
            top: auto;
        }
    }

    @media print {
        .header-actions,
        .filter-section,
        .action-buttons,
        .pagination-wrapper {
            display: none !important;
        }
    }
</style>

<div class="report-wrapper">
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-title">
                <h1>👥 Customer Report</h1>
                <p>Complete customer analytics and transaction summary</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('reports.customers.excel', request()->query()) }}" class="btn btn-success">
                    📥 Export CSV
                </a>
                <a href="{{ route('reports.customers.pdf', request()->query()) }}" class="btn btn-primary">
                    📄 Export PDF
                </a>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Customers</div>
                <div class="stat-value">{{ number_format($stats['total_customers']) }}</div>
                <div class="stat-sub">
                    Active: {{ $stats['active_customers'] }} |
                    Inactive: {{ $stats['inactive_customers'] }}
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Sales</div>
                <div class="stat-value positive">₹{{ number_format($stats['total_sales'], 2) }}</div>
                <div class="stat-sub">From all customers</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Received</div>
                <div class="stat-value positive">₹{{ number_format($stats['total_paid'], 2) }}</div>
                <div class="stat-sub">Collection Rate: {{ $stats['collection_rate'] }}%</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Total Due</div>
                <div class="stat-value negative">₹{{ number_format($stats['total_due'], 2) }}</div>
                <div class="stat-sub">{{ $stats['customers_with_due'] }} customers have due</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Wallet Balance</div>
                <div class="stat-value warning">₹{{ number_format($stats['total_wallet_balance'], 2) }}</div>
                <div class="stat-sub">Total customer wallet balance</div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="{{ route('reports.customers') }}" class="filter-form">
                <div class="filter-group">
                    <label>Search</label>
                    <input type="text" name="search" value="{{ $filters['search'] }}"
                           placeholder="Name, Mobile, Email..." autocomplete="off">
                </div>

                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="all" {{ $filters['status'] == 'all' ? 'selected' : '' }}>All Customers</option>
                        <option value="active" {{ $filters['status'] == 'active' ? 'selected' : '' }}>Active Only</option>
                        <option value="inactive" {{ $filters['status'] == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Sort By</label>
                    <select name="sort_by">
                        <option value="name" {{ $filters['sort_by'] == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="created_at" {{ $filters['sort_by'] == 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="mobile" {{ $filters['sort_by'] == 'mobile' ? 'selected' : '' }}>Mobile</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Order</label>
                    <select name="sort_order">
                        <option value="asc" {{ $filters['sort_order'] == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ $filters['sort_order'] == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                    <a href="{{ route('reports.customers') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="table-container">
            <div class="table-header">
                <h2>
                    <span>📋</span>
                    Customer List
                    <span style="font-weight: normal; color: var(--text-muted);">
                        ({{ $customers->total() }} records)
                    </span>
                </h2>
                <div>
                    <input type="text" id="tableSearch" placeholder="Search in table..."
                           style="padding: 0.5rem 0.75rem; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 0.875rem; width: 200px;">
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="customersTable">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Total Sales</th>
                            <th>Total Paid</th>
                            <th>Total Due</th>
                            <th>Wallet Balance</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $totalSales = $customer->sales()->sum('grand_total');
                                $totalPaid = $customer->payments()->where('status', 'paid')->sum('amount');
                                $totalDue = max(0, $totalSales - $totalPaid);
                                $walletBalance = $customer->getCurrentWalletBalanceAttribute();
                                $isActive = is_null($customer->deleted_at);
                            @endphp
                            <tr>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                        <div class="customer-details">
                                            <div class="customer-name">{{ $customer->name }}</div>
                                            <div class="customer-meta">ID: {{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>{{ $customer->mobile ?? 'N/A' }}</div>
                                    <div class="customer-meta">{{ $customer->email ?? 'No email' }}</div>
                                </td>
                                <td class="amount amount-positive">₹{{ number_format($totalSales, 2) }}</td>
                                <td class="amount amount-positive">₹{{ number_format($totalPaid, 2) }}</td>
                                <td class="amount {{ $totalDue > 0 ? 'amount-negative' : '' }}">
                                    ₹{{ number_format($totalDue, 2) }}
                                </td>
                                <td class="amount {{ $walletBalance > 0 ? 'amount-positive' : '' }}">
                                    ₹{{ number_format($walletBalance, 2) }}
                                </td>
                                <td>
                                    <span class="badge {{ $isActive ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="customer-meta">{{ $customer->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('customers.sales', $customer->id) }}"
                                           class="action-btn action-btn-view" title="View Details">
                                            👁️ Details
                                        </a>
                                        <a href="{{ route('customers.sales', $customer->id) }}"
                                           class="action-btn action-btn-sales" title="View Sales">
                                            📊 Sales
                                        </a>
                                        <a href="{{ route('customers.edit', $customer->id) }}"
                                           class="action-btn action-btn-view" title="Edit">
                                            ✏️ Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-icon">📭</div>
                                        <div class="empty-title">No customers found</div>
                                        <div class="empty-text">Try adjusting your filters or add a new customer</div>
                                        <a href="{{ route('customers.create') }}" class="btn btn-primary" style="margin-top: 1rem;">
                                            + Add Customer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
                <div class="pagination-wrapper">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Table search functionality
    document.getElementById('tableSearch')?.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const table = document.getElementById('customersTable');
        const rows = table.getElementsByTagName('tbody')[0]?.getElementsByTagName('tr');

        if (!rows) return;

        for (let row of rows) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        }
    });

    // Toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-notification ${type}`;
        toast.innerHTML = `
            <span>${type === 'success' ? '✅' : '❌'}</span>
            <span>${message}</span>
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Check for session messages
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    @if(session('error'))
        showToast("{{ session('error') }}", 'error');
    @endif
</script>
@endsection
