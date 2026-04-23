@extends('user.layout')

@section('content')

<h2>🕒 My Attendance</h2>

@if(session('success'))
<p style="color:green">{{ session('success') }}</p>
@endif

<div style="margin:20px 0;">
    <form method="POST" action="{{ route('attendance.checkin') }}" style="display:inline;">
        @csrf
        <button>✅ Check In</button>
    </form>

    <form method="POST" action="{{ route('attendance.checkout') }}" style="display:inline;">
        @csrf
        <button>⏹ Check Out</button>
    </form>
</div>

<h3>History</h3>
<table border="1" cellpadding="8">
<tr>
    <th>Date</th>
    <th>In</th>
    <th>Out</th>
    <th>Status</th>
</tr>

@foreach($history as $row)
<tr>
    <td>{{ $row->attendance_date }}</td>
    <td>{{ $row->check_in ?? '-' }}</td>
    <td>{{ $row->check_out ?? '-' }}</td>
    <td>{{ $row->status }}</td>
</tr>
@endforeach
</table>

@endsection
