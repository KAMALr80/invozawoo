@extends('layouts.app')

@section('page-title', 'Create New Purchase')

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
        margin-top: 8px;
        font-size: 13px;
        color: var(--text-muted);
        word-break: break-word;
    }

    .product-info.success {
        color: var(--primary);
    }

    .product-info.warning {
        color: var(--danger);
    }

    /* ================= PRICE BREAKDOWN ================= */
    .price-breakdown {
        margin-top: 20px;
        padding: 15px;
        background: var(--bg-white);
        border-radius: var(--radius-md);
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .price-row.subtotal {
        font-weight: 600;
    }

    .price-row.discount {
        color: var(--text-muted);
    }

    .price-row.tax {
        color: var(--text-muted);
    }

    .price-row.total {
        font-size: 18px;
        font-weight: 800;
        border-top: 2px solid var(--border);
        padding-top: 10px;
        margin-top: 10px;
    }

    .price-value {
        font-weight: 600;
        word-break: break-word;
    }

    .price-value.total {
        color: var(--primary);
    }

    .price-value.discount {
        color: var(--success);
    }

    .price-value.tax {
        color: var(--warning);
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

        .price-row.total {
            font-size: 16px;
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

        .price-row.total {
            font-size: 15px;
        }

        .price-value {
            font-size: 13px;
        }

        .price-value.total {
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
                    ➕ Create New Purchase
                </h2>
                <p class="header-subtitle">
                    Fill in the details to add a new purchase
                </p>
            </div>

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

            <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                @csrf

                {{-- Product Information --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="section-icon">📦</span>
                        Product Information
                    </h3>

                    <div class="form-grid">
                        {{-- Product Select --}}
                        <div style="grid-column: span 2;">
                            <label class="form-label">
                                Select Product <span class="required">*</span>
                            </label>
                            <select name="product_id" id="product_id" required class="form-control">
                                <option value="">-- Choose a product --</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" 
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->quantity }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} (Code: {{ $product->product_code }} | Stock: {{ $product->quantity }} | Price: ₹{{ number_format($product->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div id="productInfo" class="product-info"></div>
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="form-label">
                                Quantity <span class="required">*</span>
                            </label>
                            <input type="number" id="qty" name="quantity" value="{{ old('quantity') }}" 
                                min="1" required class="form-control">
                        </div>

                        {{-- Price --}}
                        <div>
                            <label class="form-label">
                                Unit Price (₹) <span class="required">*</span>
                            </label>
                            <input type="number" id="price" step="0.01" name="price" value="{{ old('price') }}"
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
                            <input type="number" id="discount" name="discount" value="{{ old('discount', 0) }}"
                                min="0" max="100" step="0.01" class="form-control">
                        </div>

                        {{-- Tax --}}
                        <div>
                            <label class="form-label">
                                Tax (%)
                            </label>
                            <input type="number" id="tax" name="tax" value="{{ old('tax', 0) }}"
                                min="0" max="100" step="0.01" class="form-control">
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="price-breakdown">
                        <div class="price-row subtotal">
                            <span>Subtotal:</span>
                            <span id="subtotal" class="price-value">₹ 0.00</span>
                        </div>
                        <div class="price-row discount">
                            <span>Discount:</span>
                            <span id="discountAmount" class="price-value discount">- ₹ 0.00</span>
                        </div>
                        <div class="price-row tax">
                            <span>Tax:</span>
                            <span id="taxAmount" class="price-value tax">+ ₹ 0.00</span>
                        </div>
                        <div class="price-row total">
                            <span>Grand Total:</span>
                            <span id="grandTotal" class="price-value total">₹ 0.00</span>
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
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}" required class="form-control">
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="form-label">
                                Payment Method <span class="required">*</span>
                            </label>
                            <select name="payment_method" required class="form-control">
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>💵 Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>💳 Card</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>🏦 Bank Transfer</option>
                                <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>📝 Cheque</option>
                            </select>
                        </div>

                        {{-- Payment Status --}}
                        <div>
                            <label class="form-label">
                                Payment Status <span class="required">*</span>
                            </label>
                            <select name="payment_status" required class="form-control">
                                <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>✅ Paid</option>
                                <option value="overdue" {{ old('payment_status') == 'overdue' ? 'selected' : '' }}>⚠️ Overdue</option>
                            </select>
                        </div>

                        {{-- Purchase Status --}}
                        <div>
                            <label class="form-label">
                                Purchase Status <span class="required">*</span>
                            </label>
                            <select name="status" required class="form-control">
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>✅ Completed</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                            </select>
                        </div>
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
                            <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" 
                                class="form-control">
                        </div>

                        {{-- Supplier Phone --}}
                        <div>
                            <label class="form-label">
                                Phone Number
                            </label>
                            <input type="tel" name="supplier_phone" value="{{ old('supplier_phone') }}"
                                class="form-control">
                        </div>

                        {{-- Supplier Email --}}
                        <div style="grid-column: span 2;">
                            <label class="form-label">
                                Email Address
                            </label>
                            <input type="email" name="supplier_email" value="{{ old('supplier_email') }}"
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
                            class="form-control">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="action-buttons">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                        ↩ Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        💾 Create Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const qtyInput = document.getElementById('qty');
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('discount');
    const taxInput = document.getElementById('tax');
    const subtotalSpan = document.getElementById('subtotal');
    const discountAmountSpan = document.getElementById('discountAmount');
    const taxAmountSpan = document.getElementById('taxAmount');
    const grandTotalSpan = document.getElementById('grandTotal');
    const productInfo = document.getElementById('productInfo');

    function calculateAll() {
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;

        const subtotal = qty * price;
        const discountAmount = (subtotal * discount) / 100;
        const afterDiscount = subtotal - discountAmount;
        const taxAmount = (afterDiscount * tax) / 100;
        const grandTotal = afterDiscount + taxAmount;

        subtotalSpan.textContent = '₹ ' + subtotal.toFixed(2);
        discountAmountSpan.textContent = '- ₹ ' + discountAmount.toFixed(2);
        taxAmountSpan.textContent = '+ ₹ ' + taxAmount.toFixed(2);
        grandTotalSpan.textContent = '₹ ' + grandTotal.toFixed(2);
    }

    function updateProductInfo() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        if (selectedOption.value) {
            const price = selectedOption.dataset.price;
            const stock = selectedOption.dataset.stock;
            priceInput.value = price;
            productInfo.innerHTML = `
                <span class="product-info success">✓ Selected: ${selectedOption.text}</span>
                <span style="margin-left:15px; color:${stock <= 5 ? '#dc2626' : '#16a34a'};">📦 Stock: ${stock}</span>
            `;
        } else {
            productInfo.innerHTML = '';
        }
        calculateAll();
    }

    productSelect.addEventListener('change', updateProductInfo);
    qtyInput.addEventListener('input', calculateAll);
    priceInput.addEventListener('input', calculateAll);
    discountInput.addEventListener('input', calculateAll);
    taxInput.addEventListener('input', calculateAll);

    if (productSelect.value) {
        updateProductInfo();
    }
    calculateAll();

    // Form submission loading state
    const form = document.getElementById('purchaseForm');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '⏳ Processing...';
    });
});
</script>
@endsection