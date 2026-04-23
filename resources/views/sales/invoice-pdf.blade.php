<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->invoice_no }}</title>
    <style>
        /* ================= PROFESSIONAL INVOICE STYLES ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            background: #fff;
            padding: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        /* ================= HEADER ================= */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .logo-cell {
            width: 20%;
            vertical-align: top;
        }

        .logo-img {
            height: 70px;
            max-width: 150px;
        }

        .company-cell {
            width: 55%;
            text-align: center;
        }

        .company-name {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin: 0 0 5px;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }

        .gst-badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin-top: 5px;
        }

        .status-cell {
            width: 25%;
            text-align: right;
            vertical-align: top;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 700;
            color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .status-paid {
            background: #16a34a;
        }

        .status-partial {
            background: #f97316;
        }

        .status-unpaid {
            background: #dc2626;
        }

        hr {
            border: none;
            border-top: 2px solid #000;
            margin: 15px 0;
        }

        .light-hr {
            border-top: 1px dashed #ccc;
            margin: 15px 0;
        }

        .invoice-title {
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin: 15px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
        }

        /* ================= DETAILS TABLE ================= */
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .details-table td {
            padding: 10px 12px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .details-table .label {
            font-weight: 700;
            background: #f2f2f2;
            width: 120px;
        }

        /* ================= ITEMS TABLE ================= */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .items-table th {
            background: #1e293b;
            color: white;
            padding: 12px 10px;
            border: 1px solid #334155;
            font-weight: 600;
            text-align: left;
            font-size: 12px;
        }

        .items-table td {
            padding: 10px;
            border: 1px solid #000;
        }

        .items-table tbody tr:hover {
            background: #f8fafc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* ================= TOTALS TABLE ================= */
        .totals-table {
            width: 40%;
            border-collapse: collapse;
            margin: 20px 0;
            float: right;
        }

        .totals-table td {
            padding: 10px 15px;
            border: 1px solid #000;
        }

        .totals-table td:first-child {
            font-weight: 600;
            background: #f2f2f2;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .grand-total {
            background: #1e293b !important;
            color: white !important;
            font-size: 16px;
        }

        .grand-total td {
            background: #1e293b;
            color: white;
        }

        /* ================= DUE AMOUNT STYLES ================= */
        .due-row {
            background: #fee2e2 !important;
            color: #dc2626 !important;
            font-weight: 700;
        }

        .due-row td {
            background: #fee2e2;
            color: #dc2626;
        }

        .paid-row {
            display: none;
        }

        .clear {
            clear: both;
        }

        /* ================= PAYMENT HISTORY ================= */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .payment-table td {
            padding: 15px;
            border: 1px solid #000;
            background: #f8fafc;
        }

        .payment-item {
            padding: 5px 0;
            border-bottom: 1px dashed #ccc;
        }

        .payment-item:last-child {
            border-bottom: none;
        }

        /* ================= BANK TABLE ================= */
        .bank-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .bank-table td {
            padding: 15px;
            border: 1px solid #000;
            background: #f8fafc;
        }

        /* ================= FOOTER ================= */
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px dashed #ccc;
            font-size: 11px;
            color: #666;
        }

        .footer strong {
            color: #333;
        }

        /* ================= AMOUNT WORDS ================= */
        .words-box {
            margin: 20px 0;
            padding: 12px 15px;
            background: #f8fafc;
            border-left: 4px solid #1e293b;
            font-weight: 500;
            border-radius: 4px;
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .header-table tr {
                display: block;
            }

            .header-table td {
                display: block;
                width: 100%;
                text-align: center;
            }

            .company-cell {
                text-align: center;
            }

            .status-cell {
                text-align: center;
                margin-top: 10px;
            }

            .totals-table {
                width: 100%;
                float: none;
            }
        }

        @media print {
            body {
                padding: 0.2in;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table th {
                background: #1e293b !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .grand-total {
                background: #1e293b !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .due-row {
                background: #fee2e2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    @php
        $paidPayments = $sale->payments->where('status', 'paid');
        $totalPaid = $paidPayments->sum('amount');
        $balanceDue = $sale->grand_total - $totalPaid;

        // Status flags
        $isPaid = ($sale->payment_status === 'paid' || $balanceDue <= 0);
        $isPartial = ($sale->payment_status === 'partial' || ($balanceDue > 0 && $totalPaid > 0));
        $isUnpaid = ($sale->payment_status === 'unpaid' || ($balanceDue == $sale->grand_total));

        // ========== AMOUNT IN WORDS FUNCTION ==========
        function numberToWords($num) {
            $ones = [
                0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
                6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
                11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
                15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
                19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 50 => 'Fifty',
                60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
            ];

            if ($num < 20) return $ones[$num];
            if ($num < 100) return $ones[floor($num/10)*10] . ($num%10 > 0 ? ' ' . $ones[$num%10] : '');
            if ($num < 1000) return $ones[floor($num/100)] . ' Hundred' . ($num%100 > 0 ? ' ' . numberToWords($num%100) : '');
            if ($num < 100000) return numberToWords(floor($num/1000)) . ' Thousand' . ($num%1000 > 0 ? ' ' . numberToWords($num%1000) : '');
            if ($num < 10000000) return numberToWords(floor($num/100000)) . ' Lakh' . ($num%100000 > 0 ? ' ' . numberToWords($num%100000) : '');
            return numberToWords(floor($num/10000000)) . ' Crore' . ($num%10000000 > 0 ? ' ' . numberToWords($num%10000000) : '');
        }

        $amountInWords = numberToWords(floor($sale->grand_total)) . ' Rupees';
        if (($sale->grand_total - floor($sale->grand_total)) > 0) {
            $paise = round(($sale->grand_total - floor($sale->grand_total)) * 100);
            $amountInWords .= ' and ' . numberToWords($paise) . ' Paise';
        }
        $amountInWords .= ' Only';
    @endphp

    <!-- ================= HEADER ================= -->
    <table class="header-table">
        <tr>
            <td class="company-cell">
                <h2 class="company-name">Invoza-one</h2>
                <div class="company-details">
                    K-110, Basement, Hauz Khas Enclave, New Delhi - 110016<br>
                    📞 +91 98765 43210 | ✉️ invoza@company.com<br>
                    <span class="gst-badge">GSTIN: 24ABCDE1234F1Z5</span>
                </div>
            </td>
            <td class="status-cell">
                @if ($isPaid)
                    <span class="status-badge status-paid">✓ PAID</span>
                @elseif($isPartial)
                    <span class="status-badge status-partial">⏳ PARTIAL</span>
                @else
                    <span class="status-badge status-unpaid">⚠ UNPAID</span>
                @endif
            </td>
        </tr>
    </table>

    <hr>

    <h3 class="invoice-title">TAX INVOICE</h3>

    <!-- ================= INVOICE DETAILS ================= -->
    <table class="details-table">
        <tr>
            <td class="label" width="20%">Invoice No:</td>
            <td width="30%"><strong>{{ $sale->invoice_no }}</strong></td>
            <td class="label" width="20%">Invoice Date:</td>
            <td width="30%"><strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d.m.Y') }}</strong></td>
        </tr>
        <tr>
            <td class="label">Payment Mode:</td>
            <td>
                <strong>
                    @if ($paidPayments->count())
                        {{ strtoupper($paidPayments->first()->method) }}
                    @else
                        Pending
                    @endif
                </strong>
            </td>
            <td class="label">Order Ref:</td>
            <td><strong>{{ $sale->order_no ?? 'N/A' }}</strong></td>
        </tr>
    </table>

    <!-- ================= BILL TO ================= -->
    <table class="details-table">
        <tr>
            <td class="label" width="20%">Bill To:</td>
            <td colspan="3">
                <strong>{{ $sale->customer->name ?? 'Walk-in Customer' }}</strong><br>
                Mob No:- {{ $sale->customer->mobile ?? 'N/A' }} | Email:- {{ $sale->customer->email ?? 'N/A' }}<br>
                GST:- {{ $sale->customer->gst_no ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <!-- ================= ITEMS ================= -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Product Name</th>
                <th width="10%" class="text-right">Rate </th>
                <th width="8%" class="text-center">Qty</th>
                <th width="12%" class="text-right">Amount </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->items as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name ?? 'Product' }}</strong>
                        @if($item->product->sku ?? false)
                            <br><small style="color: #666;">SKU: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">₹{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ================= TOTALS ================= -->
    <table class="totals-table">
        <tr>
            <td>Sub Total</td>
            <td><strong>₹ {{ number_format($sale->sub_total, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Discount</td>
            <td><strong>₹ {{ number_format($sale->discount, 2) }}</strong></td>
        </tr>
        <tr>
            <td>Tax ({{ $sale->tax }}%)</td>
            <td><strong>₹ {{ number_format($sale->tax_amount, 2) }}</strong></td>
        </tr>
        @if($sale->shipping_charge > 0)
        <tr>
            <td>Shipping</td>
            <td><strong>+ ₹ {{ number_format($sale->shipping_charge, 2) }}</strong></td>
        </tr>
        @endif

        <!-- Grand Total Row -->
        <tr class="grand-total">
            <td><strong>GRAND TOTAL</strong></td>
            <td><strong>₹ {{ number_format($sale->grand_total, 2) }}</strong></td>
        </tr>

        <!-- DUE AMOUNT ROW - Only shown for Partial/Unpaid Invoices -->
        @if(!$isPaid && $balanceDue > 0)
        <tr class="due-row">
            <td><strong>DUE AMOUNT</strong></td>
            <td><strong>₹ {{ number_format($balanceDue, 2) }}</strong></td>
        </tr>
        @endif

        <!-- PAID AMOUNT ROW - Only shown for Partial/Paid Invoices (optional) -->
        @if($totalPaid > 0 && !$isPaid)
        <tr>
            <td>Paid Amount</td>
            <td>₹ {{ number_format($totalPaid, 2) }}</td>
        </tr>
        @endif
    </table>

    <div class="clear"></div>

    <!-- ================= AMOUNT IN WORDS ================= -->
    <div class="words-box">
        <strong>Amount in Words:</strong> {{ $amountInWords }}
    </div>

    <!-- ================= PAYMENT HISTORY ================= (Only if payments exist) -->
    @if ($sale->payments->count())
        <table class="payment-table">
            <tr>
                <td>
                    <strong>📋 Payment History</strong><br><br>
                    @foreach ($sale->payments as $p)
                        <div class="payment-item">
                            ₹ {{ number_format($p->amount, 2) }} |
                            {{ strtoupper($p->method) }} |
                            <span style="color: {{ $p->status == 'paid' ? '#16a34a' : '#dc2626' }};">{{ strtoupper($p->status) }}</span>
                            @if ($p->transaction_id)
                                | Txn: {{ $p->transaction_id }}
                            @endif
                            | {{ $p->created_at->format('d M Y') }}
                        </div>
                    @endforeach

                    @if(!$isPaid && $balanceDue > 0)
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #000;">
                            <strong>Balance Due:</strong> ₹ {{ number_format($balanceDue, 2) }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    @elseif(!$isPaid)
        <!-- If no payments and invoice is unpaid -->
        <table class="payment-table">
            <tr>
                <td>
                    <strong>📋 Payment Status</strong><br><br>
                    <span style="color: #dc2626; font-weight: 700;">No payments received yet</span><br>
                    <strong>Total Due:</strong> ₹ {{ number_format($balanceDue, 2) }}
                </td>
            </tr>
        </table>
    @endif

    <!-- ================= BANK DETAILS ================= -->
    <table class="bank-table">
        <tr>
            <td>
                <strong>🏦 Bank Details</strong><br><br>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 120px; font-weight: 600;">Bank Name:</td>
                        <td>HDFC Bank Ltd.</td>
                        <td style="width: 100px; font-weight: 600;">IFSC Code:</td>
                        <td>HDFC0001234</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Account Name:</td>
                        <td>INVOZA-ONE</td>
                        <td style="font-weight: 600;">Account No:</td>
                        <td>12345678901234</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Branch:</td>
                        <td>Hauz Khas, New Delhi</td>
                        <td style="font-weight: 600;">Account Type:</td>
                        <td>Current Account</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ================= FOOTER ================= -->
    <div class="footer">
        <strong>Thank you for your business!</strong><br>
        This is a computer generated invoice - no signature required<br>
        <span style="font-size: 10px;">Invoice #{{ $sale->invoice_no }} | Generated on {{ now()->format('d M Y') }}</span>
    </div>

</body>

</html>
