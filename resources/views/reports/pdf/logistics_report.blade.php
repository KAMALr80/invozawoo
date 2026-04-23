<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Logistics Report</title>

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
    <div class="title">LOGISTICS REPORT</div>
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
    <th width="12%">Shipment</th>
    <th width="12%">Tracking</th>
    <th width="15%">City</th>
    <th width="10%">Status</th>
    <th width="10%" class="text-right">Weight</th>
    <th width="10%" class="text-right">Value</th>
    <th width="10%" class="text-right">Charge</th>
</tr>
</thead>

<tbody>
@foreach ($shipments as $index => $shipment)
<tr>
    <td class="text-center">{{ $index + 1 }}</td>
    <td>{{ $shipment->shipment_number }}</td>
    <td>{{ $shipment->tracking_number ?? '-' }}</td>
    <td>{{ $shipment->city }}</td>
    <td>{{ ucfirst(str_replace('_', ' ', $shipment->status)) }}</td>
    <td class="text-right">{{ number_format($shipment->weight ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($shipment->declared_value ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($shipment->total_charge ?? 0, 2) }}</td>
</tr>
@endforeach
</tbody>

<tfoot>
<tr class="total-row">
    <td colspan="5">TOTAL</td>
    <td class="text-right">{{ number_format($stats['total_weight'] ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($stats['total_value'] ?? 0, 2) }}</td>
    <td class="text-right">₹{{ number_format($stats['total_revenue'], 2) }}</td>
</tr>
</tfoot>

</table>

<!-- CITY SUMMARY -->
<table style="margin-top:15px;">
<tr style="border-top:1px solid #000;">
    <td style="border:none;"><strong>City Summary:</strong></td>
    @foreach ($cityData as $city)
        <td style="border:none;">
            {{ $city['city'] }}: {{ $city['total'] }}
        </td>
    @endforeach
</tr>
</table>

</div>

</body>
</html>
