<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Attendance Report</title>

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
                    <div class="title">ATTENDANCE REPORT</div>
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
                    <th width="20%">Employee</th>
                    <th width="10%">Code</th>
                    <th width="15%">Department</th>
                    <th width="12%">Date</th>
                    <th width="10%">Status</th>
                    <th width="10%">Check In</th>
                    <th width="10%">Check Out</th>
                    <th width="8%">Hours</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($attendances as $index => $attendance)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>

                        <td>{{ $attendance->employee->name ?? 'N/A' }}</td>

                        <td>{{ $attendance->employee->employee_code ?? 'N/A' }}</td>

                        <td>{{ $attendance->employee->department ?? 'N/A' }}</td>

                        <td>{{ $attendance->attendance_date->format('d M Y') }}</td>

                        <td>{{ $attendance->status }}</td>

                        <td>{{ $attendance->check_in ?? '-' }}</td>

                        <td>{{ $attendance->check_out ?? '-' }}</td>

                        <td>{{ $attendance->working_hours ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="5">SUMMARY</td>
                    <td colspan="4">
                        Present: {{ $stats['present'] }} |
                        Absent: {{ $stats['absent'] }} |
                        Late: {{ $stats['late'] }} |
                        Leave: {{ $stats['leave'] }}
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>

</body>

</html>
