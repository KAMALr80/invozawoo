@extends('layouts.app')



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
        --info: #0ea5e9;
        --info-rgb: 14, 165, 233;
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

    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        background: var(--bg-light);
        font-family: var(--font-sans);
        color: var(--text-main);
    }

    /* Background Orbs */
    .bg-orbs {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: -1;
        overflow: hidden;
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(120px);
        opacity: 0.15;
        animation: float 25s infinite alternate ease-in-out;
    }

    .orb-1 { width: 600px; height: 600px; background: var(--primary); top: -250px; right: -150px; }
    .orb-2 { width: 500px; height: 500px; background: #8b5cf6; bottom: -200px; left: -150px; animation-delay: -5s; }
    .orb-3 { width: 400px; height: 400px; background: var(--success); top: 30%; left: 15%; opacity: 0.1; animation-delay: -10s; }

    @keyframes float {
        0% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(40px, 60px) scale(1.1); }
        66% { transform: translate(-30px, 30px) scale(0.9); }
        100% { transform: translate(0, 0) scale(1); }
    }

    .credit-dashboard {
        min-height: 100vh;
        padding: clamp(20px, 5vw, 40px);
        width: 100%;
        position: relative;
    }

    .dashboard-header {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 2.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.2);
        color: white;
    }

    .header-icon-box {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* Stats Grid */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card-premium {
        background: var(--bg-white);
        padding: 1.5rem;
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-premium);
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .stat-card-premium:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
    }

    .stat-circle {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Main Content Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 2rem;
        align-items: start;
    }

    @media (max-width: 1200px) {
        .dashboard-grid { grid-template-columns: 1fr; }
    }

    .glass-card {
        background: var(--bg-white);
        backdrop-filter: blur(12px);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-premium);
        border: 1px solid var(--border);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
    }

    .card-title-premium {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
        font-weight: 800;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Select2 & Controls */
    .control-group {
        padding: 2rem;
    }

    .fetch-btn-premium {
        width: 100%;
        padding: 1.25rem;
        border-radius: 16px;
        border: none;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        font-weight: 800;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .fetch-btn-premium:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: var(--text-muted);
    }

    /* DataTables Premium Styling */
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 8px 16px;
        margin-left: 10px;
        background: var(--bg-light);
    }

    .dataTables_wrapper .dataTables_length select {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 5px 10px;
    }

    table.dataTable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
        margin: 1.5rem 0 !important;
    }

    table.dataTable thead th {
        background: var(--bg-light) !important;
        color: var(--text-muted) !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.05em !important;
        padding: 1rem 1.5rem !important;
        border-bottom: 1px solid var(--border) !important;
    }

    table.dataTable tbody td {
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid var(--border) !important;
        vertical-align: middle !important;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    .status-paid { background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2); }
    .status-partial { background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }
    .status-unpaid { background: rgba(244, 63, 94, 0.1); color: #e11d48; border: 1px solid rgba(244, 63, 94, 0.2); }
    .status-refunded { background: rgba(79, 70, 229, 0.1); color: var(--primary); border: 1px solid rgba(79, 70, 229, 0.2); }

    .return-btn {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.8rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 15px -5px rgba(var(--primary-rgb), 0.4);
    }

    .return-btn:hover { 
        background: linear-gradient(135deg, var(--primary-dark) 0%, #3730a3 100%);
        color: white; 
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -5px rgba(var(--primary-rgb), 0.5);
    }

    /* Staggered Row Animation */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-row {
        animation: fadeInUp 0.4s ease forwards;
        opacity: 0;
    }

    .items-table tr:hover {
        background: rgba(var(--primary-rgb), 0.02);
    }

    .items-table {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0;
    }

    .items-table th {
        background: var(--bg-light);
        padding: 1rem 1.5rem;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
        border-bottom: 1px solid var(--border);
    }

    .items-table td {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    /* Toast Notification */
    .toast-premium {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: white;
        padding: 1rem 2rem;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-left: 4px solid var(--primary);
        z-index: 10000;
        display: none;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>

<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="credit-dashboard">
    <div class="container-dash">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex align-items-center gap-4">
                <div class="header-icon-box">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div>
                    <h1 class="mb-1 fw-bold" style="font-size: 2rem;">Credit Memo Hub</h1>
                    <p class="mb-0 opacity-75">Manage sales returns and customer refunds</p>
                </div>
            </div>
            <div>
                <span class="badge bg-white bg-opacity-10 px-3 py-2 rounded-pill fw-bold" style="border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-clock me-2"></i> {{ date('d M Y, h:i A') }}
                </span>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card-premium">
                <div class="stat-circle" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h4 class="small fw-bold text-muted mb-1">Total Returns</h4>
                    <p class="h4 fw-bold mb-0">{{ $creditMemos->total() }}</p>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-circle" style="background: rgba(244, 63, 94, 0.1); color: var(--danger);">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div>
                    <h4 class="small fw-bold text-muted mb-1">Total Refunded</h4>
                    <p class="h4 fw-bold mb-0">₹{{ number_format($totalRefunded ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-circle" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-wallet"></i>
                </div>
                <div>
                    <h4 class="small fw-bold text-muted mb-1">Wallet Credits</h4>
                    <p class="h4 fw-bold mb-0">₹{{ number_format($walletCredits ?? 0, 2) }}</p>
                </div>
            </div>
            <div class="stat-card-premium">
                <div class="stat-circle" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h4 class="small fw-bold text-muted mb-1">Items Restocked</h4>
                    <p class="h4 fw-bold mb-0">{{ $restockedItems ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Eligible Invoices Section (Full Width) -->
        <div id="invoiceSection" class="glass-card mb-5" style="display: none; border: 1px solid var(--primary); overflow: visible;">
            <div class="card-header-premium d-flex align-items-center justify-content-between p-4" style="background: linear-gradient(135deg, #18181b 0%, #27272a 100%); border-radius: var(--radius-xl) var(--radius-xl) 0 0; color: white;">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-circle" style="background: rgba(255,255,255,0.1); width: 42px; height: 42px; font-size: 1rem; border: 1px solid rgba(255,255,255,0.1);">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Eligible Invoices Found</h5>
                        <p class="small mb-0 opacity-60">Customer: <span id="customerNameBadge" class="fw-bold text-white"></span></p>
                    </div>
                </div>
                <button class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold" onclick="$('#invoiceSection').fadeOut()" style="border-color: rgba(255,255,255,0.2);">
                    <i class="fas fa-times me-2"></i> Dismiss
                </button>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="items-table">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 200px;">Invoice ID</th>
                                <th>Billing Date</th>
                                <th class="text-end">Grand Total</th>
                                <th class="text-center">Eligibility Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="invoiceList">
                            <!-- AJAX Content -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Left Side: Actions -->
            <div class="action-column">
                <div class="glass-card">
                    <div class="card-title-premium">
                        <i class="fas fa-plus-circle text-primary"></i> Issue New Return
                    </div>
                    <div class="control-group">
                        <label class="form-label fw-bold small text-muted mb-3 uppercase tracking-wider">Select Customer</label>
                        <select id="customerSelect" class="form-control">
                            <option value="">-- Search for a customer --</option>
                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->mobile ?? 'No phone' }})</option>
                            @endforeach
                        </select>
                        <button id="fetchInvoicesBtn" class="fetch-btn-premium" disabled>
                            <i class="fas fa-file-invoice"></i> Fetch Eligible Invoices
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: History -->
            <div class="history-column">
                <div class="glass-card">
                    <div class="card-title-premium">
                        <i class="fas fa-history text-primary"></i> Recent Credit Memos
                    </div>
                    <div class="p-4">
                        <table id="creditMemosTable" class="items-table display">
                            <thead>
                                <tr>
                                    <th>CM #</th>
                                    <th>Invoice</th>
                                    <th>Customer</th>
                                    <th class="text-end">Amount</th>
                                    <th class="text-center">Method</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creditMemos as $cm)
                                <tr>
                                    <td class="fw-bold text-primary">#{{ $cm->cm_number }}</td>
                                    <td>
                                        @if($cm->sale)
                                            <a href="{{ route('sales.show', $cm->sale_id) }}" class="text-decoration-none fw-bold text-dark">
                                                #{{ $cm->sale->invoice_no }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                {{ strtoupper(substr($cm->customer->name ?? 'C', 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold">{{ \Illuminate\Support\Str::limit($cm->customer->name ?? 'Walk-in', 15) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-danger">₹{{ number_format($cm->refund_amount, 2) }}</td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-light text-dark border px-3">
                                            <i class="fas fa-{{ $cm->refund_method == 'wallet' ? 'wallet text-success' : ($cm->refund_method == 'bank' ? 'university text-primary' : 'money-bill-wave text-warning') }} me-1"></i>
                                            {{ ucfirst($cm->refund_method) }}
                                        </span>
                                    </td>
                                    <td class="text-center small text-muted">{{ \Carbon\Carbon::parse($cm->created_at)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('credit-memos.show', $cm->id) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                                            View <i class="fas fa-chevron-right ms-1 small"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toastNotification" class="toast-premium">
    <i class="fas fa-check-circle text-success me-2"></i>
    <span id="toastMsg">Action completed successfully</span>
</div>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    function showToast(msg, type = 'success') {
        const toast = $('#toastNotification');
        $('#toastMsg').text(msg);
        toast.fadeIn().delay(3000).fadeOut();
    }

    $(document).ready(function() {
        // 1. Initialize DataTable
        $('#creditMemosTable').DataTable({
            responsive: true,
            order: [[5, 'desc']], // Sort by date
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search records..."
            },
            dom: '<"d-flex justify-content-between align-items-center mb-4"lf>rt<"d-flex justify-content-between align-items-center mt-4"ip>'
        });

        // 2. Initialize Select2
        $('#customerSelect').select2({
            placeholder: "Search customer by name or phone...",
            allowClear: true,
            width: '100%'
        });

        // 3. Handle Select2 Events
        $('#customerSelect').on('change select2:select select2:clear', function() {
            const customerId = $(this).val();
            $('#fetchInvoicesBtn').prop('disabled', !customerId);
        });

        // 4. Fetch Invoices AJAX
        $('#fetchInvoicesBtn').click(function() {
            const customerId = $('#customerSelect').val();
            const customerText = $('#customerSelect option:selected').text();
            const btn = $(this);

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Fetching...');

            $.ajax({
                url: "{{ route('sales.ajax.by-customer') }}",
                method: 'GET',
                data: { customer_id: customerId },
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="fas fa-file-invoice"></i> Fetch Eligible Invoices');
                    
                    if (response.success) {
                        $('#customerNameBadge').text(customerText.split('(')[0]);
                        let html = '';
                        if (response.invoices.length === 0) {
                            html = '<tr><td colspan="5" class="text-center py-5 text-muted">No eligible invoices found for this customer.</td></tr>';
                        } else {
                            response.invoices.forEach((inv, index) => {
                                let statusClass = '';
                                let icon = '';
                                
                                switch(inv.payment_status) {
                                    case 'paid': statusClass = 'status-paid'; icon = 'check-double'; break;
                                    case 'partial': statusClass = 'status-partial'; icon = 'adjust'; break;
                                    default: statusClass = 'status-unpaid'; icon = 'times-circle';
                                }

                                html += `
                                    <tr class="animate-row" style="animation-delay: ${index * 0.05}s">
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-primary bg-opacity-10 p-2 rounded-2 text-primary small">
                                                    <i class="fas fa-hashtag"></i>
                                                </div>
                                                <span class="fw-bold">${inv.invoice_no}</span>
                                            </div>
                                        </td>
                                        <td class="text-muted fw-medium">${inv.sale_date}</td>
                                        <td class="text-end fw-bold text-dark">₹${parseFloat(inv.grand_total).toLocaleString('en-IN', {minimumFractionDigits: 2})}</td>
                                        <td class="text-center">
                                            <span class="status-badge ${statusClass}">
                                                <i class="fas fa-${icon}"></i>
                                                ${inv.payment_status}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="/credit-memos/create/${inv.id}" class="return-btn">
                                                <span>Issue Return</span>
                                                <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        $('#invoiceList').html(html);
                        $('#invoiceSection').fadeIn();
                        $('html, body').animate({ scrollTop: $("#invoiceSection").offset().top - 50 }, 500);
                        showToast(`Found ${response.invoices.length} invoices`);
                    } else {
                        showToast(response.message || 'Error fetching data', 'error');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-file-invoice"></i> Fetch Eligible Invoices');
                    showToast('Failed to connect to server', 'error');
                }
            });
        });
    });
</script>
@endpush