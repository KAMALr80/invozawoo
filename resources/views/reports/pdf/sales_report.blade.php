<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Sales Report</title>

    <style>
        @page {
            margin: 120px 30px 60px 30px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #000;
        }

        /* HEADER */
        .header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
        }

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

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            text-align: center;
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
                    <div class="title">SALES REPORT</div>
                </td>

                <td class="right meta">
                    Date: {{ $generated_date }}<br>
                    Period:
                    @if (isset($startDate) && isset($endDate))
                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @else
                        All Time
                    @endif
                </td>
            </tr>
        </table>

        <div class="hr"></div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <span class="page-number"></span>
    </div>

    <!-- CONTENT -->
    <div style="margin-top: 10px;">

        <table>

            <thead>
                <tr>
                    <th width="5%">SR</th>
                    <th width="10%">Invoice</th>
                    <th width="12%">Date</th>
                    <th width="18%">Customer</th>
                    <th width="11%" class="text-right">Sub Total</th>
                    <th width="11%" class="text-right">Discount</th>
                    <th width="11%" class="text-right">Tax</th>
                    <th width="11%" class="text-right">Total</th>
                    <th width="11%" class="text-center">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($sales as $index => $sale)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>#{{ $sale->invoice_no }}</td>

                        <td>{{ $sale->sale_date->format('d M Y') }}</td>

                        <td>
                            {{ $sale->customer->name ?? 'Walk-in' }}<br>
                            <small>{{ $sale->customer->mobile ?? '' }}</small>
                        </td>

                        <td class="text-right">₹{{ number_format($sale->sub_total, 2) }}</td>
                        <td class="text-right">₹{{ number_format($sale->discount, 2) }}</td>
                        <td class="text-right">₹{{ number_format($sale->tax_amount, 2) }}</td>
                        <td class="text-right">₹{{ number_format($sale->grand_total, 2) }}</td>

                        <td class="text-center">
                            {{ ucfirst($sale->payment_status) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="4">TOTAL</td>
                    <td class="text-right">
                        ₹{{ number_format($stats['total_revenue'] + $stats['total_discount'], 2) }}
                    </td>
                    <td class="text-right">₹{{ number_format($stats['total_discount'], 2) }}</td>
                    <td class="text-right">₹{{ number_format($stats['total_tax'], 2) }}</td>
                    <td class="text-right">₹{{ number_format($stats['total_revenue'], 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>

        </table>
        <!-- STATUS SUMMARY -->
        <table style="margin-top:15px; width:100%; border-collapse: collapse;">
            <tr style="border-top:1px solid #000;">
                <td style="border:none; padding:6px;">
                    <strong>Status Summary:</strong>
                </td>

                <td style="border:none; padding:6px;">
                    Paid: {{ $stats['paid_count'] ?? 0 }}
                </td>

                <td style="border:none; padding:6px;">
                    Partial: {{ $stats['partial_count'] ?? 0 }}
                </td>

                <td style="border:none; padding:6px;">
                    Unpaid: {{ $stats['unpaid_count'] ?? 0 }}
                </td>

                <td style="border:none; padding:6px;">
                    Total: {{ $stats['total_orders'] ?? $sales->count() }}
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
