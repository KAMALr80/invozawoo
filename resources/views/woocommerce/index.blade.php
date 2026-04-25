@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Premium Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 24px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                <div class="card-body p-5 position-relative">
                    <div class="position-absolute top-0 end-0 p-4 opacity-10">
                        <i class="fab fa-wordpress" style="font-size: 150px;"></i>
                    </div>
                    <div class="row align-items-center position-relative">
                        <div class="col-md-8">
                            <h1 class="display-5 fw-bold text-white mb-2">WooCommerce Elite</h1>
                            <p class="lead text-white text-opacity-75 mb-0">Experience seamless synchronization between your ERP and Online Store with state-of-the-art API integration.</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-4 mt-md-0">
                            <span class="badge bg-white bg-opacity-20 text-white p-3 rounded-pill">
                                <i class="fas fa-signal me-2"></i> System: Online
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Configuration Section -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white py-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 me-3 p-3">
                            <i class="fas fa-cogs fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0">API Credentials</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('woocommerce.update-settings') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase">Store URL</label>
                                <div class="input-group input-group-lg custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-globe text-muted"></i></span>
                                    <input type="url" name="store_url" class="form-control bg-light border-0" placeholder="https://yourstore.com" value="{{ $settings->store_url ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase">Consumer Key</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-key text-muted"></i></span>
                                    <input type="text" name="consumer_key" class="form-control bg-light border-0" value="{{ $settings->consumer_key ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small text-uppercase">Consumer Secret</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-0"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="consumer_secret" class="form-control bg-light border-0" value="{{ $settings->consumer_secret ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow-sm py-3" style="border-radius: 12px;">
                                    <i class="fas fa-save me-2"></i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Single Product Sync (Requested) -->
            <div class="card border-0 shadow-sm mt-4 overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white py-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 me-3 p-3">
                            <i class="fas fa-magic fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0">Individual Item Sync</h4>
                    </div>
                </div>
                <div class="card-body p-4 pt-0">
                    <p class="text-muted small mb-4">Select a specific product to push or update on WooCommerce instantly.</p>
                    <form action="{{ route('woocommerce.sync-single-product') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-9">
                                <select name="product_id" class="form-select form-select-lg bg-light border-0 select2" style="border-radius: 12px;" data-placeholder="Search for a product...">
                                    <option value=""></option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->name }} ({{ $product->product_code }}) - ₹{{ $product->price }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm" style="border-radius: 12px;">
                                    <i class="fas fa-sync-alt me-1"></i> Sync
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Operations Center -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100 overflow-hidden" style="border-radius: 20px;">
                <div class="card-header bg-white py-4 px-4 border-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 me-3 p-3">
                            <i class="fas fa-rocket fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0">Operations Hub</h4>
                    </div>
                </div>
                <div class="card-body p-4 pt-0">
                    <!-- Status Card -->
                    <div class="p-4 mb-4 rounded-4 text-center border border-2 border-dashed border-primary bg-primary bg-opacity-10">
                        <div class="avatar avatar-lg bg-white rounded-circle shadow-sm mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-check text-success fs-3"></i>
                        </div>
                        <h5 class="fw-bold text-primary mb-1">Active Connection</h5>
                        <p class="small text-primary text-opacity-75 mb-0">Last heartbeat: {{ $settings->updated_at ?? 'Never' }}</p>
                    </div>

                    <div class="d-grid gap-3">
                        <form action="{{ route('woocommerce.sync-products') }}" method="POST">
                            @csrf
                            <button type="submit" class="op-button btn btn-outline-primary border-2 text-start p-4 w-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold mb-1">Bulk Products</h5>
                                        <span class="small opacity-75">Full catalog synchronization</span>
                                    </div>
                                    <i class="fas fa-boxes-stacked fs-3"></i>
                                </div>
                            </button>
                        </form>

                        <form action="{{ route('woocommerce.sync-orders') }}" method="POST">
                            @csrf
                            <button type="submit" class="op-button btn btn-outline-indigo border-2 text-start p-4 w-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold mb-1">Sync Orders</h5>
                                        <span class="small opacity-75">Process processing store orders</span>
                                    </div>
                                    <i class="fas fa-cart-shopping fs-3"></i>
                                </div>
                            </button>
                        </form>

                        <form action="{{ route('woocommerce.sync-customers') }}" method="POST">
                            @csrf
                            <button type="submit" class="op-button btn btn-outline-success border-2 text-start p-4 w-100">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h5 class="fw-bold mb-1">Customer Hub</h5>
                                        <span class="small opacity-75">Push local CRM data to store</span>
                                    </div>
                                    <i class="fas fa-users-viewfinder fs-3"></i>
                                </div>
                            </button>
                        </form>
                    </div>

                    <div class="mt-4 p-3 rounded-3 bg-light border-start border-4 border-primary">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-lightbulb text-primary me-2"></i>
                            <span class="small text-muted fw-bold">Pro Tip:</span>
                        </div>
                        <p class="small text-muted mt-1 mb-0">Single item sync is recommended for quick inventory adjustments without loading the entire API.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-input-group .input-group-text {
        border-top-left-radius: 12px !important;
        border-bottom-left-radius: 12px !important;
    }
    .custom-input-group .form-control {
        border-top-right-radius: 12px !important;
        border-bottom-right-radius: 12px !important;
        padding-top: 12px;
        padding-bottom: 12px;
    }
    .op-button {
        border-radius: 16px !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
    }
    .op-button:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .btn-outline-indigo {
        color: #6366f1;
        border-color: #6366f1;
    }
    .btn-outline-indigo:hover {
        background-color: #6366f1;
        color: white;
    }
    .icon-shape {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Select2 Skinning */
    .select2-container--default .select2-selection--single {
        background-color: #f8fafc !important;
        border: none !important;
        height: 52px !important;
        border-radius: 12px !important;
        padding: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px !important;
    }
</style>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        // Debounce / Loading State for Forms
        $('form').on('submit', function() {
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            // Check if it's a sync button
            if ($btn.hasClass('op-button') || $btn.hasClass('btn-warning') || $btn.hasClass('btn-primary')) {
                const originalHtml = $btn.html();
                
                // Disable button
                $btn.prop('disabled', true);
                
                // Show loading spinner
                $btn.html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');
                
                // Safety timeout to re-enable if something goes wrong (e.g. page doesn't refresh)
                setTimeout(() => {
                    if ($btn.prop('disabled')) {
                        $btn.prop('disabled', false);
                        $btn.html(originalHtml);
                    }
                }, 120000); // 2 minutes
            }
        });
    });
</script>
@endpush
@endsection
