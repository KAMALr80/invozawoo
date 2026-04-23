@extends('layouts.app')

@section('page-title', 'Woocommerce Settings')

@section('content')
<div class="wc-elite-interface settings-mode">
    <div class="glass-bg"></div>

    <div class="content-wrapper p-lg-5 p-3">
        {{-- Elite Header --}}
        <header class="d-flex justify-content-between align-items-end mb-5 animate__animated animate__fadeIn">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('woocommerce.index') }}" class="text-indigo fw-bold">Console</a></li>
                        <li class="breadcrumb-item active">Infrastructure</li>
                    </ol>
                </nav>
                <h1 class="display-6 fw-900 mb-0">Bridge <span class="text-indigo">Configuration</span></h1>
            </div>
            <div class="header-status">
                <div class="badge-elite outline">v3.8.0 Stable</div>
            </div>
        </header>

        <div class="row g-5">
            {{-- Navigation Pane --}}
            <div class="col-xl-3">
                <div class="elite-sidebar-nav">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist">
                        <button class="nav-elite-link active" data-bs-toggle="pill" data-bs-target="#tab-api">
                            <i class="fas fa-key"></i>
                            <div class="link-meta">
                                <span class="title">API Ecosystem</span>
                                <span class="desc">Keys & Endpoints</span>
                            </div>
                        </button>
                        <button class="nav-elite-link" data-bs-toggle="pill" data-bs-target="#tab-sync">
                            <i class="fas fa-sync-alt"></i>
                            <div class="link-meta">
                                <span class="title">Sync Logic</span>
                                <span class="desc">Behavior & Rules</span>
                            </div>
                        </button>
                        <button class="nav-elite-link" data-bs-toggle="pill" data-bs-target="#tab-mapping">
                            <i class="fas fa-project-diagram"></i>
                            <div class="link-meta">
                                <span class="title">Field Mapping</span>
                                <span class="desc">Data Translation</span>
                            </div>
                        </button>
                    </div>

                    <div class="sidebar-help mt-5">
                        <div class="help-card-premium">
                            <i class="fas fa-question-circle"></i>
                            <h6>Need Assistance?</h6>
                            <p>Our API architects are ready to help you scale.</p>
                            <a href="#" class="btn-help">Open Docs</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Configuration Pane --}}
            <div class="col-xl-9">
                <div class="tab-content" id="v-pills-tabContent">
                    {{-- API Tab --}}
                    <div class="tab-pane fade show active" id="tab-api">
                        <div class="elite-form-card">
                            <div class="form-header">
                                <h4 class="fw-800">REST API Infrastructure</h4>
                                <p class="text-muted">Establish the primary data bridge to your WooCommerce instance.</p>
                            </div>

                            <form id="woocommerce-settings-form" action="{{ route('woocommerce.settings.update') }}" method="POST" class="p-0">
                                @csrf
                                <div class="row g-4 px-5 pb-5">
                                    <div class="col-12">
                                        <div class="elite-input-group-premium">
                                            <label>App Base URL</label>
                                            <div class="input-container">
                                                <i class="fas fa-link"></i>
                                                <input type="url" name="app_url" id="app_url" class="form-control" value="{{ $settings->app_url }}" placeholder="https://store.example.com">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="elite-input-group-premium">
                                            <label>Consumer Key</label>
                                            <div class="input-container">
                                                <i class="fas fa-shield-alt"></i>
                                                <input type="text" name="consumer_key" id="consumer_key" class="form-control" value="{{ $settings->consumer_key }}" placeholder="ck_xxxxxxxxxxxxxxxx">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="elite-input-group-premium">
                                            <label>Consumer Secret</label>
                                            <div class="input-container">
                                                <i class="fas fa-lock"></i>
                                                <input type="password" name="consumer_secret" id="consumer_secret" class="form-control" value="{{ $settings->consumer_secret }}" placeholder="cs_xxxxxxxxxxxxxxxx">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-5">
                                        <div class="action-footer d-flex justify-content-between align-items-center">
                                            <button type="button" id="btn-test-connection" class="btn-audit">
                                                <span class="spinner-border spinner-border-sm d-none me-2" role="status"></span>
                                                <i class="fas fa-microscope me-2"></i> Audit Connection
                                            </button>
                                            <button type="submit" class="btn-deploy">
                                                Deploy Changes <i class="fas fa-chevron-right ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Mapping Tab Placeholder --}}
                    <div class="tab-pane fade" id="tab-mapping">
                        <div class="elite-form-card p-5 text-center">
                            <div class="empty-state">
                                <div class="icon-box-xl mx-auto mb-4">
                                    <i class="fas fa-drafting-compass"></i>
                                </div>
                                <h4 class="fw-800">Advanced Mapping Engine</h4>
                                <p class="text-muted">Currently using system default translations. Custom mapping will be enabled in v4.0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

    .wc-elite-interface {
        font-family: 'Inter', sans-serif;
        background: #f8fafc;
        min-height: 100vh;
        position: relative;
        color: #1e293b;
    }

    .glass-bg {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.05) 0%, transparent 40%);
        pointer-events: none;
    }

    .fw-900 { font-weight: 900; }
    .text-indigo { color: #6366f1 !important; }

    /* Breadcrumbs */
    .breadcrumb-item + .breadcrumb-item::before { content: "→"; color: #cbd5e1; }
    .breadcrumb-item a { text-decoration: none; }

    /* Nav Links */
    .nav-elite-link {
        background: transparent; border: none; padding: 20px 25px;
        margin-bottom: 12px; border-radius: 20px; display: flex; align-items: center;
        gap: 20px; text-align: left; transition: all 0.3s ease; width: 100%;
        color: #64748b;
    }
    .nav-elite-link i { font-size: 20px; opacity: 0.5; }
    .nav-elite-link .title { display: block; font-weight: 800; font-size: 15px; }
    .nav-elite-link .desc { font-size: 11px; opacity: 0.7; font-weight: 500; }

    .nav-elite-link.active {
        background: white; color: #6366f1;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.1);
    }
    .nav-elite-link.active i { opacity: 1; }

    /* Help Card */
    .help-card-premium {
        background: #0f172a; color: white; padding: 30px; border-radius: 30px;
        position: relative; overflow: hidden;
    }
    .help-card-premium i { font-size: 40px; opacity: 0.1; position: absolute; right: -10px; bottom: -10px; }
    .help-card-premium h6 { font-weight: 800; margin-bottom: 10px; }
    .help-card-premium p { font-size: 12px; opacity: 0.6; line-height: 1.6; }
    .btn-help {
        display: inline-block; margin-top: 15px; color: #6366f1; font-weight: 800;
        font-size: 12px; text-decoration: none; text-transform: uppercase; letter-spacing: 1px;
    }

    /* Elite Form Card */
    .elite-form-card {
        background: white; border-radius: 40px; border: 1px solid #f1f5f9;
        box-shadow: 0 40px 80px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }
    .form-header { padding: 50px; background: #fcfdff; border-bottom: 1px solid #f1f5f9; margin-bottom: 50px; }

    .elite-input-group-premium label {
        display: block; font-weight: 800; font-size: 12px; text-transform: uppercase;
        color: #94a3b8; letter-spacing: 1px; margin-bottom: 12px;
    }
    .input-container { position: relative; }
    .input-container i { position: absolute; left: 22px; top: 18px; color: #cbd5e1; }
    .input-container .form-control {
        border: 2px solid #f1f5f9; padding: 16px 25px; padding-left: 55px;
        border-radius: 20px; font-weight: 600; transition: all 0.3s ease;
    }
    .input-container .form-control:focus {
        border-color: #6366f1; box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.05);
    }

    /* Actions */
    .btn-audit {
        background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px 30px;
        border-radius: 18px; font-weight: 700; color: #64748b; transition: 0.3s;
    }
    .btn-audit:hover { background: #f1f5f9; color: #1e293b; border-color: #cbd5e1; }

    .btn-deploy {
        background: #0f172a; color: white; border: none; padding: 15px 40px;
        border-radius: 18px; font-weight: 800; box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
        transition: 0.3s;
    }
    .btn-deploy:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(15, 23, 42, 0.3); }

    .icon-box-xl {
        width: 100px; height: 100px; background: #f5f3ff; color: #6366f1;
        border-radius: 30px; display: flex; align-items: center; justify-content: center;
        font-size: 40px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#btn-test-connection').click(function() {
            const btn = $(this);
            const spinner = btn.find('.spinner-border');
            
            btn.prop('disabled', true);
            spinner.removeClass('d-none');
            
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
                    alert(response.message);
                },
                error: function() {
                    alert('An error occurred while auditing the connection.');
                },
                complete: function() {
                    btn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            });
        });
    });
</script>
@endsection
