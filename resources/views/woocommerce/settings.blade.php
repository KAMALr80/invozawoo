@extends('layouts.app')

@section('page-title', 'Bridge Configuration')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="wc-elite-v2 settings-mode">
    <div class="aurora-bg"></div>
    
    <div class="container-fluid p-lg-5 p-3">
        {{-- Elite Header --}}
        <header class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center">
                <a href="{{ route('woocommerce.index') }}" class="btn-back-elite me-4">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="display-6 fw-900 mb-1 text-white">Bridge <span class="text-indigo-glow">Infrastructure</span></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('woocommerce.index') }}" class="text-muted text-decoration-none">Architect</a></li>
                            <li class="breadcrumb-item active text-indigo-glow fw-bold">Configuration</li>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="header-actions">
                <div class="badge-status-glow">
                    <span class="status-dot"></span> Secure Tunnel Active
                </div>
            </div>
        </header>

        <div class="row g-5">
            {{-- Navigation Pane --}}
            <div class="col-xl-3">
                <div class="sidebar-config-glass animate__animated animate__fadeInLeft">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <button class="nav-elite-config active" data-bs-toggle="pill" data-bs-target="#tab-api">
                            <i class="bi bi-hdd-network"></i>
                            <div class="link-info">
                                <span class="title">API Gateway</span>
                                <span class="subtitle">Endpoints & Keys</span>
                            </div>
                        </button>
                        <button class="nav-elite-config" data-bs-toggle="pill" data-bs-target="#tab-sync">
                            <i class="bi bi-arrow-repeat"></i>
                            <div class="link-info">
                                <span class="title">Synchronization</span>
                                <span class="subtitle">Logic & Intervals</span>
                            </div>
                        </button>
                        <button class="nav-elite-config" data-bs-toggle="pill" data-bs-target="#tab-advanced">
                            <i class="bi bi-shield-lock"></i>
                            <div class="link-info">
                                <span class="title">Advanced</span>
                                <span class="subtitle">Security & Debug</span>
                            </div>
                        </button>
                    </div>

                    <div class="config-help-box mt-5">
                        <div class="help-inner">
                            <i class="bi bi-info-circle-fill text-indigo-glow"></i>
                            <p class="small text-muted mb-0">Changes deployed here affect the real-time neural bridge. Proceed with caution.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Pane --}}
            <div class="col-xl-9">
                <div class="tab-content animate__animated animate__fadeInUp">
                    <div class="tab-pane fade show active" id="tab-api">
                        <div class="card-config-glass">
                            <div class="card-header-elite">
                                <h4 class="text-white fw-800 mb-2">Core API Credentials</h4>
                                <p class="text-muted mb-0">Configure the primary REST API handshake parameters.</p>
                            </div>

                            <form action="{{ route('woocommerce.settings.update') }}" method="POST" id="configForm">
                                @csrf
                                <div class="card-body-elite p-5">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="input-elite-group">
                                                <label class="text-muted small fw-800 text-uppercase tracking-wider">WooCommerce Store URL</label>
                                                <div class="input-wrapper">
                                                    <i class="bi bi-link-45deg"></i>
                                                    <input type="url" name="app_url" id="app_url" class="form-control-elite" value="{{ $settings->app_url }}" placeholder="https://yourstore.com" required>
                                                </div>
                                                <small class="text-muted-dim">The full URL including https:// (e.g., https://shop.example.com/)</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="input-elite-group">
                                                <label class="text-muted small fw-800 text-uppercase tracking-wider">Consumer Key</label>
                                                <div class="input-wrapper">
                                                    <i class="bi bi-key"></i>
                                                    <input type="text" name="consumer_key" id="consumer_key" class="form-control-elite" value="{{ $settings->consumer_key }}" placeholder="ck_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-elite-group">
                                                <label class="text-muted small fw-800 text-uppercase tracking-wider">Consumer Secret</label>
                                                <div class="input-wrapper">
                                                    <i class="bi bi-shield-shaded"></i>
                                                    <input type="password" name="consumer_secret" id="consumer_secret" class="form-control-elite" value="{{ $settings->consumer_secret }}" placeholder="cs_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer-elite d-flex justify-content-between p-5">
                                    <button type="button" id="btn-audit" class="btn-audit-elite">
                                        <span class="spinner-border spinner-border-sm d-none me-2"></span>
                                        <i class="bi bi-activity me-2"></i> Audit Neural Link
                                    </button>
                                    <button type="submit" class="btn-deploy-elite">
                                        Deploy Configuration <i class="bi bi-cloud-upload ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Placeholder tabs --}}
                    <div class="tab-pane fade" id="tab-sync">
                        <div class="card-config-glass p-5 text-center">
                            <div class="py-5">
                                <i class="bi bi-hourglass-split display-1 text-muted-dim mb-4"></i>
                                <h4 class="text-white">Batch Engine Settings</h4>
                                <p class="text-muted">Currently optimized for High Performance (50 items/batch). Advanced interval control coming in v4.1</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap');

    :root {
        --indigo-primary: #6366f1;
        --indigo-glow: #818cf8;
        --dark-bg: #030712;
        --glass-bg: rgba(17, 24, 39, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
        --text-muted-dim: #475569;
    }

    .wc-elite-v2 {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--dark-bg);
        min-height: 100vh;
        color: #94a3b8;
        position: relative;
        overflow-x: hidden;
    }

    .aurora-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.1) 0%, transparent 40%),
                    radial-gradient(circle at 0% 100%, rgba(16, 185, 129, 0.05) 0%, transparent 40%);
        z-index: 0;
        pointer-events: none;
    }

    .fw-900 { font-weight: 900; }
    .fw-800 { font-weight: 800; }
    .text-indigo-glow { color: var(--indigo-glow) !important; }

    /* Header & Back Button */
    .btn-back-elite {
        width: 50px; height: 50px; border-radius: 15px;
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        display: flex; align-items: center; justify-content: center;
        color: white; text-decoration: none; transition: 0.3s;
    }
    .btn-back-elite:hover { background: rgba(255,255,255,0.1); color: var(--indigo-glow); transform: translateX(-5px); }

    .badge-status-glow {
        padding: 8px 16px; background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px;
        color: #10b981; font-size: 12px; font-weight: 800;
        display: flex; align-items: center; gap: 8px;
    }
    .status-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; }

    /* Sidebar Config */
    .sidebar-config-glass {
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px); border-radius: 35px; padding: 25px;
    }
    .nav-elite-config {
        background: transparent; border: none; padding: 20px;
        margin-bottom: 10px; border-radius: 20px; display: flex; align-items: center;
        gap: 15px; text-align: left; transition: 0.3s; width: 100%; color: #64748b;
    }
    .nav-elite-config i { font-size: 22px; }
    .nav-elite-config .title { display: block; font-weight: 800; color: white; font-size: 15px; }
    .nav-elite-config .subtitle { display: block; font-size: 11px; opacity: 0.6; }
    
    .nav-elite-config.active {
        background: rgba(99, 102, 241, 0.15); border: 1px solid rgba(99, 102, 241, 0.3);
        color: var(--indigo-glow);
    }
    .nav-elite-config.active .title { color: var(--indigo-glow); }

    .config-help-box { padding: 20px; border-top: 1px solid var(--glass-border); }

    /* Config Cards */
    .card-config-glass {
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px); border-radius: 40px; overflow: hidden;
    }
    .card-header-elite { padding: 50px 50px 0; }
    
    .input-elite-group label { margin-bottom: 15px; }
    .input-wrapper { position: relative; }
    .input-wrapper i { position: absolute; left: 20px; top: 16px; color: #475569; font-size: 20px; }
    .form-control-elite {
        background: rgba(0, 0, 0, 0.2) !important;
        border: 2px solid var(--glass-border) !important;
        color: white !important; padding: 15px 25px 15px 55px !important;
        border-radius: 18px !important; transition: 0.3s !important;
    }
    .form-control-elite:focus { border-color: var(--indigo-glow) !important; box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1) !important; }

    /* Footer Actions */
    .btn-audit-elite {
        background: transparent; border: 1px solid var(--glass-border);
        color: #94a3b8; padding: 15px 30px; border-radius: 18px;
        font-weight: 800; font-size: 14px; transition: 0.3s;
    }
    .btn-audit-elite:hover { background: rgba(255,255,255,0.05); color: white; border-color: white; }

    .btn-deploy-elite {
        background: white; color: var(--dark-bg); border: none;
        padding: 15px 40px; border-radius: 18px; font-weight: 900;
        box-shadow: 0 10px 20px rgba(255,255,255,0.05); transition: 0.3s;
    }
    .btn-deploy-elite:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(255,255,255,0.1); }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#btn-audit').click(function() {
            const btn = $(this);
            const spinner = btn.find('.spinner-border');
            const icon = btn.find('i');
            
            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            icon.addClass('d-none');
            
            $.ajax({
                url: "{{ route('woocommerce.settings.test') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    app_url: $('#app_url').val(),
                    consumer_key: $('#consumer_key').val(),
                    consumer_secret: $('#consumer_secret').val()
                },
                success: function(response) {
                    if(response.success) {
                        alert('✅ ' + response.message);
                    } else {
                        alert('❌ ' + response.message);
                    }
                },
                error: function() {
                    alert('Critical Handshake Failure: Could not reach the bridge service.');
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                    icon.removeClass('d-none');
                }
            });
        });
    });
</script>
@endsection
