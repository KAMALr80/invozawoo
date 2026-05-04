@extends('layouts.app')

@section('page-title', 'WooCommerce Integration')

@section('content')
<style>
    /* Modern CSS Reset & Variables */
    :root {
        --primary: #4f46e5;
        --primary-dark: #4338ca;
        --primary-light: #818cf8;
        --secondary: #06b6d4;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --dark: #1e293b;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Dashboard Layout */
    .dashboard-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 24px;
    }

    /* Top Navigation Bar */
    .top-nav {
        background: white;
        border-radius: 20px;
        padding: 16px 32px;
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }

    .logo-area {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .logo-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .logo-text h2 {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .logo-text p {
        font-size: 12px;
        color: var(--gray-500);
        margin: 0;
    }

    .status-indicator {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--gray-50);
        padding: 8px 20px;
        border-radius: 50px;
    }

    .status-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    .status-dot.online {
        background: var(--success);
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .stat-card {
        background: white;
        border-radius: 24px;
        padding: 24px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-xl);
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--gray-800);
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 14px;
        color: var(--gray-500);
        font-weight: 500;
    }

    .stat-trend {
        font-size: 12px;
        margin-top: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Main Content Grid */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 32px;
    }

    /* Card Styles */
    .card-modern {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        margin-bottom: 32px;
        box-shadow: var(--shadow-lg);
    }

    .card-header {
        padding: 24px 28px;
        border-bottom: 1px solid var(--gray-200);
        background: white;
    }

    .card-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 4px;
    }

    .card-subtitle {
        font-size: 13px;
        color: var(--gray-500);
    }

    .card-body {
        padding: 28px;
    }

    /* Form Styles */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        font-size: 16px;
    }

    .form-control-modern {
        width: 100%;
        padding: 14px 16px 14px 48px;
        border: 2px solid var(--gray-200);
        border-radius: 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--gray-50);
    }

    .form-control-modern:focus {
        outline: none;
        border-color: var(--primary);
        background: white;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }

    /* Button Styles */
    .btn-modern {
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }

    .btn-secondary {
        background: white;
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .btn-secondary:hover {
        border-color: var(--primary);
        color: var(--primary);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
        color: white;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    /* Action Buttons Grid */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .action-item {
        background: var(--gray-50);
        border-radius: 20px;
        padding: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }

    .action-item:hover {
        background: white;
        border-color: var(--primary);
        transform: translateX(5px);
        box-shadow: var(--shadow-md);
    }

    .action-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .action-info h4 {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
        color: var(--gray-800);
    }

    .action-info p {
        font-size: 12px;
        color: var(--gray-500);
        margin: 0;
    }

    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    /* Select2 Customization */
    .select2-container--default .select2-selection--single {
        height: 54px !important;
        border: 2px solid var(--gray-200) !important;
        border-radius: 16px !important;
        background-color: var(--gray-50) !important;
        padding: 8px !important;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 16px;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-container">


    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #667eea20, #764ba220);">
                    <i class="fas fa-box" style="color: var(--primary);"></i>
                </div>
                <i class="fas fa-ellipsis-h" style="color: var(--gray-400);"></i>
            </div>
            <div class="stat-value" id="totalProducts">0</div>
            <div class="stat-label">Total Products</div>
            <div class="stat-trend">
                <i class="fas fa-check-circle" style="color: var(--success); font-size: 12px;"></i>
                <span id="syncedProducts">0</span>
                <span style="color: var(--gray-400);">synced to store</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #10b98120, #05966920);">
                    <i class="fas fa-shopping-cart" style="color: var(--success);"></i>
                </div>
                <i class="fas fa-ellipsis-h" style="color: var(--gray-400);"></i>
            </div>
            <div class="stat-value" id="totalOrders">0</div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-trend">
                <i class="fas fa-sync-alt" style="color: var(--secondary); font-size: 12px;"></i>
                <span id="syncedOrders">0</span>
                <span style="color: var(--gray-400);">imported from store</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b20, #d9770620);">
                    <i class="fas fa-users" style="color: var(--warning);"></i>
                </div>
                <i class="fas fa-ellipsis-h" style="color: var(--gray-400);"></i>
            </div>
            <div class="stat-value" id="totalCustomers">0</div>
            <div class="stat-label">Customers</div>
            <div class="stat-trend">
                <i class="fas fa-users" style="color: var(--warning); font-size: 12px;"></i>
                <span id="syncedCustomers">0</span>
                <span style="color: var(--gray-400);">synced to store</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #06b6d420, #0891b220);">
                    <i class="fas fa-chart-line" style="color: var(--secondary);"></i>
                </div>
                <i class="fas fa-ellipsis-h" style="color: var(--gray-400);"></i>
            </div>
            <div class="stat-value" id="totalRevenue">₹0</div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-trend">
                <i class="fas fa-calendar" style="color: var(--secondary); font-size: 12px;"></i>
                <span id="lastSyncDate">Last sync: Never</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-grid">
        <!-- Left Column -->
        <div>
            <!-- API Configuration Card -->
            <div class="card-modern">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-key" style="color: var(--primary); margin-right: 8px;"></i>
                        API Configuration
                    </div>
                    <div class="card-subtitle">Configure your WooCommerce REST API credentials for seamless integration</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('woocommerce.update-settings') }}" method="POST" id="settingsForm">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Store URL</label>
                            <div class="input-wrapper">
                                <i class="fas fa-globe input-icon"></i>
                                <input type="url" name="store_url" class="form-control-modern" placeholder="https://yourstore.com" value="{{ $settings->store_url ?? '' }}" required>
                            </div>
                            <small style="font-size: 11px; color: var(--gray-500); margin-top: 6px; display: block;">
                                <i class="fas fa-info-circle"></i> Example: https://yourdomain.com (without trailing slash)
                            </small>
                        </div>

                        <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label class="form-label">Consumer Key</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="text" name="consumer_key" class="form-control-modern" placeholder="ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="{{ $settings->consumer_key ?? '' }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Consumer Secret</label>
                                <div class="input-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="consumer_secret" class="form-control-modern" placeholder="cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" value="{{ $settings->consumer_secret ?? '' }}" required>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 12px; margin-top: 8px;">
                            <button type="submit" class="btn-modern btn-primary" style="flex: 1;">
                                <i class="fas fa-save"></i> Save Configuration
                            </button>
                            <button type="button" id="testConnectionBtn" class="btn-modern btn-secondary">
                                <i class="fas fa-vial"></i> Test
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Single Product Sync Card -->
            <div class="card-modern">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-magic" style="color: var(--warning); margin-right: 8px;"></i>
                        Quick Sync
                    </div>
                    <div class="card-subtitle">Instantly sync a single product to WooCommerce</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('woocommerce.sync-single-product') }}" method="POST" id="singleSyncForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">Select Product</label>
                                    <select name="product_id" id="productSelect" class="form-control-modern select2-single" style="width: 100%; padding: 14px;">
                                        <option value=""></option>
                                        @foreach($products as $product)
                                            @php
                                                $imgUrl = $product->image ? (str_contains($product->image, 'http') ? $product->image : asset('storage/' . $product->image)) : asset('images/no-image.png');
                                            @endphp
                                            <option value="{{ $product->id }}" data-image="{{ $imgUrl }}" data-synced="{{ $product->woocommerce_product_id ? 'true' : 'false' }}">
                                                {{ $product->name }} ({{ $product->product_code }}) - ₹{{ number_format($product->price, 2) }}
                                                @if($product->woocommerce_product_id) ✓ @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div id="productPreviewContainer" style="width: 100%; height: 100px; background: var(--gray-50); border: 2px dashed var(--gray-200); border-radius: 16px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                    <img id="productPreviewImg" src="" style="max-width: 100%; max-height: 100%; display: none;">
                                    <i id="previewPlaceholder" class="fas fa-image" style="font-size: 30px; color: var(--gray-300);"></i>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn-modern btn-primary btn-block mt-3">
                            <i class="fas fa-sync-alt"></i> Sync Product Now
                        </button>
                    </form>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card-modern">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-history" style="color: var(--secondary); margin-right: 8px;"></i>
                        Recent Sync Activity
                    </div>
                    <div class="card-subtitle">Latest synchronization events</div>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div id="activityLog" style="max-height: 300px; overflow-y: auto;">
                        <div style="padding: 20px; text-align: center; color: var(--gray-500);">
                            <i class="fas fa-clock"></i> No recent activity
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Connection Status Card -->
            <div class="card-modern">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-plug" style="color: var(--success); margin-right: 8px;"></i>
                        Connection Status
                    </div>
                </div>
                <div class="card-body">
                    <div id="connectionStatusCard" style="text-align: center; padding: 20px;">
                        @if(isset($settings) && $settings && $settings->store_url)
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b98120, #05966920); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-check-circle" style="font-size: 40px; color: var(--success);"></i>
                            </div>
                            <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Connected</h4>
                            <p style="font-size: 13px; color: var(--gray-600); margin-bottom: 16px;">
                                {{ parse_url($settings->store_url, PHP_URL_HOST) }}
                            </p>
                            <div style="background: var(--gray-50); border-radius: 12px; padding: 12px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-size: 12px; color: var(--gray-600);">Last Config Update</span>
                                    <span style="font-size: 12px; font-weight: 600;">{{ isset($settings->updated_at) ? date('d M Y, h:i A', strtotime($settings->updated_at)) : 'Never' }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--gray-200); pt-2; mt-2;">
                                    <span style="font-size: 12px; color: var(--gray-600);">Real-time Auto Sync</span>
                                    <div class="form-check form-switch p-0 m-0" style="min-height: auto;">
                                        <input class="form-check-input" type="checkbox" id="autoSyncToggle" style="width: 40px; height: 20px; cursor: pointer;" {{ ($settings->is_sync_enabled ?? false) ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: var(--warning);"></i>
                            </div>
                            <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Not Configured</h4>
                            <p style="font-size: 13px; color: var(--gray-600);">Please add your API credentials to start syncing</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bulk Operations Card -->
            <div class="card-modern">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-rocket" style="color: var(--primary); margin-right: 8px;"></i>
                        Bulk Operations
                    </div>
                    <div class="card-subtitle">Perform large-scale synchronization tasks</div>
                </div>
                <div class="card-body">
                    <div class="action-buttons">
                        <form action="{{ route('woocommerce.sync-products') }}" method="POST" class="sync-bulk-form">
                            @csrf
                            <div class="action-item">
                                <div class="action-content">
                                    <div class="action-info">
                                        <h4>Sync All Products</h4>
                                        <p>Push entire product catalog to WooCommerce</p>
                                    </div>
                                    <div class="action-icon" style="background: linear-gradient(135deg, #667eea20, #764ba220);">
                                        <i class="fas fa-boxes" style="color: var(--primary);"></i>
                                    </div>
                                </div>
                                <button type="submit" class="btn-modern btn-primary" style="width: 100%; margin-top: 16px;">
                                    <i class="fas fa-sync-alt"></i> Start Bulk Sync
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('woocommerce.sync-orders') }}" method="POST" class="sync-bulk-form">
                            @csrf
                            <div class="action-item">
                                <div class="action-content">
                                    <div class="action-info">
                                        <h4>Import Orders</h4>
                                        <p>Fetch recent orders from WooCommerce</p>
                                    </div>
                                    <div class="action-icon" style="background: linear-gradient(135deg, #10b98120, #05966920);">
                                        <i class="fas fa-shopping-cart" style="color: var(--success);"></i>
                                    </div>
                                </div>
                                <button type="submit" class="btn-modern btn-success" style="width: 100%; margin-top: 16px;">
                                    <i class="fas fa-download"></i> Import Orders
                                </button>
                            </div>
                        </form>

                        <form action="{{ route('woocommerce.sync-customers') }}" method="POST" class="sync-bulk-form">
                            @csrf
                            <div class="action-item">
                                <div class="action-content">
                                    <div class="action-info">
                                        <h4>Sync Customers</h4>
                                        <p>Push customer data to WooCommerce</p>
                                    </div>
                                    <div class="action-icon" style="background: linear-gradient(135deg, #f59e0b20, #d9770620);">
                                        <i class="fas fa-users" style="color: var(--warning);"></i>
                                    </div>
                                </div>
                                <button type="submit" class="btn-modern btn-secondary" style="width: 100%; margin-top: 16px;">
                                    <i class="fas fa-users"></i> Sync Customers
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card-modern">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-lightbulb" style="color: var(--warning); margin-right: 8px;"></i>
                        Pro Tips
                    </div>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 16px; display: flex; gap: 12px;">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-top: 2px;"></i>
                        <div>
                            <strong style="font-size: 13px;">Enable API Access</strong>
                            <p style="font-size: 12px; color: var(--gray-600); margin-top: 4px;">Go to WooCommerce > Settings > Advanced > REST API</p>
                        </div>
                    </div>
                    <div style="margin-bottom: 16px; display: flex; gap: 12px;">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-top: 2px;"></i>
                        <div>
                            <strong style="font-size: 13px;">Set Proper Permissions</strong>
                            <p style="font-size: 12px; color: var(--gray-600); margin-top: 4px;">Give Read/Write access for full functionality</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <i class="fas fa-check-circle" style="color: var(--success); margin-top: 2px;"></i>
                        <div>
                            <strong style="font-size: 13px;">Webhook Setup</strong>
                            <p style="font-size: 12px; color: var(--gray-600); margin-top: 4px;">Configure webhooks for real-time updates</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 24px; padding: 32px; text-align: center;">
        <div class="spinner-border text-primary" style="width: 50px; height: 50px;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <h5 style="margin-top: 20px;">Processing Request</h5>
        <p style="color: var(--gray-600); margin-top: 8px;">Please wait...</p>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2-single').select2({
        placeholder: 'Search for a product...',
        allowClear: true,
        width: '100%'
    });

    // Handle Product Selection Preview
    $('#productSelect').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const imgUrl = selectedOption.data('image');
        
        if (imgUrl) {
            $('#productPreviewImg').attr('src', imgUrl).fadeIn();
            $('#previewPlaceholder').hide();
        } else {
            $('#productPreviewImg').hide();
            $('#previewPlaceholder').fadeIn();
        }
    });

    // Load stats
    loadStats();

    // Handle form submissions
    $('.sync-bulk-form, #singleSyncForm, #settingsForm').on('submit', function() {
        $('#loadingOverlay').fadeIn();
        const $btn = $(this).find('button[type="submit"]');
        if ($btn.length) {
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');
        }
        setTimeout(() => {
            $('#loadingOverlay').fadeOut();
        }, 30000);
    });

    // Test connection
    $('#testConnectionBtn').on('click', function() {
        const $btn = $(this);
        const storeUrl = $('input[name="store_url"]').val();
        const consumerKey = $('input[name="consumer_key"]').val();
        const consumerSecret = $('input[name="consumer_secret"]').val();
        
        if (!storeUrl || !consumerKey || !consumerSecret) {
            showNotification('Please fill in all API credentials first!', 'warning');
            return;
        }
        
        $btn.prop('disabled', true);
        $btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Testing...');
        
        $.ajax({
            url: '{{ route("woocommerce.test-connection") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                store_url: storeUrl,
                consumer_key: consumerKey,
                consumer_secret: consumerSecret
            },
            success: function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    updateConnectionStatus(true, storeUrl);
                } else {
                    showNotification(response.message, 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Connection failed. Please check your credentials.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                showNotification(errorMsg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.html('<i class="fas fa-vial"></i> Test');
            }
        });
    });

    // Auto Sync Toggle Logic
    $('#autoSyncToggle').on('change', function() {
        const isEnabled = $(this).is(':checked');
        $.ajax({
            url: '{{ route("woocommerce.toggle-auto-sync") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                enabled: isEnabled ? 1 : 0
            },
            success: function(response) {
                if (response.success) {
                    showNotification('Auto-sync ' + (isEnabled ? 'enabled' : 'disabled'), 'success');
                }
            },
            error: function() {
                showNotification('Failed to update auto-sync setting', 'error');
                $('#autoSyncToggle').prop('checked', !isEnabled);
            }
        });
    });
});

