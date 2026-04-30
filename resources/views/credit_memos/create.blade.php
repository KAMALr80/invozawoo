@extends('layouts.app')

@section('page-title', 'Issue Credit Memo - Sale #' . $sale->invoice_no)

@section('content')
<style>
    /* ================= ULTRA-PREMIUM ENTERPRISE DESIGN SYSTEM ================= */
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
        --text-main: #09090b;
        --text-muted: #71717a;
        --border: #e4e4e7;
        --bg-light: #fafafa;
        --bg-white: rgba(255, 255, 255, 0.95);
        --glass-bg: rgba(255, 255, 255, 0.8);
        --glass-border: rgba(255, 255, 255, 0.6);
        --shadow-premium: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        --radius-xl: 20px;
        --font-sans: 'Plus Jakarta Sans', 'Inter', system-ui, sans-serif;
    }

    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .create-memo-wrapper {
        min-height: 100vh;
        padding: clamp(20px, 5vw, 40px);
        position: relative;
        z-index: 1;
        background: var(--bg-light);
    }

    /* Background Orbs */
    .bg-orbs {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
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

    /* Header Section */
    .memo-header {
        background: linear-gradient(135deg, #18181b 0%, #27272a 100%);
        border-radius: var(--radius-xl);
        padding: 2.5rem;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.2);
    }

    .memo-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 300px;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03));
        pointer-events: none;
    }

    .memo-title-group h1 {
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        color: white;
        margin-bottom: 0.5rem;
    }

    .memo-title-group p {
        color: rgba(255,255,255,0.6);
        font-size: 1rem;
        margin-bottom: 0;
    }

    .invoice-badge {
        background: rgba(var(--primary-rgb), 0.2);
        color: var(--primary);
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-weight: 700;
        border: 1px solid rgba(var(--primary-rgb), 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background: var(--bg-white);
        padding: 1.5rem;
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-premium);
        border-color: var(--primary);
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-info h4 {
        font-size: 0.875rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-info p {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
    }

    /* Main Layout */
    .memo-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
    }

    .glass-card {
        background: var(--bg-white);
        backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-premium);
        overflow: hidden;
    }

    .card-header-premium {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header-premium h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* Table Design */
    .items-table {
        width: 100%;
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

    .product-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .product-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--bg-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }

    .product-name {
        font-weight: 700;
        color: var(--text-main);
        display: block;
    }

    .product-sku {
        font-size: 0.75rem;
        color: var(--text-muted);
    }

    /* Quantity Input */
    .qty-control {
        display: flex;
        align-items: center;
        background: var(--bg-light);
        border-radius: 12px;
        padding: 4px;
        width: 120px;
        border: 1px solid var(--border);
    }

    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: none;
        background: white;
        color: var(--text-main);
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        background: var(--primary);
        color: white;
    }

    .qty-input {
        flex: 1;
        background: transparent;
        border: none;
        text-align: center;
        font-weight: 700;
        font-size: 0.9rem;
        width: 100%;
    }

    .qty-input:focus { outline: none; }

    /* Summary Sidebar */
    .summary-card {
        position: sticky;
        top: 2rem;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .summary-item.total {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px dashed var(--border);
        color: var(--text-main);
        font-size: 1.25rem;
        font-weight: 800;
    }

    .config-section {
        padding: 2rem;
        background: var(--bg-light);
        border-top: 1px solid var(--border);
    }

    .form-label-premium {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
        letter-spacing: 0.05em;
    }

    .select-premium {
        width: 100%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: white;
        font-weight: 600;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .select-premium:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(var(--primary-rgb), 0.1);
        outline: none;
    }

    /* Buttons */
    .btn-confirm {
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
        box-shadow: 0 10px 20px -5px rgba(var(--primary-rgb), 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .btn-confirm:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -10px rgba(var(--primary-rgb), 0.5);
    }

    .btn-confirm:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: var(--text-muted);
        box-shadow: none;
    }

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loader-content {
        background: white;
        padding: 3rem;
        border-radius: var(--radius-xl);
        text-align: center;
        animation: scaleUp 0.3s ease;
    }

    @keyframes scaleUp { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid var(--bg-light);
        border-top: 4px solid var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem;
    }

    @keyframes spin { 100% { transform: rotate(360deg); } }

    /* Toast */
    #toastContainer {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 10000;
    }

    .premium-toast {
        background: white;
        padding: 1.25rem 2rem;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
        border-left: 4px solid var(--primary);
        animation: slideIn 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
    }

    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

    @media (max-width: 1200px) {
        .memo-grid { grid-template-columns: 1fr; }
        .summary-card { position: static; }
    }
