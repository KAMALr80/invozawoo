<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Sales Report - {{ $company_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #1f2937;
            padding: 20px;
        }

        /* Header Section */
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }

        .company-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .report-title {
            font-size: 16px;
            font-weight: 600;
            color: #2563eb;
            margin-top: 5px;
        }

        .customer-info {
            background: #f8fafc;
            padding: 12px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #2563eb;
        }

        .customer-name {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
        }

        .customer-details {
            font-size: 9px;
            color: #64748b;
            margin-top: 5px;
        }

        .report-meta {
            color: #64748b;
            font-size: 9px;
            margin-top: 8px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .stat-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #1e293b;
        }

        .stat-value.positive {
            color: #10b981;
        }

        .stat-value.negative {
            color: #ef4444;
        }

        /* Table Styles */
        .table-container {
            margin-top: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th {
            background: #f1f5f9;
            padding: 8px 6px;
            text-align: left;
            font-weight: 600;
            color: #334155;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 8px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .invoice-link {
            color: #2563eb;
            font-weight: 600;
            text-decoration: none;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8px;
            font-weight: 600;
        }

        .status-paid {
            background: #dcfce7;
            color: #166534;
        }

        .status-partial {
            background: #fef3c7;
            color: #92400e;
        }

        .status-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-emi {
            background: #e0e7ff;
            color: #3730a3;
        }

        .amount {
            font-weight: 600;
        }

        .amount-positive {
            color: #10b981;
        }

        .amount-negative {
            color: #ef4444;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* Footer */
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 8px;
            color: #64748b;
        }

        .summary-row {
            background: #f1f5f9;
            font-weight: bold;
        }

        @media print {
            body {
                padding: 0;
            }
            tr {
                page-break-inside: avoid;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-title">{{ $company_name }}</div>
        <div class="report-title">Customer Sales Report</div>
        <div class="report-meta">
            Generated on: {{ $generated_date }} |
            Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </div>
    </div>

    <!-- Customer Info -->
    @if($customer)
    <div class="customer-info">
        <div class="customer-name">👤 {{ $customer->name }}</div>
        <div class="customer-details">
            📱 {{ $customer->mobile ?? 'N/A' }} | ✉️ {{ $customer->email ?? 'N/A' }} |
            🆔 ID: {{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-label">Total Sales</div>
            <div class="stat-value positive">₹{{ number_format($stats['total_sales'], 2) }}</div>
        </div>

        <div class="stat-box">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
        </div>

        <div class="stat-box">
            <div class="stat-label">Avg Order Value</div>
            <div class="stat-value">₹{{ number_format($stats['avg_order_value'], 2) }}</div>
        </div>

        <div class="stat-box">
            <div class="stat-label">Paid Amount</div>
            <div class="stat-value positive">₹{{ number_format($stats['paid_amount'], 2) }}</div>
        </div>

        <div class="stat-box">
            <div class="stat-label">Pending Amount</div>
            <div class="stat-value negative">₹{{ number_format($stats['pending_amount'], 2) }}</div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="table-container">
        <table>
            <thead>
                
                    <th>Invoice #</th>
                    <th>Date</th>
                    <th class="text-right">Grand Total</th>
                    <th class="text-right">Paid Amount</th>
                    <th class="text-right">Due Amount</th>
                    <th class="text-center">Status</th>
                    <th>Payment Method</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sales as $sale)
                    @php
                        $paidAmount = $sale->payments()->where('status', 'paid')->sum('amount');
                        $dueAmount = max(0, $sale->grand_total - $paidAmount);
                    @endphp
                    <tr>
                        <td>
                            <span class="invoice-link">#{{ $sale->invoice_no }}</span>
                        </td>
                        <td>{{ $sale->sale_date->format('d M Y') }}</td>
                        <td class="text-right amount">₹{{ number_format($sale->grand_total, 2) }}</td>
                        <td class="text-right amount amount-positive">₹{{ number_format($paidAmount, 2) }}</td>
                        <td class="text-right amount {{ $dueAmount > 0 ? 'amount-negative' : '' }}">
                            ₹{{ number_format($dueAmount, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $sale->payment_status }}">
                                {{ strtoupper($sale->payment_status) }}
                            </span>
                        </td>
                        <td>{{ $sale->payment_method ?? 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No sales found for the selected period
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($sales->count() > 0)
            <tfoot>
                <tr class="summary-row">
                    <td colspan="2" style="text-align: right; font-weight: bold;">Total:</td>
                    <td class="text-right amount">₹{{ number_format($stats['total_sales'], 2) }}</td>
                    <td class="text-right amount amount-positive">₹{{ number_format($stats['paid_amount'], 2) }}</td>
                    <td class="text-right amount amount-negative">₹{{ number_format($stats['pending_amount'], 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>This is a computer-generated report. No signature required.</div>
        <div>{{ $company_name }} - Customer Sales Report | Page 1 of 1</div>
    </div>
</body>
</html>
