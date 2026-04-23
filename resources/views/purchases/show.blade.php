@extends('layouts.app')

@section('page-title', 'Purchase Details - Invoice #' . $purchase->invoice_number)

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
        --shadow-xl: 0 10px 25px rgba(0, 0, 0, 0.08);
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
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER ================= */
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-left {
        flex: 1;
        min-width: 280px;
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

    .header-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-edit {
        background: #f59e0b;
        color: white;
        padding: 10px 18px;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-edit:hover {
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-back {
        background: #e5e7eb;
        color: #111827;
        padding: 10px 18px;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-back:hover {
        background: #d1d5db;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= STATUS BANNER ================= */
    .status-banner {
        margin-bottom: 20px;
        padding: 15px 20px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-banner.completed {
        background: #dcfce7;
        color: #166534;
    }

    .status-banner.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-banner.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-icon {
        font-size: 24px;
    }

    .status-text {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .status-text strong {
        font-size: 16px;
    }

    .status-separator {
        margin-left: 15px;
        padding-left: 15px;
        border-left: 1px solid currentColor;
    }

    @media (max-width: 768px) {
        .status-separator {
            margin-left: 0;
            padding-left: 0;
            border-left: none;
        }
        
        .status-text {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }

    /* ================= MAIN GRID ================= */
    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    @media (max-width: 992px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ================= CARDS ================= */
    .card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-xl);
        padding: 25px;
        margin-bottom: 20px;
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .card-title {
        margin: 0;
        font-size: clamp(18px, 3vw, 20px);
        font-weight: 700;
        word-break: break-word;
    }

    .invoice-badge {
        background: var(--bg-light);
        padding: 5px 10px;
        border-radius: var(--radius-sm);
        font-family: monospace;
        font-weight: 600;
        word-break: break-word;
    }

    /* ================= PRODUCT INFO ================= */
    .product-info {
        display: flex;
        gap: 15px;
        padding: 15px;
        background: var(--bg-light);
        border-radius: var(--radius-md);
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .product-icon {
        width: 60px;
        height: 60px;
        background: var(--primary);
        color: white;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .product-details {
        flex: 1;
    }

    .product-name {
        font-size: 18px;
        font-weight: 600;
        word-break: break-word;
    }

    .product-meta {
        color: var(--text-muted);
        margin-top: 5px;
        word-break: break-word;
    }

    /* ================= PRICE TABLE ================= */
    .price-table {
        width: 100%;
        border-collapse: collapse;
    }

    .price-table tr td {
        padding: 8px 0;
        color: var(--text-muted);
    }

    .price-table tr td:last-child {
        font-weight: 600;
        text-align: right;
        color: var(--text-main);
    }

    .price-table tr.total-row td {
        padding-top: 12px;
        font-size: 18px;
        font-weight: 600;
    }

    .price-table tr.total-row td:last-child {
        font-size: 24px;
        font-weight: 800;
        color: var(--primary);
    }

    .price-table tr.discount td:last-child {
        color: var(--success);
    }

    .price-table tr.tax td:last-child {
        color: var(--danger);
    }

    .price-divider {
        border-top: 2px solid var(--border);
    }

    /* ================= SUPPLIER INFO ================= */
    .supplier-details {
        margin-bottom: 15px;
    }

    .supplier-details:last-child {
        margin-bottom: 0;
    }

    .supplier-label {
        color: var(--text-muted);
        font-size: 13px;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .supplier-value {
        font-weight: 600;
        word-break: break-word;
    }

    /* ================= PAYMENT INFO ================= */
    .payment-info {
        margin-bottom: 15px;
    }

    .payment-label {
        color: var(--text-muted);
        font-size: 13px;
        margin-bottom: 5px;
        word-break: break-word;
    }

    .payment-value {
        font-weight: 600;
        word-break: break-word;
    }

    .payment-status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }

    .payment-status-badge.paid {
        background: #dcfce7;
        color: #166534;
    }

    .payment-status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .payment-status-badge.overdue {
        background: #fee2e2;
        color: #991b1b;
    }

    /* ================= NOTES ================= */
    .notes-content {
        background: var(--bg-light);
        padding: 15px;
        border-radius: var(--radius-md);
        white-space: pre-wrap;
        word-break: break-word;
        line-height: 1.6;
    }

    /* ================= ACTION BUTTONS ================= */
    .action-buttons {
        margin-top: 30px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-print {
        background: var(--bg-light);
        color: #374151;
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-print:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-delete {
        background: var(--danger);
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius-md);
        cursor: pointer;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-delete:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= PRINT STYLES ================= */
    @media print {
        .header-actions,
        .action-buttons,
        .btn-edit,
        .btn-back,
        .btn-print,
        .btn-delete {
            display: none !important;
        }

        .status-banner {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .payment-status-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .card {
            break-inside: avoid;
            box-shadow: none;
            border: 1px solid var(--border);
        }

        .product-icon {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .container {
            max-width: 1400px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .container {
            max-width: 1200px;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .purchase-page {
            padding: 20px;
        }

        .header-section {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
        }

        .btn-edit,
        .btn-back {
            flex: 1;
            justify-content: center;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .purchase-page {
            padding: 15px;
        }

        .card {
            padding: 20px;
        }

        .product-info {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .product-icon {
            margin: 0 auto;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-print,
        .btn-delete {
            width: 100%;
            justify-content: center;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .purchase-page {
            padding: 12px;
        }

        .header-title {
            font-size: 24px;
        }

        .header-subtitle {
            font-size: 14px;
        }

        .card {
            padding: 18px;
        }

        .card-title {
            font-size: 18px;
        }

        .product-name {
            font-size: 16px;
        }

        .product-meta {
            font-size: 13px;
        }

        .price-table tr td {
            font-size: 14px;
        }

        .price-table tr.total-row td:last-child {
            font-size: 20px;
        }

        .payment-status-badge {
            font-size: 12px;
            padding: 3px 10px;
        }

        .notes-content {
            padding: 12px;
            font-size: 13px;
        }

        .btn-print,
        .btn-delete {
            padding: 8px 16px;
            font-size: 14px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .purchase-page {
            padding: 10px;
        }

        .header-title {
            font-size: 22px;
        }

        .card {
            padding: 15px;
        }

        .product-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .product-name {
            font-size: 15px;
        }

        .product-meta {
            font-size: 12px;
        }

        .price-table tr td {
            font-size: 13px;
        }

        .price-table tr.total-row td:last-child {
            font-size: 18px;
        }

        .payment-status-badge {
            font-size: 11px;
            padding: 2px 8px;
        }

        .notes-content {
            padding: 10px;
            font-size: 12px;
        }

        .btn-print,
        .btn-delete {
            padding: 7px 14px;
            font-size: 13px;
        }
    }
</style>

<div class="purchase-page">
    <div class="container">
        
        {{-- Header with Actions --}}
        <div class="header-section">
            <div class="header-left">
                <h2 class="header-title">
                    🧾 Purchase Details
                </h2>
                <p class="header-subtitle">
                    View and manage purchase information
                </p>
            </div>
            <div class="header-actions">
                <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-edit">
                    ✏️ Edit
                </a>
                <a href="{{ route('purchases.index') }}" class="btn-back">
                    ↩ Back
                </a>
            </div>
        </div>

        {{-- Status Banner --}}
        <div class="status-banner {{ $purchase->status }}">
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span class="status-icon">
                    {{ $purchase->status == 'completed' ? '✅' : ($purchase->status == 'pending' ? '⏳' : '❌') }}
                </span>
                <div class="status-text">
                    <strong>Status: {{ ucfirst($purchase->status) }}</strong>
                    <span class="status-separator">
                        Payment: {{ ucfirst($purchase->payment_status) }}
                    </span>
                    <span class="status-separator">
                        Invoice: {{ $purchase->invoice_number }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="main-grid">
            
            {{-- Left Column --}}
            <div>
                {{-- Invoice Card --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">📄 Invoice Details</h3>
                        <div class="invoice-badge">
                            {{ $purchase->invoice_number }}
                        </div>
                    </div>

                    {{-- Product Info --}}
                    <div class="product-info">
                        <div class="product-icon">
                            📦
                        </div>
                        <div class="product-details">
                            <div class="product-name">{{ $purchase->product->name }}</div>
                            <div class="product-meta">
                                Code: {{ $purchase->product->product_code ?? 'N/A' }} | 
                                Current Stock: {{ $purchase->product->quantity }}
                            </div>
                        </div>
                    </div>

                    {{-- Price Breakdown --}}
                    <table class="price-table">
                        <tr>
                            <td>Quantity:</td>
                            <td>{{ $purchase->quantity }}</td>
                        </tr>
                        <tr>
                            <td>Unit Price:</td>
                            <td>₹ {{ number_format($purchase->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Subtotal:</td>
                            <td>₹ {{ number_format($purchase->total, 2) }}</td>
                        </tr>
                        @if($purchase->discount > 0)
                        <tr class="discount">
                            <td>Discount ({{ $purchase->discount }}%):</td>
                            <td>- ₹ {{ number_format(($purchase->total * $purchase->discount) / 100, 2) }}</td>
                        </tr>
                        @endif
                        @if($purchase->tax > 0)
                        <tr class="tax">
                            <td>Tax ({{ $purchase->tax }}%):</td>
                            <td>+ ₹ {{ number_format((($purchase->total - ($purchase->total * $purchase->discount) / 100) * $purchase->tax) / 100, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="total-row">
                            <td>Grand Total:</td>
                            <td>₹ {{ number_format($purchase->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Right Column --}}
            <div>
                {{-- Supplier Info --}}
                <div class="card">
                    <h3 class="card-title" style="margin-bottom: 20px;">👤 Supplier Details</h3>
                    
                    @if($purchase->supplier_name)
                    <div class="supplier-details">
                        <div class="supplier-label">Name</div>
                        <div class="supplier-value">{{ $purchase->supplier_name }}</div>
                    </div>
                    @endif

                    @if($purchase->supplier_phone)
                    <div class="supplier-details">
                        <div class="supplier-label">Phone</div>
                        <div class="supplier-value">📞 {{ $purchase->supplier_phone }}</div>
                    </div>
                    @endif

                    @if($purchase->supplier_email)
                    <div class="supplier-details">
                        <div class="supplier-label">Email</div>
                        <div class="supplier-value">✉️ {{ $purchase->supplier_email }}</div>
                    </div>
                    @endif

                    @if(!$purchase->supplier_name && !$purchase->supplier_phone && !$purchase->supplier_email)
                        <p style="color: var(--text-muted);">No supplier information provided</p>
                    @endif
                </div>

                {{-- Payment Info --}}
                <div class="card">
                    <h3 class="card-title" style="margin-bottom: 20px;">💳 Payment Information</h3>
                    
                    <div class="payment-info">
                        <div class="payment-label">Payment Method</div>
                        <div class="payment-value">
                            @if($purchase->payment_method == 'cash') 💵 Cash
                            @elseif($purchase->payment_method == 'card') 💳 Card
                            @elseif($purchase->payment_method == 'bank_transfer') 🏦 Bank Transfer
                            @elseif($purchase->payment_method == 'cheque') 📝 Cheque
                            @else {{ $purchase->payment_method }}
                            @endif
                        </div>
                    </div>

                    <div class="payment-info">
                        <div class="payment-label">Payment Status</div>
                        <div>
                            <span class="payment-status-badge {{ $purchase->payment_status }}">
                                {{ ucfirst($purchase->payment_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="payment-info">
                        <div class="payment-label">Purchase Date</div>
                        <div class="payment-value">📅 {{ $purchase->purchase_date->format('l, d F Y') }}</div>
                    </div>

                    <div class="payment-info">
                        <div class="payment-label">Created By</div>
                        <div class="payment-value">👤 {{ $purchase->user->name ?? 'System' }}</div>
                        <div style="color: var(--text-muted); font-size: 12px; margin-top: 2px;">
                            {{ $purchase->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                @if($purchase->notes)
                <div class="card">
                    <h3 class="card-title" style="margin-bottom: 15px;">📝 Notes</h3>
                    <div class="notes-content">
                        {{ $purchase->notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons">
            <button onclick="window.print()" class="btn-print">
                🖨️ Print
            </button>
            <form method="POST" action="{{ route('purchases.destroy', $purchase->id) }}" 
                onsubmit="return confirm('Are you sure you want to delete this purchase? This action cannot be undone!')"
                style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete">
                    🗑️ Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection