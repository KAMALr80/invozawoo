@extends('user.layout')

@section('content')
    <h2>Welcome, {{ auth()->user()->name }}</h2>

    <div style="margin-top:20px;">
        <div style="background:#fff; padding:20px; border-radius:8px;">
            <h3>Today</h3>
            <p>Date: {{ date('d M Y') }}</p>
            <p>Status: Logged In</p>
        </div>
    </div>
@endsection
