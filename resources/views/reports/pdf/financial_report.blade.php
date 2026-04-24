<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Financial Summary Report</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 28px; font-weight: bold; color: #1a56db; margin: 0; }
        .report-title { font-size: 18px; color: #666; margin: 5px 0 0 0; text-transform: uppercase; letter-spacing: 2px; }
        
        .meta-info { width: 100%; margin-bottom: 30px; }
        .meta-info td { font-size: 12px; color: #555; }
        
        .stats-grid { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .stat-box { width: 25%; padding: 15px; border: 1px solid #e5e7eb; text-align: center; }
        .stat-label { font-size: 10px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 5px; }
        .stat-value { font-size: 18px; font-weight: bold; color: #111827; }
        
        .section-title { font-size: 16px; font-weight: bold; color: #1f2937; margin-bottom: 15px; border-left: 4px solid #1a56db; padding-left: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #f9fafb; color: #374151; font-weight: bold; font-size: 11px; text-transform: uppercase; padding: 10px; border: 1px solid #e5e7eb; text-align: left; }
        td { padding: 10px; border: 1px solid #e5e7eb; font-size: 11px; }
        
        .row-even { background-color: #ffffff; }
        .row-odd { background-color: #f9fafb; }
        
        .text-right { text-align: right; }
        .text-success { color: #059669; }
        .text-danger { color: #dc2626; }
        
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="company-name">{{ $company_name }}</h1>
        <p class="report-title">Financial Summary Report</p>
    </div>

    <table class="meta-info">
        <tr>
            <td><strong>Reporting Period:</strong> {{ $startDate }} to {{ $endDate }}</td>
            <td class="text-right"><strong>Generated On:</strong> {{ $generated_date }}</td>
        </tr>
    </table>

    <div class="section-title">Key Performance Indicators</div>
    <table class="stats-grid">
        <tr>
            <td class="stat-box">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">₹ {{ number_format($totalRevenue, 2) }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-label">Total Expenses</div>
                <div class="stat-value">₹ {{ number_format($totalExpenses, 2) }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-label">Net Profit</div>
                <div class="stat-value text-success">₹ {{ number_format($netProfit, 2) }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-label">Profit Margin</div>
                <div class="stat-value">{{ $netProfitMargin }}%</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Detailed Analysis</div>
    <table>
        <thead>
            <tr>
                <th>Metric Category</th>
                <th>Performance Indicator</th>
                <th class="text-right">Value / Result</th>
            </tr>
        </thead>
        <tbody>
            <tr class="row-odd">
                <td rowspan="3"><strong>Sales Analysis</strong></td>
                <td>Total Orders Processed</td>
                <td class="text-right">{{ $totalOrders }}</td>
            </tr>
            <tr class="row-even">
                <td>Average Order Value (AOV)</td>
                <td class="text-right">₹ {{ number_format($avgOrderValue, 2) }}</td>
            </tr>
            <tr class="row-odd">
                <td>Total Sales Volume</td>
                <td class="text-right">₹ {{ number_format($totalSales, 2) }}</td>
            </tr>
            
            <tr class="row-even">
                <td rowspan="3"><strong>Purchase Analysis</strong></td>
                <td>Total Purchase Orders</td>
                <td class="text-right">{{ $totalPurchaseOrders }}</td>
            </tr>
            <tr class="row-odd">
                <td>Average Purchase Value</td>
                <td class="text-right">₹ {{ number_format($avgPurchaseValue, 2) }}</td>
            </tr>
            <tr class="row-even">
                <td>Total Procurement Cost</td>
                <td class="text-right">₹ {{ number_format($totalPurchases, 2) }}</td>
            </tr>

            <tr class="row-odd">
                <td rowspan="3"><strong>Cash Flow</strong></td>
                <td>Actual Amount Received</td>
                <td class="text-right text-success">₹ {{ number_format($amountReceived, 2) }}</td>
            </tr>
            <tr class="row-even">
                <td>Total Outstanding Receivables</td>
                <td class="text-right text-danger">₹ {{ number_format($outstandingAmount, 2) }}</td>
            </tr>
            <tr class="row-odd">
                <td>Collection Efficiency Rate</td>
                <td class="text-right">{{ $collectionRate }}%</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">Financial Health Indicators</div>
    <table>
        <tr>
            <th>Indicator Name</th>
            <th>Description</th>
            <th class="text-right">Status / Value</th>
        </tr>
        <tr>
            <td>Liquidity Ratio</td>
            <td>Ability to pay off short-term obligations.</td>
            <td class="text-right">{{ $liquidityRatio }}</td>
        </tr>
        <tr>
            <td>Debt Ratio</td>
            <td>Percentage of assets financed by debt.</td>
            <td class="text-right">{{ $debtRatio }}%</td>
        </tr>
        <tr>
            <td>Operating Cash Flow</td>
            <td>Cash generated from normal business operations.</td>
            <td class="text-right">₹ {{ number_format($operatingCashFlow, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        Confidential Document - {{ $company_name }} &copy; {{ date('Y') }}. This report is generated automatically by the system.
    </div>
</body>
</html>
