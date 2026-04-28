@extends('layouts.app')

@section('page-title', 'Employee Details - ' . $employee->name)

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
    .employee-page {
        min-height: calc(100vh - 70px);
        background: radial-gradient(circle at top right, rgba(99, 102, 241, 0.05) 0%, transparent 50%),
                    radial-gradient(circle at bottom left, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
        padding: 40px 24px;
        width: 100%;
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

    .container { max-width: 1200px; width: 100%; margin: 0 auto; position: relative; z-index: 1; }

    /* ================= HEADER ================= */
    .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px; }
    .page-title { font-size: 32px; font-weight: 900; background: linear-gradient(135deg, var(--text-main) 0%, var(--text-muted) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 5px; }
    .breadcrumb { font-size: 13px; color: var(--text-muted); font-weight: 600; display: flex; gap: 8px; }
    .breadcrumb a { color: var(--primary); text-decoration: none; transition: color 0.3s; }
    .breadcrumb a:hover { color: var(--primary-dark); }
    
    .btn-action {
        padding: 12px 24px; border-radius: var(--radius-input); font-weight: 700; font-size: 14px;
        display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; cursor: pointer; text-decoration: none;
    }
    .btn-back { background: var(--glass-bg); color: var(--text-main); border: 1px solid var(--glass-border); backdrop-filter: blur(10px); }
    .btn-back:hover { background: rgba(255,255,255,0.9); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    [data-theme="dark"] .btn-back:hover { background: rgba(0,0,0,0.5); }
    
    .btn-edit {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; border: none;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); position: relative; overflow: hidden;
    }
    .btn-edit::after { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent); transform: skewX(-20deg); animation: shimmer 3s infinite; }
    .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5); }

    /* ================= MAIN CARD ================= */
    .glass-card {
        background: linear-gradient(135deg, var(--glass-bg), rgba(255,255,255,0.2));
        backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border); border-radius: var(--radius-premium);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); overflow: hidden;
    }
    [data-theme="dark"] .glass-card { background: linear-gradient(135deg, var(--glass-bg), rgba(0,0,0,0.2)); }

    /* Profile Header */
    .profile-header { padding: 40px; border-bottom: 1px solid var(--glass-border); display: flex; align-items: center; gap: 30px; flex-wrap: wrap; position: relative; }
    .profile-header::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), transparent); z-index: 0; }
    .profile-avatar { width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 48px; font-weight: 900; color: white; box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4); flex-shrink: 0; position: relative; z-index: 1; border: 4px solid rgba(255,255,255,0.5); }
    [data-theme="dark"] .profile-avatar { border-color: rgba(0,0,0,0.5); }
    .profile-info { flex: 1; position: relative; z-index: 1; }
    .profile-name { font-size: 36px; font-weight: 900; color: var(--text-main); margin: 0 0 10px 0; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .profile-badge { background: var(--glass-bg); padding: 6px 16px; border-radius: 20px; font-size: 14px; border: 1px solid var(--glass-border); font-weight: 700; color: var(--primary); backdrop-filter: blur(10px); }
    .profile-status { padding: 6px 16px; border-radius: 20px; font-size: 14px; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; backdrop-filter: blur(10px); border: 1px solid transparent; }
    .status-active { background: rgba(16, 185, 129, 0.15); color: var(--success); border-color: rgba(16, 185, 129, 0.3); }
    .status-inactive { background: rgba(239, 68, 68, 0.15); color: var(--danger); border-color: rgba(239, 68, 68, 0.3); }
    .profile-contact { display: flex; gap: 25px; color: var(--text-muted); font-size: 15px; font-weight: 600; flex-wrap: wrap; margin-top: 15px; }
    .contact-item { display: flex; align-items: center; gap: 10px; background: var(--glass-bg); padding: 8px 16px; border-radius: 12px; border: 1px solid var(--glass-border); }

    /* Information Grid */
    .info-section { padding: 40px; }
    .section-title { font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 30px; display: flex; align-items: center; gap: 12px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; }
    .info-card { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-premium); padding: 30px; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    .info-card:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 20px 40px rgba(0,0,0,0.08); border-color: var(--primary); background: linear-gradient(135deg, var(--glass-bg), rgba(99, 102, 241, 0.05)); }
    [data-theme="dark"] .info-card:hover { box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    
    .info-header { display: flex; align-items: center; gap: 12px; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--glass-border); }
    .info-icon { font-size: 24px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .info-title { font-size: 18px; font-weight: 800; color: var(--text-main); }
    .info-row { display: flex; flex-direction: column; gap: 6px; margin-bottom: 20px; }
    .info-row:last-child { margin-bottom: 0; }
    .info-label { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: var(--text-muted); }
    .info-value { font-size: 16px; font-weight: 600; color: var(--text-main); word-break: break-word; }

    /* Quick Actions */
    .quick-actions-section { padding: 40px; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.02); }
    [data-theme="dark"] .quick-actions-section { background: rgba(255,255,255,0.02); }
    .quick-actions-grid { display: flex; gap: 30px; flex-wrap: wrap; }
    .email-form { flex: 2; min-width: 300px; background: var(--glass-bg); padding: 30px; border-radius: var(--radius-premium); border: 1px solid var(--glass-border); box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    
    .form-group { margin-bottom: 25px; }
    .form-label { display: block; margin-bottom: 12px; font-weight: 700; color: var(--text-main); font-size: 14px; }
    .form-select { width: 100%; padding: 15px; border-radius: var(--radius-input); background: var(--glass-bg); border: 2px solid var(--glass-border); color: var(--text-main); font-weight: 600; font-size: 15px; appearance: none; transition: all 0.3s; outline: none; }
    .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
    
    .btn-email { width: 100%; display: flex; align-items: center; gap: 15px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); padding: 18px 25px; border-radius: var(--radius-input); border: none; color: white; cursor: pointer; transition: all 0.4s; position: relative; overflow: hidden; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); }
    .btn-email::after { content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%; background: linear-gradient(to right, transparent, rgba(255,255,255,0.4), transparent); transform: skewX(-20deg); animation: shimmer 3s infinite; }
    .btn-email:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(99, 102, 241, 0.5); filter: brightness(1.1); }
    .btn-email-content { text-align: left; }
    .btn-email-title { font-weight: 800; font-size: 16px; margin-bottom: 4px; }
    .btn-email-desc { font-size: 13px; opacity: 0.9; font-weight: 500; }
    
    .action-buttons { flex: 1; min-width: 250px; display: flex; flex-direction: column; gap: 20px; }
    .action-btn { background: var(--glass-bg); border: 2px solid var(--glass-border); padding: 20px; border-radius: var(--radius-input); display: flex; align-items: center; gap: 15px; color: var(--text-main); text-decoration: none; transition: all 0.4s; cursor: pointer; width: 100%; text-align: left; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
    .action-btn:hover { background: rgba(255,255,255,0.5); transform: translateX(5px) scale(1.02); box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    [data-theme="dark"] .action-btn:hover { background: rgba(0,0,0,0.3); }
    .action-btn.call:hover { border-color: var(--success); }
    .action-btn.delete { color: var(--danger); }
    .action-btn.delete:hover { border-color: var(--danger); background: rgba(239, 68, 68, 0.1); }
    
    .action-content { flex: 1; }
    .action-title { font-weight: 800; font-size: 15px; margin-bottom: 4px; }
    .action-desc { font-size: 13px; color: var(--text-muted); font-weight: 500; }

    /* Modals & Messages */
    .message-container { margin-top: 20px; }
    .message-success { background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), transparent); border: 1px solid rgba(16, 185, 129, 0.3); color: var(--success); padding: 15px 20px; border-radius: var(--radius-input); display: flex; align-items: center; gap: 12px; font-weight: 700; }
    .message-error { background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), transparent); border: 1px solid rgba(239, 68, 68, 0.3); color: var(--danger); padding: 15px 20px; border-radius: var(--radius-input); display: flex; align-items: center; gap: 12px; font-weight: 700; }
    .loading-indicator { color: var(--primary); text-align: center; margin-top: 20px; font-weight: 800; display: none; }
    
    .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); z-index: 1000; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.active { display: flex; animation: fadeIn 0.3s ease; }
    .modal-content { background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-premium); width: 100%; max-width: 600px; padding: 35px; box-shadow: 0 30px 60px rgba(0,0,0,0.3); transform: scale(0.95); animation: scaleUp 0.3s forwards cubic-bezier(0.16, 1, 0.3, 1); backdrop-filter: blur(25px); }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .modal-title { font-size: 24px; font-weight: 900; color: var(--text-main); }
    .modal-close { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); width: 40px; height: 40px; border-radius: 50%; font-size: 20px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; }
    .modal-close:hover { background: var(--danger); color: white; border-color: var(--danger); transform: rotate(90deg); }
    .modal-input, .modal-textarea { width: 100%; background: var(--glass-bg); border: 2px solid var(--glass-border); color: var(--text-main); padding: 16px; border-radius: var(--radius-input); margin-bottom: 25px; font-family: inherit; font-size: 15px; font-weight: 500; transition: all 0.3s; outline: none; }
    .modal-input:focus, .modal-textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15); }
    .modal-footer { display: flex; justify-content: flex-end; gap: 15px; }
    .modal-btn { padding: 14px 28px; border-radius: var(--radius-input); font-weight: 800; font-size: 15px; cursor: pointer; transition: all 0.3s; border: none; }
    .modal-btn.cancel { background: var(--glass-bg); border: 1px solid var(--glass-border); color: var(--text-main); }
    .modal-btn.cancel:hover { background: rgba(255,255,255,0.5); }
    [data-theme="dark"] .modal-btn.cancel:hover { background: rgba(0,0,0,0.5); }
    .modal-btn.submit { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3); }
    .modal-btn.submit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5); }
    
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes scaleUp { from { transform: scale(0.95) translateY(20px); opacity: 0; } to { transform: scale(1) translateY(0); opacity: 1; } }
    @keyframes shimmer { 0% { left: -100%; } 15% { left: 200%; } 100% { left: 200%; } }

    /* Stagger Animations */
    .stagger-item { opacity: 0; transform: translateY(30px); animation: slideUpFade 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .stagger-1 { animation-delay: 0.1s; } .stagger-2 { animation-delay: 0.2s; }
    .stagger-3 { animation-delay: 0.3s; } .stagger-4 { animation-delay: 0.4s; }
    .stagger-5 { animation-delay: 0.5s; } .stagger-6 { animation-delay: 0.6s; }
    @keyframes slideUpFade { to { opacity: 1; transform: translateY(0); } }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-header { flex-direction: column; text-align: center; }
        .profile-name { justify-content: center; }
        .profile-contact { justify-content: center; }
        .info-grid { grid-template-columns: 1fr; }
        .quick-actions-grid { flex-direction: column; }
    }
