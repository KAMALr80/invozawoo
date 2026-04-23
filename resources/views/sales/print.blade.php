<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $sale->invoice_no }}</title>
    <style>
        /* ================= MODERN INVOICE DESIGN SYSTEM ================= */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .invoice-wrapper {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        /* ================= MAIN INVOICE CARD ================= */
        .invoice-card {
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ================= INVOICE HEADER ================= */
        .invoice-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            padding: 35px 40px;
            color: white;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .company-info h1 {
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 5px;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .company-info p {
            color: #94a3b8;
            font-size: 14px;
        }

        .invoice-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 15px 25px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .invoice-badge small {
            color: #94a3b8;
            font-size: 12px;
            display: block;
            margin-bottom: 5px;
        }

        .invoice-badge .invoice-number {
            font-size: 28px;
            font-weight: 800;
            color: white;
            letter-spacing: 1px;
        }

        .company-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .detail-text {
            flex: 1;
        }

        .detail-text .label {
            color: #94a3b8;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 2px;
        }

        .detail-text .value {
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        /* ================= STATUS BAR ================= */
        .status-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            flex-wrap: wrap;
            gap: 20px;
        }

        .status-group {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .status-badge-modern {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .status-badge-modern.paid {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .status-badge-modern.partial {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .status-badge-modern.unpaid {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .date-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: white;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .date-chip strong {
            color: #1e293b;
            margin-left: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            padding: 10px 20px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn:hover {
            background: #1e293b;
            color: white;
            border-color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        /* ================= DETAILS CARDS ================= */
        .details-section {
            padding: 30px 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            background: white;
        }

        .info-card {
            background: #f8fafc;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e1;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .card-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            width: 80px;
            color: #64748b;
            font-weight: 500;
        }

        .info-value {
            flex: 1;
            font-weight: 600;
            color: #1e293b;
        }

        /* ================= ITEMS TABLE ================= */
        .items-section {
            padding: 0 40px 30px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .table-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .items-table th {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e1;
        }

        .items-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        .items-table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .product-name {
            font-weight: 700;
            color: #1e293b;
            font-size: 15px;
        }

        .product-sku {
            font-size: 11px;
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 10px;
            border-radius: 50px;
            display: inline-block;
            width: fit-content;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: 700;
        }

        /* ================= TOTALS SECTION ================= */
        .totals-section {
            padding: 30px 40px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
        }

        .totals-card {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .total-row:last-child {
            border-bottom: none;
        }

        .total-label {
            color: #64748b;
            font-size: 14px;
        }

        .total-value {
            font-weight: 600;
            color: #1e293b;
        }

        .grand-total-row {
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #1e293b;
        }

        .grand-total-label {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .grand-total-value {
            font-size: 24px;
            font-weight: 800;
            color: #3b82f6;
        }

        .due-amount-row {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            margin-top: 10px;
            padding: 15px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .due-amount-label {
            font-weight: 700;
            color: #991b1b;
            font-size: 16px;
        }

        .due-amount-value {
            font-weight: 800;
            color: #b91c1c;
            font-size: 20px;
        }

        /* ================= PAYMENT SECTION ================= */
        .payment-section {
            padding: 0 40px 30px;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .payment-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .payment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .payment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .payment-method {
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            font-size: 14px;
        }

        .payment-status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .payment-status-badge.paid {
            background: #d1fae5;
            color: #065f46;
        }

        .payment-status-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .payment-amount {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 15px;
        }

        .payment-details {
            display: grid;
            gap: 10px;
        }

        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 5px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .payment-detail-row:last-child {
            border-bottom: none;
        }

        .payment-detail-label {
            color: #64748b;
        }

        .payment-detail-value {
            font-weight: 600;
            color: #1e293b;
        }

        /* ================= AMOUNT IN WORDS ================= */
        .words-section {
            margin: 0 40px 30px;
            padding: 20px 25px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-radius: 16px;
            border-left: 6px solid #3b82f6;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .words-icon {
            width: 48px;
            height: 48px;
            background: #3b82f6;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .words-content {
            flex: 1;
            font-size: 15px;
            color: #1e3a8a;
            font-weight: 500;
            line-height: 1.6;
        }

        .words-content strong {
            color: #1e293b;
            margin-right: 10px;
        }

        /* ================= BANK DETAILS ================= */
        .bank-section {
            padding: 0 40px 30px;
        }

        .bank-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .bank-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .bank-card:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .bank-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
        }

        .bank-value {
            font-weight: 700;
            color: #1e293b;
            font-size: 14px;
        }

        /* ================= FOOTER ================= */
        .invoice-footer {
            padding: 30px 40px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border-top: 1px solid #cbd5e1;
            text-align: center;
        }

        .footer-main {
            font-size: 16px;
            color: #1e293b;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .footer-text {
            font-size: 13px;
            color: #64748b;
        }

        .footer-small {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 8px;
        }

        /* ================= PRINT BUTTON ================= */
        .print-button-container {
            padding: 20px 40px 30px;
            text-align: center;
        }

        .print-btn {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border: none;
            padding: 16px 45px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .print-btn:active {
            transform: translateY(0);
        }

        /* ================= RESPONSIVE ================= */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .invoice-header {
                padding: 25px;
            }

            .header-top {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .status-bar {
                flex-direction: column;
                padding: 20px;
            }

            .status-group {
                justify-content: center;
            }

            .details-section {
                padding: 20px;
            }

            .items-section {
                padding: 0 20px 20px;
            }

            .items-table {
                font-size: 12px;
            }

            .items-table th,
            .items-table td {
                padding: 12px 10px;
            }

            .totals-section {
                padding: 20px;
            }

            .totals-card {
                max-width: 100%;
            }

            .payment-section {
                padding: 0 20px 20px;
            }

            .bank-section {
                padding: 0 20px 20px;
            }

            .words-section {
                margin: 0 20px 20px;
            }

            .print-button-container {
                padding: 15px 20px 20px;
            }
        }

        @media (max-width: 480px) {
            .items-table {
                font-size: 11px;
            }

            .items-table th,
            .items-table td {
                padding: 8px 6px;
            }

            .product-name {
                font-size: 12px;
            }

            .grand-total-value {
                font-size: 20px;
            }

            .due-amount-value {
                font-size: 18px;
            }

            .payment-grid {
                grid-template-columns: 1fr;
            }

            .bank-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ================= PRINT STYLES ================= */
        @media print {
            body {
                background: white;
                padding: 0.2in;
            }

            .invoice-card {
                box-shadow: none;
                border: 1px solid #e2e8f0;
            }

            .print-button-container,
            .action-buttons,
            .action-btn {
                display: none !important;
            }

            .status-badge-modern {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .due-amount-row {
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
        $totalPaid = $paidPayments->sum('amount');
        $balanceDue = max(0, $sale->grand_total - $totalPaid);

        // Status determination
        $isPaid = $sale->payment_status === 'paid' || $balanceDue <= 0;
        $isPartial = $sale->payment_status === 'partial' || ($balanceDue > 0 && $totalPaid > 0);
        $isUnpaid = $sale->payment_status === 'unpaid' || ($balanceDue > 0 && $totalPaid == 0);

        // ========== ADVANCED NUMBER TO WORDS FUNCTION ==========
        function numberToWords($num)
        {
            $ones = [
                0 => 'Zero',
                1 => 'One',
                2 => 'Two',
                3 => 'Three',
                4 => 'Four',
                5 => 'Five',
                6 => 'Six',
                7 => 'Seven',
                8 => 'Eight',
                9 => 'Nine',
                10 => 'Ten',
                11 => 'Eleven',
                12 => 'Twelve',
                13 => 'Thirteen',
                14 => 'Fourteen',
                15 => 'Fifteen',
                16 => 'Sixteen',
                17 => 'Seventeen',
                18 => 'Eighteen',
                19 => 'Nineteen',
                20 => 'Twenty',
                30 => 'Thirty',
                40 => 'Forty',
                50 => 'Fifty',
                60 => 'Sixty',
                70 => 'Seventy',
                80 => 'Eighty',
                90 => 'Ninety',
            ];

            if ($num < 20) {
                return $ones[$num];
            }
            if ($num < 100) {
                return $ones[floor($num / 10) * 10] . ($num % 10 > 0 ? ' ' . $ones[$num % 10] : '');
            }
            if ($num < 1000) {
                return $ones[floor($num / 100)] . ' Hundred' . ($num % 100 > 0 ? ' ' . numberToWords($num % 100) : '');
            }
            if ($num < 100000) {
                return numberToWords(floor($num / 1000)) .
                    ' Thousand' .
                    ($num % 1000 > 0 ? ' ' . numberToWords($num % 1000) : '');
            }
            if ($num < 10000000) {
                return numberToWords(floor($num / 100000)) .
                    ' Lakh' .
                    ($num % 100000 > 0 ? ' ' . numberToWords($num % 100000) : '');
            }
            return numberToWords(floor($num / 10000000)) .
                ' Crore' .
                ($num % 10000000 > 0 ? ' ' . numberToWords($num % 10000000) : '');
        }

        $amountInWords = numberToWords(floor($sale->grand_total)) . ' Rupees';
        if ($sale->grand_total - floor($sale->grand_total) > 0) {
            $paise = round(($sale->grand_total - floor($sale->grand_total)) * 100);
            $amountInWords .= ' and ' . numberToWords($paise) . ' Paise';
        }
        $amountInWords .= ' Only';
    @endphp

    <div class="invoice-wrapper">
        <div class="invoice-card">
            <!-- ================= HEADER ================= -->
            <div class="invoice-header">
                <div class="header-top">
                    <div class="company-info">
                        <h1>INVOZA-ONE</h1>
                        <p>Premium Business Solutions</p>
                    </div>
                    <div class="invoice-badge">
                        <small>INVOICE NUMBER</small>
                        <div class="invoice-number">#{{ $sale->invoice_no }}</div>
                    </div>
                </div>

                <div class="company-details-grid">
                    <div class="detail-item">
                        <div class="detail-icon">🏢</div>
                        <div class="detail-text">
                            <span class="label">Office Address</span>
                            <span class="value">K-110, Basement, Hauz Khas Enclave, New Delhi - 110016</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">📞</div>
                        <div class="detail-text">
                            <span class="label">Contact</span>
                            <span class="value">+91 98765 43210 | invoza@company.com</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-icon">🏷️</div>
                        <div class="detail-text">
                            <span class="label">GSTIN</span>
                            <span class="value">24ABCDE1234F1Z5</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= STATUS BAR ================= -->
            <div class="status-bar">
                <div class="status-group">
                    @if ($isPaid)
                        <span class="status-badge-modern paid">
                            <span>✓</span> PAID
                        </span>
                    @elseif($isPartial)
                        <span class="status-badge-modern partial">
                            <span>⏳</span> PARTIAL
                        </span>
                    @else
                        <span class="status-badge-modern unpaid">
                            <span>⚠</span> UNPAID
                        </span>
                    @endif

                    <div class="date-chip">
                        <span>📅</span>
                        <span>Invoice Date:
                            <strong>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</strong></span>
                    </div>

                    <div class="date-chip">
                        <span>⏰</span>
                        <span>Due Date:
                            <strong>{{ \Carbon\Carbon::parse($sale->sale_date)->addDays(30)->format('d M Y') }}</strong></span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="{{ route('sales.print', $sale->id) }}" class="action-btn" target="_blank">
                        <span>🖨️</span> Print
                    </a>
                    <button class="action-btn" onclick="copyInvoiceNumber()">
                        <span>📋</span> Copy Number
                    </button>
                </div>
            </div>

            <!-- ================= DETAILS CARDS ================= -->
            <div class="details-section">
                <!-- Invoice Details Card -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon">📄</div>
                        <div>
                            <h3 class="card-title">Invoice Details</h3>
                            <p class="card-subtitle">Transaction information</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Number:</span>
                        <span class="info-value">{{ $sale->invoice_no }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">{{ $sale->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Order Ref:</span>
                        <span class="info-value">{{ $sale->order_no ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Payment:</span>
                        <span class="info-value">
                            @if ($paidPayments->count())
                                {{ strtoupper($paidPayments->first()->method) }}
                            @else
                                Not Paid
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Customer Details Card -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon">👤</div>
                        <div>
                            <h3 class="card-title">Bill To</h3>
                            <p class="card-subtitle">Customer information</p>
                        </div>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $sale->customer->name ?? 'Walk-in Customer' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mobile:</span>
                        <span class="info-value">{{ $sale->customer->mobile ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $sale->customer->email ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">GSTIN:</span>
                        <span class="info-value">{{ $sale->customer->gst_no ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Shipping Details Card -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="card-icon">📦</div>
                        <div>
                            <h3 class="card-title">Shipping Address</h3>
                            <p class="card-subtitle">Delivery location</p>
                        </div>
                    </div>
                    @if ($sale->requires_shipping && $sale->full_shipping_address)
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $sale->full_shipping_address }}</span>
                        </div>
                        @if ($sale->city || $sale->state)
                            <div class="info-row">
                                <span class="info-label">City/State:</span>
                                <span
                                    class="info-value">{{ $sale->city }}{{ $sale->city && $sale->state ? ', ' : '' }}{{ $sale->state }}</span>
                            </div>
                        @endif
                        @if ($sale->pincode)
                            <div class="info-row">
                                <span class="info-label">Pincode:</span>
                                <span class="info-value">{{ $sale->pincode }}</span>
                            </div>
                        @endif
                        @if ($sale->receiver_name)
                            <div class="info-row">
                                <span class="info-label">Receiver:</span>
                                <span class="info-value">{{ $sale->receiver_name }}</span>
                            </div>
                        @endif
                    @else
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $sale->customer->address ?? 'Same as billing address' }}</span>
                        </div>
                        @if ($sale->customer->city)
                            <div class="info-row">
                                <span class="info-label">City:</span>
                                <span class="info-value">{{ $sale->customer->city }}</span>
                            </div>
                        @endif
                        @if ($sale->customer->pincode)
                            <div class="info-row">
                                <span class="info-label">PIN:</span>
                                <span class="info-value">{{ $sale->customer->pincode }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- ================= ITEMS TABLE ================= -->
            <div class="items-section">
                <div class="section-header">
                    <div class="section-icon">🛒</div>
                    <h3 class="section-title">Order Items</h3>
                </div>

                <div class="table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">Product Description</th>
                                <th width="10%" class="text-right">Unit Price</th>
                                <th width="8%" class="text-center">Quantity</th>
                                <th width="12%" class="text-right">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $i => $item)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-name">{{ $item->product->name ?? 'Product' }}</span>
                                            @if ($item->product && $item->product->product_code)
                                                <span class="product-sku">SKU:
                                                    {{ $item->product->product_code }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-right">₹ {{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-right fw-bold">₹ {{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================= TOTALS SECTION ================= -->
            <div class="totals-section">
                <div class="totals-card">
                    <div class="total-row">
                        <span class="total-label">Sub Total:</span>
                        <span class="total-value">₹ {{ number_format($sale->sub_total, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Discount:</span>
                        <span class="total-value">- ₹ {{ number_format($sale->discount, 2) }}</span>
                    </div>
                    <div class="total-row">
                        <span class="total-label">Tax ({{ $sale->tax }}%):</span>
                        <span class="total-value">+ ₹ {{ number_format($sale->tax_amount, 2) }}</span>
                    </div>
                    @if ($sale->shipping_charge > 0)
                        <div class="total-row">
                            <span class="total-label">Shipping:</span>
                            <span class="total-value">+ ₹ {{ number_format($sale->shipping_charge, 2) }}</span>
                        </div>
                    @endif
                    <div class="total-row grand-total-row">
                        <span class="grand-total-label">Grand Total:</span>
                        <span class="grand-total-value">₹ {{ number_format($sale->grand_total, 2) }}</span>
                    </div>

                    <!-- Due Amount Section -->
                    @if (!$isPaid && $balanceDue > 0)
                        <div class="due-amount-row">
                            <span class="due-amount-label">Balance Due:</span>
                            <span class="due-amount-value">₹ {{ number_format($balanceDue, 2) }}</span>
                        </div>
                    @endif

                    <!-- Paid Amount Info -->
                    @if ($totalPaid > 0)
                        <div style="margin-top: 10px; padding: 10px; background: #f1f5f9; border-radius: 10px;">
                            <div style="display: flex; justify-content: space-between; color: #475569;">
                                <span>Paid Amount:</span>
                                <span class="fw-bold">₹ {{ number_format($totalPaid, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ================= PAYMENT HISTORY ================= -->
            @if ($sale->payments->count() > 0)
                <div class="payment-section">
                    <div class="section-header">
                        <div class="section-icon">💳</div>
                        <h3 class="section-title">Payment History</h3>
                    </div>

                    <div class="payment-grid">
                        @foreach ($sale->payments as $payment)
                            <div class="payment-card">
                                <div class="payment-header">
                                    <span class="payment-method">{{ strtoupper($payment->method) }}</span>
                                    <span class="payment-status-badge {{ $payment->status }}">
                                        {{ $payment->status }}
                                    </span>
                                </div>
                                <div class="payment-amount">₹ {{ number_format($payment->amount, 2) }}</div>
                                <div class="payment-details">
                                    <div class="payment-detail-row">
                                        <span class="payment-detail-label">Transaction ID:</span>
                                        <span
                                            class="payment-detail-value">{{ $payment->transaction_id ?? 'N/A' }}</span>
                                    </div>
                                    <div class="payment-detail-row">
                                        <span class="payment-detail-label">Date:</span>
                                        <span
                                            class="payment-detail-value">{{ $payment->created_at->format('d M Y, h:i A') }}</span>
                                    </div>
                                    @if ($payment->remarks)
                                        <div class="payment-detail-row">
                                            <span class="payment-detail-label">Remarks:</span>
                                            <span class="payment-detail-value">{{ $payment->remarks }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ================= AMOUNT IN WORDS ================= -->
            <div class="words-section">
                <div class="words-icon">📝</div>
                <div class="words-content">
                    <strong>Amount in Words:</strong> {{ $amountInWords }}
                </div>
            </div>

            <!-- ================= BANK DETAILS ================= -->
            <div class="bank-section">
                <div class="section-header">
                    <div class="section-icon">🏦</div>
                    <h3 class="section-title">Bank Details</h3>
                </div>

                <div class="bank-grid">
                    <div class="bank-card">
                        <div class="bank-label">Bank Name</div>
                        <div class="bank-value">HDFC Bank Ltd.</div>
                    </div>
                    <div class="bank-card">
                        <div class="bank-label">Account Number</div>
                        <div class="bank-value">12345678901234</div>
                    </div>
                    <div class="bank-card">
                        <div class="bank-label">IFSC Code</div>
                        <div class="bank-value">HDFC0001234</div>
                    </div>
                    <div class="bank-card">
                        <div class="bank-label">Account Name</div>
                        <div class="bank-value">INVOZA-ONE</div>
                    </div>
                    <div class="bank-card">
                        <div class="bank-label">Branch</div>
                        <div class="bank-value">Hauz Khas, New Delhi</div>
                    </div>
                    <div class="bank-card">
                        <div class="bank-label">Account Type</div>
                        <div class="bank-value">Current Account</div>
                    </div>
                </div>
            </div>

            <!-- ================= FOOTER ================= -->
            <div class="invoice-footer">
                <div class="footer-main">Thank you for your business!</div>
                <div class="footer-text">This is a computer generated invoice - no signature required</div>
                <div class="footer-small">
                    Invoice #{{ $sale->invoice_no }} | Generated on {{ now()->format('d M Y, h:i A') }}
                </div>
            </div>

            <!-- ================= PRINT BUTTON ================= -->
            <div class="print-button-container">
                <a href="{{ route('sales.print', $sale->id) }}" class="print-btn" target="_blank">
                    <span>🖨️</span>
                    Print / Download Invoice
                </a>
            </div>
        </div>
    </div>

    <script>
        function copyInvoiceNumber() {
            navigator.clipboard.writeText('{{ $sale->invoice_no }}')
                .then(() => {
                    alert('✅ Invoice number copied to clipboard!');
                })
                .catch(() => {
                    alert('❌ Failed to copy invoice number');
                });
        }

        // Keyboard shortcut for print
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>

</html>
