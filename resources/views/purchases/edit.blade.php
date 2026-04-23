@extends('layouts.app')

@section('page-title', 'Edit Purchase #' . $purchase->invoice_number)

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --success: #16a34a;
        --danger: #dc2626;
        --warning: #d97706;
        --purple: #7c3aed;
        --text-main: #111827;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 12px 30px rgba(0, 0, 0, 0.08);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 18px;
        --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f3f4f6;
        font-family: var(--font-sans);
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .purchase-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= MAIN CARD ================= */
    .purchase-card {
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(24px, 5vw, 32px);
        box-shadow: var(--shadow-xl);
        width: 100%;
    }

    /* ================= HEADER ================= */
    .header-section {
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
        font-size: clamp(14px, 3vw, 16px);
        word-break: break-word;
    }

    /* ================= STATUS WARNING ================= */
    .status-warning {
        margin-bottom: 20px;
        background: #fee2e2;
        color: #991b1b;
        padding: 12px 16px;
        border-radius: var(--radius-md);
        border-left: 4px solid var(--danger);
        word-break: break-word;
    }

    .status-warning strong {
        display: inline-block;
        margin-bottom: 4px;
    }

    /* ================= ALERTS ================= */
    .alert {
        background: #fee2e2;
        color: #991b1b;
        padding: 16px;
        border-radius: var(--radius-md);
        margin-bottom: 20px;
        border-left: 4px solid var(--danger);
        word-break: break-word;
    }

    .alert-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .alert-icon {
        font-size: 20px;
    }

    .alert-list {
        margin: 0;
        padding-left: 28px;
    }

    .alert-list li {
        margin-bottom: 4px;
    }

    /* ================= FORM SECTIONS ================= */
    .form-section {
        margin-bottom: 30px;
        padding: 20px;
        background: var(--bg-light);
        border-radius: var(--radius-lg);
    }

    .section-title {
        margin: 0 0 20px 0;
        font-size: clamp(16px, 3vw, 18px);
        color: #374151;
        border-bottom: 2px solid var(--border);
        padding-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        word-break: break-word;
    }

    .section-icon {
        font-size: 20px;
    }

    /* ================= FORM GRID ================= */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 768px) {
        .form-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* ================= FORM GROUPS ================= */
    .form-group {
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        display: block;
        margin-bottom: 5px;
        font-size: clamp(13px, 2.5vw, 14px);
        color: #374151;
        word-break: break-word;
    }

    .required {
        color: var(--danger);
        margin-left: 2px;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-white);
        font-size: clamp(13px, 2.5vw, 14px);
        transition: all 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-control:hover {
        border-color: var(--primary);
    }

    .form-control[readonly] {
        background: var(--bg-light);
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 12px center;
        background-repeat: no-repeat;
        background-size: 16px;
        padding-right: 40px;
    }

    /* ================= PRODUCT INFO ================= */
    .product-info {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .product-details {
        flex: 1;
        padding: 12px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--bg-light);
        display: flex;
        align-items: center;
        gap: 10px;
        word-break: break-word;
    }

    .product-icon {
        font-size: 24px;
    }

    .product-name {
        font-weight: 600;
    }

    .product-meta {
        font-size: 13px;
        color: var(--text-muted);
    }

    .product-code {
        margin-right: 10px;
    }

    .product-stock {
        font-weight: 600;
    }

    .product-stock.low {
        color: var(--danger);
    }

    .product-stock.normal {
        color: var(--success);
    }

    .product-hint {
        color: var(--text-muted);
        display: block;
        margin-top: 5px;
        font-size: 13px;
    }

    /* ================= STOCK WARNING ================= */
    .stock-warning {
        margin-top: 5px;
        font-size: 13px;
        color: var(--danger);
        display: none;
    }

    /* ================= PRICE BREAKDOWN ================= */
    .price-breakdown {
        margin-top: 20px;
        padding: 20px;
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .price-title {
        margin: 0 0 15px 0;
        font-size: 16px;
        color: #374151;
        word-break: break-word;
    }

    .price-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    @media (max-width: 768px) {
        .price-grid {
            grid-template-columns: 1fr;
        }
    }

    .price-column {
        padding-right: 15px;
    }

    .price-column.border {
        border-left: 2px solid var(--border);
        padding-left: 15px;
    }

    @media (max-width: 768px) {
        .price-column.border {
            border-left: none;
            border-top: 2px solid var(--border);
            padding-top: 15px;
            padding-left: 0;
        }
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .price-label {
        color: var(--text-muted);
    }

    .price-value {
        font-weight: 600;
        word-break: break-word;
    }

    .price-value.original {
        font-weight: 600;
    }

    .price-value.discount {
        color: var(--success);
    }

    .price-value.tax {
        color: var(--warning);
    }

    .price-value.total {
        font-weight: 800;
    }

    .price-value.total.original {
        color: var(--text-main);
    }

    .price-value.total.new {
        color: var(--primary);
    }

    .price-divider {
        border-top: 1px solid var(--border);
        padding-top: 8px;
        margin-top: 8px;
    }

    /* ================= DIFFERENCE INDICATOR ================= */
    .difference-indicator {
        margin-top: 15px;
        padding: 10px;
        border-radius: var(--radius-md);
        display: none;
    }

    .difference-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .difference-label {
        font-weight: 600;
    }

    .difference-amount {
        font-weight: 800;
        font-size: 18px;
    }

    /* ================= STATUS WARNING ================= */
    .status-change-warning {
        margin-top: 15px;
        padding: 10px;
        border-radius: var(--radius-md);
        background: #fef3c7;
        color: #92400e;
        display: none;
        word-break: break-word;
    }

    /* ================= ACTION BUTTONS ================= */
    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        border-top: 2px solid var(--border);
        padding-top: 20px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 16px);
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: none;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        border: none;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 10px -1px rgba(37, 99, 235, 0.3);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-reset {
        background: #f3f4f6;
        color: #374151;
        border: none;
    }

    .btn-reset:hover {
        background: #e5e7eb;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .container {
            max-width: 1000px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .container {
            max-width: 900px;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .purchase-page {
            padding: 15px;
        }

        .form-section {
            padding: 16px;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .purchase-page {
            padding: 12px;
        }

        .purchase-card {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .product-info {
            flex-direction: column;
            align-items: stretch;
        }

        .product-details {
            flex-direction: column;
            text-align: center;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .purchase-page {
            padding: 8px;
        }

        .purchase-card {
            padding: 16px;
        }

        .header-title {
            font-size: 22px;
        }

        .header-subtitle {
            font-size: 13px;
        }

        .section-title {
            font-size: 16px;
        }

        .form-control {
            padding: 10px;
            font-size: 13px;
        }

        .price-breakdown {
            padding: 12px;
        }

        .price-row {
            font-size: 13px;
        }

        .difference-amount {
            font-size: 16px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .purchase-card {
            padding: 12px;
        }

        .header-title {
            font-size: 20px;
        }

        .form-control {
            padding: 8px;
            font-size: 12px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 13px;
        }

        .price-row {
            font-size: 12px;
        }
    }

    /* Print Styles */
    @media print {
        .btn {
            display: none !important;
        }

        .form-section {
            break-inside: avoid;
        }
    }
</style>

<div class="purchase-page">
    <div class="container">
        <div class="purchase-card">
            {{-- Header --}}
            <div class="header-section">
                <h2 class="header-title">
                    ✏️ Edit Purchase #{{ $purchase->invoice_number }}
                </h2>
                <p class="header-subtitle">
                    Update purchase details - stock will auto-adjust
                </p>
            </div>

            {{-- Status Warning for Cancelled Purchases --}}
            @if($purchase->status === 'cancelled')
                <div class="status-warning">
                    <strong>⚠️ Warning:</strong> This purchase is cancelled. Editing may affect stock levels.
                </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
                <div class="alert">
                    <div class="alert-header">
                        <span class="alert-icon">⚠️</span>
                        <span style="font-weight:600;">Please fix the following errors:</span>
                    </div>
                    <ul class="alert-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('purchases.update', $purchase->id) }}" id="editPurchaseForm">
                @csrf
                @method('PUT')

                {{-- Product Information (Read-only) --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon">📦</span>
                        Product Information
                    </h3>

                    <div class="form-grid-2">
                        {{-- Product (Locked) --}}
                        <div style="grid-column: span 2;">
                            <label class="form-label">
                                Product <span class="required">*</span>
                            </label>
                            <div class="product-info">
                                <div class="product-details">
                                    <span class="product-icon">📦</span>
                                    <div>
                                        <div class="product-name">{{ $purchase->product->name }}</div>
                                        <div class="product-meta">
                                            <span class="product-code">Code: {{ $purchase->product->product_code ?? 'N/A' }}</span> | 
                                            Current Stock: <span class="product-stock {{ $purchase->product->quantity <= 5 ? 'low' : 'normal' }}">
                                                {{ $purchase->product->quantity }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="product_id" value="{{ $purchase->product_id }}">
                            </div>
                            <small class="product-hint">
                                🔒 Product cannot be changed after purchase creation
                            </small>
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="form-label">
                                Quantity <span class="required">*</span>
                            </label>
                            <input type="number" id="qty" name="quantity" 
                                value="{{ old('quantity', $purchase->quantity) }}" 
                                min="1" required class="form-control">
                            <div id="stockWarning" class="stock-warning">
                                ⚠️ Quantity changed from original {{ $purchase->quantity }}
                            </div>
                        </div>

                        {{-- Price --}}
                        <div>
                            <label class="form-label">
                                Unit Price (₹) <span class="required">*</span>
                            </label>
                            <input type="number" id="price" step="0.01" name="price" 
                                value="{{ old('price', $purchase->price) }}"
                                min="0" required class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Pricing Details --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon">💰</span>
                        Pricing Details
                    </h3>

                    <div class="form-grid-2">
                        {{-- Discount --}}
                        <div>
                            <label class="form-label">
                                Discount (%)
                            </label>
                            <input type="number" id="discount" name="discount" 
                                value="{{ old('discount', $purchase->discount ?? 0) }}"
                                min="0" max="100" step="0.01" class="form-control">
                        </div>

                        {{-- Tax --}}
                        <div>
                            <label class="form-label">
                                Tax (%)
                            </label>
                            <input type="number" id="tax" name="tax" 
                                value="{{ old('tax', $purchase->tax ?? 0) }}"
                                min="0" max="100" step="0.01" class="form-control">
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="price-breakdown">
                        <h4 class="price-title">Price Breakdown</h4>
                        
                        <div class="price-grid">
                            <div class="price-column">
                                <div class="price-row">
                                    <span class="price-label">Original Subtotal:</span>
                                    <span id="originalSubtotal" class="price-value original">₹ {{ number_format($purchase->quantity * $purchase->price, 2) }}</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Original Discount:</span>
                                    <span id="originalDiscount" class="price-value discount">- ₹ {{ number_format(($purchase->quantity * $purchase->price * ($purchase->discount ?? 0)) / 100, 2) }}</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">Original Tax:</span>
                                    <span id="originalTax" class="price-value tax">+ ₹ {{ number_format((($purchase->quantity * $purchase->price - ($purchase->quantity * $purchase->price * ($purchase->discount ?? 0)) / 100) * ($purchase->tax ?? 0)) / 100, 2) }}</span>
                                </div>
                                <div class="price-row price-divider">
                                    <span class="price-label">Original Total:</span>
                                    <span id="originalTotal" class="price-value total original">₹ {{ number_format($purchase->grand_total, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="price-column border">
                                <div class="price-row">
                                    <span class="price-label">New Subtotal:</span>
                                    <span id="newSubtotal" class="price-value original">₹ 0.00</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">New Discount:</span>
                                    <span id="newDiscount" class="price-value discount">- ₹ 0.00</span>
                                </div>
                                <div class="price-row">
                                    <span class="price-label">New Tax:</span>
                                    <span id="newTax" class="price-value tax">+ ₹ 0.00</span>
                                </div>
                                <div class="price-row price-divider">
                                    <span class="price-label">New Total:</span>
                                    <span id="newTotal" class="price-value total new">₹ 0.00</span>
                                </div>
                            </div>
                        </div>

                        {{-- Difference Indicator --}}
                        <div id="differenceIndicator" class="difference-indicator">
                            <div class="difference-content">
                                <span class="difference-label">Difference:</span>
                                <span id="differenceAmount" class="difference-amount">₹ 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Purchase Details --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon">📅</span>
                        Purchase Details
                    </h3>

                    <div class="form-grid-2">
                        {{-- Purchase Date --}}
                        <div>
                            <label class="form-label">
                                Purchase Date <span class="required">*</span>
                            </label>
                            <input type="date" name="purchase_date" 
                                value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}" required class="form-control">
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="form-label">
                                Payment Method <span class="required">*</span>
                            </label>
                            <select name="payment_method" required class="form-control">
                                <option value="cash" {{ old('payment_method', $purchase->payment_method) == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                <option value="card" {{ old('payment_method', $purchase->payment_method) == 'card' ? 'selected' : '' }}>💳 Card</option>
                                <option value="bank_transfer" {{ old('payment_method', $purchase->payment_method) == 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method', $purchase->payment_method) == 'cheque' ? 'selected' : '' }}>📝 Cheque</option>
                            </select>
                        </div>

                        {{-- Payment Status --}}
                        <div>
                            <label class="form-label">
                                Payment Status <span class="required">*</span>
                            </label>
                            <select name="payment_status" id="paymentStatus" required class="form-control">
                                <option value="pending" {{ old('payment_status', $purchase->payment_status) == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="paid" {{ old('payment_status', $purchase->payment_status) == 'paid' ? 'selected' : '' }}>✅ Paid</option>
                                <option value="overdue" {{ old('payment_status', $purchase->payment_status) == 'overdue' ? 'selected' : '' }}>⚠️ Overdue</option>
                            </select>
                        </div>

                        {{-- Purchase Status --}}
                        <div>
                            <label class="form-label">
                                Purchase Status <span class="required">*</span>
                            </label>
                            <select name="status" id="purchaseStatus" required class="form-control">
                                <option value="completed" {{ old('status', $purchase->status) == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                <option value="pending" {{ old('status', $purchase->status) == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="cancelled" {{ old('status', $purchase->status) == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            </select>
                        </div>
                    </div>

                    {{-- Status Change Warning --}}
                    <div id="statusWarning" class="status-change-warning">
                        <strong>⚠️ Status Change:</strong> <span id="statusMessage"></span>
                    </div>
                </div>

                {{-- Supplier Information --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon">👤</span>
                        Supplier Information
                    </h3>

                    <div class="form-grid-2">
                        {{-- Supplier Name --}}
                        <div>
                            <label class="form-label">
                                Supplier Name
                            </label>
                            <input type="text" name="supplier_name" value="{{ old('supplier_name', $purchase->supplier_name) }}" 
                                class="form-control">
                        </div>

                        {{-- Supplier Phone --}}
                        <div>
                            <label class="form-label">
                                Phone Number
                            </label>
                            <input type="tel" name="supplier_phone" value="{{ old('supplier_phone', $purchase->supplier_phone) }}"
                                class="form-control">
                        </div>

                        {{-- Supplier Email --}}
                        <div style="grid-column: span 2;">
                            <label class="form-label">
                                Email Address
                            </label>
                            <input type="email" name="supplier_email" value="{{ old('supplier_email', $purchase->supplier_email) }}"
                                class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon">📝</span>
                        Additional Notes
                    </h3>

                    <div>
                        <textarea name="notes" rows="4" 
                            placeholder="Enter any additional notes, terms, or conditions..."
                            class="form-control">{{ old('notes', $purchase->notes) }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="action-buttons">
                    <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-secondary">
                        ↩ Cancel
                    </a>
                    <button type="button" onclick="resetForm()" class="btn btn-reset">
                        🔄 Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        💾 Update Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.getElementById('qty');
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const purchaseStatus = document.getElementById('purchaseStatus');
    const paymentStatus = document.getElementById('paymentStatus');
    
    const newSubtotalSpan = document.getElementById('newSubtotal');
    const newDiscountSpan = document.getElementById('newDiscount');
    const newTaxSpan = document.getElementById('newTax');
    const newTotalSpan = document.getElementById('newTotal');
    const differenceIndicator = document.getElementById('differenceIndicator');
    const differenceAmount = document.getElementById('differenceAmount');
    const stockWarning = document.getElementById('stockWarning');
    const statusWarning = document.getElementById('statusWarning');
    const statusMessage = document.getElementById('statusMessage');

    const originalQty = {{ $purchase->quantity }};
    const originalPrice = {{ $purchase->price }};
    const originalDiscount = {{ $purchase->discount ?? 0 }};
    const originalTax = {{ $purchase->tax ?? 0 }};
    const originalStatus = '{{ $purchase->status }}';
    const originalPaymentStatus = '{{ $purchase->payment_status }}';
    const originalTotal = {{ $purchase->grand_total }};

    function calculateNewTotal() {
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;

        const subtotal = qty * price;
        const discountAmount = (subtotal * discount) / 100;
        const afterDiscount = subtotal - discountAmount;
        const taxAmount = (afterDiscount * tax) / 100;
        const grandTotal = afterDiscount + taxAmount;

        newSubtotalSpan.textContent = '₹ ' + subtotal.toFixed(2);
        newDiscountSpan.textContent = '- ₹ ' + discountAmount.toFixed(2);
        newTaxSpan.textContent = '+ ₹ ' + taxAmount.toFixed(2);
        newTotalSpan.textContent = '₹ ' + grandTotal.toFixed(2);

        const difference = grandTotal - originalTotal;
        if (Math.abs(difference) > 0.01) {
            differenceIndicator.style.display = 'block';
            differenceAmount.textContent = (difference > 0 ? '+' : '') + '₹ ' + difference.toFixed(2);
            differenceAmount.style.color = difference > 0 ? '#dc2626' : '#16a34a';
            differenceIndicator.style.background = difference > 0 ? '#fee2e2' : '#dcfce7';
        } else {
            differenceIndicator.style.display = 'none';
        }

        if (qty !== originalQty) {
            stockWarning.style.display = 'block';
        } else {
            stockWarning.style.display = 'none';
        }

        return grandTotal;
    }

    function checkStatusChanges() {
        const newStatus = purchaseStatus.value;
        const newPaymentStatus = paymentStatus.value;
        let message = [];

        if (newStatus !== originalStatus) {
            message.push(`Purchase status changing from "${originalStatus}" to "${newStatus}"`);
        }

        if (newPaymentStatus !== originalPaymentStatus) {
            message.push(`Payment status changing from "${originalPaymentStatus}" to "${newPaymentStatus}"`);
        }

        if (message.length > 0) {
            statusWarning.style.display = 'block';
            statusMessage.innerHTML = message.join('<br>');
        } else {
            statusWarning.style.display = 'none';
        }
    }

    qtyInput.addEventListener('input', function() {
        calculateNewTotal();
        checkStatusChanges();
    });
    
    priceInput.addEventListener('input', function() {
        calculateNewTotal();
        checkStatusChanges();
    });
    
    discountInput.addEventListener('input', calculateNewTotal);
    taxInput.addEventListener('input', calculateNewTotal);
    purchaseStatus.addEventListener('change', checkStatusChanges);
    paymentStatus.addEventListener('change', checkStatusChanges);

    window.resetForm = function() {
        if (confirm('Reset all changes to original values?')) {
            qtyInput.value = originalQty;
            priceInput.value = originalPrice;
            discountInput.value = originalDiscount;
            taxInput.value = originalTax;
            purchaseStatus.value = originalStatus;
            paymentStatus.value = originalPaymentStatus;
            
            calculateNewTotal();
            checkStatusChanges();
            stockWarning.style.display = 'none';
        }
    };

    calculateNewTotal();
    checkStatusChanges();

    // Form submission loading state
    const form = document.getElementById('editPurchaseForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Processing...';
    });
});
</script>
@endsection