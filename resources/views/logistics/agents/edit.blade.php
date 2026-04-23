{{-- resources/views/logistics/agents/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Agent - ' . $agent->name)

@section('content')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        .edit-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .edit-card {
            background: #ffffff;
            border-radius: 30px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .edit-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .edit-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            position: relative;
            z-index: 1;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
            min-width: 280px;
        }

        .header-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            flex-shrink: 0;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .header-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.available {
            background: #10b981;
            color: white;
        }

        .status-badge.busy {
            background: #f59e0b;
            color: white;
        }

        .status-badge.offline {
            background: #6b7280;
            color: white;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .header-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-btn:hover {
            background: white;
            color: #1e293b;
            transform: translateY(-2px);
        }

        .form-section {
            padding: clamp(1.5rem, 4vw, 2rem);
            border-bottom: 1px solid #e5e7eb;
            background: white;
            transition: all 0.3s ease;
            position: relative;
        }

        .form-section:hover {
            background: #f8fafc;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .section-subtitle {
            color: #64748b;
            font-size: 0.9rem;
            margin: 0.25rem 0 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        .form-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-label i {
            color: #667eea;
        }

        .required-star {
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 14px;
            font-size: 0.95rem;
            color: #1e293b;
            background: white;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control[readonly] {
            background: #f1f5f9;
        }

        .map-preview {
            height: 300px;
            border-radius: 16px;
            margin-top: 1rem;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .map-preview:hover {
            border-color: #667eea;
        }

        .location-detect-btn {
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .location-detect-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .performance-stats {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 1.5rem;
            margin-top: 1rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
        }

        .performance-item {
            text-align: center;
            background: white;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
        }

        .performance-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .performance-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .service-areas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .service-area-item {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .service-area-item:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-2px);
        }

        .service-area-item.selected {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            border-color: #667eea;
            border-width: 2px;
        }

        .checkbox-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .checkbox-label {
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            flex: 1;
        }

        .current-doc {
            margin-top: 0.5rem;
            padding: 0.75rem 1rem;
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .current-doc:hover {
            border-color: #667eea;
            background: white;
        }

        .current-doc a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .delete-doc {
            color: #ef4444;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .delete-doc:hover {
            background: #fee2e2;
            transform: scale(1.1);
        }

        .file-upload {
            border: 2px dashed #e5e7eb;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8fafc;
            margin-top: 0.5rem;
        }

        .file-upload:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-2px);
        }

        .file-upload input {
            display: none;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .file-preview {
            margin-top: 0.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .file-preview-item {
            background: linear-gradient(135deg, #667eea10, #764ba210);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }

        .file-preview-remove {
            color: #ef4444;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0 0.25rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
            padding: 1.5rem clamp(1.5rem, 4vw, 2rem);
            background: #f8fafc;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #e5e7eb;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(239, 68, 68, 0.3);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-error {
            background: #fee2e2;
            border-left-color: #ef4444;
            color: #991b1b;
        }

        .alert-success {
            background: #d1fae5;
            border-left-color: #10b981;
            color: #065f46;
        }

        .toast {
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            display: none;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast.success {
            border-left-color: #10b981;
        }

        .toast.error {
            border-left-color: #ef4444;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            z-index: 11000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1.5rem;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e5e7eb;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 768px) {
            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .service-areas-grid {
                grid-template-columns: 1fr;
            }

            .map-preview {
                height: 250px;
            }
        }
    </style>

    <div class="edit-page">
        <div class="container">
            <div class="edit-card">
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Updating agent data...</div>
                </div>

                <div class="edit-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-avatar">{{ substr($agent->name, 0, 1) }}</div>
                            <div>
                                <div class="header-title">
                                    {{ $agent->name }}
                                    <span class="status-badge {{ $agent->status }}">{{ ucfirst($agent->status) }}</span>
                                </div>
                                <div class="header-subtitle">
                                    <span><i class="fas fa-id-card"></i>
                                        {{ $agent->agent_code ?? 'AG' . str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span>•</span>
                                    <span><i class="fas fa-phone"></i> {{ $agent->phone }}</span>
                                    @if ($agent->email)
                                        <span>•</span>
                                        <span><i class="fas fa-envelope"></i> {{ $agent->email }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('logistics.agents.show', $agent->id) }}" class="header-btn"><i
                                    class="fas fa-arrow-left"></i> Back to Profile</a>
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="form-section" style="padding-bottom: 0;">
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <ul style="margin:0; padding-left:1rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="form-section" style="padding-bottom: 0;">
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    </div>
                @endif

                <div class="form-section" style="padding-bottom: 0;">
                    <div class="performance-stats">
                        <div class="performance-item">
                            <div class="performance-value">{{ $agent->total_deliveries ?? 0 }}</div>
                            <div class="performance-label">Total Deliveries</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-value">{{ number_format($agent->rating ?? 4.5, 1) }} ★</div>
                            <div class="performance-label">Rating</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-value">{{ $agent->successful_deliveries ?? 0 }}</div>
                            <div class="performance-label">Successful</div>
                        </div>
                        <div class="performance-item">
                            <div class="performance-value">
                                {{ $agent->total_deliveries > 0 ? round(($agent->successful_deliveries / $agent->total_deliveries) * 100) : 0 }}%
                            </div>
                            <div class="performance-label">Success Rate</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logistics.agents.update', $agent->id) }}"
                    enctype="multipart/form-data" id="agentForm">
                    @csrf
                    @method('PUT')

                    {{-- Personal Information --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <h3 class="section-title">Personal Information</h3>
                                <p class="section-subtitle">Update agent's personal details</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> Full Name <span
                                        class="required-star">*</span></label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $agent->name) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i> Phone Number <span
                                        class="required-star">*</span></label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $agent->phone) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $agent->email) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone-alt"></i> Alternate Phone</label>
                                <input type="text" name="alternate_phone" class="form-control"
                                    value="{{ old('alternate_phone', $agent->alternate_phone) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Location Information with Google Maps --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h3 class="section-title">Location Information</h3>
                                <p class="section-subtitle">Update agent's current location</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-search"></i> Search Address / Current
                                Location</label>
                            <input type="text" id="location-search" class="form-control"
                                placeholder="Search for address or drop pin on map" autocomplete="off"
                                value="{{ $agent->current_location }}">
                            <button type="button" id="detect-location" class="location-detect-btn">
                                <i class="fas fa-location-dot"></i> Update from Current Location
                            </button>
                        </div>

                        <div id="map-preview" class="map-preview"></div>

                        <input type="hidden" name="current_latitude" id="latitude"
                            value="{{ old('current_latitude', $agent->current_latitude) }}">
                        <input type="hidden" name="current_longitude" id="longitude"
                            value="{{ old('current_longitude', $agent->current_longitude) }}">
                        <input type="hidden" name="current_location" id="current_location"
                            value="{{ old('current_location', $agent->current_location) }}">

                        <div class="form-grid" style="margin-top: 1rem;">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-city"></i> City</label>
                                <input type="text" name="city" id="city" class="form-control"
                                    value="{{ old('city', $agent->city) }}" readonly style="background:#f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-map"></i> State</label>
                                <input type="text" name="state" id="state" class="form-control"
                                    value="{{ old('state', $agent->state) }}" readonly style="background:#f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-mail-bulk"></i> Pincode</label>
                                <input type="text" name="pincode" id="pincode" class="form-control"
                                    value="{{ old('pincode', $agent->pincode) }}" readonly style="background:#f8fafc;">
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Information --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-truck"></i></div>
                            <div>
                                <h3 class="section-title">Vehicle Information</h3>
                                <p class="section-subtitle">Update vehicle and license details</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-motorcycle"></i> Vehicle Type</label>
                                <select name="vehicle_type" class="form-control">
                                    <option value="">Select Vehicle</option>
                                    <option value="bike"
                                        {{ old('vehicle_type', $agent->vehicle_type) == 'bike' ? 'selected' : '' }}>🏍️
                                        Bike</option>
                                    <option value="scooter"
                                        {{ old('vehicle_type', $agent->vehicle_type) == 'scooter' ? 'selected' : '' }}>🛵
                                        Scooter</option>
                                    <option value="van"
                                        {{ old('vehicle_type', $agent->vehicle_type) == 'van' ? 'selected' : '' }}>🚐 Van
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-hashtag"></i> Vehicle Number</label>
                                <input type="text" name="vehicle_number" class="form-control"
                                    value="{{ old('vehicle_number', $agent->vehicle_number) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> License Number</label>
                                <input type="text" name="license_number" class="form-control"
                                    value="{{ old('license_number', $agent->license_number) }}">
                            </div>
                        </div>
                    </div>

                    {{-- Employment Details --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                            <div>
                                <h3 class="section-title">Employment Details</h3>
                                <p class="section-subtitle">Update employment and compensation</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-briefcase"></i> Employment Type</label>
                                <select name="employment_type" class="form-control">
                                    <option value="full_time"
                                        {{ old('employment_type', $agent->employment_type) == 'full_time' ? 'selected' : '' }}>
                                        Full Time</option>
                                    <option value="part_time"
                                        {{ old('employment_type', $agent->employment_type) == 'part_time' ? 'selected' : '' }}>
                                        Part Time</option>
                                    <option value="contract"
                                        {{ old('employment_type', $agent->employment_type) == 'contract' ? 'selected' : '' }}>
                                        Contract</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-calendar-alt"></i> Joining Date</label>
                                <input type="date" name="joining_date" class="form-control"
                                    value="{{ old('joining_date', $agent->joining_date ? $agent->joining_date->format('Y-m-d') : '') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-rupee-sign"></i> Salary (₹)</label>
                                <input type="number" name="salary" class="form-control"
                                    value="{{ old('salary', $agent->salary) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    {{-- Service Areas (All cities from seeder) --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-map-marked-alt"></i></div>
                            <div>
                                <h3 class="section-title">Service Areas</h3>
                                <p class="section-subtitle">Select areas where agent can deliver</p>
                            </div>
                        </div>

                        @php
                            $serviceAreas = $agent->service_areas
                                ? (is_array($agent->service_areas)
                                    ? $agent->service_areas
                                    : json_decode($agent->service_areas, true))
                                : [];
                            $oldAreas = old('service_areas', $serviceAreas ?? []);
                            $allCities = [
                                'Ahmedabad',
                                'Gandhinagar',
                                'Surat',
                                'Vadodara',
                                'Rajkot',
                                'Bhavnagar',
                                'Anand',
                                'Jamnagar',
                                'Junagadh',
                            ];
                        @endphp

                        <div class="service-areas-grid" id="serviceAreasContainer">
                            @foreach ($allCities as $city)
                                <div class="service-area-item" data-area="{{ $city }}">
                                    <input type="checkbox" name="service_areas[]" value="{{ $city }}"
                                        class="checkbox-input" id="area_{{ strtolower(str_replace(' ', '_', $city)) }}"
                                        {{ in_array($city, $oldAreas) ? 'checked' : '' }}>
                                    <label for="area_{{ strtolower(str_replace(' ', '_', $city)) }}"
                                        class="checkbox-label">{{ $city }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group" style="margin-top: 1rem;">
                            <label class="form-label"><i class="fas fa-plus-circle"></i> Additional Areas</label>
                            <input type="text" name="additional_areas" class="form-control"
                                value="{{ old('additional_areas', implode(', ', array_diff($oldAreas, $allCities))) }}"
                                placeholder="Enter additional areas separated by commas">
                        </div>
                    </div>

                    {{-- Documents --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-file-alt"></i></div>
                            <div>
                                <h3 class="section-title">Documents</h3>
                                <p class="section-subtitle">Upload or replace agent documents</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> Aadhar Card</label>
                                @if ($agent->aadhar_card)
                                    <div class="current-doc">
                                        <a href="{{ Storage::url($agent->aadhar_card) }}" target="_blank"><i
                                                class="fas fa-file-pdf"></i> View Current Aadhar</a>
                                        <span class="delete-doc" onclick="markForDeletion('aadhar')"><i
                                                class="fas fa-trash-alt"></i></span>
                                        <input type="hidden" name="delete_aadhar" id="delete_aadhar" value="0">
                                    </div>
                                @endif
                                <div class="file-upload" onclick="document.getElementById('aadhar_input').click()">
                                    <input type="file" name="aadhar_card" id="aadhar_input" accept="image/*,.pdf"
                                        onchange="previewFile(this, 'aadhar_preview')">
                                    <div class="file-upload-label">
                                        <span class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
                                        <span>{{ $agent->aadhar_card ? 'Replace Aadhar Card' : 'Upload Aadhar Card' }}</span>
                                    </div>
                                </div>
                                <div id="aadhar_preview" class="file-preview"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> Driving License</label>
                                @if ($agent->driving_license)
                                    <div class="current-doc">
                                        <a href="{{ Storage::url($agent->driving_license) }}" target="_blank"><i
                                                class="fas fa-file-pdf"></i> View Current License</a>
                                        <span class="delete-doc" onclick="markForDeletion('license')"><i
                                                class="fas fa-trash-alt"></i></span>
                                        <input type="hidden" name="delete_license" id="delete_license" value="0">
                                    </div>
                                @endif
                                <div class="file-upload" onclick="document.getElementById('license_input').click()">
                                    <input type="file" name="driving_license" id="license_input"
                                        accept="image/*,.pdf" onchange="previewFile(this, 'license_preview')">
                                    <div class="file-upload-label">
                                        <span class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
                                        <span>{{ $agent->driving_license ? 'Replace License' : 'Upload Driving License' }}</span>
                                    </div>
                                </div>
                                <div id="license_preview" class="file-preview"></div>
                            </div>

                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-camera"></i> Profile Photo</label>
                                @if ($agent->photo)
                                    <div class="current-doc">
                                        <a href="{{ Storage::url($agent->photo) }}" target="_blank"><i
                                                class="fas fa-image"></i> View Current Photo</a>
                                        <span class="delete-doc" onclick="markForDeletion('photo')"><i
                                                class="fas fa-trash-alt"></i></span>
                                        <input type="hidden" name="delete_photo" id="delete_photo" value="0">
                                    </div>
                                @endif
                                <div class="file-upload" onclick="document.getElementById('photo_input').click()">
                                    <input type="file" name="photo" id="photo_input" accept="image/*"
                                        onchange="previewFile(this, 'photo_preview')">
                                    <div class="file-upload-label">
                                        <span class="file-upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
                                        <span>{{ $agent->photo ? 'Replace Photo' : 'Upload Photo' }}</span>
                                    </div>
                                </div>
                                <div id="photo_preview" class="file-preview"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-toggle-on"></i></div>
                            <div>
                                <h3 class="section-title">Status & Availability</h3>
                                <p class="section-subtitle">Update agent status and activity</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-toggle-on"></i> Current Status</label>
                                <select name="status" class="form-control">
                                    <option value="available"
                                        {{ old('status', $agent->status) == 'available' ? 'selected' : '' }}>✅ Available -
                                        Ready for deliveries</option>
                                    <option value="busy"
                                        {{ old('status', $agent->status) == 'busy' ? 'selected' : '' }}>🚚 Busy - Currently
                                        delivering</option>
                                    <option value="offline"
                                        {{ old('status', $agent->status) == 'offline' ? 'selected' : '' }}>⭕ Offline - Not
                                        available</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="checkbox-group"
                                    style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem;">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                        class="checkbox-input" {{ old('is_active', $agent->is_active) ? 'checked' : '' }}>
                                    <label for="is_active" class="checkbox-label">Agent is active and can receive
                                        deliveries</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('logistics.agents.show', $agent->id) }}" class="btn btn-secondary"><i
                                class="fas fa-times"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Update
                            Agent</button>
                    </div>
                </form>

                <div class="action-buttons"
                    style="justify-content: flex-start; padding-top: 0; border-top: 2px dashed #e5e7eb;">
                    <form method="POST" action="{{ route('logistics.agents.destroy', $agent->id) }}"
                        onsubmit="return confirm('⚠️ Are you sure you want to delete this agent?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Delete Agent
                            Permanently</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"
        async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        let map, marker, geocoder, autocompleteService;

        function initMap() {
            const defaultLat = 22.524768;
            const defaultLng = 72.955568;
            const existingLat = parseFloat(document.getElementById('latitude').value);
            const existingLng = parseFloat(document.getElementById('longitude').value);
            const centerLat = existingLat && existingLat !== 0 ? existingLat : defaultLat;
            const centerLng = existingLng && existingLng !== 0 ? existingLng : defaultLng;

            map = new google.maps.Map(document.getElementById('map-preview'), {
                center: {
                    lat: centerLat,
                    lng: centerLng
                },
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: [{
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{
                        visibility: 'off'
                    }]
                }]
            });

            geocoder = new google.maps.Geocoder();
            autocompleteService = new google.maps.places.AutocompleteService();

            if (existingLat && existingLat !== 0) {
                marker = new google.maps.Marker({
                    position: {
                        lat: existingLat,
                        lng: existingLng
                    },
                    map: map,
                    title: 'Agent Location',
                    draggable: true,
                    icon: {
                        url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        scaledSize: new google.maps.Size(40, 40)
                    }
                });
                google.maps.event.addListener(marker, 'dragend', function() {
                    updateLocationFromLatLng(marker.getPosition().lat(), marker.getPosition().lng());
                });
            } else {
                map.addListener('click', function(e) {
                    if (marker) marker.setMap(null);
                    marker = new google.maps.Marker({
                        position: e.latLng,
                        map: map,
                        title: 'Agent Location',
                        draggable: true,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });
                    updateLocationFromLatLng(e.latLng.lat(), e.latLng.lng());
                    google.maps.event.addListener(marker, 'dragend', function() {
                        updateLocationFromLatLng(marker.getPosition().lat(), marker.getPosition().lng());
                    });
                });
            }

            setupPlaceAutocomplete();
        }

        function setupPlaceAutocomplete() {
            const input = document.getElementById('location-search');
            let debounceTimer;

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                if (query.length < 3) return;

                debounceTimer = setTimeout(() => {
                    autocompleteService.getPlacePredictions({
                        input: query,
                        types: ['address'],
                        componentRestrictions: {
                            country: 'IN'
                        }
                    }, (predictions, status) => {
                        if (status === google.maps.places.PlacesServiceStatus.OK && predictions &&
                            predictions.length) {
                            const place = predictions[0];
                            geocoder.geocode({
                                placeId: place.place_id
                            }, (results, status) => {
                                if (status === 'OK' && results[0]) {
                                    const location = results[0].geometry.location;
                                    if (marker) marker.setMap(null);
                                    marker = new google.maps.Marker({
                                        position: location,
                                        map: map,
                                        title: 'Agent Location',
                                        draggable: true,
                                        icon: {
                                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                                            scaledSize: new google.maps.Size(40, 40)
                                        }
                                    });
                                    map.setCenter(location);
                                    map.setZoom(15);
                                    updateLocationFromLatLng(location.lat(), location
                                .lng());
                                    input.value = results[0].formatted_address;
                                }
                            });
                        }
                    });
                }, 300);
            });
        }

        document.getElementById('detect-location').addEventListener('click', function() {
            if (!navigator.geolocation) {
                showToast('Geolocation not supported', 'error');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner" style="width:20px;height:20px;"></span> Detecting...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    if (marker) marker.setMap(null);
                    marker = new google.maps.Marker({
                        position: {
                            lat: lat,
                            lng: lng
                        },
                        map: map,
                        title: 'Agent Location',
                        draggable: true,
                        icon: {
                            url: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png',
                            scaledSize: new google.maps.Size(40, 40)
                        }
                    });
                    map.setCenter({
                        lat: lat,
                        lng: lng
                    });
                    map.setZoom(15);
                    updateLocationFromLatLng(lat, lng);

                    document.getElementById('detect-location').disabled = false;
                    document.getElementById('detect-location').innerHTML =
                        '<i class="fas fa-location-dot"></i> Update from Current Location';
                },
                function(error) {
                    showToast('Could not detect location', 'error');
                    document.getElementById('detect-location').disabled = false;
                    document.getElementById('detect-location').innerHTML =
                        '<i class="fas fa-location-dot"></i> Update from Current Location';
                }
            );
        });

        function updateLocationFromLatLng(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            geocoder.geocode({
                location: {
                    lat: lat,
                    lng: lng
                }
            }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    document.getElementById('current_location').value = results[0].formatted_address;
                    document.getElementById('location-search').value = results[0].formatted_address;

                    const components = {};
                    results[0].address_components.forEach(comp => {
                        const type = comp.types[0];
                        components[type] = comp;
                    });

                    if (components.locality) document.getElementById('city').value = components.locality.long_name;
                    if (components.administrative_area_level_1) document.getElementById('state').value = components
                        .administrative_area_level_1.long_name;
                    if (components.postal_code) document.getElementById('pincode').value = components.postal_code
                        .long_name;

                    autoSelectServiceAreas(components.locality?.long_name || '');
                }
            });
        }

        function autoSelectServiceAreas(city) {
            const cityLower = city.toLowerCase();
            const cities = ['ahmedabad', 'gandhinagar', 'surat', 'vadodara', 'rajkot', 'bhavnagar', 'anand', 'jamnagar',
                'junagadh'
            ];

            cities.forEach(area => {
                const checkbox = document.querySelector(
                    `input[value="${area.charAt(0).toUpperCase() + area.slice(1)}"]`);
                const container = document.querySelector(
                    `.service-area-item[data-area="${area.charAt(0).toUpperCase() + area.slice(1)}"]`);
                if (checkbox && cityLower.includes(area)) {
                    checkbox.checked = true;
                    if (container) container.classList.add('selected');
                }
            });
        }

        document.querySelectorAll('.service-area-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const checkbox = this.querySelector('.checkbox-input');
                checkbox.checked = !checkbox.checked;
                if (checkbox.checked) {
                    this.classList.add('selected');
                } else {
                    this.classList.remove('selected');
                }
            });
        });

        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';

            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    showToast('File size must be less than 2MB', 'error');
                    input.value = '';
                    return;
                }
                const fileItem = document.createElement('div');
                fileItem.className = 'file-preview-item';
                fileItem.innerHTML =
                    `<span><i class="fas fa-file"></i> ${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
                                  <span class="file-preview-remove" onclick="removeFile('${input.id}', '${previewId}')"><i class="fas fa-times-circle"></i></span>`;
                preview.appendChild(fileItem);
            }
        }

        function removeFile(inputId, previewId) {
            document.getElementById(inputId).value = '';
            document.getElementById(previewId).innerHTML = '';
            showToast('File removed', 'warning');
        }

        function markForDeletion(type) {
            if (confirm(`Remove current ${type} document?`)) {
                document.getElementById(`delete_${type}`).value = '1';
                showToast(`${type} document will be removed when you save`, 'warning');
                const docSection = event.target.closest('.current-doc');
                if (docSection) docSection.style.opacity = '0.5';
            }
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = `<div class="toast-content"><span>${type === 'success' ? '✅' : '❌'}</span> ${message}</div>`;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        document.getElementById('agentForm').addEventListener('submit', function(e) {
            const phone = document.querySelector('[name="phone"]').value;
            if (!/^[0-9]{10}$/.test(phone)) {
                e.preventDefault();
                showToast('Please enter a valid 10-digit phone number', 'error');
                return;
            }
            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        window.initMap = initMap;
    </script>
@endsection
