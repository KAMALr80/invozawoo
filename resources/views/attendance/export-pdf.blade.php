<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report - {{ $month }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #007bff; color: white; }
    </style>
</head>
<body>
    <h2>Attendance Report - {{ \Carbon\Carbon::parse($month)->format('F Y') }}</h2>
    <table>
        <thead>
            <tr><th>Employee</th><th>Date</th><th>Status</th><th>Check In</th><th>Check Out</th><th>Working Hours</th><th>Remarks</th></tr>
        </thead>
        <tbody>
            @foreach($attendances as $att)
            <tr>
                <td>{{ $att->employee->name }}</td>
                <td>{{ $att->attendance_date->format('d-m-Y') }}</td>
                <td>{{ $att->status }}</td>
                <td>{{ $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '' }}</td>
                <td>{{ $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '' }}</td>
                <td>{{ $att->working_hours ?? '' }}</td>
                <td>{{ $att->remarks ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
