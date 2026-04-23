<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <style>
        /* ================= PROFESSIONAL INVOICE STYLES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            background: #ffffff;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ================= HEADER ================= */
        .invoice-header {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .logo-section {
            flex: 0 0 150px;
        }

        .logo-img {
            max-width: 100%;
            height: auto;
            max-height: 70px;
        }

        .company-section {
            flex: 1;
            min-width: 250px;
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 5px;
            letter-spacing: -0.5px;
        }

        .company-details {
            color: #64748b;
            font-size: 11px;
            line-height: 1.6;
        }

        .gst-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #4f46e5;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            margin-top: 5px;
        }

        .status-section {
            flex: 0 0 auto;
            text-align: right;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: white;
        }

        .status-paid {
            background: #10b981;
        }

        .status-partial {
            background: #f59e0b;
        }

        .status-unpaid {
            background: #ef4444;
        }

        /* ================= TITLE ================= */
        .invoice-title {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin: 20px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ================= INVOICE DETAILS GRID ================= */
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .detail-section {
            padding: 5px;
        }

        .detail-title {
            font-size: 14px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-content {
            color: #334155;
            line-height: 1.8;
        }

        .detail-content strong {
            color: #1e293b;
            min-width: 80px;
            display: inline-block;
        }

        /* ================= TABLE STYLES ================= */
        .table-container {
            overflow-x: auto;
            margin: 25px 0;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 600px;
        }

        .items-table th {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 15px 12px;
            text-align: left;
            font-weight: 700;
            color: #1e293b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #cbd5e1;
            white-space: nowrap;
        }

        .items-table td {
            padding: 12px;
            border: 1px solid #cbd5e1;
            color: #334155;
        }

        .items-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* ================= TOTALS TABLE ================= */
        .totals-container {
            display: flex;
            justify-content: flex-end;
            margin: 20px 0;
        }

        .totals-table {
            width: 40%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 280px;
        }

        .totals-table td {
            padding: 10px 15px;
            border: 1px solid #cbd5e1;
        }

        .totals-table td:first-child {
            font-weight: 600;
            color: #475569;
            background: #f8fafc;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .grand-total {
            background: #e0e7ff !important;
            color: #4f46e5 !important;
            font-size: 16px;
            font-weight: 800 !important;
        }

        /* ================= PAYMENT HISTORY ================= */
        .payment-history {
            margin: 25px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .payment-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
        }

        .payment-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .payment-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .payment-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }

        .payment-label {
            color: #64748b;
            font-weight: 500;
        }

        .payment-value {
            font-weight: 600;
            color: #1e293b;
        }

        .payment-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .payment-status.paid {
            background: #dcfce7;
            color: #166534;
        }

        .payment-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* ================= AMOUNT IN WORDS ================= */
        .words-section {
            margin: 25px 0;
            padding: 15px 20px;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border-radius: 12px;
            border-left: 4px solid #0ea5e9;
            font-size: 14px;
            color: #0369a1;
            word-break: break-word;
        }

        .words-section strong {
            color: #075985;
        }

        /* ================= BANK DETAILS ================= */
        .bank-details {
            margin: 25px 0;
            padding: 20px;
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .bank-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .bank-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .bank-item {
            background: #f8fafc;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .bank-label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .bank-value {
            font-weight: 600;
            color: #1e293b;
        }

        /* ================= FOOTER ================= */
        .invoice-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }

        .footer-note {
            font-size: 12px;
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 5px;
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */
        
        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            body {
                padding: 15px;
            }

            .invoice-header {
                flex-direction: column;
            }

            .logo-section {
                flex: 0 0 auto;
                text-align: center;
            }

            .status-section {
                text-align: left;
            }

            .details-grid {
                grid-template-columns: 1fr;
                padding: 15px;
            }

            .totals-container {
                justify-content: flex-start;
            }

            .totals-table {
                width: 100%;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            body {
                padding: 12px;
                font-size: 11px;
            }

            .company-name {
                font-size: 20px;
            }

            .invoice-title {
                font-size: 20px;
                margin: 15px 0;
            }

            .detail-content strong {
                min-width: 60px;
            }

            .items-table {
                font-size: 11px;
            }

            .items-table th {
                padding: 10px 8px;
                font-size: 10px;
            }

            .items-table td {
                padding: 8px;
            }

            .totals-table {
                min-width: 100%;
            }

            .totals-table td {
                padding: 8px 10px;
            }

            .grand-total {
                font-size: 14px;
            }

            .payment-grid {
                grid-template-columns: 1fr;
            }

            .bank-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            body {
                padding: 10px;
            }

            .company-name {
                font-size: 18px;
            }

            .company-details {
                font-size: 10px;
            }

            .status-badge {
                padding: 6px 12px;
                font-size: 12px;
            }

            .details-grid {
                padding: 12px;
            }

            .detail-title {
                font-size: 12px;
            }

            .detail-content {
                font-size: 11px;
            }

            .items-table {
                min-width: 500px;
            }

            .items-table th,
            .items-table td {
                padding: 8px 6px;
                font-size: 10px;
            }

            .totals-table td {
                padding: 6px 8px;
                font-size: 11px;
            }

            .grand-total {
                font-size: 13px;
            }

            .payment-card {
                padding: 12px;
            }

            .payment-row {
                font-size: 11px;
            }

            .words-section {
                padding: 12px 15px;
                font-size: 12px;
            }

            .bank-details {
                padding: 15px;
            }

            .bank-item {
                padding: 8px 12px;
            }

            .bank-value {
                font-size: 12px;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            body {
                padding: 8px;
            }

            .company-name {
                font-size: 16px;
            }

            .items-table {
                min-width: 400px;
            }

            .items-table th,
            .items-table td {
                padding: 6px 4px;
                font-size: 9px;
            }

            .totals-table td {
                padding: 5px 6px;
                font-size: 10px;
            }

            .grand-total {
                font-size: 12px;
            }

            .payment-row {
                flex-direction: column;
                gap: 4px;
            }

            .words-section {
                font-size: 11px;
            }
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0.5in;
                background: white;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table th {
                background: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grand-total {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .words-section {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    @php
        $paidPayments = $sale->payments->where('status', 'paid');
    @endphp

    <!-- ================= HEADER ================= -->
    <div class="invoice-header">
        <div class="logo-section">
            <img src="{{ public_path('logo.png') }}" alt="Company Logo" class="logo-img" onerror="this.style.display='none'">
        </div>

        <div class="company-section">
            <h1 class="company-name">{{ config('app.name', 'YOUR COMPANY NAME') }}</h1>
            <div class="company-details">
                <div>123 Business Avenue, Tech Park, Mumbai - 400001</div>
                <div>📞 +91 9876543210 | ✉️ info@company.com</div>
                <span class="gst-badge">GSTIN: 27ABCDE1234F1Z5</span>
            </div>
        </div>

        <div class="status-section">
            @if ($sale->payment_status === 'paid')
                <span class="status-badge status-paid">✅ PAID</span>
            @elseif($sale->payment_status === 'partial')
                <span class="status-badge status-partial">⏳ PARTIAL</span>
            @else
                <span class="status-badge status-unpaid">❌ UNPAID</span>
            @endif
        </div>
    </div>

    <hr style="border: none; border-top: 2px solid #e5e7eb; margin: 10px 0 20px;">

    <h2 class="invoice-title">TAX INVOICE</h2>

    <!-- ================= INVOICE DETAILS ================= -->
    <div class="details-grid">
        <div class="detail-section">
            <div class="detail-title">Invoice Details</div>
            <div class="detail-content">
                <div><strong>Invoice No:</strong> {{ $sale->invoice_no }}</div>
                <div><strong>Date:</strong> {{ $sale->formatted_date }}</div>
                <div><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($sale->sale_date)->addDays(30)->format('d M Y') }}</div>
                <div><strong>Payment Mode:</strong> 
                    @if ($paidPayments->count())
                        {{ strtoupper($paidPayments->first()->method) }}
                    @else
                        Pending
                    @endif
                </div>
            </div>
        </div>

        <div class="detail-section">
            <div class="detail-title">Bill To</div>
            <div class="detail-content">
                <div><strong>Name:</strong> {{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                <div><strong>Mobile:</strong> {{ $sale->customer->mobile ?? '-' }}</div>
                <div><strong>Email:</strong> {{ $sale->customer->email ?? '-' }}</div>
                <div><strong>GSTIN:</strong> {{ $sale->customer->gst_no ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="detail-section">
            <div class="detail-title">Shipping Address</div>
            <div class="detail-content">
                <div>{{ $sale->customer->address ?? 'Same as billing address' }}</div>
                @if($sale->customer->city)
                    <div>{{ $sale->customer->city }}, {{ $sale->customer->state ?? '' }}</div>
                @endif
                @if($sale->customer->pincode)
                    <div>PIN: {{ $sale->customer->pincode }}</div>
                @endif
            </div>
        </div>
    </div>

    <!-- ================= ITEMS ================= -->
    <div class="table-container">
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%" class="text-center">#</th>
                    <th width="40%">Product</th>
                    <th width="15%" class="text-right">Rate (₹)</th>
                    <th width="10%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Amount (₹)</th>
                    <th width="15%" class="text-right">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sale->items as $i => $item)
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>
                            <div><strong>{{ $item->product->name }}</strong></div>
                            <div style="font-size: 10px; color: #64748b;">Code: {{ $item->product->product_code ?? 'N/A' }}</div>
                        </td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price * $item->quantity, 2) }}</td>
                        <td class="text-right"><strong>{{ number_format($item->total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ================= TOTALS ================= -->
    <div class="totals-container">
        <table class="totals-table">
            <tr>
                <td>Sub Total</td>
                <td>₹ {{ number_format($sale->sub_total, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td>- ₹ {{ number_format($sale->discount, 2) }}</td>
            </tr>
            <tr>
                <td>Tax ({{ $sale->tax }}%)</td>
                <td>+ ₹ {{ number_format(($sale->sub_total - $sale->discount) * $sale->tax / 100, 2) }}</td>
            </tr>
            <tr>
                <td class="grand-total">Grand Total</td>
                <td class="grand-total">₹ {{ number_format($sale->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- ================= PAYMENT HISTORY ================= -->
    @if ($sale->payments->count() > 0)
        <div class="payment-history">
            <div class="payment-title">
                <span>💳</span>
                Payment History
            </div>
            <div class="payment-grid">
                @foreach ($sale->payments as $payment)
                    <div class="payment-card">
                        <div class="payment-row">
                            <span class="payment-label">Amount:</span>
                            <span class="payment-value">₹ {{ number_format($payment->amount, 2) }}</span>
                        </div>
                        <div class="payment-row">
                            <span class="payment-label">Method:</span>
                            <span class="payment-value">{{ strtoupper($payment->method) }}</span>
                        </div>
                        <div class="payment-row">
                            <span class="payment-label">Status:</span>
                            <span class="payment-status {{ $payment->status }}">{{ strtoupper($payment->status) }}</span>
                        </div>
                        @if ($payment->transaction_id)
                            <div class="payment-row">
                                <span class="payment-label">Txn ID:</span>
                                <span class="payment-value">{{ $payment->transaction_id }}</span>
                            </div>
                        @endif
                        <div class="payment-row">
                            <span class="payment-label">Date:</span>
                            <span class="payment-value">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ================= AMOUNT IN WORDS ================= -->
    <div class="words-section">
        <strong>Amount in Words:</strong> {{ $amountInWords }}
    </div>

    <!-- ================= BANK DETAILS ================= -->
    <div class="bank-details">
        <div class="bank-title">
            <span>🏦</span>
            Bank Details
        </div>
        <div class="bank-grid">
            <div class="bank-item">
                <div class="bank-label">Bank Name</div>
                <div class="bank-value">HDFC Bank</div>
            </div>
            <div class="bank-item">
                <div class="bank-label">Account Number</div>
                <div class="bank-value">12345678901234</div>
            </div>
            <div class="bank-item">
                <div class="bank-label">IFSC Code</div>
                <div class="bank-value">HDFC0001234</div>
            </div>
            <div class="bank-item">
                <div class="bank-label">Account Name</div>
                <div class="bank-value">{{ config('app.name', 'YOUR COMPANY NAME') }}</div>
            </div>
        </div>
    </div>

    <!-- ================= FOOTER ================= -->
    <div class="invoice-footer">
        <div class="footer-note">Thank you for your business!</div>
        <div>This is a computer generated invoice. No signature is required.</div>
        <div style="margin-top: 10px;">For any queries, please contact us at support@company.com</div>
    </div>

</body>
</html>