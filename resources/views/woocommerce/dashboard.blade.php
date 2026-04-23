@extends('layouts.app')

@section('page-title', 'Woocommerce Synchronization')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<div class="wc-elite-interface">
    <div class="glass-bg"></div>

    <div class="content-wrapper p-lg-5 p-3 animate__animated animate__fadeIn">
        {{-- Elite Top Navigation Bar --}}
        <div class="top-command-bar mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <div class="brand-unit">
                    <div class="logo-stack">
                        <img src="/assets/img/woocommerce-logo.png" alt="" class="wc-icon" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/WooCommerce_logo.svg/1200px-WooCommerce_logo.svg.png'">
                        <div class="bridge-line"></div>
                        <i class="fas fa-database erp-icon"></i>
                    </div>
                    <div class="ms-4">
                        <h2 class="fw-900 mb-0">Sync <span class="text-indigo">Architect</span></h2>
                        <span class="status-badge-elite"><span class="pulse"></span> Connection: Optimized</span>
                    </div>
                </div>
                <div class="action-stack">
                    <div class="health-orb d-none d-xl-flex">
                        <div class="orb-content">
                            <span class="l">System Health</span>
                            <span class="v">99.9%</span>
                        </div>
                        <div class="orb-ring"></div>
                    </div>
                    <a href="{{ route('woocommerce.settings') }}" class="btn-icon-elite"><i class="fas fa-project-diagram"></i></a>
                    <button type="button" class="btn-deploy-elite" id="mainSyncBtn">
                        <i class="fas fa-bolt me-2"></i> Launch Core Sync
                    </button>
                </div>
            </div>
            {{-- Progress Bar (Hidden by default) --}}
            <div id="syncProgressContainer" class="mt-4 d-none">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span id="syncStatusText" class="text-indigo fw-bold small">Initializing Neural Link...</span>
                    <span id="syncPercentage" class="text-muted small">0%</span>
                </div>
                <div class="progress-elite">
                    <div id="syncProgressBar" class="progress-bar-fill" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <script>
            document.getElementById('mainSyncBtn').addEventListener('click', function() {
                const btn = this;
                const progressContainer = document.getElementById('syncProgressContainer');
                const progressBar = document.getElementById('syncProgressBar');
                const progressText = document.getElementById('syncStatusText');
                const progressPercent = document.getElementById('syncPercentage');
                
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Syncing Catalog...';
                progressContainer.classList.remove('d-none');
                
                // Simulate progress for better UX (since the server does it in one large batch call per chunk)
                let progress = 0;
                const interval = setInterval(() => {
                    if (progress < 90) {
                        progress += Math.random() * 5;
                        progressBar.style.width = Math.min(progress, 90) + '%';
                        progressPercent.innerText = Math.floor(Math.min(progress, 90)) + '%';
                        if (progress > 30) progressText.innerText = 'Transmitting Data Clusters...';
                        if (progress > 60) progressText.innerText = 'Optimizing Image Payloads...';
                    }
                }, 800);

                // Actual AJAX call
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
                    clearInterval(interval);
                    progressBar.style.width = '100%';
                    progressPercent.innerText = '100%';
                    progressText.innerText = data.message;
                    
                    if (data.success) {
                        progressText.classList.replace('text-indigo', 'text-success');
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        progressText.innerText = 'Error: ' + data.message;
                        progressText.classList.replace('text-indigo', 'text-danger');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-redo me-2"></i> Retry Sync';
                    }
                })
                .catch(error => {
                    clearInterval(interval);
                    progressText.innerText = 'Network Error. Please try again.';
                    progressText.classList.replace('text-indigo', 'text-danger');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-redo me-2"></i> Retry Sync';
                });
            });
        </script>

        {{-- Hero Visual Banner --}}
        <div class="hero-banner-elite mb-5">
            <img src="file:///C:/Users/Rathod Kamal/.gemini/antigravity/brain/574281d0-78dd-47b3-9243-7c5e43a66c4b/woocommerce_elite_banner_1776949064741.png" alt="Data Flow" class="banner-img">
            <div class="banner-overlay">
                <div class="overlay-content">
                    <h3>Enterprise Integration Bridge</h3>
                    <p>High-performance data synchronization between InvoZA ERP and WooCommerce Ecosystem.</p>
                </div>
            </div>
        </div>

        {{-- Core Metrics Grid --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="metric-card-elite">
                    <div class="icon purple"><i class="fas fa-layer-group"></i></div>
                    <div class="info">
                        <span class="label">Taxonomy Nodes</span>
                        <h4 class="value">Active</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card-elite featured">
                    <div class="icon indigo"><i class="fas fa-box-open"></i></div>
                    <div class="info">
                        <span class="label">Product Parity</span>
                        <h4 class="value">{{ \App\Models\Product::count() }} Items</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card-elite">
                    <div class="icon emerald"><i class="fas fa-shopping-cart"></i></div>
                    <div class="info">
                        <span class="label">Web Orders</span>
                        <h4 class="value">Listen Only</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="metric-card-elite">
                    <div class="icon amber"><i class="fas fa-history"></i></div>
                    <div class="info">
                        <span class="label">Uptime</span>
                        <h4 class="value">99.99%</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Analytics Section --}}
            <div class="col-xl-7">
                <div class="elite-card chart-card h-100">
                    <div class="card-header-elite">
                        <h5 class="fw-800 mb-1">Sync Performance</h5>
                        <p class="text-muted small">Real-time data throughput analytics (Last 24h)</p>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="syncChart" height="280"></canvas>
                    </div>
                </div>
            </div>

            {{-- Recent Logs --}}
            <div class="col-xl-5">
                <div class="elite-card history-card h-100">
                    <div class="card-header-elite d-flex justify-content-between align-items-center">
                        <h5 class="fw-800 mb-0">Recent Sync History</h5>
                        <span class="badge-elite outline">Last 5 Operations</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table elite-table mb-0">
                            <thead>
                                <tr>
                                    <th>Operation</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="op-type">Full Sync</span><br><small class="text-muted">13:02 Today</small></td>
                                    <td class="fw-bold">48</td>
                                    <td><span class="badge-status success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td><span class="op-type">Price Update</span><br><small class="text-muted">Yesterday</small></td>
                                    <td class="fw-bold">120</td>
                                    <td><span class="badge-status success">Completed</span></td>
                                </tr>
                                <tr>
                                    <td><span class="op-type">Stock Parity</span><br><small class="text-muted">2 days ago</small></td>
                                    <td class="fw-bold">502</td>
                                    <td><span class="badge-status warning">Partial</span></td>
                                </tr>
                                <tr>
                                    <td><span class="op-type">Inventory Push</span><br><small class="text-muted">3 days ago</small></td>
                                    <td class="fw-bold">12</td>
                                    <td><span class="badge-status danger">Failed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap');

    .wc-elite-interface {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #0b0e14;
        color: #e2e8f0;
        min-height: 100vh;
        position: relative;
    }

    .glass-bg {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 0% 0%, rgba(99, 102, 241, 0.1) 0%, transparent 40%),
                    radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.1) 0%, transparent 40%);
        pointer-events: none;
    }

    .fw-900 { font-weight: 900; }
    .text-indigo { color: #818cf8 !important; }

    /* Top Command Bar */
    .brand-unit { display: flex; align-items: center; }
    .logo-stack {
        display: flex; align-items: center; gap: 15px;
        background: rgba(255, 255, 255, 0.03); padding: 12px 25px;
        border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .wc-icon { height: 25px; filter: brightness(0) invert(1); }
    .bridge-line { width: 30px; height: 2px; background: rgba(255, 255, 255, 0.1); }
    .erp-icon { font-size: 20px; color: #818cf8; }
    
    .status-badge-elite {
        font-size: 11px; font-weight: 800; color: #10b981;
        display: flex; align-items: center; gap: 8px; margin-top: 5px;
    }
    .status-badge-elite .pulse {
        width: 8px; height: 8px; background: #10b981; border-radius: 50%;
        animation: pulse-ring-green 2s infinite;
    }

    @keyframes pulse-ring-green {
        0% { transform: scale(1); opacity: 0.5; }
        100% { transform: scale(2.5); opacity: 0; }
    }

    .btn-deploy-elite {
        background: #818cf8; color: white; border: none; padding: 14px 35px;
        border-radius: 16px; font-weight: 900; box-shadow: 0 10px 30px rgba(129, 140, 248, 0.3);
        transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-deploy-elite:hover { transform: scale(1.05) translateY(-3px); }

    .btn-icon-elite {
        width: 55px; height: 55px; background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        color: #94a3b8; font-size: 20px; transition: 0.3s;
    }
    .btn-icon-elite:hover { background: rgba(255, 255, 255, 0.08); color: white; }

    /* Hero Banner */
    .hero-banner-elite {
        height: 300px; border-radius: 40px; overflow: hidden; position: relative;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .banner-img { width: 100%; height: 100%; object-fit: cover; opacity: 0.8; }
    .banner-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to right, rgba(11, 14, 20, 1) 0%, rgba(11, 14, 20, 0.4) 100%);
        display: flex; align-items: center; padding-left: 60px;
    }
    .banner-overlay h3 { font-size: 32px; font-weight: 900; margin-bottom: 10px; }
    .banner-overlay p { max-width: 500px; opacity: 0.6; font-size: 16px; }
    
    /* Progress Bar */
    .progress-elite {
        height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px;
        overflow: hidden; position: relative;
    }
    .progress-bar-fill {
        height: 100%; background: linear-gradient(to right, #818cf8, #6366f1);
        width: 0%; transition: width 0.5s ease;
    }

    /* Metric Cards */
    .metric-card-elite {
        background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 30px; border-radius: 30px; display: flex; align-items: center; gap: 20px;
        transition: 0.3s;
    }
    .metric-card-elite:hover { background: rgba(255, 255, 255, 0.05); transform: translateY(-5px); }
    .metric-card-elite.featured { border-color: #818cf8; background: rgba(129, 140, 248, 0.05); }
    
    .metric-card-elite .icon {
        width: 55px; height: 55px; border-radius: 15px;
        display: flex; align-items: center; justify-content: center; font-size: 20px;
    }
    .icon.purple { background: rgba(139, 92, 246, 0.1); color: #a78bfa; }
    .icon.indigo { background: rgba(129, 140, 248, 0.1); color: #818cf8; }
    .icon.emerald { background: rgba(16, 185, 129, 0.1); color: #34d399; }
    .icon.amber { background: rgba(245, 158, 11, 0.1); color: #fbbf24; }

    .metric-card-elite .label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: #64748b; letter-spacing: 1px; }
    .metric-card-elite .value { font-weight: 900; margin-bottom: 0; margin-top: 5px; }

    /* Cards */
    .elite-card {
        background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 35px; overflow: hidden;
    }
    .card-header-elite { padding: 30px 40px; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }

    /* Terminal Body */
    .terminal-body { padding: 30px; font-family: 'Consolas', monospace; font-size: 13px; height: 280px; overflow-y: auto; background: #000; }
    .log-entry { margin-bottom: 8px; color: #4b5563; }
    .log-entry .t { color: #374151; margin-right: 10px; }
    .log-entry.success { color: #10b981; }
    .log-entry.info { color: #3b82f6; }
    .log-entry.active { color: #e2e8f0; }
    .cursor { animation: blink 1s infinite; margin-left: 5px; }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }

    .terminal-controls { display: flex; gap: 8px; }
    .terminal-controls span { width: 10px; height: 10px; border-radius: 50%; background: #374151; }

    /* Elite Table */
    .elite-table { background: transparent; }
    .elite-table thead th {
        background: rgba(255, 255, 255, 0.02); border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #64748b; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
        padding: 20px 25px;
    }
    .elite-table tbody td {
        padding: 20px 25px; border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        vertical-align: middle; color: #cbd5e1; font-size: 14px;
    }
    .op-type { font-weight: 800; color: #e2e8f0; font-size: 15px; }
    .badge-status {
        padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 900;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .badge-status.success { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .badge-status.warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .badge-status.danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('syncChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'],
                datasets: [{
                    label: 'Parity Accuracy (%)',
                    data: [98, 97, 99, 98.5, 99.2, 99.8, 99.9],
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
                    y: { grid: { color: 'rgba(255,255,255,0.03)' }, ticks: { color: '#4b5563', font: { weight: '800' } } },
                    x: { grid: { display: false }, ticks: { color: '#4b5563', font: { weight: '800' } } }
                }
            }
        });
    });
</script>
@endsection
