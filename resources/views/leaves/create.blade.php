@extends('layouts.app')

@section('page-title', 'Apply for Leave')

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

    /* ================= MAIN CONTAINER ================= */
    .leave-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        padding: clamp(16px, 3vw, 30px);
        width: 100%;
    }

    .container {
        max-width: 1000px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= HEADER CARD ================= */
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

    .employee-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        font-size: clamp(13px, 3vw, 14px);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        white-space: nowrap;
    }

    /* ================= BALANCE CARDS ================= */
    .balance-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .balance-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
    }

    .balance-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .balance-icon {
        width: 50px;
        height: 50px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .balance-icon.annual { background: #e3f2fd; color: #1976d2; }
    .balance-icon.sick { background: #ffebee; color: #c62828; }
    .balance-icon.casual { background: #e8f5e9; color: #2e7d32; }
    .balance-icon.unpaid { background: #f3e5f5; color: #7b1fa2; }
    .balance-icon.maternity { background: #fce4ec; color: #c2185b; }
    .balance-icon.paternity { background: #e1f5fe; color: #0288d1; }
    .balance-icon.bereavement { background: #efebe9; color: #5d4037; }
    .balance-icon.study { background: #fff3e0; color: #f57c00; }

    .balance-info {
        flex: 1;
    }

    .balance-label {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 5px;
        font-weight: 500;
    }

    .balance-values {
        display: flex;
        align-items: baseline;
        gap: 10px;
        flex-wrap: wrap;
    }

    .balance-available {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-main);
    }

    .balance-used {
        font-size: 13px;
        color: var(--text-muted);
    }

    .balance-progress {
        margin-top: 8px;
        height: 6px;
        background: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
    }

    .balance-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        border-radius: 3px;
        transition: width 0.3s ease;
    }

    /* ================= HOLIDAYS CARD ================= */
    .holidays-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
    }

    .holidays-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .holidays-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .holiday-item {
        background: var(--bg-light);
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .holiday-item.public {
        background: #fff3cd;
        color: #856404;
        border-color: #ffeeba;
    }

    .holiday-item.company {
        background: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .holiday-date {
        font-weight: 600;
    }

    /* ================= MAIN FORM CARD ================= */
    .form-card {
        background: var(--bg-white);
        border-radius: var(--radius-lg);
        padding: clamp(25px, 5vw, 35px);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
        width: 100%;
    }

    .form-title {
        color: #444;
        margin-bottom: 30px;
        font-size: clamp(20px, 4vw, 24px);
        font-weight: 700;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding-bottom: 20px;
        border-bottom: 2px dashed var(--border);
    }

    /* ================= FORM SECTIONS ================= */
    .form-section {
        background: var(--bg-light);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-bottom: 25px;
        border: 1px solid var(--border);
    }

    .section-heading {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-heading i {
        font-size: 18px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        font-size: 14px;
        color: var(--text-main);
    }

    .form-label .required {
        color: var(--danger);
        margin-left: 3px;
    }

    .form-label .hint {
        color: var(--text-muted);
        font-weight: normal;
        font-size: 12px;
        margin-left: 8px;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.3s ease;
        outline: none;
        background: white;
        font-family: var(--font-sans);
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-input.error, .form-select.error, .form-textarea.error {
        border-color: var(--danger);
    }

    .form-input.error:focus, .form-select.error:focus, .form-textarea.error:focus {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
    }

    .error-message {
        color: var(--danger);
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .help-text {
        color: var(--text-muted);
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* ================= FILE INPUT ================= */
    .file-input-wrapper {
        position: relative;
    }

    .file-input {
        position: absolute;
        opacity: 0;
        width: 0.1px;
        height: 0.1px;
    }

    .file-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        background: white;
        border: 2px dashed var(--border);
        border-radius: var(--radius-md);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .file-label:hover {
        border-color: var(--primary);
        background: #f0f7ff;
    }

    .file-icon {
        font-size: 20px;
        color: var(--primary);
    }

    .file-text {
        flex: 1;
        font-size: 14px;
        color: var(--text-muted);
    }

    .file-name {
        font-weight: 600;
        color: var(--text-main);
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ================= INFO BOX ================= */
    .info-box {
        background: #e7f3ff;
        border: 1px solid #b8daff;
        border-radius: var(--radius-md);
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .info-icon {
        font-size: 20px;
        color: var(--info);
    }

    .info-content {
        flex: 1;
        font-size: 13px;
        color: #004085;
    }

    .info-content ul {
        margin: 10px 0 0 20px;
    }

    .info-content li {
        margin: 5px 0;
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
        border-left: 4px solid transparent;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border-left-color: #28a745;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border-left-color: #dc3545;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border-left-color: #ffc107;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border-left-color: #17a2b8;
    }

    /* ================= BUTTONS ================= */
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 2px solid var(--border);
    }

    .btn {
        padding: 12px 30px;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
        transform: translateY(-2px);
    }

    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
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
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .balance-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 480px) {
        .balance-grid {
            grid-template-columns: 1fr;
        }

        .header-card {
            flex-direction: column;
            text-align: center;
        }

        .header-left {
            justify-content: center;
        }
    }
</style>

<div class="leave-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">📝</div>
                <div class="header-title">
                    <h1>Apply for Leave</h1>
                    <p>Submit your leave request for approval</p>
                </div>
            </div>
            <div class="employee-badge">
                {{ $employee->name }} • {{ $employee->employee_code }}
            </div>
        </div>

        <!-- Leave Balance Cards -->
        @if(isset($leaveBalances))
        <div class="balance-grid">
            @foreach($leaveBalances as $type => $balance)
                @php
                    $percentage = $balance['entitled'] > 0 ? round(($balance['used'] / $balance['entitled']) * 100) : 0;
                    $iconClass = $type;
                @endphp
                @if($balance['entitled'] > 0 || $type == 'unpaid')
                <div class="balance-card">
                    <div class="balance-icon {{ $iconClass }}">
                        @switch($type)
                            @case('annual') 🏖 @break
                            @case('sick') 🤒 @break
                            @case('casual') 🎉 @break
                            @case('unpaid') 💰 @break
                            @case('maternity') 👶 @break
                            @case('paternity') 👨‍👧 @break
                            @case('bereavement') 🕊 @break
                            @case('study') 📚 @break
                            @default 📅
                        @endswitch
                    </div>
                    <div class="balance-info">
                        <div class="balance-label">{{ ucfirst($type) }} Leave</div>
                        <div class="balance-values">
                            <span class="balance-available">{{ $balance['available'] }}</span>
                            <span class="balance-used">/ {{ $balance['entitled'] }} days</span>
                        </div>
                        <div class="balance-progress">
                            <div class="balance-progress-bar" style="width: {{ $percentage }}%"></div>
                        </div>
                        <small>Used: {{ $balance['used'] }} | Pending: {{ $balance['pending'] }}</small>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        <!-- Upcoming Holidays -->
        @if(isset($upcomingHolidays) && $upcomingHolidays->count() > 0)
        <div class="holidays-card">
            <div class="holidays-title">
                <span>📅</span> Upcoming Holidays
            </div>
            <div class="holidays-list">
                @foreach($upcomingHolidays as $holiday)
                    <div class="holiday-item {{ $holiday->type }}">
                        <span class="holiday-date">{{ \Carbon\Carbon::parse($holiday->date)->format('d M') }}</span>
                        <span>{{ $holiday->name }}</span>
                        @if($holiday->type == 'public')
                            <span>🏛️</span>
                        @else
                            <span>🏢</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Main Form Card -->
        <div class="form-card">
            <h2 class="form-title">
                <span>📋</span> Leave Application Form
            </h2>

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

            @if ($errors->any())
                <div class="alert alert-error">
                    <span>⚠️</span> Please fix the errors below
                </div>
            @endif

            <!-- Info Box -->
            <div class="info-box">
                <span class="info-icon">ℹ️</span>
                <div class="info-content">
                    <strong>Leave Application Guidelines:</strong>
                    <ul>
                        <li>Submit your leave request at least 3 days in advance</li>
                        <li>Attach medical certificate for sick leaves longer than 3 days</li>
                        <li>Provide handover notes for leaves longer than 2 days</li>
                        <li>You can cancel pending requests anytime</li>
                    </ul>
                </div>
            </div>

            <form method="POST" action="{{ route('leaves.store') }}" enctype="multipart/form-data" id="leaveForm">
                @csrf

                <!-- Leave Details Section -->
                <div class="form-section">
                    <div class="section-heading">
                        <span>📌</span> Leave Details
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                Leave Type <span class="required">*</span>
                            </label>
                            <select name="leave_type" class="form-select @error('leave_type') error @enderror" required id="leaveType">
                                <option value="">-- Select Leave Type --</option>
                                <option value="annual" {{ old('leave_type') == 'annual' ? 'selected' : '' }}>Annual Leave</option>
                                <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>Sick Leave</option>
                                <option value="casual" {{ old('leave_type') == 'casual' ? 'selected' : '' }}>Casual Leave</option>
                                <option value="unpaid" {{ old('leave_type') == 'unpaid' ? 'selected' : '' }}>Unpaid Leave</option>
                                <option value="maternity" {{ old('leave_type') == 'maternity' ? 'selected' : '' }}>Maternity Leave</option>
                                <option value="paternity" {{ old('leave_type') == 'paternity' ? 'selected' : '' }}>Paternity Leave</option>
                                <option value="bereavement" {{ old('leave_type') == 'bereavement' ? 'selected' : '' }}>Bereavement Leave</option>
                                <option value="study" {{ old('leave_type') == 'study' ? 'selected' : '' }}>Study Leave</option>
                                <option value="half_day" {{ old('leave_type') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                <option value="short_leave" {{ old('leave_type') == 'short_leave' ? 'selected' : '' }}>Short Leave</option>
                            </select>
                            @error('leave_type')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Duration Type <span class="required">*</span>
                            </label>
                            <select name="duration_type" class="form-select @error('duration_type') error @enderror" required id="durationType">
                                <option value="">-- Select Duration --</option>
                                <option value="full_day" {{ old('duration_type') == 'full_day' ? 'selected' : '' }}>Full Day</option>
                                <option value="half_day" {{ old('duration_type') == 'half_day' ? 'selected' : '' }}>Half Day</option>
                                <option value="short_leave" {{ old('duration_type') == 'short_leave' ? 'selected' : '' }}>Short Leave (Hours)</option>
                            </select>
                            @error('duration_type')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Session for Half Day -->
                        <div class="form-group" id="sessionGroup" style="display: none;">
                            <label class="form-label">
                                Session <span class="required">*</span>
                            </label>
                            <select name="session" class="form-select @error('session') error @enderror">
                                <option value="">-- Select Session --</option>
                                <option value="first_half" {{ old('session') == 'first_half' ? 'selected' : '' }}>First Half (9:00 AM - 1:00 PM)</option>
                                <option value="second_half" {{ old('session') == 'second_half' ? 'selected' : '' }}>Second Half (2:00 PM - 6:00 PM)</option>
                            </select>
                            @error('session')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Time for Short Leave -->
                        <div class="form-group" id="startTimeGroup" style="display: none;">
                            <label class="form-label">
                                Start Time <span class="required">*</span>
                            </label>
                            <input type="time" name="start_time" class="form-input @error('start_time') error @enderror"
                                   value="{{ old('start_time') }}" step="60">
                            @error('start_time')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group" id="endTimeGroup" style="display: none;">
                            <label class="form-label">
                                End Time <span class="required">*</span>
                            </label>
                            <input type="time" name="end_time" class="form-input @error('end_time') error @enderror"
                                   value="{{ old('end_time') }}" step="60">
                            @error('end_time')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                From Date <span class="required">*</span>
                            </label>
                            <input type="date" name="from_date" required class="form-input @error('from_date') error @enderror"
                                   value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" id="fromDate">
                            @error('from_date')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group" id="toDateGroup">
                            <label class="form-label">
                                To Date <span class="required">*</span>
                            </label>
                            <input type="date" name="to_date" class="form-input @error('to_date') error @enderror"
                                   value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" id="toDate">
                            @error('to_date')
                                <div class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </div>
                            @enderror
                            <div class="help-text" id="daysCount"></div>
                        </div>
                    </div>
                </div>

                <!-- Reason Section -->
                <div class="form-section">
                    <div class="section-heading">
                        <span>📝</span> Reason & Details
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Reason for Leave <span class="required">*</span>
                            <span class="hint">(minimum 10 characters)</span>
                        </label>
                        <textarea name="reason" class="form-textarea @error('reason') error @enderror"
                                  placeholder="Please provide detailed reason for your leave..."
                                  required minlength="10">{{ old('reason') }}</textarea>
                        @error('reason')
                            <div class="error-message">
                                <span>⚠️</span> {{ $message }}
                            </div>
                        @enderror
                        <div class="help-text" id="reasonCounter">0/2000 characters</div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                Contact Number <span class="hint">(during leave)</span>
                            </label>
                            <input type="text" name="contact_number" class="form-input"
                                   placeholder="Your contact number" value="{{ old('contact_number', $employee->phone) }}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Emergency Contact
                            </label>
                            <input type="text" name="emergency_contact" class="form-input"
                                   placeholder="Emergency contact number" value="{{ old('emergency_contact') }}">
                        </div>
                    </div>
                </div>

                <!-- Handover Section -->
                <div class="form-section">
                    <div class="section-heading">
                        <span>🔄</span> Work Handover
                    </div>

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">
                                Handover Notes <span class="hint">(who will handle your work?)</span>
                            </label>
                            <textarea name="handover_notes" class="form-textarea"
                                      placeholder="Provide details about work delegation, pending tasks, etc.">{{ old('handover_notes') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Handover Person
                            </label>
                            <input type="text" name="handover_person" class="form-input"
                                   placeholder="Name of person handling your work" value="{{ old('handover_person') }}">
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">
                                Alternate Arrangements
                            </label>
                            <textarea name="alternate_arrangements" class="form-textarea"
                                      placeholder="Any special arrangements or instructions">{{ old('alternate_arrangements') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Document Section -->
                <div class="form-section">
                    <div class="section-heading">
                        <span>📎</span> Supporting Documents
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            Upload Document <span class="hint">(PDF, JPG, PNG up to 5MB)</span>
                        </label>
                        <div class="file-input-wrapper">
                            <input type="file" name="document" id="document" class="file-input"
                                   accept=".pdf,.jpg,.jpeg,.png">
                            <label for="document" class="file-label">
                                <span class="file-icon">📎</span>
                                <span class="file-text" id="fileText">Choose file or drag here</span>
                            </label>
                        </div>
                        @error('document')
                            <div class="error-message">
                                <span>⚠️</span> {{ $message }}
                            </div>
                        @enderror
                        <div class="help-text" id="fileHelp"></div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('leaves.my') }}" class="btn btn-secondary">
                        <span>↩️</span> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span>📤</span> Submit Leave Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
    // Duration type toggle
    document.getElementById('durationType')?.addEventListener('change', function() {
        const sessionGroup = document.getElementById('sessionGroup');
        const startTimeGroup = document.getElementById('startTimeGroup');
        const endTimeGroup = document.getElementById('endTimeGroup');
        const toDateGroup = document.getElementById('toDateGroup');
        const toDateInput = document.getElementById('toDate');

        // Hide all dynamic fields first
        sessionGroup.style.display = 'none';
        startTimeGroup.style.display = 'none';
        endTimeGroup.style.display = 'none';
        toDateGroup.style.display = 'block';

        if (this.value === 'half_day') {
            sessionGroup.style.display = 'block';
            toDateGroup.style.display = 'none';
            toDateInput.removeAttribute('required');
        } else if (this.value === 'short_leave') {
            startTimeGroup.style.display = 'block';
            endTimeGroup.style.display = 'block';
            toDateGroup.style.display = 'none';
            toDateInput.removeAttribute('required');
        } else {
            toDateGroup.style.display = 'block';
            toDateInput.setAttribute('required', 'required');
        }
    });

    // Trigger on page load if old value exists
    const durationType = document.getElementById('durationType').value;
    if (durationType === 'half_day') {
        document.getElementById('sessionGroup').style.display = 'block';
        document.getElementById('toDateGroup').style.display = 'none';
    } else if (durationType === 'short_leave') {
        document.getElementById('startTimeGroup').style.display = 'block';
        document.getElementById('endTimeGroup').style.display = 'block';
        document.getElementById('toDateGroup').style.display = 'none';
    }

    // Calculate days and validate
    document.getElementById('fromDate')?.addEventListener('change', updateDays);
    document.getElementById('toDate')?.addEventListener('change', updateDays);
    document.getElementById('durationType')?.addEventListener('change', updateDays);

    function updateDays() {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
        const durationType = document.getElementById('durationType').value;
        const daysCount = document.getElementById('daysCount');

        if (fromDate && toDate && durationType === 'full_day') {
            const start = new Date(fromDate);
            const end = new Date(toDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            daysCount.innerHTML = `📅 Total days: <strong>${diffDays}</strong>`;

            if (diffDays > 10) {
                daysCount.innerHTML += ' (⚠️ Long leave, please ensure proper handover)';
            }
        } else {
            daysCount.innerHTML = '';
        }
    }

    // File input handler
    document.getElementById('document')?.addEventListener('change', function(e) {
        const fileText = document.getElementById('fileText');
        const fileHelp = document.getElementById('fileHelp');

        if (this.files.length > 0) {
            const file = this.files[0];
            fileText.innerHTML = `<span class="file-name">${file.name}</span> (${(file.size / 1024).toFixed(1)} KB)`;

            if (file.size > 5 * 1024 * 1024) {
                fileHelp.innerHTML = '❌ File size exceeds 5MB limit';
                fileHelp.style.color = 'var(--danger)';
            } else {
                fileHelp.innerHTML = '✅ File ready for upload';
                fileHelp.style.color = 'var(--success)';
            }
        } else {
            fileText.innerHTML = 'Choose file or drag here';
            fileHelp.innerHTML = '';
        }
    });

    // Reason character counter
    const reasonField = document.querySelector('textarea[name="reason"]');
    if (reasonField) {
        reasonField.addEventListener('input', function() {
            const counter = document.getElementById('reasonCounter');
            const length = this.value.length;
            counter.innerHTML = `${length}/2000 characters`;

            if (length < 10) {
                counter.style.color = 'var(--danger)';
            } else {
                counter.style.color = 'var(--success)';
            }
        });
    }

    // Form submission with loading
    document.getElementById('leaveForm')?.addEventListener('submit', function(e) {
        const fromDate = new Date(this.from_date.value);
        const toDate = new Date(this.to_date.value);
        const durationType = this.duration_type.value;
        const reasonLength = this.reason.value.length;

        // Validate reason length
        if (reasonLength < 10) {
            e.preventDefault();
            alert('❌ Please provide a detailed reason (minimum 10 characters)');
            return false;
        }

        // Validate dates based on duration type
        if (durationType === 'full_day') {
            if (toDate < fromDate) {
                e.preventDefault();
                alert('❌ To date cannot be earlier than from date');
                return false;
            }
        } else if (durationType === 'half_day') {
            if (this.from_date.value !== this.to_date.value) {
                e.preventDefault();
                alert('❌ Half day leave must be for a single date only');
                return false;
            }
            if (!this.session.value) {
                e.preventDefault();
                alert('❌ Please select half day session');
                return false;
            }
        } else if (durationType === 'short_leave') {
            const startTime = this.start_time.value;
            const endTime = this.end_time.value;

            if (!startTime || !endTime) {
                e.preventDefault();
                alert('❌ Please select start and end time for short leave');
                return false;
            }

            if (startTime >= endTime) {
                e.preventDefault();
                alert('❌ End time must be after start time');
                return false;
            }
        }

        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<span>⏳</span> Submitting...';
    });

    // Leave type change - show balance info
    document.getElementById('leaveType')?.addEventListener('change', function() {
        const selectedType = this.value;
        const balanceCards = document.querySelectorAll('.balance-card');

        balanceCards.forEach(card => {
            if (card.querySelector(`.${selectedType}`)) {
                card.style.border = '2px solid var(--primary)';
                card.style.transform = 'scale(1.02)';
            } else {
                card.style.border = '1px solid var(--border)';
                card.style.transform = 'scale(1)';
            }
        });
    });

    // Drag and drop for file input
    const dropZone = document.querySelector('.file-label');

    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            dropZone.style.borderColor = 'var(--primary)';
            dropZone.style.background = '#f0f7ff';
        }

        function unhighlight() {
            dropZone.style.borderColor = 'var(--border)';
            dropZone.style.background = 'white';
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('document').files = files;

            // Trigger change event
            const event = new Event('change', { bubbles: true });
            document.getElementById('document').dispatchEvent(event);
        }
    }
</script>
@endsection
