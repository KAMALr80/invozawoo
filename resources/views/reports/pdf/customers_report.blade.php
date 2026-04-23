<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Customer Statement</title>

    <style>
        @page {
            margin: 120px 30px 60px 30px;
        }

        /* BODY */
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        /* HEADER (REPEAT ON EVERY PAGE) */
        .header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
        }

        /* HEADER LAYOUT */
        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .left {
            width: 33%;
        }

        .center {
            width: 34%;
            text-align: center;
        }

        .right {
            width: 33%;
            text-align: right;
        }

        /* COMPANY */
        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .company-details {
            font-size: 10px;
            margin-top: 5px;
        }

        /* TITLE */
        .title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* META */
        .meta {
            font-size: 10px;
        }

        /* LINE */
        .hr {
            border-top: 1px solid #000;
            margin-top: 10px;
        }

        /* CONTENT */
        .content {
            margin-top: 10px;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border-bottom: 2px solid #000;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            border-bottom: 1px solid #ccc;
            padding: 10px 8px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* TOTAL */
        .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
        }

        /* FOOTER PAGE NUMBER */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="left">
                    <div class="company-name">Invoza-one</div>
                    <div class="company-details">
                        Anand, Gujarat<br>
                        +91 9724956858
                    </div>
                </td>

                <td class="center">
                    <div class="title">CUSTOMER STATEMENT</div>
                </td>

                <td class="right meta">
                    Date: {{ $generated_date }}<br>
                    Status: {{ $filters['status'] ?? 'All' }}
                </td>
            </tr>
        </table>

        <div class="hr"></div>
    </div>

    <!-- FOOTER PAGE NUMBER -->
    <div class="footer">
        <span class="page-number"></span>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <table>

            <thead>
                <tr>
                    <th width="5%">SR</th>
                    <th width="25%">Customer</th>
                    <th width="25%">Contact</th>
                    <th class="text-right">Sales</th>
                    <th class="text-right">Paid</th>
                    <th class="text-right">Due</th>
                    <th class="text-right">Wallet</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($customers as $index => $customer)
                    @php
                        $totalSales = $customer->sales()->sum('grand_total');
                        $totalPaid = $customer->payments()->where('status', 'paid')->sum('amount');
                        $totalDue = max(0, $totalSales - $totalPaid);
                        $walletBalance = $customer->getCurrentWalletBalanceAttribute();
                    @endphp

                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>
                            {{ $customer->name }}<br>
                            <small>ID: {{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</small>
                        </td>

                        <td>
                            {{ $customer->mobile }}<br>
                            <small>{{ $customer->email }}</small>
                        </td>

                        <td class="text-right">₹{{ number_format($totalSales, 2) }}</td>
                        <td class="text-right">₹{{ number_format($totalPaid, 2) }}</td>
                        <td class="text-right">₹{{ number_format($totalDue, 2) }}</td>
                        <td class="text-right">₹{{ number_format($walletBalance, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="3">TOTAL</td>
                    <td class="text-right">₹{{ number_format($stats['total_sales'], 2) }}</td>
                    <td class="text-right">₹{{ number_format($stats['total_paid'], 2) }}</td>
                    <td class="text-right">₹{{ number_format($stats['total_due'], 2) }}</td>
                    <td class="text-right">₹{{ number_format($stats['total_wallet_balance'], 2) }}</td>
                </tr>
            </tfoot>

        </table>

    </div>

</body>

</html>