</style>

<div class="employee-page">
    <!-- Animated Glass Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="container">
        <!-- Header -->
        <div class="header-section stagger-item stagger-1">
            <div class="header-left">
                <h1 class="page-title">Employee Overview</h1>
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span>•</span>
                    <a href="{{ route('employees.index') }}">Employees</a>
                    <span>•</span>
                    <span>Details</span>
                </div>
            </div>
            <div class="header-actions">
                <a href="{{ route('employees.index') }}" class="btn-action btn-back">
                    <span>←</span> Back
                </a>
                @if (auth()->user()->hasPermission('edit_employees'))
                    <a href="{{ route('employees.edit', $employee->id) }}" class="btn-action btn-edit">
                        <span>✏️</span> Edit Profile
                    </a>
                @endif
            </div>
        </div>

        <!-- Main Card -->
        <div class="glass-card stagger-item stagger-2">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
                <div class="profile-info">
                    <div class="profile-name">
                        {{ $employee->name }}
                        <span class="profile-badge">#{{ $employee->employee_code }}</span>
                        <span class="profile-status {{ $employee->status == 1 ? 'status-active' : 'status-inactive' }}">
                            ● {{ $employee->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="profile-contact">
                        <div class="contact-item">
                            <span>📧</span> {{ $employee->email }}
                        </div>
                        @if ($employee->phone)
                            <div class="contact-item">
                                <span>📱</span> {{ $employee->phone }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Information Grid -->
            <div class="info-section">
                <h3 class="section-title">
                    <span>📋</span> Personal Information
                </h3>
                <div class="info-grid">
                    <!-- Personal Details Card -->
                    <div class="info-card stagger-item stagger-3">
                        <div class="info-header">
                            <span class="info-icon">👤</span>
                            <span class="info-title">Personal Details</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Full Name</span>
                            <span class="info-value">{{ $employee->name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Employee Code</span>
                            <span class="info-value">{{ $employee->employee_code }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status</span>
                            <span class="info-value" style="color: {{ $employee->status == 1 ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $employee->status == 1 ? 'Active Employee' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    <!-- Contact Details Card -->
                    <div class="info-card stagger-item stagger-4">
                        <div class="info-header">
                            <span class="info-icon">📞</span>
                            <span class="info-title">Contact Information</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email Address</span>
                            <span class="info-value">{{ $employee->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone Number</span>
                            <span class="info-value">{{ $employee->phone ?? 'Not provided' }}</span>
                        </div>
                    </div>

                    <!-- Work Details Card -->
                    <div class="info-card stagger-item stagger-5">
                        <div class="info-header">
                            <span class="info-icon">🏢</span>
                            <span class="info-title">Work Information</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Department</span>
                            <span class="info-value">{{ $employee->department ?? 'Not assigned' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Role</span>
                            <span class="info-value">{{ ucfirst($employee->role ?? 'staff') }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Joining Date</span>
                            <span class="info-value">
                                {{ $employee->joining_date ? \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') : 'Not set' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="quick-actions-section">
                <h3 class="section-title">
                    <span>⚡</span> Quick Actions
                </h3>
                <div class="quick-actions-grid stagger-item stagger-6">
                    <!-- Send Email Form -->
                    <div class="email-form">
                        <form id="sendEmailForm" action="{{ route('employee.send.email', $employee->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Email Template:</label>
                                <select id="emailTemplate" name="template" onchange="updateEmailForm()" class="form-select">
                                    <option value="general">General Inquiry</option>
                                    <option value="meeting">Meeting Request</option>
                                    <option value="welcome">Welcome Email</option>
                                    <option value="followup">Follow-up</option>
                                    <option value="status">Status Update</option>
                                    <option value="custom">Custom Message</option>
                                </select>
                            </div>

                            <!-- Hidden fields for custom message -->
                            <input type="hidden" id="emailSubject" name="subject" value="">
                            <input type="hidden" id="emailBody" name="body" value="">

                            <button type="button" onclick="prepareAndSendEmail()" class="btn-email">
                                <span style="font-size: 24px;">📧</span>
                                <div class="btn-email-content">
                                    <div class="btn-email-title" id="emailButtonText">Send General Email</div>
                                    <div class="btn-email-desc" id="emailButtonDesc">Send via Laravel Mail</div>
                                </div>
                            </button>

                            <div id="loadingIndicator" class="loading-indicator">
                                ⏳ Sending email safely...
                            </div>
                            <div id="messageContainer" class="message-container"></div>
                        </form>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if ($employee->phone)
                            <a href="tel:{{ $employee->phone }}" class="action-btn call">
                                <span style="font-size: 24px;">📱</span>
                                <div class="action-content">
                                    <div class="action-title">Make a Call</div>
                                    <div class="action-desc">Call {{ $employee->phone }}</div>
                                </div>
                            </a>
                        @endif

                        @if (auth()->user()->hasPermission('delete_employees'))
                            <button onclick="confirmDelete()" class="action-btn delete">
                                <span style="font-size: 24px;">🗑️</span>
                                <div class="action-content">
                                    <div class="action-title">Delete Employee</div>
                                    <div class="action-desc">Remove permanently</div>
                                </div>
                            </button>
                            <form id="deleteForm" action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Message Modal -->
<div id="customMessageModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Write Custom Message</h3>
            <button onclick="closeCustomMessageModal()" class="modal-close">×</button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">Subject:</label>
                <input type="text" id="customSubject" class="modal-input"
                    value="Regarding Employee: {{ $employee->name }} ({{ $employee->employee_code }})">
            </div>

            <div class="form-group">
                <label class="form-label">Message:</label>
                <textarea id="customBody" rows="8" class="modal-textarea">Dear {{ $employee->name }},

I hope this email finds you well.

Regarding your employment details at our company, I wanted to discuss a few important matters with you.

Please let me know your availability for a brief meeting next week.

Best regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}</textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button onclick="closeCustomMessageModal()" class="modal-btn cancel">Cancel</button>
            <button onclick="sendCustomEmail()" class="modal-btn submit">Send Email</button>
        </div>
    </div>
</div>

<script>
    // Email Templates
    const emailTemplates = {
        general: {
            name: "General Inquiry",
            description: "Send via Laravel Mail",
            subject: "Regarding Employee: {{ $employee->name }} ({{ $employee->employee_code }})",
            body: `Dear {{ $employee->name }},

I hope this email finds you well.

Regarding your employment details at our company, I wanted to discuss a few important matters with you.

Please let me know your availability for a brief meeting next week.

Best regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        meeting: {
            name: "Meeting Request",
            description: "Send via Laravel Mail",
            subject: "Meeting Request - {{ $employee->name }} ({{ $employee->employee_code }})",
            body: `Dear {{ $employee->name }},

I hope you are doing well.

I would like to schedule a meeting with you to discuss your recent work and future projects. Please let me know your availability for the coming week.

Looking forward to our discussion.

Best regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        welcome: {
            name: "Welcome Email",
            description: "Send via Laravel Mail",
            subject: "Welcome to the Team - {{ $employee->name }}",
            body: `Dear {{ $employee->name }},

Welcome to our team! We are excited to have you on board as part of the {{ $employee->department ?? 'our' }} team.

Your employee code is: {{ $employee->employee_code }}

If you have any questions or need assistance, please don't hesitate to reach out.

Warm regards,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        followup: {
            name: "Follow-up",
            description: "Send via Laravel Mail",
            subject: "Follow-up: {{ $employee->name }}",
            body: `Hi {{ $employee->name }},

Just following up on our previous conversation. Please provide an update when you get a chance.

Thank you.

Best,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        status: {
            name: "Status Update",
            description: "Send via Laravel Mail",
            subject: "Status Update Request - {{ $employee->name }}",
            body: `Hello {{ $employee->name }},

Could you please provide a status update on your current projects?

Please share your progress and any blockers you might be facing.

Thanks,
{{ auth()->user()->name ?? 'Your Name' }}
{{ auth()->user()->role ? '(' . ucfirst(auth()->user()->role) . ')' : '' }}`
        },
        custom: {
            name: "Custom Message",
            description: "Write your own message",
            subject: "",
            body: ""
        }
    };

    let selectedTemplate = 'general';

    function updateEmailForm() {
        const templateSelect = document.getElementById('emailTemplate');
        selectedTemplate = templateSelect.value;
        const template = emailTemplates[selectedTemplate];

        document.getElementById('emailButtonText').textContent = `Send ${template.name}`;
        document.getElementById('emailButtonDesc').textContent = template.description;

        // Update hidden fields for non-custom templates
        if (selectedTemplate !== 'custom') {
            document.getElementById('emailSubject').value = template.subject;
            document.getElementById('emailBody').value = template.body;
        }
    }

    function prepareAndSendEmail() {
        if (selectedTemplate === 'custom') {
            openCustomMessageModal();
        } else {
            sendEmail();
        }
    }

    function sendEmail() {
        const form = document.getElementById('sendEmailForm');
        const submitButton = form.querySelector('button[type="button"]');
        const loadingIndicator = document.getElementById('loadingIndicator');
        const messageContainer = document.getElementById('messageContainer');

        // Show loading
        submitButton.disabled = true;
        submitButton.style.opacity = '0.7';
        loadingIndicator.style.display = 'block';
        messageContainer.innerHTML = '';

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                subject: document.getElementById('emailSubject').value,
                body: document.getElementById('emailBody').value,
                template: selectedTemplate
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            loadingIndicator.style.display = 'none';

            // Show message
            if (data.success) {
                messageContainer.innerHTML = `
                    <div class="message-success">
                        <span>✅</span>
                        <div>
                            <div>${data.message}</div>
                            <div style="font-size: 12px; font-weight: normal;">Email sent to {{ $employee->email }}</div>
                        </div>
                    </div>
                `;
            } else {
                messageContainer.innerHTML = `
                    <div class="message-error">
                        <span>❌</span>
                        <div>
                            <div>${data.message || 'Failed to send email'}</div>
                            <div style="font-size: 12px; font-weight: normal;">Please try again</div>
                        </div>
                    </div>
                `;
            }

            // Clear message after 5 seconds
            setTimeout(() => {
                messageContainer.innerHTML = '';
            }, 5000);
        })
        .catch(error => {
            submitButton.disabled = false;
            submitButton.style.opacity = '1';
            loadingIndicator.style.display = 'none';

            messageContainer.innerHTML = `
                <div class="message-error">
                    <span>❌</span>
                    <div>
                        <div>Network error occurred</div>
                        <div style="font-size: 12px; font-weight: normal;">Please check your connection</div>
                    </div>
                </div>
            `;
        });
    }

    function openCustomMessageModal() {
        document.getElementById('customMessageModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeCustomMessageModal() {
        document.getElementById('customMessageModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function sendCustomEmail() {
        const subject = document.getElementById('customSubject').value;
        const body = document.getElementById('customBody').value;

        // Update hidden fields
        document.getElementById('emailSubject').value = subject;
        document.getElementById('emailBody').value = body;

        closeCustomMessageModal();
        sendEmail();
    }

    function confirmDelete() {
        if (confirm('Are you sure you want to delete this employee? This action cannot be undone.')) {
            document.getElementById('deleteForm').submit();
        }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateEmailForm();

        // Close modal on outside click
        document.getElementById('customMessageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCustomMessageModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCustomMessageModal();
            }
        });
    });
</script>
@endsection