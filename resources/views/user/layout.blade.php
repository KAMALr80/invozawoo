<!DOCTYPE html>
<html>

<head>
    <title>User Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            font-family: Arial;
            background: #f3f4f6;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 220px;
            background: #111827;
            color: #fff;
            padding: 20px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: #374151;
        }

        .content {
            flex: 1;
            padding: 25px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="sidebar">
            <h3>User Portal</h3>

            <a href="{{ route('user.dashboard') }}">🏠 Dashboard</a>
            <a href="{{ route('attendance.my') }}">🕒 My Attendance</a>
            <a href="{{ route('profile.edit') }}">👤 Profile</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button style="width:100%; margin-top:10px;">Logout</button>
            </form>

            @if (auth()->user()->role === 'staff')
                <a href="{{ route('attendance.my') }}">🕒 My Attendance</a>
            @endif

        </div>

        <div class="content">
            @yield('content')
        </div>
    </div>

</body>

</html>