</style>

<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="create-memo-wrapper">
    <!-- Success/Error Alerts -->
    <div class="container-dash mb-4">
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle fs-4"></i>
                <div class="fw-bold">{{ session('error') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <i class="fas fa-list-ul fs-5"></i>
                    <div class="fw-bold">Please correct the following errors:</div>
                </div>
                <ul class="mb-0 small fw-medium">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <form action="{{ route('credit-memos.store', $sale->id) }}" method="POST" id="cmForm">
        @csrf
        <input type="hidden" name="type" value="partial" id="returnType">
        <input type="hidden" name="restock" value="0"> <!-- Default if checkbox unchecked -->
        
        <!-- Header -->
        <div class="memo-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
                <div class="memo-title-group">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="invoice-badge">#{{ $sale->invoice_no }}</span>
                        <span class="text-white opacity-25">|</span>
                        <span class="text-white opacity-60 fw-bold">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                    </div>
                    <h1>Issue Credit Memo</h1>
                    <p>Process return and generate refund for this sale transaction.</p>
                </div>
                <div class="d-flex gap-3">
                    <a href="{{ route('credit-memos.index') }}" class="btn btn-outline-light rounded-pill px-4" style="border-color: rgba(255,255,255,0.2);">
                        <i class="fas fa-arrow-left me-2"></i> Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(79, 70, 229, 0.1); color: var(--primary);">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h4>Customer</h4>
                    <p>{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <h4>Sale Total</h4>
                    <p>₹{{ number_format($sale->grand_total, 2) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(244, 63, 94, 0.1); color: var(--danger);">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h4>Paid Amount</h4>
                    <p>₹{{ number_format($sale->paid_amount, 2) }}</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-info">
                    <h4>Status</h4>
                    <p>{{ ucfirst($sale->payment_status) }}</p>
                </div>
            </div>
        </div>

        <div class="memo-grid">
            <!-- Left Side: Items -->
            <div class="memo-content">
                <div class="glass-card">
                    <div class="card-header-premium">
                        <h2><i class="fas fa-shopping-basket"></i> Select Items to Return</h2>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                            {{ $sale->items->count() }} Products Found
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="items-table" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>Product Details</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-center">Sold</th>
                                    <th class="text-center">Available</th>
                                    <th class="text-center">Return Qty</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $index => $item)
                                @php 
                                    $available = $item->quantity - $item->returned_quantity;
                                    $taxRate = $sale->sub_total > 0 ? ($sale->tax_amount / $sale->sub_total) : 0;
                                @endphp
                                <tr data-price="{{ $item->price }}" data-tax-rate="{{ $taxRate }}">
                                    <td>
                                        <div class="product-info">
                                            <div class="product-icon">
                                                <i class="fas fa-cube"></i>
                                            </div>
                                            <div>
                                                <span class="product-name">{{ $item->product->name }}</span>
                                                <span class="product-sku">{{ $item->product->product_code }}</span>
                                                <input type="hidden" name="items[{{ $index }}][sale_item_id]" value="{{ $item->id }}">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end fw-semibold">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center"><span class="fw-bold">{{ $item->quantity }}</span></td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $available }}</span></td>
                                    <td class="text-center">
                                        <div class="qty-control mx-auto">
                                            <button type="button" class="qty-btn" onclick="adjustQty(this, -1)">−</button>
                                            <input type="number" name="items[{{ $index }}][quantity]" 
                                                   class="qty-input" value="0" min="0" max="{{ $available }}"
                                                   onchange="updateCalculations()">
                                            <button type="button" class="qty-btn" onclick="adjustQty(this, 1, {{ $available }})">+</button>
                                        </div>
                                    </td>
                                    <td class="text-end fw-bold text-primary line-total">₹0.00</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="glass-card mt-4 p-4">
                    <label class="form-label-premium">Return Reason & Remarks</label>
                    <textarea name="reason" class="select-premium" rows="4" placeholder="Briefly describe why these items are being returned..."></textarea>
                </div>
            </div>

            <!-- Right Side: Summary & Actions -->
            <div class="memo-sidebar">
                <div class="glass-card summary-card">
                    <div class="card-header-premium">
                        <h2><i class="fas fa-calculator"></i> Reversal Summary</h2>
                    </div>
                    <div class="p-4">
                        <div class="summary-item">
                            <span>Subtotal Reversal</span>
                            <span id="subtotalDisplay">₹0.00</span>
                        </div>
                        <div class="summary-item">
                            <span>Tax Reversal</span>
                            <span id="taxDisplay">₹0.00</span>
                        </div>
                        <div class="summary-item total">
                            <span>Total Refund</span>
                            <span id="totalDisplay">₹0.00</span>
                        </div>
                    </div>

                    <div class="config-section">
                        <div class="mb-4">
                            <label class="form-label-premium">Refund Method</label>
                            <select name="refund_method" class="select-premium">
                                <option value="wallet">👛 Customer Wallet</option>
                                <option value="cash">💵 Cash Refund</option>
                                <option value="bank">🏦 Bank Transfer</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label-premium">Inventory Update</label>
                            <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 border">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="restock" value="1" checked id="restockSwitch">
                                    <label class="form-check-label fw-bold" for="restockSwitch">Restock items</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-confirm" id="submitBtn" disabled>
                            <i class="fas fa-check-circle"></i> Confirm Credit Memo
                        </button>
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-info bg-opacity-10 rounded-4 border border-info border-opacity-20">
                    <div class="d-flex gap-3">
                        <i class="fas fa-info-circle text-info mt-1"></i>
                        <div>
                            <p class="small text-info-emphasis fw-bold mb-1">Audit Trail Active</p>
                            <p class="small text-muted mb-0">This transaction will be recorded and inventory balances will be adjusted immediately upon confirmation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loader-content">
        <div class="spinner"></div>
        <h4 class="fw-bold mb-2">Processing Reversal</h4>
        <p class="text-muted mb-0">Synchronizing inventory and ledger balances...</p>
    </div>
</div>

<!-- Toast Container -->
<div id="toastContainer"></div>

@endsection

@push('scripts')
<script>
    function showToast(message, type = 'success') {
        const toast = $(`
            <div class="premium-toast">
                <i class="fas fa-${type === 'success' ? 'check-circle text-success' : 'exclamation-circle text-danger'}"></i>
                <span class="fw-bold">${message}</span>
            </div>
        `);
        $('#toastContainer').append(toast);
        setTimeout(() => toast.fadeOut(400, () => toast.remove()), 4000);
    }

    function adjustQty(btn, delta, max = 999) {
        const input = $(btn).siblings('.qty-input');
        let val = parseInt(input.val()) + delta;
        if (val < 0) val = 0;
        if (val > max) {
            val = max;
            showToast(`Max available quantity is ${max}`, 'error');
        }
        input.val(val).trigger('change');
    }

    function updateCalculations() {
        let totalSub = 0;
        let totalTax = 0;
        let hasQuantity = false;

        $('#itemsTable tbody tr').each(function() {
            const price = parseFloat($(this).data('price'));
            const taxRate = parseFloat($(this).data('tax-rate'));
            const qty = parseInt($(this).find('.qty-input').val()) || 0;

            const lineSub = price * qty;
            const lineTax = lineSub * taxRate;
            const lineTotal = lineSub + lineTax;

            $(this).find('.line-total').text('₹' + lineTotal.toLocaleString('en-IN', {minimumFractionDigits: 2}));

            totalSub += lineSub;
            totalTax += lineTax;
            if (qty > 0) hasQuantity = true;
        });

        const grandTotal = totalSub + totalTax;

        $('#subtotalDisplay').text('₹' + totalSub.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#taxDisplay').text('₹' + totalTax.toLocaleString('en-IN', {minimumFractionDigits: 2}));
        $('#totalDisplay').text('₹' + grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2}));

        $('#submitBtn').prop('disabled', !hasQuantity);
    }

    $(document).ready(function() {
        // Initial calc
        updateCalculations();

        // Form submission
        $('#cmForm').on('submit', function() {
            $('#loadingOverlay').css('display', 'flex');
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');
        });
    });
</script>
@endpush