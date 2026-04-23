<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Communication</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 16px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid #00d4ff;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h2 {
            color: #00d4ff;
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }

        .greeting {
            color: #fff;
            font-size: 18px;
            margin-bottom: 15px;
            text-align: center;
        }

        .content {
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid #00d4ff;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            color: #b0c4de;
            font-size: 16px;
            line-height: 1.8;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px dashed #334155;
            color: #94a3b8;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>📧 {{ config('app.name') }}</h2>
        </div>

        <div class="greeting">
            Hello {{ $employee->name ?? 'Employee' }},
        </div>

        <div class="content">
            {!! nl2br(e($body)) !!}
        </div>

        <div class="footer">
            <p>Sent by: {{ $sender->name ?? 'System Admin' }} ({{ $sender->email ?? '' }})</p>
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