function loadStats() {
    $.ajax({
        url: '{{ route("woocommerce.sync-stats") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#totalProducts').text(response.data.total_products);
                $('#totalOrders').text(response.data.total_orders);
                $('#totalCustomers').text(response.data.total_customers);
                $('#totalRevenue').text('₹' + response.data.total_revenue);
                $('#syncedProducts').text(response.data.products_synced);
                $('#syncedOrders').text(response.data.orders_synced);
                $('#syncedCustomers').text(response.data.customers_synced);
                $('#lastSyncDate').text('Last sync: ' + (response.data.last_sync || 'Never'));
            }
        }
    });
}

function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#f59e0b');
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
    
    const notification = $(`
        <div style="position: fixed; top: 20px; right: 20px; z-index: 10000; animation: slideIn 0.3s ease;">
            <div style="background: white; border-radius: 16px; padding: 16px 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-left: 4px solid ${bgColor}; min-width: 300px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas ${icon}" style="color: ${bgColor}; font-size: 20px;"></i>
                    <div>
                        <p style="margin: 0; font-size: 14px; color: var(--gray-700);">${message}</p>
                    </div>
                    <button onclick="$(this).closest('div').remove()" style="margin-left: auto; background: none; border: none; cursor: pointer;">
                        <i class="fas fa-times" style="color: var(--gray-400);"></i>
                    </button>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(() => {
        notification.fadeOut('slow', function() { $(this).remove(); });
    }, 5000);
}

function updateConnectionStatus(connected, storeUrl) {
    const statusHtml = connected ? `
        <div style="text-align: center; padding: 20px;">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b98120, #05966920); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-check-circle" style="font-size: 40px; color: var(--success);"></i>
            </div>
            <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Connected</h4>
            <p style="font-size: 13px; color: var(--gray-600); margin-bottom: 16px;">
                ${new URL(storeUrl).hostname}
            </p>
            <div style="background: var(--gray-50); border-radius: 12px; padding: 12px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="font-size: 12px; color: var(--gray-600);">API Status</span>
                    <span style="font-size: 12px; font-weight: 600; color: var(--success);">Active</span>
                </div>
            </div>
        </div>
    ` : `
        <div style="text-align: center; padding: 20px;">
            <div style="width: 80px; height: 80px; background: var(--gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 40px; color: var(--warning);"></i>
            </div>
            <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Not Configured</h4>
            <p style="font-size: 13px; color: var(--gray-600);">Please add your API credentials to start syncing</p>
        </div>
    `;
    
    $('#connectionStatusCard').html(statusHtml);
}

// Add animation keyframes
$('head').append(`
    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
`);
</script>
@endpush
@endsection