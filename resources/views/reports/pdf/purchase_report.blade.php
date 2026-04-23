<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Purchase Report</title>

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

.left { width: 33%; }
.center { width: 34%; text-align: center; }
.right { width: 33%; text-align: right; }

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

.text-right { text-align: right; }
.text-center { text-align: center; }

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
    <div class="title">PURCHASE REPORT</div>
</td>

<td class="right meta">
    Date: {{ $generated_date }}<br>
    Period:
    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
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
    <th width="5%">SR</th>
    <th width="12%">Invoice</th>
    <th width="12%">Date</th>
    <th width="20%">Product</th>
    <th width="15%">Supplier</th>
    <th width="8%" class="text-right">Qty</th>
    <th width="10%" class="text-right">Price</th>
    <th width="10%" class="text-right">Total</th>
    <th width="8%">Status</th>
</tr>
</thead>

<tbody>
@foreach ($purchases as $index => $purchase)

<tr>
    <td class="text-center">{{ $index + 1 }}</td>

    <td>{{ $purchase->invoice_number }}</td>

    <td>{{ $purchase->purchase_date->format('d M Y') }}</td>

    <td>
        {{ $purchase->product->name ?? 'N/A' }}<br>
        <small>{{ $purchase->product->product_code ?? '' }}</small>
    </td>

    <td>{{ $purchase->supplier_name ?? 'N/A' }}</td>

    <td class="text-right">{{ $purchase->quantity }}</td>

    <td class="text-right">₹{{ number_format($purchase->price, 2) }}</td>

    <td class="text-right">₹{{ number_format($purchase->grand_total, 2) }}</td>

    <td>{{ ucfirst($purchase->status) }}</td>
</tr>

@endforeach
</tbody>

<tfoot>
<tr class="total-row">
    <td colspan="7">TOTAL</td>
    <td class="text-right">₹{{ number_format($stats['total_spent'], 2) }}</td>
    <td></td>
</tr>
</tfoot>

</table>

<!-- STATUS SUMMARY -->
<table style="margin-top:10px;">
<tr style="border-top:1px solid #000;">
    <td style="border:none;"><strong>Status:</strong></td>
    <td style="border:none;">Completed: {{ $stats['completed'] ?? 0 }}</td>
    <td style="border:none;">Pending: {{ $stats['pending'] ?? 0 }}</td>
    <td style="border:none;">Cancelled: {{ $stats['cancelled'] ?? 0 }}</td>
</tr>
</table>

</div>

</body>
</html>
