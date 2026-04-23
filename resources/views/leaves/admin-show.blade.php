@extends('layouts.app')

@section('page-title', 'Leave Details - Admin View')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
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
        --font-sans: 'Segoe UI', Arial, -apple-system, BlinkMacSystemFont, sans-serif;
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

    /* ================= MAIN CONTAINER ================= */
    .admin-leave-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER CARD ================= */
    .header-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-xl);
        padding: clamp(20px, 4vw, 30px);
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
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 4vw, 30px);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .header-title h1 {
        margin: 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 700;
        color: white;
    }

    .header-title p {
        margin: 5px 0 0 0;
        font-size: clamp(14px, 3vw, 16px);
        opacity: 0.9;
    }

    .status-badge-large {
        padding: 12px 30px;
        border-radius: 40px;
        font-weight: 700;
        font-size: 18px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .status-badge-large.pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
    }

    .status-badge-large.approved {
        background: rgba(40, 167, 69, 0.2);
        color: #28a745;
    }

    .status-badge-large.rejected {
        background: rgba(220, 53, 69, 0.2);
        color: #dc3545;
    }

    .status-badge-large.cancelled {
        background: rgba(108, 117, 125, 0.2);
        color: #6c757d;
    }

    /* ================= ACTION BUTTONS ================= */
    .action-bar {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
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
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-action.approve {
        background: var(--success);
        color: white;
    }

    .btn-action.reject {
        background: var(--danger);
        color: white;
    }

    .btn-action.print {
        background: var(--warning);
        color: #333;
    }

    .btn-action.download {
        background: var(--primary);
        color: white;
    }

    .btn-action.back {
        background: #6c757d;
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-action:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* ================= LEAVE INFO CARD ================= */
    .leave-number-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .leave-number-label {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .leave-number-value {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary);
        font-family: monospace;
        background: #e3f2fd;
        padding: 8px 20px;
        border-radius: var(--radius-md);
    }

    .employee-summary {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .employee-avatar-large {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 24px;
    }

    .employee-info h3 {
        font-size: 20px;
        margin-bottom: 5px;
    }

    .employee-info p {
        color: var(--text-muted);
        font-size: 14px;
    }

    /* ================= MAIN GRID ================= */
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    /* ================= INFO CARDS ================= */
    .info-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 25px;
        box-shadow: var(--shadow-md);
        border: 1px solid var(--border);
        margin-bottom: 25px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
    }

    .card-title span {
        width: 6px;
        height: 24px;
        background: var(--primary);
        border-radius: 3px;
        display: inline-block;
    }

    .card-title .badge {
        margin-left: auto;
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 20px;
        background: var(--bg-light);
        color: var(--text-muted);
    }

    /* ================= INFO ROWS ================= */
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

    .info-value.warning {
        color: var(--danger);
        font-weight: 600;
    }

    /* ================= STATUS TIMELINE ================= */
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 30px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -22px;
        top: 0;
        width: 2px;
        height: 100%;
        background: var(--border);
    }

    .timeline-item:last-child::before {
        display: none;
    }

    .timeline-icon {
        position: absolute;
        left: -30px;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        border: 3px solid var(--border);
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .timeline-item.completed .timeline-icon {
        background: var(--success);
        border-color: var(--success-dark);
        color: white;
    }

    .timeline-item.current .timeline-icon {
        background: var(--primary);
        border-color: var(--primary-dark);
        color: white;
        animation: pulse 2s infinite;
    }

    .timeline-item.rejected .timeline-icon {
        background: var(--danger);
        border-color: var(--danger-dark);
        color: white;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }

    .timeline-content {
        background: var(--bg-light);
        padding: 15px 20px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
    }

    .timeline-title {
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 5px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .timeline-date {
        font-size: 12px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .timeline-remarks {
        margin-top: 10px;
        padding: 12px;
        background: white;
        border-radius: var(--radius-sm);
        font-size: 13px;
        border-left: 3px solid var(--primary);
    }

    .timeline-user {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .timeline-user img {
        width: 20px;
        height: 20px;
        border-radius: 50%;
    }

    /* ================= DOCUMENT CARD ================= */
    .document-card {
        background: var(--bg-light);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-top: 15px;
        border: 1px solid var(--border);
    }

    .document-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .document-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .document-info h4 {
        font-size: 16px;
        margin-bottom: 5px;
    }

    .document-info p {
        font-size: 12px;
        color: var(--text-muted);
    }

    .document-preview {
        background: white;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        padding: 20px;
        text-align: center;
        margin-top: 15px;
    }

    .document-preview img {
        max-width: 100%;
        max-height: 300px;
        object-fit: contain;
    }

    .document-preview iframe {
        width: 100%;
        height: 500px;
        border: none;
    }

    .btn-download {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--primary);
        color: white;
        text-decoration: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= EMPLOYEE HISTORY ================= */
    .history-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .history-table th {
        text-align: left;
        padding: 10px;
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 600;
    }

    .history-table td {
        padding: 10px;
        border-bottom: 1px solid var(--border);
    }

    .history-table tr:hover td {
        background: #f1f9ff;
    }

    .history-status {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }

    .history-status.approved {
        background: #d4edda;
        color: #155724;
    }

    .history-status.pending {
        background: #fff3cd;
        color: #856404;
    }

    /* ================= BALANCE GRID ================= */
    .balance-mini-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 15px;
    }

    .balance-mini-item {
        background: var(--bg-light);
        padding: 10px;
        border-radius: var(--radius-md);
        text-align: center;
    }

    .balance-mini-label {
        font-size: 11px;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .balance-mini-value {
        font-weight: 700;
        color: var(--primary);
    }

    /* ================= APPROVAL FORM ================= */
    .approval-form, .rejection-form {
        background: var(--bg-light);
        border-radius: var(--radius-md);
        padding: 20px;
        border: 1px solid var(--border);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: var(--text-main);
    }

    .form-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        resize: vertical;
        min-height: 100px;
    }

    .form-textarea:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 15px 0;
    }

    .btn-submit {
        padding: 12px 25px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit.approve {
        background: var(--success);
        color: white;
    }

    .btn-submit.reject {
        background: var(--danger);
        color: white;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    /* ================= NOTES SECTION ================= */
    .notes-section {
        margin-top: 20px;
    }

    .note-item {
        background: var(--bg-light);
        padding: 15px;
        border-radius: var(--radius-md);
        margin-bottom: 10px;
        border-left: 3px solid var(--primary);
    }

    .note-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 12px;
        color: var(--text-muted);
    }

    .note-content {
        font-size: 14px;
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 15px 20px;
        border-radius: var(--radius-md);
        margin-bottom: 25px;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    /* ================= LOADING ================= */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 992px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .header-card {
            flex-direction: column;
            text-align: center;
        }

        .header-left {
            justify-content: center;
        }

        .leave-number-card {
            flex-direction: column;
            text-align: center;
        }

        .action-bar {
            flex-direction: column;
        }

        .action-buttons {
            width: 100%;
            justify-content: center;
        }

        .info-row {
            flex-direction: column;
            gap: 5px;
        }

        .info-label, .info-value {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .balance-mini-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-leave-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📋</div>
                <div class="header-title">
                    <h1>Leave Details</h1>
                    <p>Admin Review Panel</p>
                </div>
            </div>
            <div class="status-badge-large {{ $leave->status }}">
                @if($leave->status == 'pending')
                    ⏳ Pending Approval
                @elseif($leave->status == 'approved')
                    ✅ Approved
                @elseif($leave->status == 'rejected')
                    ❌ Rejected
                @elseif($leave->status == 'cancelled')
                    ↩️ Cancelled
                @endif
            </div>
        </div>

        <!-- Alerts -->
        @if (session('success'))
            <div class="alert alert-success">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <span>❌</span> {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">
                <span>⚠️</span> {{ session('warning') }}
            </div>
        @endif

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="employee-summary">
                <div class="employee-avatar-large">
                    {{ strtoupper(substr($leave->employee->name ?? 'U', 0, 1)) }}
                </div>
                <div class="employee-info">
                    <h3>{{ $leave->employee->name ?? 'Unknown' }}</h3>
                    <p>{{ $leave->employee->employee_code ?? 'N/A' }} • {{ $leave->employee->department ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="action-buttons">
                @if($leave->status == 'pending')
                    <button class="btn-action approve" onclick="openApproveForm()">
                        <span>✅</span> Approve Leave
                    </button>
                    <button class="btn-action reject" onclick="openRejectForm()">
                        <span>❌</span> Reject Leave
                    </button>
                @endif
                <a href="{{ route('leaves.print', $leave->id) }}" class="btn-action print" target="_blank">
                    <span>🖨️</span> Print
                </a>
                @if($leave->document_path)
                    <a href="{{ route('leaves.download', $leave->id) }}" class="btn-action download">
                        <span>📎</span> Download
                    </a>
                @endif
                <a href="{{ route('leaves.manage') }}" class="btn-action back">
                    <span>↩️</span> Back to List
                </a>
            </div>
        </div>

        <!-- Leave Number Card -->
        <div class="leave-number-card">
            <div>
                <div class="leave-number-label">Leave Number</div>
                <div class="leave-number-value">{{ $leave->leave_number ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="leave-number-label">Applied On</div>
                <div style="font-size: 18px; font-weight: 600;">
                    {{ $leave->applied_on ? $leave->applied_on->format('d M Y, h:i A') : 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="detail-grid">
            <!-- Left Column - Leave Details -->
            <div>
                <!-- Leave Information -->
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Leave Information
                        <span class="badge">ID: {{ $leave->id }}</span>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Leave Type</div>
                        <div class="info-value highlight">{{ $leave->leave_type_label ?? $leave->leave_type }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Duration Type</div>
                        <div class="info-value">{{ $leave->duration_label ?? $leave->duration_type }}</div>
                    </div>

                    @if($leave->session)
                    <div class="info-row">
                        <div class="info-label">Session</div>
                        <div class="info-value">{{ $leave->session == 'first_half' ? 'First Half (9:00 AM - 1:00 PM)' : 'Second Half (2:00 PM - 6:00 PM)' }}</div>
                    </div>
                    @endif

                    @if($leave->start_time && $leave->end_time)
                    <div class="info-row">
                        <div class="info-label">Time</div>
                        <div class="info-value">{{ \Carbon\Carbon::parse($leave->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($leave->end_time)->format('h:i A') }}</div>
                    </div>
                    @endif

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

                    @if($leave->ip_address)
                    <div class="info-row">
                        <div class="info-label">IP Address</div>
                        <div class="info-value">{{ $leave->ip_address }}</div>
                    </div>
                    @endif

                    @if($leave->user_agent)
                    <div class="info-row">
                        <div class="info-label">User Agent</div>
                        <div class="info-value">{{ \Str::limit($leave->user_agent, 50) }}</div>
                    </div>
                    @endif
                </div>

                <!-- Employee Details -->
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Employee Details
                    </div>

                    <div class="info-row">
                        <div class="info-label">Full Name</div>
                        <div class="info-value">{{ $leave->employee->name ?? 'N/A' }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Employee Code</div>
                        <div class="info-value">{{ $leave->employee->employee_code ?? 'N/A' }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Department</div>
                        <div class="info-value">{{ $leave->employee->department ?? 'N/A' }}</div>
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
                        <div class="info-label">Joining Date</div>
                        <div class="info-value">{{ $leave->employee->joining_date ? \Carbon\Carbon::parse($leave->employee->joining_date)->format('d M Y') : 'N/A' }}</div>
                    </div>
                </div>

                <!-- Reason and Details -->
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Reason & Contact
                    </div>

                    <div class="info-row">
                        <div class="info-label">Reason</div>
                        <div class="info-value">{{ $leave->reason ?? 'N/A' }}</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value">{{ $leave->contact_number ?? 'N/A' }}</div>
                    </div>

                    @if($leave->emergency_contact)
                    <div class="info-row">
                        <div class="info-label">Emergency Contact</div>
                        <div class="info-value">{{ $leave->emergency_contact }}</div>
                    </div>
                    @endif
                </div>

                <!-- Handover Information -->
                @if($leave->handover_notes || $leave->handover_person || $leave->alternate_arrangements)
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Work Handover
                    </div>

                    @if($leave->handover_person)
                    <div class="info-row">
                        <div class="info-label">Handover Person</div>
                        <div class="info-value">{{ $leave->handover_person }}</div>
                    </div>
                    @endif

                    @if($leave->handover_notes)
                    <div class="info-row">
                        <div class="info-label">Handover Notes</div>
                        <div class="info-value">{{ $leave->handover_notes }}</div>
                    </div>
                    @endif

                    @if($leave->alternate_arrangements)
                    <div class="info-row">
                        <div class="info-label">Alternate Arrangements</div>
                        <div class="info-value">{{ $leave->alternate_arrangements }}</div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Document Section -->
                @if($leave->document_path)
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Supporting Document
                    </div>

                    <div class="document-card">
                        <div class="document-header">
                            <div class="document-icon">📄</div>
                            <div class="document-info">
                                <h4>{{ $leave->document_name ?? 'Document' }}</h4>
                                @php
                                    $filePath = storage_path('app/public/' . $leave->document_path);
                                    $fileSize = file_exists($filePath) ? round(filesize($filePath) / 1024, 1) . ' KB' : 'N/A';
                                    $fileExt = pathinfo($leave->document_path, PATHINFO_EXTENSION);
                                @endphp
                                <p>{{ strtoupper($fileExt) }} • {{ $fileSize }}</p>
                            </div>
                        </div>

                        @if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif']))
                        <div class="document-preview">
                            <img src="{{ Storage::url($leave->document_path) }}" alt="Document Preview">
                        </div>
                        @elseif(strtolower($fileExt) == 'pdf')
                        <div class="document-preview">
                            <iframe src="{{ Storage::url($leave->document_path) }}"></iframe>
                        </div>
                        @endif

                        <a href="{{ route('leaves.download', $leave->id) }}" class="btn-download">
                            <span>📥</span> Download Document
                        </a>
                    </div>
                </div>
                @endif

                <!-- Approval/Rejection Form (Only for Pending) -->
                @if($leave->status == 'pending')
                <div class="info-card" id="approvalForm" style="display: none;">
                    <div class="card-title">
                        <span></span> Approve Leave
                    </div>
                    <div class="approval-form">
                        <form method="POST" action="{{ route('leaves.approve', $leave->id) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Remarks (Optional)</label>
                                <textarea name="remarks" class="form-textarea" placeholder="Add any approval remarks..."></textarea>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="notify_employee" id="notifyApprove" value="1" checked>
                                <label for="notifyApprove">Notify employee via email</label>
                            </div>
                            <button type="submit" class="btn-submit approve">
                                <span>✅</span> Confirm Approval
                            </button>
                        </form>
                    </div>
                </div>

                <div class="info-card" id="rejectionForm" style="display: none;">
                    <div class="card-title">
                        <span></span> Reject Leave
                    </div>
                    <div class="rejection-form">
                        <form method="POST" action="{{ route('leaves.reject', $leave->id) }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Rejection Reason <span style="color: var(--danger);">*</span></label>
                                <textarea name="rejection_reason" class="form-textarea" placeholder="Please provide reason for rejection..." required></textarea>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="notify_employee" id="notifyReject" value="1" checked>
                                <label for="notifyReject">Notify employee via email</label>
                            </div>
                            <button type="submit" class="btn-submit reject">
                                <span>❌</span> Confirm Rejection
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Status & History -->
            <div>
                <!-- Status Timeline -->
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Status Timeline
                    </div>

                    <div class="timeline">
                        <!-- Applied -->
                        <div class="timeline-item completed">
                            <div class="timeline-icon">📝</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Leave Applied</div>
                                <div class="timeline-date">
                                    {{ $leave->applied_on ? $leave->applied_on->format('d M Y, h:i A') : 'N/A' }}
                                </div>
                                <div class="timeline-user">
                                    <span>👤 {{ $leave->employee->name ?? 'Employee' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Under Review (if pending) -->
                        @if($leave->status == 'pending')
                        <div class="timeline-item current">
                            <div class="timeline-icon">⏳</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Under Review</div>
                                <div class="timeline-date">
                                    Waiting for admin approval
                                </div>
                                <div class="timeline-remarks">
                                    <strong>Expected response time:</strong> Within 24 hours
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Approved -->
                        @if($leave->status == 'approved')
                        <div class="timeline-item completed">
                            <div class="timeline-icon">✅</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Approved</div>
                                <div class="timeline-date">
                                    {{ $leave->approved_at ? $leave->approved_at->format('d M Y, h:i A') : 'N/A' }}
                                </div>
                                <div class="timeline-user">
                                    <span>👤 {{ $leave->approver->name ?? 'Admin' }}</span>
                                </div>
                                @if($leave->approval_remarks)
                                <div class="timeline-remarks">
                                    <strong>Remarks:</strong> {{ $leave->approval_remarks }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Rejected -->
                        @if($leave->status == 'rejected')
                        <div class="timeline-item rejected">
                            <div class="timeline-icon">❌</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Rejected</div>
                                <div class="timeline-date">
                                    {{ $leave->rejected_at ? $leave->rejected_at->format('d M Y, h:i A') : 'N/A' }}
                                </div>
                                <div class="timeline-user">
                                    <span>👤 {{ $leave->rejector->name ?? 'Admin' }}</span>
                                </div>
                                @if($leave->rejection_reason)
                                <div class="timeline-remarks">
                                    <strong>Reason:</strong> {{ $leave->rejection_reason }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Cancelled -->
                        @if($leave->status == 'cancelled')
                        <div class="timeline-item rejected">
                            <div class="timeline-icon">↩️</div>
                            <div class="timeline-content">
                                <div class="timeline-title">Cancelled</div>
                                <div class="timeline-date">
                                    {{ $leave->cancelled_at ? $leave->cancelled_at->format('d M Y, h:i A') : 'N/A' }}
                                </div>
                                <div class="timeline-user">
                                    <span>👤 {{ $leave->canceller->name ?? 'Employee' }}</span>
                                </div>
                                @if($leave->cancellation_reason)
                                <div class="timeline-remarks">
                                    <strong>Reason:</strong> {{ $leave->cancellation_reason }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Leave Balance Summary -->
                @if(isset($leaveBalance))
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Leave Balance Summary
                        <span class="badge">Year {{ date('Y') }}</span>
                    </div>

                    <div class="balance-mini-grid">
                        @foreach($leaveBalance as $type => $balance)
                            @if($balance['entitled'] > 0)
                            <div class="balance-mini-item">
                                <div class="balance-mini-label">{{ ucfirst($type) }}</div>
                                <div class="balance-mini-value">
                                    {{ $balance['available'] }}/{{ $balance['entitled'] }}
                                </div>
                                <small style="font-size: 10px;">Used: {{ $balance['used'] }}</small>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <div style="margin-top: 15px; font-size: 12px; color: var(--text-muted);">
                        <span>📌 Available balances after approval will be updated automatically</span>
                    </div>
                </div>
                @endif

                <!-- Employee Leave History -->
                @if(isset($leaveHistory) && $leaveHistory->count() > 0)
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Recent Leave History
                    </div>

                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Dates</th>
                                <th>Type</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveHistory as $history)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history->from_date)->format('d M') }} - {{ \Carbon\Carbon::parse($history->to_date)->format('d M') }}</td>
                                <td>{{ $history->leave_type_label ?? $history->leave_type }}</td>
                                <td>{{ $history->total_days }}</td>
                                <td>
                                    <span class="history-status {{ $history->status }}">
                                        {{ ucfirst($history->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div style="margin-top: 15px; text-align: right;">
                        <a href="#" style="color: var(--primary); font-size: 13px;">View All History →</a>
                    </div>
                </div>
                @endif

                <!-- Admin Notes -->
                <div class="info-card">
                    <div class="card-title">
                        <span></span> Admin Notes
                    </div>

                    <div class="notes-section">
                        <div class="note-item">
                            <div class="note-header">
                                <span>System</span>
                                <span>{{ now()->format('d M Y') }}</span>
                            </div>
                            <div class="note-content">
                                This leave request is being reviewed by {{ auth()->user()->name }}.
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 15px;">
                        <textarea class="form-textarea" placeholder="Add private notes... (not visible to employee)" style="min-height: 80px;"></textarea>
                        <button class="btn-submit" style="margin-top: 10px; background: var(--info); color: white;">Save Note</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
    // Toggle approval form
    function openApproveForm() {
        document.getElementById('approvalForm').style.display = 'block';
        document.getElementById('rejectionForm').style.display = 'none';
    }

    function openRejectForm() {
        document.getElementById('rejectionForm').style.display = 'block';
        document.getElementById('approvalForm').style.display = 'none';
    }

    // Character counter for rejection reason
    const rejectTextarea = document.querySelector('textarea[name="rejection_reason"]');
    if (rejectTextarea) {
        const counterDiv = document.createElement('div');
        counterDiv.style.fontSize = '12px';
        counterDiv.style.marginTop = '5px';
        counterDiv.style.color = 'var(--text-muted)';
        rejectTextarea.parentNode.insertBefore(counterDiv, rejectTextarea.nextSibling);

        rejectTextarea.addEventListener('input', function() {
            const length = this.value.length;
            counterDiv.innerHTML = `${length}/1000 characters`;

            if (length < 10) {
                counterDiv.style.color = 'var(--danger)';
            } else {
                counterDiv.style.color = 'var(--success)';
            }
        });
    }

    // Confirmation before approve/reject
    document.querySelectorAll('.approval-form form, .rejection-form form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const isReject = this.querySelector('textarea[name="rejection_reason"]');
            let message = 'Are you sure you want to ';
            message += isReject ? 'reject' : 'approve';
            message += ' this leave request?';

            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }

            document.getElementById('loadingOverlay').style.display = 'flex';
        });
    });

    // Auto-refresh for pending status
    @if($leave->status == 'pending')
    setTimeout(function() {
        location.reload();
    }, 30000); // Refresh every 30 seconds
    @endif
</script>
@endsection
