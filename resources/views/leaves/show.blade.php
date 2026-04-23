@extends('layouts.app')

@section('page-title', 'Leave Details')

@section('content')
<style>
    /* ================= SAME DESIGN SYSTEM AS MY.BLADE.PHP ================= */
    :root {
        --primary: #007bff;
        --primary-dark: #0056b3;
        --success: #28a745;
        --success-dark: #218838;
        --danger: #dc3545;
        --danger-dark: #c82333;
        --warning: #ffc107;
        --warning-dark: #e0a800;
        --info: #17a2b8;
        --text-main: #333;
        --text-muted: #666;
        --border: #ddd;
        --bg-light: #f9f9f9;
        --bg-white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
        --shadow-md: 0 4px 8px rgba(0,0,0,0.1);
        --shadow-lg: 0 8px 16px rgba(0,0,0,0.1);
        --radius-sm: 4px;
        --radius-md: 6px;
        --radius-lg: 8px;
        --radius-xl: 12px;
        --font-sans: Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-sans);
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        color: var(--text-main);
        line-height: 1.5;
    }

    .detail-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        width: 100%;
    }

    .header-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 4vw, 25px);
        margin-bottom: 30px;
        box-shadow: var(--shadow-lg);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .header-icon {
        width: clamp(50px, 8vw, 60px);
        height: clamp(50px, 8vw, 60px);
        background: rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 4vw, 28px);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .header-title h1 {
        margin: 0;
        font-size: clamp(22px, 5vw, 26px);
        font-weight: 700;
        color: white;
    }

    .header-title p {
        margin: 5px 0 0 0;
        font-size: clamp(13px, 3vw, 14px);
        opacity: 0.9;
    }

    .info-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-row {
        display: flex;
        padding: 12px 0;
        border-bottom: 1px dashed var(--border);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        width: 35%;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 14px;
    }

    .info-value {
        width: 65%;
        font-weight: 500;
        color: var(--text-main);
        font-size: 14px;
    }

    .info-value.highlight {
        color: var(--primary);
        font-weight: 700;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 12px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .btn-action {
        padding: 10px 20px;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-action.back {
        background: #6c757d;
        color: white;
    }

    .btn-action.cancel {
        background: var(--danger);
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    @media (max-width: 768px) {
        .header-card {
            flex-direction: column;
            text-align: center;
        }

        .info-row {
            flex-direction: column;
            gap: 5px;
        }

        .info-label, .info-value {
            width: 100%;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-action {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="detail-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📋</div>
                <div class="header-title">
                    <h1>Leave Details</h1>
                    <p>{{ $leave->employee->name }} • {{ $leave->employee->employee_code }}</p>
                </div>
            </div>
            <div>
                <span class="status-badge status-{{ strtolower($leave->status) }}" style="font-size:14px; padding:8px 20px;">
                    @if($leave->status == 'Pending') ⏳
                    @elseif($leave->status == 'Approved') ✅
                    @elseif($leave->status == 'Rejected') ❌
                    @elseif($leave->status == 'Cancelled') ↩️
                    @endif
                    {{ $leave->status }}
                </span>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success" style="background:#d4edda; color:#155724; padding:12px 20px; border-radius:var(--radius-md); margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" style="background:#f8d7da; color:#721c24; padding:12px 20px; border-radius:var(--radius-md); margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                <span>❌</span> {{ session('error') }}
            </div>
        @endif

        <!-- Leave Number Card -->
        <div class="info-card">
            <div class="card-title">
                <span>🔢</span> Leave Information
            </div>
            <div class="info-row">
                <div class="info-label">Leave Number</div>
                <div class="info-value highlight">{{ $leave->leave_number ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">From Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($leave->from_date)->format('l, d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">To Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($leave->to_date)->format('l, d F Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Days</div>
                <div class="info-value highlight">{{ $leave->total_days ?? 1 }} day(s)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Type</div>
                <div class="info-value">{{ $leave->type }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Reason</div>
                <div class="info-value">{{ $leave->reason ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Applied On</div>
                <div class="info-value">{{ $leave->created_at->format('l, d F Y h:i A') }}</div>
            </div>
        </div>

        <!-- Employee Info Card -->
        <div class="info-card">
            <div class="card-title">
                <span>👤</span> Employee Information
            </div>
            <div class="info-row">
                <div class="info-label">Name</div>
                <div class="info-value">{{ $leave->employee->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Employee Code</div>
                <div class="info-value">{{ $leave->employee->employee_code }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $leave->employee->email ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Phone</div>
                <div class="info-value">{{ $leave->employee->phone ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Department</div>
                <div class="info-value">{{ $leave->employee->department ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Rejection Reason (if rejected) -->
        @if($leave->status == 'Rejected' && $leave->rejection_reason)
        <div class="info-card">
            <div class="card-title">
                <span>❌</span> Rejection Reason
            </div>
            <div class="info-row">
                <div class="info-value" style="color: var(--danger);">{{ $leave->rejection_reason }}</div>
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('leaves.my') }}" class="btn-action back">
                <span>↩️</span> Back to My Leaves
            </a>

            @if($leave->status == 'Pending')
                <form method="POST" action="{{ route('leaves.cancel', $leave->id) }}" style="display:inline;"
                      onsubmit="return confirm('Are you sure you want to cancel this leave request?');">
                    @csrf
                    <button type="submit" class="btn-action cancel">
                        <span>✖️</span> Cancel Request
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
