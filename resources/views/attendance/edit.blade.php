@extends('layouts.app')

@section('page-title', 'Edit Attendance')

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
        --text-main: #2c3e50;
        --text-muted: #6b7280;
        --border: #ddd;
        --bg-light: #f8f9fa;
        --bg-white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
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

    .edit-page {
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

    /* ================= FORM CARD ================= */
    .form-card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        padding: 30px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .form-section {
        margin-bottom: 25px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border);
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-title span {
        width: 6px;
        height: 24px;
        background: var(--primary);
        border-radius: 3px;
        display: inline-block;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
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

    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid var(--border);
        border-radius: var(--radius-md);
        font-size: 14px;
        transition: all 0.3s ease;
        outline: none;
        background: white;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
    }

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .form-control.is-invalid:focus {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(220,53,69,0.1);
    }

    .error-message {
        color: var(--danger);
        font-size: 12px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .help-text {
        color: var(--text-muted);
        font-size: 12px;
        margin-top: 5px;
    }

    /* ================= ALERT ================= */
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
        box-shadow: 0 2px 4px rgba(0,123,255,0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    /* ================= LOADING OVERLAY ================= */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
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
    }
</style>

<div class="edit-page">
    <div class="container">
        <!-- Header Card -->
        <div class="header-card">
            <div class="header-left">
                <div class="header-icon">✏️</div>
                <div class="header-title">
                    <h1>Edit Attendance Record</h1>
                    <p>{{ $attendance->employee->name }} • {{ $attendance->employee->employee_code ?? '' }}</p>
                </div>
            </div>
            <div class="date-badge">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('l, d M Y') }}</div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="alert alert-success">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <span>❌</span> {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <span>⚠️</span> Please fix the errors below.
            </div>
        @endif

        <!-- Form Card -->
        <div class="form-card">
            <form method="POST" action="{{ route('attendance.update', $attendance->id) }}" id="editForm">
                @csrf
                @method('PUT')

                <!-- Employee Section -->
                <div class="form-section">
                    <div class="section-title">
                        <span></span> Employee Information
                    </div>
                    <div class="form-group">

                        {{-- Debugging --}}
@if(empty($employees))
    <div style="background:red; color:white; padding:10px;">❌ $employees EMPTY hai! Koi employee nahi mila.</div>
@else
    <div style="background:green; color:white; padding:10px;">✅ $employees me {{ count($employees) }} records hain.</div>
@endif
                        <label class="form-label">Employee <span class="required">*</span></label>
                        <select name="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ (old('employee_id', $attendance->employee_id) == $emp->id) ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->employee_code ?? $emp->id }}) - {{ $emp->department ?? '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Attendance Details -->
                <div class="form-section">
                    <div class="section-title">
                        <span></span> Attendance Details
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Date <span class="required">*</span></label>
                            <input type="date" name="attendance_date" class="form-control @error('attendance_date') is-invalid @enderror"
                                   value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}" required>
                            @error('attendance_date')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status <span class="required">*</span></label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="Present" {{ old('status', $attendance->status) == 'Present' ? 'selected' : '' }}>✅ Present</option>
                                <option value="Absent" {{ old('status', $attendance->status) == 'Absent' ? 'selected' : '' }}>❌ Absent</option>
                                <option value="Late" {{ old('status', $attendance->status) == 'Late' ? 'selected' : '' }}>⏰ Late</option>
                                <option value="Half Day" {{ old('status', $attendance->status) == 'Half Day' ? 'selected' : '' }}>⚠️ Half Day</option>
                                <option value="Leave" {{ old('status', $attendance->status) == 'Leave' ? 'selected' : '' }}>🏖️ Leave</option>
                            </select>
                            @error('status')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Check In</label>
                            <input type="time" name="check_in" class="form-control @error('check_in') is-invalid @enderror"
                                   value="{{ old('check_in', $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '') }}">
                            @error('check_in')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Check Out</label>
                            <input type="time" name="check_out" class="form-control @error('check_out') is-invalid @enderror"
                                   value="{{ old('check_out', $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '') }}">
                            @error('check_out')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Working Hours (HH:MM:SS)</label>
                            <input type="text" name="working_hours" class="form-control @error('working_hours') is-invalid @enderror"
                                   placeholder="08:30:00" value="{{ old('working_hours', $attendance->working_hours) }}">
                            @error('working_hours')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                            <div class="help-text">Format: hours:minutes:seconds (e.g., 08:30:00). Leave empty to auto‑calculate.</div>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="form-section">
                    <div class="section-title">
                        <span></span> Remarks
                    </div>
                    <div class="form-group">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                                  rows="4" placeholder="Optional remarks...">{{ old('remarks', $attendance->remarks) }}</textarea>
                        @error('remarks')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="{{ route('attendance.manage') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        Update Attendance
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
    document.getElementById('editForm')?.addEventListener('submit', function() {
        document.getElementById('loadingOverlay').style.display = 'flex';
        document.getElementById('submitBtn').disabled = true;
    });
</script>
@endsection
