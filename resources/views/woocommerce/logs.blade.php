@extends('layouts.app')

@section('page-title', 'Neural Transmission Logs')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap">

<div class="wc-elite-v2 logs-mode">
    <div class="aurora-bg"></div>
    
    <div class="container-fluid p-lg-5 p-3">
        {{-- Elite Header --}}
        <header class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center">
                <a href="{{ route('woocommerce.index') }}" class="btn-back-elite me-4">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="display-6 fw-900 mb-1 text-white">Transmission <span class="text-indigo-glow">History</span></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('woocommerce.index') }}" class="text-muted text-decoration-none">Architect</a></li>
                            <li class="breadcrumb-item active text-indigo-glow fw-bold">Neural Logs</li>
                        </ol>
                    </nav>
                </div>
            </div>
            
            <div class="header-actions">
                <div class="badge-elite-outline">Audit Trail v2.4</div>
            </div>
        </header>

        <div class="row">
            <div class="col-12">
                <div class="logs-panel-glass animate__animated animate__fadeInUp">
                    <div class="panel-header d-flex justify-content-between align-items-center p-4 border-bottom border-glass">
                        <div>
                            <h5 class="fw-800 text-white mb-0">System Operation Logs</h5>
                            <p class="text-muted small mb-0">Detailed breakdown of neural bridge synchronization</p>
                        </div>
                        <div class="search-box-elite">
                            <i class="bi bi-search"></i>
                            <input type="text" id="logSearch" placeholder="Filter operations...">
                        </div>
                    </div>
                    
                    <div class="panel-body p-0">
                        <div class="table-responsive">
                            <table class="table table-dark-elite mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Timestamp</th>
                                        <th>Operation</th>
                                        <th>Status</th>
                                        <th>Integrity</th>
                                        <th>Details</th>
                                        <th class="text-end pe-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr class="log-row">
                                        <td class="ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="text-white fw-bold">{{ $log->created_at->format('M d, H:i:s') }}</span>
                                                <span class="text-muted smaller">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="badge-type {{ strtolower($log->operation_type) }}">
                                                {{ $log->operation_type }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="status-pill {{ strtolower($log->status) }}">
                                                <span class="dot"></span> {{ $log->status }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="integrity-metric">
                                                <div class="progress-mini">
                                                    @php 
                                                        $percent = $log->items_total > 0 ? ($log->items_success / $log->items_total) * 100 : 0;
                                                        $color = $percent == 100 ? '#10b981' : ($percent > 0 ? '#f59e0b' : '#ef4444');
                                                    @endphp
                                                    <div class="fill" style="width: {{ $percent }}%; background: {{ $color }}"></div>
                                                </div>
                                                <span class="ms-2 small fw-bold text-white">{{ $log->items_success }} / {{ $log->items_total }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="details-snippet text-muted small">
                                                @if(is_array($log->details) && isset($log->details['triggered_by']))
                                                    By: {{ $log->details['triggered_by'] }}
                                                @elseif(is_array($log->details) && isset($log->details['product_name']))
                                                    Product: {{ $log->details['product_name'] }}
                                                @else
                                                    System Automatic
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn-inspect" onclick="inspectLog({{ json_encode($log) }})">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="bi bi-database-exclamation display-1 text-muted-dim"></i>
                                                <h4 class="text-white mt-3">No Transmission Logs</h4>
                                                <p class="text-muted">Initiate a sync to begin recording operations.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="panel-footer p-4 border-top border-glass d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Log Inspection Modal --}}
<div id="logModal" class="log-modal" style="display: none;">
    <div class="modal-glass">
        <div class="modal-header-elite">
            <h5 class="text-white fw-900 mb-0">Transmission Diagnostics</h5>
            <button onclick="closeModal()" class="btn-close-modal"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="modal-body-elite">
            <div id="modalContent"></div>
        </div>
    </div>
</div>

<style>
    :root {
        --indigo-primary: #6366f1;
        --indigo-glow: #818cf8;
        --emerald-primary: #10b981;
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

    /* Back Button */
    .btn-back-elite {
        width: 50px; height: 50px; border-radius: 15px;
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        display: flex; align-items: center; justify-content: center;
        color: white; text-decoration: none; transition: 0.3s;
    }
    .btn-back-elite:hover { background: rgba(255,255,255,0.1); color: var(--indigo-glow); transform: translateX(-5px); }

    /* Logs Panel */
    .logs-panel-glass {
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px); border-radius: 40px; overflow: hidden;
    }
    .border-glass { border-color: var(--glass-border) !important; }

    .search-box-elite {
        position: relative; width: 300px;
    }
    .search-box-elite i { position: absolute; left: 20px; top: 12px; color: #475569; }
    .search-box-elite input {
        width: 100%; background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
        padding: 10px 20px 10px 50px; border-radius: 15px; color: white; outline: none; transition: 0.3s;
    }
    .search-box-elite input:focus { border-color: var(--indigo-glow); box-shadow: 0 0 15px rgba(99, 102, 241, 0.2); }

    /* Table Styles */
    .table-dark-elite { color: #94a3b8; }
    .table-dark-elite thead th {
        background: rgba(255, 255, 255, 0.02); color: #475569; font-size: 11px;
        text-transform: uppercase; letter-spacing: 1.5px; font-weight: 800; border: none;
        padding: 20px 15px;
    }
    .table-dark-elite tbody tr { border-bottom: 1px solid rgba(255,255,255,0.03); transition: 0.3s; }
    .table-dark-elite tbody tr:hover { background: rgba(255, 255, 255, 0.02); }
    .table-dark-elite td { padding: 25px 15px; vertical-align: middle; border: none; }

    .smaller { font-size: 11px; }

    /* Badge & Status */
    .badge-type {
        display: inline-block; padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase;
    }
    .badge-type.full { background: rgba(99, 102, 241, 0.1); color: var(--indigo-glow); }
    .badge-type.single { background: rgba(168, 85, 247, 0.1); color: #a855f7; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 30px;
        font-size: 12px; font-weight: 700;
    }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; }
    .status-pill.completed { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-pill.completed .dot { background: #10b981; box-shadow: 0 0 8px #10b981; }
    .status-pill.partial { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .status-pill.partial .dot { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
    .status-pill.failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .status-pill.failed .dot { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

    /* Integrity Metric */
    .integrity-metric { display: flex; align-items: center; }
    .progress-mini { width: 60px; height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; }
    .progress-mini .fill { height: 100%; transition: 0.5s; }

    .btn-inspect {
        width: 35px; height: 35px; border-radius: 10px; border: 1px solid var(--glass-border);
        background: transparent; color: #64748b; transition: 0.3s;
    }
    .btn-inspect:hover { border-color: var(--indigo-glow); color: var(--indigo-glow); background: rgba(99, 102, 241, 0.1); }

    /* Modal */
    .log-modal {
        position: fixed; inset: 0; z-index: 1000; background: rgba(0,0,0,0.8); backdrop-filter: blur(10px);
        display: flex; align-items: center; justify-content: center; padding: 20px;
    }
    .modal-glass {
        background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: 35px;
        width: 100%; max-width: 600px; overflow: hidden; animation: zoomIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .modal-header-elite { padding: 30px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; }
    .modal-body-elite { padding: 30px; }
    .btn-close-modal { background: transparent; border: none; color: #64748b; font-size: 20px; }

    .diagnostic-card { background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); border-radius: 20px; padding: 20px; margin-bottom: 15px; }
    .diagnostic-card .label { display: block; font-size: 10px; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 5px; }
    .diagnostic-card .val { font-size: 15px; color: white; font-weight: 600; }

    .raw-log-box { background: #000; padding: 20px; border-radius: 15px; font-family: monospace; font-size: 12px; color: #10b981; max-height: 200px; overflow-y: auto; }

    /* Pagination Styling */
    .pagination { gap: 8px; }
    .page-item .page-link { background: var(--glass-bg); border: 1px solid var(--glass-border); color: #94a3b8; border-radius: 10px !important; padding: 8px 16px; }
    .page-item.active .page-link { background: var(--indigo-primary); border-color: var(--indigo-primary); color: white; }
</style>

<script>
    function inspectLog(log) {
        const modal = document.getElementById('logModal');
        const content = document.getElementById('modalContent');
        
        let detailsHtml = '';
        if (log.details) {
            try {
                const details = typeof log.details === 'string' ? JSON.parse(log.details) : log.details;
                detailsHtml = '<div class="raw-log-box">' + JSON.stringify(details, null, 2) + '</div>';
            } catch(e) {
                detailsHtml = '<div class="raw-log-box">' + log.details + '</div>';
            }
        } else {
            detailsHtml = '<div class="text-muted italic">No raw data available for this transmission.</div>';
        }

        content.innerHTML = `
            <div class="row g-3">
                <div class="col-6">
                    <div class="diagnostic-card">
                        <span class="label">Operation</span>
                        <span class="val">${log.operation_type}</span>
                    </div>
                </div>
                <div class="col-6">
                    <div class="diagnostic-card">
                        <span class="label">Status</span>
                        <span class="val ${log.status.toLowerCase()}">${log.status}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="diagnostic-card">
                        <span class="label">Total Items</span>
                        <span class="val">${log.items_total}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="diagnostic-card">
                        <span class="label text-success">Success</span>
                        <span class="val text-success">${log.items_success}</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="diagnostic-card">
                        <span class="label text-danger">Failed</span>
                        <span class="val text-danger">${log.items_failed}</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="diagnostic-card">
                        <span class="label">Raw Transmission Data / Error Logs</span>
                        ${detailsHtml}
                    </div>
                </div>
            </div>
        `;
        
        modal.style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('logModal').style.display = 'none';
    }

    // Basic Filter
    document.getElementById('logSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.log-row').forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // Close on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('logModal');
        if (event.target == modal) closeModal();
    }
</script>
@endsection
