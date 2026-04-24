@extends('layouts.app')

@section('page-title', 'Woocommerce Sync Architect')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="wc-elite-v2">
    <div class="aurora-bg"></div>
    
    <div class="container-fluid p-lg-5 p-3">
        {{-- Elite Header --}}
        <header class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center">
                <div class="logo-orb me-4">
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
                <div>
                    <h1 class="display-5 fw-900 mb-1 text-white">Bridge <span class="text-indigo-glow">Architect</span></h1>
                    <div class="d-flex align-items-center">
                        <span class="status-indicator online me-2"></span>
                        <span class="text-muted small fw-bold text-uppercase tracking-widest">Neural Link: Active</span>
                    </div>
                </div>
            </div>
            
            <div class="action-hub">
                <a href="{{ route('woocommerce.settings') }}" class="btn-settings-elite me-3">
                    <i class="bi bi-sliders2"></i>
                </a>
                <button type="button" class="btn-sync-core" id="mainSyncBtn">
                    <span class="btn-content">
                        <i class="bi bi-lightning-charge-fill me-2"></i> Launch Core Sync
                    </span>
                    <span class="btn-loader d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span> Transmitting...
                    </span>
                </button>
            </div>
        </header>

        {{-- Progress Overlay (Global) --}}
        <div id="syncOverlay" class="sync-overlay" style="display: none;">
            <div class="overlay-glass">
                <div class="sync-node-visual">
                    <div class="node-erp"><i class="bi bi-database-fill"></i><span>ERP</span></div>
                    <div class="node-line"><div class="pulse-flow"></div></div>
                    <div class="node-wc"><i class="bi bi-shop"></i><span>Woo</span></div>
                </div>
                <h4 id="syncStatusText" class="mt-4 fw-800 text-white">Initializing Data Stream...</h4>
                <div class="progress-container-elite mt-3">
                    <div id="syncProgressBar" class="progress-fill" style="width: 0%"></div>
                </div>
                <div id="syncPercentage" class="mt-2 text-indigo-glow fw-bold">0%</div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            {{-- Metrics Grid --}}
            <div class="col-xl-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="metric-card-glass animate__animated animate__zoomIn" style="animation-delay: 0.1s">
                            <div class="card-icon indigo"><i class="bi bi-box-seam"></i></div>
                            <div class="card-info">
                                <span class="label">Product Catalog</span>
                                <h2 class="value text-white">{{ \App\Models\Product::count() }} <small>Items</small></h2>
                            </div>
                            <div class="card-graph">
                                <div class="sparkline"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="metric-card-glass animate__animated animate__zoomIn" style="animation-delay: 0.2s">
                            <div class="card-icon emerald"><i class="bi bi-check2-circle"></i></div>
                            <div class="card-info">
                                <span class="label">Last Sync Success</span>
                                <h2 class="value text-white">{{ $stats['products']['count'] ?? 0 }} <small>Synced</small></h2>
                            </div>
                            <div class="card-badge success">Optimized</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="analytics-card-glass p-4 animate__animated animate__fadeInUp">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-800 text-white mb-0">Quick Actions</h5>
                                <div class="badge-elite-outline">Neural Links</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <a href="{{ route('woocommerce.products') }}" class="action-card-glass">
                                        <div class="action-icon purple"><i class="bi bi-box-seam"></i></div>
                                        <div class="action-info">
                                            <div class="title">Product Nexus</div>
                                            <div class="desc">Individual Product Sync</div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{ route('woocommerce.settings') }}" class="action-card-glass">
                                        <div class="action-icon blue"><i class="bi bi-gear-fill"></i></div>
                                        <div class="action-info">
                                            <div class="title">Config Bridge</div>
                                            <div class="desc">Manage API & Logic</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- History Sidebar --}}
            <div class="col-xl-4">
                <div class="history-panel-glass h-100 animate__animated animate__fadeInRight">
                    <div class="panel-header">
                        <h5 class="fw-800 text-white mb-0">Transmission Logs</h5>
                        <p class="text-muted small mb-0">Real-time operation history</p>
                    </div>
                    <div class="panel-body">
                        @php /** @var \App\Models\WoocommerceSyncLog $log */ @endphp
                        @forelse($logs ?? [] as $log)
                        <div class="log-item">
                            <div class="log-icon {{ strtolower($log->status) }}">
                                <i class="bi bi-{{ $log->status == 'Completed' ? 'check-lg' : ($log->status == 'Partial' ? 'exclamation-lg' : 'x-lg') }}"></i>
                            </div>
                            <div class="log-meta">
                                <div class="d-flex justify-content-between">
                                    <span class="type fw-bold text-white">{{ $log->operation_type }}</span>
                                    <span class="time text-muted small">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="stats small">
                                    <span class="text-indigo-glow fw-bold">{{ $log->items_success }}</span> / {{ $log->items_total }} items synced
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="empty-logs text-center p-5">
                            <i class="bi bi-cloud-slash display-4 text-muted"></i>
                            <p class="text-muted mt-3">No transmission logs available.</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="panel-footer">
                        <button class="btn-view-all">View Extended Logs <i class="bi bi-arrow-right ms-2"></i></button>
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
        --emerald-primary: #10b981;
        --dark-bg: #030712;
        --glass-bg: rgba(17, 24, 39, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
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
        background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                    radial-gradient(circle at 0% 100%, rgba(16, 185, 129, 0.1) 0%, transparent 40%);
        z-index: 0;
        pointer-events: none;
    }

    .fw-900 { font-weight: 900; }
    .text-indigo-glow { color: var(--indigo-glow) !important; }
    .tracking-widest { letter-spacing: 0.2em; }

    /* Header & Action Hub */
    .logo-orb {
        width: 65px; height: 65px;
        background: linear-gradient(135deg, var(--indigo-primary), #4f46e5);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; color: white;
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
    }

    .status-indicator {
        width: 10px; height: 10px; border-radius: 50%;
    }
    .status-indicator.online {
        background: var(--emerald-primary);
        box-shadow: 0 0 15px var(--emerald-primary);
        animation: pulse-online 2s infinite;
    }
    @keyframes pulse-online {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }

    .btn-sync-core {
        background: white; color: var(--dark-bg);
        border: none; padding: 14px 35px; border-radius: 16px;
        font-weight: 800; font-size: 15px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-sync-core:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 35px rgba(255, 255, 255, 0.15);
    }

    .btn-settings-elite {
        width: 55px; height: 55px; border-radius: 16px;
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        display: inline-flex; align-items: center; justify-content: center;
        color: white; font-size: 20px; transition: 0.3s; text-decoration: none;
    }
    .btn-settings-elite:hover { background: rgba(255,255,255,0.1); color: var(--indigo-glow); }

    /* Metric Cards */
    .metric-card-glass {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px);
        padding: 35px; border-radius: 35px;
        display: flex; align-items: center; gap: 25px;
        position: relative; overflow: hidden;
        transition: 0.4s;
    }
    .metric-card-glass:hover { border-color: var(--indigo-glow); transform: translateY(-5px); }

    .card-icon {
        width: 70px; height: 70px; border-radius: 22px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
    }
    .card-icon.indigo { background: rgba(99, 102, 241, 0.1); color: var(--indigo-glow); }
    .card-icon.emerald { background: rgba(16, 185, 129, 0.1); color: var(--emerald-primary); }

    .metric-card-glass .label { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #64748b; }
    .metric-card-glass .value { font-weight: 900; margin: 5px 0 0; }
    .metric-card-glass .value small { font-size: 14px; color: #64748b; font-weight: 600; }

    /* Analytics Card */
    .analytics-card-glass {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px);
        border-radius: 40px;
    }
    .badge-elite-outline {
        padding: 6px 16px; border-radius: 10px; border: 1px solid var(--glass-border);
        font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--indigo-glow);
    }

    /* History Panel */
    .history-panel-glass {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px);
        border-radius: 40px;
        display: flex; flex-direction: column;
    }
    .panel-header { padding: 35px; border-bottom: 1px solid var(--glass-border); }
    .panel-body { padding: 25px; flex-grow: 1; overflow-y: auto; max-height: 500px; }
    
    .log-item {
        display: flex; gap: 20px; padding: 20px;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 20px; margin-bottom: 15px;
        border: 1px solid transparent; transition: 0.3s;
    }
    .log-item:hover { border-color: var(--glass-border); background: rgba(255, 255, 255, 0.04); }
    
    .log-icon {
        width: 45px; height: 45px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; font-size: 18px;
    }
    .log-icon.completed { background: rgba(16, 185, 129, 0.1); color: var(--emerald-primary); }
    .log-icon.partial { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .log-icon.failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .log-meta { flex-grow: 1; }
    .log-meta .type { font-size: 15px; }

    .panel-footer { padding: 25px; border-top: 1px solid var(--glass-border); text-center: center; }
    .btn-view-all {
        background: transparent; border: none; color: #64748b;
        font-weight: 800; font-size: 13px; text-transform: uppercase;
        transition: 0.3s;
    }
    .btn-view-all:hover { color: white; }

    /* Quick Action Cards */
    .action-card-glass {
        display: flex; align-items: center; gap: 20px;
        background: rgba(255, 255, 255, 0.03); border: 1px solid var(--glass-border);
        padding: 20px; border-radius: 20px; text-decoration: none; transition: 0.3s;
    }
    .action-card-glass:hover {
        background: rgba(255, 255, 255, 0.07); transform: translateY(-5px);
        border-color: rgba(255, 255, 255, 0.2);
    }
    .action-icon {
        width: 50px; height: 50px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .action-icon.purple { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
    .action-icon.blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    
    .action-info .title { display: block; font-weight: 800; color: white; font-size: 15px; }
    .action-info .desc { font-size: 11px; color: #64748b; }

    /* Sync Overlay */
    .sync-overlay {
        position: absolute; inset: 0; z-index: 100;
        background: rgba(3, 7, 18, 0.98);
        display: none; align-items: center; justify-content: center;
        backdrop-filter: blur(15px);
        border-radius: 40px;
    }
    .overlay-glass {
        text-align: center; max-width: 500px; width: 90%;
    }
    .sync-node-visual {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 40px;
    }
    .node-erp, .node-wc {
        width: 100px; height: 100px; background: var(--glass-bg);
        border: 1px solid var(--indigo-glow); border-radius: 30px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        color: white; font-size: 35px;
    }
    .node-erp span, .node-wc span { font-size: 12px; font-weight: 800; margin-top: 8px; color: var(--indigo-glow); }
    
    .node-line { flex-grow: 1; height: 2px; background: var(--glass-border); margin: 0 20px; position: relative; }
    .pulse-flow {
        position: absolute; top: 0; left: 0; height: 100%; width: 50px;
        background: linear-gradient(to right, transparent, var(--indigo-glow), transparent);
        animation: flow 1.5s infinite linear;
    }
    @keyframes flow { 0% { left: -50px; } 100% { left: 100%; } }

    .progress-container-elite { height: 10px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; }
    .progress-fill { height: 100%; background: var(--indigo-glow); box-shadow: 0 0 15px var(--indigo-glow); transition: 0.4s; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const syncBtn = document.getElementById('mainSyncBtn');
        const syncOverlay = document.getElementById('syncOverlay');
        const progressBar = document.getElementById('syncProgressBar');
        const progressText = document.getElementById('syncStatusText');
        const progressPercent = document.getElementById('syncPercentage');

        syncBtn.addEventListener('click', function() {
            syncBtn.querySelector('.btn-content').classList.add('d-none');
            syncBtn.querySelector('.btn-loader').classList.remove('d-none');
            syncOverlay.style.display = 'flex';
            
            let progress = 0;
            const fakeProgress = setInterval(() => {
                if (progress < 90) {
                    progress += Math.random() * 5;
                    updateProgress(progress, 'Syncing Data Clusters...');
                }
            }, 800);

            fetch("{{ route('woocommerce.sync.products') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(fakeProgress);
                updateProgress(100, data.message);
                
                if (data.success) {
                    setTimeout(() => location.reload(), 1500);
                } else {
                    progressText.classList.add('text-danger');
                    setTimeout(() => {
                        syncOverlay.style.display = 'none';
                        syncBtn.querySelector('.btn-content').classList.remove('d-none');
                        syncBtn.querySelector('.btn-loader').classList.add('d-none');
                    }, 3000);
                }
            })
            .catch(error => {
                clearInterval(fakeProgress);
                updateProgress(0, 'Critical Network Error!');
                progressText.classList.add('text-danger');
            });
        });

        function updateProgress(value, status) {
            const val = Math.min(value, 100);
            progressBar.style.width = val + '%';
            progressPercent.innerText = Math.floor(val) + '%';
            if (status) progressText.innerText = status;
        }

        // Initialize Analytics Chart
        const ctx = document.getElementById('syncChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Efficiency',
                    data: [65, 78, 82, 75, 94, 88, 99],
                    borderColor: '#818cf8',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(129, 140, 248, 0.1)',
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#64748b' } },
                    x: { grid: { display: false }, ticks: { color: '#64748b' } }
                }
            }
        });
    });
</script>
@endsection
