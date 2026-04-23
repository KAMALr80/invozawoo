<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>

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

        .company-name {
            font-size: 18px;
            font-weight: bold;
        }

        .company-details {
            font-size: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .meta {
            font-size: 10px;
        }

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
            padding: 8px 6px;
            text-align: left;
            font-size: 10px;
        }

        td {
            border-bottom: 1px solid #ccc;
            padding: 8px 6px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row td {
            border-top: 2px solid #000;
            font-weight: bold;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: -30px;
            width: 100%;
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
                    <div class="title">INVENTORY REPORT</div>
                </td>

                <td class="right meta">
                    Date: {{ $generated_date }}<br>
                    Total Products: {{ $products->count() }}
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
    <div style="margin-top:10px;">

        <table>

            <thead>
                <tr>
                    <th width="15%">Code</th>
                    <th width="30%">Product</th>
                    <th width="15%">Category</th>
                    <th width="10%" class="text-right">Qty</th>
                    <th width="15%" class="text-right">Price</th>
                    <th width="15%" class="text-right">Value</th>
                </tr>
            </thead>

            <tbody>
                @forelse($products as $product)
                    @php
                        $stockValue = $product->price * $product->quantity;
                    @endphp

                    <tr>
                        <td>{{ $product->product_code }}</td>

                        <td>
                            {{ $product->name }}<br>
                            <small>{{ $product->description ?? '' }}</small>
                        </td>

                        <td>{{ $product->category ?? 'N/A' }}</td>

                        <td class="text-right">{{ number_format($product->quantity) }}</td>

                        <td class="text-right">₹{{ number_format($product->price, 2) }}</td>

                        <td class="text-right">₹{{ number_format($stockValue, 2) }}</td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="3">TOTAL</td>
                    <td class="text-right">{{ number_format($stats['total_quantity']) }}</td>
                    <td></td>
                    <td class="text-right">₹{{ number_format($stats['total_value'], 2) }}</td>
                </tr>
            </tfoot>

        </table>

        <!-- STOCK SUMMARY -->
        <table style="margin-top:10px;">
            <tr style="border-top:1px solid #000;">
                <td style="border:none;"><strong>Stock Summary:</strong></td>
                <td style="border:none;">Low: {{ $stats['low_stock_count'] }}</td>
                <td style="border:none;">Normal: {{ $stats['normal_stock_count'] }}</td>
                <td style="border:none;">High: {{ $stats['high_stock_count'] }}</td>
            </tr>
        </table>

    </div>

</body>

</html>
