{{-- resources/views/logistics/agents/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Delivery Agent')

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

        .create-page {
            min-height: 100vh;
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }

        .create-card {
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

        .create-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            padding: clamp(1.5rem, 4vw, 2rem);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .create-header::before {
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

        .header-icon {
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
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: clamp(0.9rem, 2.5vw, 1rem);
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

    <div class="create-page">
        <div class="container">
            <div class="create-card">
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Creating agent...</div>
                </div>

                <div class="create-header">
                    <div class="header-content">
                        <div class="header-left">
                            <div class="header-icon"><i class="fas fa-user-plus"></i></div>
                            <div>
                                <h1 class="header-title">Add New Delivery Agent</h1>
                                <p class="header-subtitle"><i class="fas fa-truck"></i> Fill in the agent details below</p>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="{{ route('logistics.agents.index') }}" class="header-btn"><i
                                    class="fas fa-arrow-left"></i> Back to List</a>
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

                <form method="POST" action="{{ route('logistics.agents.store') }}" enctype="multipart/form-data"
                    id="agentForm">
                    @csrf

                    {{-- Personal Information --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-user"></i></div>
                            <div>
                                <h3 class="section-title">Personal Information</h3>
                                <p class="section-subtitle">Enter agent's personal details</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-user"></i> Full Name <span
                                        class="required-star">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required placeholder="Enter full name">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone"></i> Phone Number <span
                                        class="required-star">*</span></label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}"
                                    required placeholder="10 digit mobile number">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                    placeholder="email@example.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-phone-alt"></i> Alternate Phone</label>
                                <input type="text" name="alternate_phone" class="form-control"
                                    value="{{ old('alternate_phone') }}" placeholder="Alternate contact">
                            </div>
                        </div>
                    </div>

                    {{-- Location Information with Google Maps --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h3 class="section-title">Location Information</h3>
                                <p class="section-subtitle">Select agent's current location and service areas</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-search"></i> Search Address / Current
                                Location</label>
                            <input type="text" id="location-search" class="form-control"
                                placeholder="Search for address or drop pin on map" autocomplete="off">
                            <button type="button" id="detect-location" class="location-detect-btn">
                                <i class="fas fa-location-dot"></i> Use My Current Location
                            </button>
                        </div>

                        <div id="map-preview" class="map-preview"></div>

                        <input type="hidden" name="current_latitude" id="latitude" value="{{ old('current_latitude') }}">
                        <input type="hidden" name="current_longitude" id="longitude"
                            value="{{ old('current_longitude') }}">
                        <input type="hidden" name="current_location" id="current_location">

                        <div class="form-grid" style="margin-top: 1rem;">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-city"></i> City</label>
                                <input type="text" name="city" id="city" class="form-control"
                                    value="{{ old('city') }}" readonly style="background:#f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-map"></i> State</label>
                                <input type="text" name="state" id="state" class="form-control"
                                    value="{{ old('state') }}" readonly style="background:#f8fafc;">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-mail-bulk"></i> Pincode</label>
                                <input type="text" name="pincode" id="pincode" class="form-control"
                                    value="{{ old('pincode') }}" readonly style="background:#f8fafc;">
                            </div>
                        </div>
                    </div>

                    {{-- Vehicle Information --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-truck"></i></div>
                            <div>
                                <h3 class="section-title">Vehicle Information</h3>
                                <p class="section-subtitle">Enter vehicle and license details</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-motorcycle"></i> Vehicle Type</label>
                                <select name="vehicle_type" class="form-control">
                                    <option value="">Select Vehicle</option>
                                    <option value="bike" {{ old('vehicle_type') == 'bike' ? 'selected' : '' }}>🏍️ Bike
                                    </option>
                                    <option value="scooter" {{ old('vehicle_type') == 'scooter' ? 'selected' : '' }}>🛵
                                        Scooter</option>
                                    <option value="van" {{ old('vehicle_type') == 'van' ? 'selected' : '' }}>🚐 Van
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-hashtag"></i> Vehicle Number</label>
                                <input type="text" name="vehicle_number" class="form-control"
                                    value="{{ old('vehicle_number') }}" placeholder="e.g., GJ01AB1234">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> License Number</label>
                                <input type="text" name="license_number" class="form-control"
                                    value="{{ old('license_number') }}" placeholder="Driving license number">
                            </div>
                        </div>
                    </div>

                    {{-- Employment Details --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-briefcase"></i></div>
                            <div>
                                <h3 class="section-title">Employment Details</h3>
                                <p class="section-subtitle">Enter employment and compensation</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-briefcase"></i> Employment Type <span
                                        class="required-star">*</span></label>
                                <select name="employment_type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="full_time"
                                        {{ old('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                    <option value="part_time"
                                        {{ old('employment_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                    <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>
                                        Contract</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-calendar-alt"></i> Joining Date <span
                                        class="required-star">*</span></label>
                                <input type="date" name="joining_date" class="form-control"
                                    value="{{ old('joining_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-rupee-sign"></i> Salary (₹)</label>
                                <input type="number" name="salary" class="form-control" value="{{ old('salary') }}"
                                    step="0.01" min="0" placeholder="0.00">
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-percent"></i> Commission Type</label>
                                <select name="commission_type" class="form-control">
                                    <option value="">No Commission</option>
                                    <option value="fixed" {{ old('commission_type') == 'fixed' ? 'selected' : '' }}>Fixed
                                        Amount</option>
                                    <option value="percentage"
                                        {{ old('commission_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-coins"></i> Commission Value</label>
                                <input type="number" name="commission_value" class="form-control"
                                    value="{{ old('commission_value') }}" step="0.01" min="0"
                                    placeholder="0">
                            </div>
                        </div>
                    </div>

                    {{-- Service Areas (Based on seeder cities) --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-map-marked-alt"></i></div>
                            <div>
                                <h3 class="section-title">Service Areas</h3>
                                <p class="section-subtitle">Select areas where agent can deliver (auto-suggested based on
                                    city)</p>
                            </div>
                        </div>
                        <div id="serviceAreasContainer" class="service-areas-grid">
                            @php
                                $cities = [
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
                            @foreach ($cities as $city)
                                <div class="service-area-item" data-city="{{ $city }}">
                                    <input type="checkbox" name="service_areas[]" value="{{ $city }}"
                                        class="checkbox-input" id="area_{{ strtolower($city) }}">
                                    <label for="area_{{ strtolower($city) }}"
                                        class="checkbox-label">{{ $city }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top: 1rem;">
                            <label class="form-label"><i class="fas fa-plus-circle"></i> Additional Areas</label>
                            <input type="text" name="additional_areas" class="form-control"
                                placeholder="Enter additional areas separated by commas"
                                value="{{ old('additional_areas') }}">
                            <div class="input-hint">e.g., Anand, Nadiad, Mehsana</div>
                        </div>
                    </div>

                    {{-- Documents (Matching seeder structure) --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-file-alt"></i></div>
                            <div>
                                <h3 class="section-title">Documents</h3>
                                <p class="section-subtitle">Upload agent documents (optional)</p>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> Aadhar Card</label>
                                <input type="file" name="aadhar_card" class="form-control" accept="image/*,.pdf">
                                <div class="input-hint">Upload Aadhar card (JPG, PNG, PDF, Max 2MB)</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-id-card"></i> Driving License</label>
                                <input type="file" name="driving_license" class="form-control" accept="image/*,.pdf">
                                <div class="input-hint">Upload Driving License (JPG, PNG, PDF, Max 2MB)</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label"><i class="fas fa-camera"></i> Profile Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                <div class="input-hint">Upload profile photo (JPG, PNG, Max 2MB)</div>
                            </div>
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="form-section">
                        <div class="section-header">
                            <div class="section-icon"><i class="fas fa-toggle-on"></i></div>
                            <div>
                                <h3 class="section-title">Status</h3>
                                <p class="section-subtitle">Set agent availability</p>
                            </div>
                        </div>
                        <div class="checkbox-group"
                            style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: #f8fafc; border-radius: 12px;">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                class="checkbox-input" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="checkbox-label">Agent is active and can receive
                                deliveries</label>
                        </div>
                        <div class="input-hint" style="margin-top: 0.5rem;">Active agents will appear in assignment lists
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="{{ route('logistics.agents.index') }}" class="btn btn-secondary"><i
                                class="fas fa-times"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Create
                            Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"
        async defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

    <script>
        let map;
        let marker;
        let geocoder;
        let autocompleteService;

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
                                    document.getElementById('location-search').value =
                                        results[0].formatted_address;
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
                        '<i class="fas fa-location-dot"></i> Use My Current Location';
                },
                function(error) {
                    showToast('Could not detect location: ' + error.message, 'error');
                    document.getElementById('detect-location').disabled = false;
                    document.getElementById('detect-location').innerHTML =
                        '<i class="fas fa-location-dot"></i> Use My Current Location';
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
                if (checkbox && cityLower.includes(area)) {
                    checkbox.checked = true;
                    checkbox.closest('.service-area-item').classList.add('selected');
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

            const lat = document.getElementById('latitude').value;
            if (!lat || lat === '0') {
                e.preventDefault();
                showToast('Please select agent location on map', 'error');
                return;
            }

            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        window.initMap = initMap;
    </script>
@endsection
