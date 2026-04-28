@extends('layouts.app')

@section('page-title', 'Edit Employee - ' . ($employee->name ?? ''))

@section('content')
<style>
    /* ================= ULTRA-PREMIUM DESIGN SYSTEM ================= */
    :root {
        --primary: #6366f1;
        --primary-dark: #4f46e5;
        --secondary: #8b5cf6;
        --success: #10b981;
        --danger: #ef4444;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --radius-premium: 20px;
        --radius-input: 14px;
    }

    [data-theme="dark"] {
        --text-main: #f1f5f9;
        --text-muted: #94a3b8;
        --glass-bg: rgba(30, 41, 59, 0.7);
        --glass-border: rgba(255, 255, 255, 0.05);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* ================= MAIN CONTAINER ================= */
    .form-page {
        min-height: calc(100vh - 70px);
        background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
                    radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        padding: 40px 24px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Background Orbs for Extreme UI */
    .orb { position: absolute; border-radius: 50%; filter: blur(80px); z-index: 0; opacity: 0.5; animation: floatOrb 10s infinite alternate cubic-bezier(0.4, 0, 0.2, 1); pointer-events: none; }
    .orb-1 { width: 400px; height: 400px; background: rgba(99, 102, 241, 0.4); top: -10%; left: -10%; }
    .orb-2 { width: 500px; height: 500px; background: rgba(139, 92, 246, 0.3); bottom: -20%; right: -10%; animation-delay: -5s; }
    .orb-3 { width: 350px; height: 350px; background: rgba(16, 185, 129, 0.2); top: 30%; left: 40%; animation-duration: 15s; }
    [data-theme="dark"] .orb-1 { background: rgba(99, 102, 241, 0.3); }
    [data-theme="dark"] .orb-2 { background: rgba(139, 92, 246, 0.2); }
    @keyframes floatOrb { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(30px, 50px) scale(1.1); } }

    .form-container { max-width: 900px; width: 100%; margin: 0 auto; perspective: 1000px; position: relative; z-index: 1; }

    /* ================= FORM CARD (GLASSMORPHISM) ================= */
    .form-card, .unauthorized-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.8), rgba(255,255,255,0.4));
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-top: 1px solid rgba(255,255,255,0.8);
        border-radius: var(--radius-premium);
        padding: 45px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.5);
        animation: slideUpFadeCard 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    
    [data-theme="dark"] .form-card, [data-theme="dark"] .unauthorized-card {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.5));
        border-top: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }

    @keyframes slideUpFadeCard {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ================= FORM HEADER ================= */
    .form-header {
        display: flex; align-items: center; gap: 24px; margin-bottom: 40px; flex-wrap: wrap;
        padding-bottom: 25px; border-bottom: 2px dashed var(--glass-border);
    }

    .form-icon {
        font-size: 32px; width: 64px; height: 64px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 18px; display: flex; align-items: center; justify-content: center;
        color: white; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3), inset 0 2px 4px rgba(255,255,255,0.3);
    }

    .form-title {
        font-size: 32px; font-weight: 900; margin: 0; letter-spacing: -1px;
        background: linear-gradient(135deg, var(--text-main) 0%, var(--primary) 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }

    .form-subtitle { color: var(--text-muted); font-size: 15px; margin: 6px 0 0; font-weight: 600; letter-spacing: 0.5px; }

    /* ================= EMPLOYEE INFO WIDGET ================= */
    .employee-info {
        background: linear-gradient(135deg, rgba(255,255,255,0.6), rgba(255,255,255,0.2));
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-premium);
        padding: 24px 30px; margin-bottom: 35px;
        display: flex; align-items: center; gap: 20px; flex-wrap: wrap;
        box-shadow: 0 8px 16px rgba(0,0,0,0.02);
        animation: slideUpFadeCard 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    [data-theme="dark"] .employee-info { background: rgba(15, 23, 42, 0.5); }

    .employee-avatar {
        width: 64px; height: 64px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        border-radius: 18px; display: flex; align-items: center; justify-content: center;
        color: white; font-size: 28px; font-weight: 900;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3), inset 0 2px 4px rgba(255,255,255,0.3);
        flex-shrink: 0;
    }

    .employee-details { flex: 1; min-width: 200px; }

    .employee-code {
        background: rgba(99, 102, 241, 0.1); color: var(--primary);
        padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 800; font-family: 'JetBrains Mono', monospace;
        display: inline-block; margin-bottom: 8px; border: 1px solid rgba(99, 102, 241, 0.2);
    }
    [data-theme="dark"] .employee-code { background: rgba(99, 102, 241, 0.2); }

    .employee-name { font-size: 22px; font-weight: 900; color: var(--text-main); margin-bottom: 4px; letter-spacing: -0.5px; }
    .employee-email { color: var(--text-muted); font-size: 14px; font-weight: 600; }

    /* ================= FORM GROUPS & INPUTS ================= */
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 28px; margin-bottom: 40px; }
    .form-group { width: 100%; }
    
    /* Staggered Animations */
    .stagger-item { opacity: 0; transform: translateY(15px); animation: fadeInUp 0.5s ease forwards; }
    .stagger-1 { animation-delay: 0.1s; }
    .stagger-2 { animation-delay: 0.2s; }
    .stagger-3 { animation-delay: 0.3s; }
    .stagger-4 { animation-delay: 0.4s; }
    .stagger-5 { animation-delay: 0.5s; }
    .stagger-6 { animation-delay: 0.6s; }
    .stagger-7 { animation-delay: 0.7s; }
    .stagger-8 { animation-delay: 0.8s; }
    .stagger-9 { animation-delay: 0.9s; }
    .stagger-10 { animation-delay: 1.0s; }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }

    .form-label {
        display: flex; align-items: center; gap: 10px; margin-bottom: 12px;
        font-size: 13px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;
    }
    .label-icon { 
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 8px;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(139, 92, 246, 0.15));
        color: var(--primary); font-size: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .required { color: var(--danger); font-size: 16px; line-height: 0; margin-top: 5px; }
    .optional { color: var(--text-muted); font-size: 11px; text-transform: none; margin-left: auto; letter-spacing: 0; font-weight: 600; }

    .form-input, .form-select {
        width: 100%; padding: 16px 20px; border-radius: var(--radius-input);
        border: 2px solid rgba(99, 102, 241, 0.1);
        background: rgba(255, 255, 255, 0.8);
        color: var(--text-main); font-size: 15px; font-weight: 600;
        outline: none; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 5px rgba(0,0,0,0.01), inset 0 2px 4px rgba(0,0,0,0.02);
        backdrop-filter: blur(10px);
    }
    
    [data-theme="dark"] .form-input, [data-theme="dark"] .form-select {
        background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.05);
    }

    .form-input:focus, .form-select:focus {
        border-color: var(--primary); background: #ffffff;
        box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.2), 0 0 0 4px rgba(99, 102, 241, 0.1);
        transform: translateY(-2px);
    }
    
    [data-theme="dark"] .form-input:focus, [data-theme="dark"] .form-select:focus {
        background: rgba(15, 23, 42, 0.9); border-color: var(--primary);
    }

    .form-input[readonly] { background: rgba(255,255,255,0.3); color: var(--text-muted); cursor: not-allowed; border-color: var(--glass-border); }
    [data-theme="dark"] .form-input[readonly] { background: rgba(0,0,0,0.2); }
    .form-input::placeholder { color: #9ca3af; font-weight: 500; opacity: 0.8; }

    /* ================= EXTRAS (Select, Password) ================= */
    .select-wrapper, .password-wrapper { position: relative; }
    .select-arrow { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; font-size: 12px; }
    .form-select { appearance: none; cursor: pointer; padding-right: 40px; }

    .password-toggle {
        position: absolute; right: 15px; top: 50%; transform: translateY(-50%);
        background: rgba(99, 102, 241, 0.1); border: none; color: var(--primary);
        font-size: 16px; cursor: pointer; padding: 6px; border-radius: 8px; transition: all 0.3s;
    }
    .password-toggle:hover { background: var(--primary); color: white; transform: translateY(-50%) scale(1.1); }

    .field-hint, .password-hint { font-size: 12px; color: var(--text-muted); margin-top: 8px; font-weight: 600; }

    /* ================= ACTIONS ================= */
    .form-actions { display: flex; gap: 16px; padding-top: 30px; border-top: 1px solid var(--glass-border); }

    .btn-submit, .btn-cancel, .btn-back {
        padding: 16px 32px; border-radius: var(--radius-input); font-weight: 800; font-size: 15px;
        display: inline-flex; align-items: center; justify-content: center; gap: 10px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); cursor: pointer; text-decoration: none;
        flex: 1; border: none; letter-spacing: 0.5px;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white;
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25), inset 0 1px 1px rgba(255,255,255,0.3);
        position: relative; overflow: hidden;
    }
    .btn-submit::after {
        content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent);
        transform: skewX(-20deg); animation: shimmer 3s infinite;
    }
    @keyframes shimmer { 0% { left: -100%; } 15% { left: 200%; } 100% { left: 200%; } }
    .btn-submit:hover { box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4); transform: translateY(-3px) scale(1.02); filter: brightness(1.1); }

    .btn-cancel {
        background: rgba(255,255,255,0.5); color: var(--text-main); border: 1px solid var(--glass-border);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    [data-theme="dark"] .btn-cancel { background: rgba(0,0,0,0.2); color: var(--text-muted); }
    .btn-cancel:hover { background: rgba(255,255,255,0.9); transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
    
    .btn-back {
        background: rgba(255,255,255,0.5); color: var(--text-main); border: 1px solid var(--glass-border); max-width: max-content; margin: 0 auto;
    }
    .btn-back:hover { background: rgba(255,255,255,0.9); transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }

    /* ================= UNAUTHORIZED / ERRORS ================= */
    .error-alert {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
        border: 1px solid rgba(239, 68, 68, 0.3); border-radius: var(--radius-input);
        padding: 20px; margin-bottom: 30px; display: flex; gap: 15px; align-items: flex-start;
    }
    .error-icon { font-size: 24px; }
    .error-title { font-size: 15px; font-weight: 800; color: var(--danger); margin: 0 0 8px 0; }
    .error-list { margin: 0; padding-left: 20px; color: var(--danger); font-weight: 600; font-size: 13px; }
    .error-list li { margin-bottom: 5px; }
    
    .success-message {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);
        border: 1px solid rgba(16, 185, 129, 0.3); border-radius: var(--radius-input);
        padding: 20px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px;
        color: var(--success); font-weight: 800;
    }

    .unauthorized-card { text-align: center; }
    .unauthorized-icon { font-size: 64px; margin-bottom: 20px; animation: float 3s ease-in-out infinite; }
    .unauthorized-title { font-size: 28px; font-weight: 900; color: var(--danger); margin-bottom: 10px; }
    .unauthorized-message { color: var(--text-muted); font-size: 16px; margin-bottom: 30px; }
    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

    /* ================= RESPONSIVE ================= */
    @media (max-width: 768px) {
        .form-grid { grid-template-columns: 1fr; }
        .form-actions { flex-direction: column; }
        .form-card { padding: 25px; }
        .employee-info { flex-direction: column; text-align: center; }
    }
</style>

<div class="form-page">
    <!-- Animated Glass Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="form-container">
        {{-- Permission Check --}}
        @if (!auth()->user()->hasPermission('edit_employees'))
            <div class="unauthorized-container">
                <div class="unauthorized-card">
                    <div class="unauthorized-icon">🚫</div>
                    <h2 class="unauthorized-title">Access Restricted</h2>
                    <p class="unauthorized-message">Only administrators and HR managers can edit employee details.</p>
                    <a href="{{ route('employees.index') }}" class="btn-back">
                        ← Back to Employees
                    </a>
                </div>
            </div>
        @else
            <div class="form-card">
                <div class="form-header">
                    <div class="form-icon">✏️</div>
                    <div class="form-header-text">
                        <h1 class="form-title">Edit Employee</h1>
                        <p class="form-subtitle">Update details for {{ $employee->name }}</p>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="error-alert">
                        <span class="error-icon">⚠️</span>
                        <div class="error-content">
                            <h4 class="error-title">Please fix the following errors:</h4>
                            <ul class="error-list">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">
                        <span class="success-icon">✓</span>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="employee-form">
                    @csrf
                    @method('PUT')

                    <div class="employee-info">
                        <div class="employee-avatar">
                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                        </div>
                        <div class="employee-details">
                            <div class="employee-code">{{ $employee->employee_code }}</div>
                            <div class="employee-name">{{ $employee->name }}</div>
                            <div class="employee-email">{{ $employee->email }}</div>
                        </div>
                    </div>

                    <div class="form-grid">
                        {{-- Name Field --}}
                        <div class="form-group stagger-item stagger-1">
                            <label class="form-label">
                                <span class="label-icon">👤</span>
                                Full Name
                                <span class="required">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $employee->name) }}" required
                                class="form-input" placeholder="Enter employee name">
                        </div>

                        {{-- Email Field --}}
                        <div class="form-group stagger-item stagger-2">
                            <label class="form-label">
                                <span class="label-icon">📧</span>
                                Email Address
                                <span class="required">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}" required
                                class="form-input" placeholder="employee@company.com">
                        </div>

                        {{-- Password Field --}}
                        <div class="form-group stagger-item stagger-3">
                            <label class="form-label">
                                <span class="label-icon">🔒</span>
                                Password
                                <span class="optional">(Optional)</span>
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="password" class="form-input password-input"
                                    placeholder="Leave blank to keep current" id="password">
                                <button type="button" class="password-toggle" onclick="togglePassword()">
                                    👁️
                                </button>
                            </div>
                            <div class="password-hint">
                                Only enter if you want to change the password
                            </div>
                        </div>

                        {{-- Phone Field --}}
                        <div class="form-group stagger-item stagger-4">
                            <label class="form-label">
                                <span class="label-icon">📱</span>
                                Phone Number
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone', $employee->phone) }}"
                                class="form-input" placeholder="+1 234 567 8900">
                        </div>

                        {{-- Department Field --}}
                        @php
                            $deptOptions = ['Human Resources', 'Engineering', 'Sales', 'Marketing', 'Finance', 'Operations', 'Customer Support'];
                            $oldDept = old('department', $employee->department);
                            $isOther = $oldDept && !in_array($oldDept, $deptOptions);
                        @endphp
                        <div class="form-group stagger-item stagger-5">
                            <label class="form-label">
                                <span class="label-icon">🏢</span>
                                Department
                            </label>
                            <div class="select-wrapper">
                                <select id="departmentSelect" name="{{ $isOther ? 'department_ignore' : 'department' }}" class="form-select" onchange="toggleOtherDept()">
                                    <option value="">Select Department</option>
                                    @foreach($deptOptions as $option)
                                        <option value="{{ $option }}" {{ $oldDept == $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                    <option value="Other" {{ $isOther ? 'selected' : '' }}>Other (Specify)</option>
                                </select>
                                <span class="select-arrow">▼</span>
                            </div>
                            <div id="otherDeptGroup" style="display: {{ $isOther ? 'block' : 'none' }}; margin-top: 15px; animation: slideUpFade 0.3s ease;">
                                <input type="text" id="otherDeptInput" name="{{ $isOther ? 'department' : 'other_department_ignore' }}" class="form-input" placeholder="Please specify your department..." value="{{ $isOther ? $oldDept : '' }}">
                            </div>
                        </div>

                        {{-- Joining Date Field --}}
                        <div class="form-group stagger-item stagger-6">
                            <label class="form-label">
                                <span class="label-icon">📅</span>
                                Joining Date
                            </label>
                            <input type="date" name="joining_date"
                                value="{{ old('joining_date', $employee->joining_date) }}" class="form-input">
                        </div>

                        {{-- Employee Code Field --}}
                        <div class="form-group stagger-item stagger-7">
                            <label class="form-label">
                                <span class="label-icon">#️⃣</span>
                                Employee Code
                            </label>
                            <input type="text" name="employee_code"
                                value="{{ old('employee_code', $employee->employee_code) }}" class="form-input" readonly>
                            <div class="field-hint">Employee code cannot be changed</div>
                        </div>

                        {{-- Role Field --}}
                        @if (auth()->user()->hasPermission('manage_roles'))
                            <div class="form-group stagger-item stagger-8">
                                <label class="form-label">
                                    <span class="label-icon">🎭</span>
                                    Role
                                </label>
                                <div class="select-wrapper">
                                    <select name="role" class="form-select">
                                        <option value="staff"
                                            {{ old('role', $employee->role) == 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="hr"
                                            {{ old('role', $employee->role) == 'hr' ? 'selected' : '' }}>HR Manager
                                        </option>
                                        <option value="admin"
                                            {{ old('role', $employee->role) == 'admin' ? 'selected' : '' }}>Administrator
                                        </option>
                                    </select>
                                    <span class="select-arrow">▼</span>
                                </div>
                                <div class="field-hint">Admin only: Change user role</div>
                            </div>
                        @endif

                        {{-- Status Field --}}
                        @if (auth()->user()->hasPermission('edit_employees'))
                            <div class="form-group stagger-item stagger-9">
                                <label class="form-label">
                                    <span class="label-icon">📊</span>
                                    Status
                                </label>
                                <div class="select-wrapper">
                                    <select name="status" class="form-select">
                                        <option value="active"
                                            {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="on_leave"
                                            {{ old('status', $employee->status) == 'on_leave' ? 'selected' : '' }}>On Leave
                                        </option>
                                    </select>
                                    <span class="select-arrow">▼</span>
                                </div>
                                <div class="field-hint">Admin only: Change employment status</div>
                            </div>
                        @endif
                    </div>

                    <div class="form-actions stagger-item stagger-10">
                        <button type="submit" class="btn-submit">
                            <span class="btn-icon">💾</span>
                            Update Employee
                        </button>
                        <a href="{{ route('employees.index') }}" class="btn-cancel">
                            <span class="btn-icon">❌</span>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
    function toggleOtherDept() {
        const select = document.getElementById('departmentSelect');
        const otherGroup = document.getElementById('otherDeptGroup');
        const otherInput = document.getElementById('otherDeptInput');

        if (select && select.value === 'Other') {
            otherGroup.style.display = 'block';
            select.name = 'department_ignore';
            otherInput.name = 'department';
            otherInput.required = true;
            otherInput.focus();
        } else if (select) {
            otherGroup.style.display = 'none';
            select.name = 'department';
            otherInput.name = 'other_department_ignore';
            otherInput.required = false;
        }
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = '👁️';
        }
    }

    // Responsive handling for window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Any responsive adjustments if needed
        }, 250);
    });
</script>
@endsection