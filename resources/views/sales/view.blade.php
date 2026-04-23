@extends('layouts.app')

@section('page-title', 'Invoice #' . $sale->invoice_no)

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
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border: #e5e7eb;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 10px 25px rgba(0, 0, 0, 0.08);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 14px;
        --radius-2xl: 16px;
        --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        font-family: var(--font-sans);
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .invoice-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= INVOICE CARD ================= */
    .invoice-card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: clamp(20px, 4vw, 30px);
        box-shadow: var(--shadow-xl);
        width: 100%;
    }

    /* ================= HEADER ================= */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .header-left {
        flex: 1;
        min-width: 280px;
    }

    .invoice-title {
        margin: 0 0 5px 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 700;
        color: var(--text-main);
        word-break: break-word;
    }

    .invoice-number {
        font-size: clamp(18px, 4vw, 20px);
        color: var(--primary);
        font-weight: 600;
        word-break: break-word;
    }

    .header-right {
        text-align: right;
    }

    .invoice-meta {
        background: var(--bg-light);
        padding: 12px 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 5px;
        flex-wrap: wrap;
    }

    .meta-row:last-child {
        margin-bottom: 0;
    }

    .meta-label {
        color: var(--text-muted);
        font-size: 14px;
    }

    .meta-value {
        font-weight: 600;
        color: var(--text-main);
    }

    /* ================= CUSTOMER INFO ================= */
    .customer-section {
        background: var(--bg-light);
        border-radius: var(--radius-lg);
        padding: clamp(16px, 3vw, 20px);
        margin-bottom: 25px;
        border: 1px solid var(--border);
    }

    .customer-label {
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .customer-name {
        font-size: clamp(18px, 4vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 8px;
        word-break: break-word;
    }

    .customer-details {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        color: var(--text-muted);
        font-size: 14px;
    }

    .customer-detail-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* ================= TABLE SECTION ================= */
    .table-section {
        margin-bottom: 25px;
    }

    .section-title {
        font-size: clamp(16px, 3.5vw, 18px);
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-icon {
        font-size: 20px;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        width: 100%;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
    }

    .items-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
    }

    .items-table thead tr {
        background: #1f2937;
        color: white;
    }

    .items-table th {
        padding: 12px 15px;
        text-align: left;
        font-size: clamp(12px, 2.5vw, 14px);
        font-weight: 600;
        white-space: nowrap;
    }

    .items-table td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--border);
        font-size: clamp(13px, 2.5vw, 14px);
        white-space: nowrap;
    }

    .items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .items-table tbody tr:hover {
        background: var(--bg-light);
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    /* ================= SUMMARY SECTION ================= */
    .summary-section {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 25px;
    }

    .summary-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        min-width: 300px;
        width: 100%;
        max-width: 400px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed var(--border);
        flex-wrap: wrap;
        gap: 10px;
    }

    .summary-row:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: var(--text-muted);
        font-weight: 500;
    }

    .summary-value {
        font-weight: 600;
        color: var(--text-main);
    }

    .grand-total-row {
        margin-top: 8px;
        padding-top: 12px;
        border-top: 2px solid var(--border);
    }

    .grand-total-label {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-main);
    }

    .grand-total-value {
        font-size: 20px;
        font-weight: 800;
        color: var(--primary);
    }

    /* ================= ACTION BUTTONS ================= */
    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 25px;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 10px 18px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: clamp(13px, 2.5vw, 14px);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3);
    }

    .btn-secondary {
        background: var(--bg-light);
        color: #475569;
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    .btn-success {
        background: var(--success);
        color: white;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.3);
    }

    .btn-warning {
        background: var(--warning);
        color: white;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
    }

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(245, 158, 11, 0.3);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
        box-shadow: 0 4px 10px rgba(220, 38, 38, 0.2);
    }

    .btn-danger:hover {
        background: #b91c1c;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(220, 38, 38, 0.3);
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .summary-card {
            max-width: 400px;
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .invoice-card {
            padding: 25px;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .invoice-page {
            padding: 20px;
        }

        .invoice-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-right {
            width: 100%;
        }

        .invoice-meta {
            width: 100%;
        }

        .summary-section {
            justify-content: flex-start;
        }

        .summary-card {
            max-width: 100%;
        }

        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-group {
            justify-content: center;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .invoice-page {
            padding: 15px;
        }

        .invoice-card {
            padding: 20px;
        }

        .customer-details {
            flex-direction: column;
            gap: 10px;
        }

        .items-table {
            min-width: 600px;
        }

        .items-table th,
        .items-table td {
            padding: 10px;
            font-size: 12px;
        }

        .summary-card {
            padding: 16px;
        }

        .summary-label {
            font-size: 13px;
        }

        .summary-value {
            font-size: 13px;
        }

        .grand-total-label {
            font-size: 14px;
        }

        .grand-total-value {
            font-size: 18px;
        }

        .action-buttons {
            margin-top: 20px;
        }

        .btn-group {
            width: 100%;
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .invoice-page {
            padding: 12px;
        }

        .invoice-card {
            padding: 16px;
        }

        .invoice-title {
            font-size: 22px;
        }

        .invoice-number {
            font-size: 18px;
        }

        .meta-row {
            flex-direction: column;
            gap: 5px;
        }

        .customer-name {
            font-size: 18px;
        }

        .customer-detail-item {
            font-size: 13px;
        }

        .section-title {
            font-size: 16px;
        }

        .items-table {
            min-width: 500px;
        }

        .items-table th,
        .items-table td {
            padding: 8px;
            font-size: 11px;
        }

        .summary-card {
            padding: 14px;
        }

        .summary-label {
            font-size: 12px;
        }

        .summary-value {
            font-size: 12px;
        }

        .grand-total-label {
            font-size: 13px;
        }

        .grand-total-value {
            font-size: 16px;
        }

        .btn {
            padding: 8px 14px;
            font-size: 12px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .invoice-card {
            padding: 12px;
        }

        .invoice-title {
            font-size: 20px;
        }

        .invoice-number {
            font-size: 16px;
        }

        .customer-name {
            font-size: 16px;
        }

        .items-table {
            min-width: 400px;
        }

        .items-table th,
        .items-table td {
            padding: 6px;
            font-size: 10px;
        }

        .summary-card {
            padding: 12px;
        }

        .summary-label {
            font-size: 11px;
        }

        .summary-value {
            font-size: 11px;
        }

        .grand-total-label {
            font-size: 12px;
        }

        .grand-total-value {
            font-size: 14px;
        }

        .btn {
            padding: 6px 12px;
            font-size: 11px;
        }
    }

    /* Print Styles */
    @media print {
        .action-buttons,
        .btn-group {
            display: none !important;
        }

        .invoice-card {
            box-shadow: none;
            border: 1px solid #000;
        }

        .items-table thead tr {
            background: #f0f0f0 !important;
            color: #000 !important;
        }
    }
</style>

<div class="invoice-page">
    <div class="container">
        <div class="invoice-card">
            {{-- Header --}}
            <div class="invoice-header">
                <div class="header-left">
                    <h2 class="invoice-title">👁️ Invoice View</h2>
                    <div class="invoice-number">#{{ $sale->invoice_no }}</div>
                </div>
                <div class="header-right">
                    <div class="invoice-meta">
                        <div class="meta-row">
                            <span class="meta-label">Status:</span>
                            <span class="meta-value" style="color: {{ 
                                $sale->payment_status == 'paid' ? '#16a34a' : 
                                ($sale->payment_status == 'partial' ? '#d97706' : '#dc2626') 
                            }}">{{ ucfirst($sale->payment_status) }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Date:</span>
                            <span class="meta-value">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Created:</span>
                            <span class="meta-value">{{ $sale->created_at->format('d M Y h:i A') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Customer Information --}}
            <div class="customer-section">
                <div class="customer-label">Customer Details</div>
                <div class="customer-name">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                @if ($sale->customer)
                    <div class="customer-details">
                        <div class="customer-detail-item">
                            <span>📱</span>
                            <span>{{ $sale->customer->mobile ?? 'N/A' }}</span>
                        </div>
                        <div class="customer-detail-item">
                            <span>✉️</span>
                            <span>{{ $sale->customer->email ?? 'N/A' }}</span>
                        </div>
                        @if ($sale->customer->address)
                            <div class="customer-detail-item">
                                <span>📍</span>
                                <span>{{ $sale->customer->address }}</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Items Table --}}
            <div class="table-section">
                <h3 class="section-title">
                    <span class="section-icon">🛒</span>
                    Items Purchased
                </h3>

                <div class="table-responsive">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Product</th>
                                <th class="text-right">Price (₹)</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Total (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <div style="font-weight: 600;">{{ $item->product->name ?? 'Product Deleted' }}</div>
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            Code: {{ $item->product->product_code ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right fw-bold">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary --}}
            <div class="summary-section">
                <div class="summary-card">
                    <div class="summary-row">
                        <span class="summary-label">Sub Total:</span>
                        <span class="summary-value">₹{{ number_format($sale->sub_total, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Discount:</span>
                        <span class="summary-value">- ₹{{ number_format($sale->discount, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Tax ({{ $sale->tax }}%):</span>
                        <span class="summary-value">+ ₹{{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    <div class="summary-row grand-total-row">
                        <span class="grand-total-label">Grand Total:</span>
                        <span class="grand-total-value">₹{{ number_format($sale->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="action-buttons">
                <div class="btn-group">
                    <a href="{{ route('sales.print', $sale->id) }}" target="_blank" class="btn btn-primary">
                        <span>🖨️</span>
                        Print Invoice
                    </a>
                    <a href="{{ route('sales.invoice', $sale->id) }}" target="_blank" class="btn btn-success">
                        <span>📥</span>
                        Download PDF
                    </a>
                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning">
                        <span>✏️</span>
                        Edit
                    </a>
                </div>
                <div class="btn-group">
                    <a href="{{ route('customers.sales', $sale->customer_id) }}" class="btn btn-secondary">
                        <span>⬅️</span>
                        Back to Customer History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection