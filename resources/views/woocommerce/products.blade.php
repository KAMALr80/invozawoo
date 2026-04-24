@extends('layouts.app')

@section('page-title', 'Product Nexus')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div class="wc-elite-v2 nexus-mode">
    <div class="aurora-bg"></div>
    
    <div class="container-fluid p-lg-5 p-3">
        {{-- Elite Header --}}
        <header class="d-flex justify-content-between align-items-center mb-5 animate__animated animate__fadeIn">
            <div>
                <h1 class="display-6 fw-900 mb-1 text-white">Product <span class="text-indigo-glow">Nexus</span></h1>
                <p class="text-muted mb-0">Granular synchronization control for your neural product bridge.</p>
            </div>
            
            <div class="header-actions d-flex gap-3">
                <a href="{{ route('woocommerce.index') }}" class="btn-nexus-outline">
                    <i class="bi bi-cpu me-2"></i> Console
                </a>
                <div class="badge-status-glow">
                    <span class="status-dot"></span> Bridge Online
                </div>
            </div>
        </header>

        {{-- Search & Filters --}}
        <div class="card-nexus-glass mb-5 p-4 animate__animated animate__fadeInUp">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <div class="nexus-search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="nexusSearch" class="form-control-nexus" placeholder="Search by name or SKU...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select-nexus">
                        <option>All Categories</option>
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <span class="text-muted small">Showing {{ $products->count() }} of {{ $products->total() }} units</span>
                </div>
            </div>
        </div>

        {{-- Products Table --}}
        <div class="card-nexus-glass overflow-hidden animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="table-responsive">
                <table class="table nexus-table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-5">Product Info</th>
                            <th>SKU / Code</th>
                            <th>Stock Status</th>
                            <th>Pricing</th>
                            <th class="text-end pe-5">Neural Sync</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td class="ps-5">
                                <div class="d-flex align-items-center">
                                    <div class="product-orb me-3">
                                        @if($product->image)
                                            @if(filter_var($product->image, FILTER_VALIDATE_URL))
                                                <img src="{{ $product->image }}" alt="">
                                            @else
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="">
                                            @endif
                                        @else
                                            <i class="bi bi-box-seam"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-800 text-white">{{ $product->name }}</div>
                                        <div class="text-muted-dim small">ID: #{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="sku-badge">{{ $product->product_code }}</code>
                            </td>
                            <td>
                                @if($product->quantity > 10)
                                    <span class="stock-badge in-stock">
                                        <i class="bi bi-check2-circle me-1"></i> {{ $product->quantity }} Units
                                    </span>
                                @elseif($product->quantity > 0)
                                    <span class="stock-badge low-stock">
                                        <i class="bi bi-exclamation-triangle me-1"></i> {{ $product->quantity }} Low
                                    </span>
                                @else
                                    <span class="stock-badge out-of-stock">
                                        <i class="bi bi-x-circle me-1"></i> Out of Stock
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="price-nexus">₹{{ number_format($product->price, 2) }}</div>
                            </td>
                            <td class="text-end pe-5">
                                <button class="btn-pulse-sync" data-id="{{ $product->id }}" onclick="pulseSync(this)">
                                    <span class="btn-text"><i class="bi bi-arrow-repeat me-1"></i> Pulse Sync</span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                    </span>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="nexus-pagination p-4 border-top border-glass">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Toast Notification --}}
<div id="nexusToast" class="nexus-toast animate__animated"></div>

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

    /* Header UI */
    .btn-nexus-outline {
        padding: 10px 20px; background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border); border-radius: 12px;
        color: white; text-decoration: none; font-weight: 700; font-size: 14px;
        transition: 0.3s;
    }
    .btn-nexus-outline:hover { background: rgba(255,255,255,0.08); border-color: white; }

    .badge-status-glow {
        padding: 8px 16px; background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 12px;
        color: #10b981; font-size: 12px; font-weight: 800;
        display: flex; align-items: center; gap: 8px;
    }
    .status-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; }

    /* Card & Forms */
    .card-nexus-glass {
        background: var(--glass-bg); border: 1px solid var(--glass-border);
        backdrop-filter: blur(20px); border-radius: 30px;
    }
    
    .nexus-search-wrapper { position: relative; }
    .nexus-search-wrapper i { position: absolute; left: 20px; top: 12px; color: #475569; }
    .form-control-nexus {
        background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
        border-radius: 15px; padding: 12px 20px 12px 50px; color: white; transition: 0.3s;
    }
    .form-control-nexus:focus { background: rgba(0,0,0,0.4); border-color: var(--indigo-glow); box-shadow: none; color: white; }

    .form-select-nexus {
        background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border);
        border-radius: 15px; padding: 12px 20px; color: white; appearance: none;
    }

    /* Table UI */
    .nexus-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .nexus-table th { background: rgba(255,255,255,0.02); padding: 20px; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #475569; border-bottom: 1px solid var(--glass-border); }
    .nexus-table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid var(--glass-border); }
    .nexus-table tr:hover td { background: rgba(255,255,255,0.01); }

    .product-orb {
        width: 45px; height: 45px; border-radius: 14px; background: rgba(0,0,0,0.3);
        border: 1px solid var(--glass-border); display: flex; align-items: center; justify-content: center;
        overflow: hidden; font-size: 20px; color: #475569;
    }
    .product-orb img { width: 100%; height: 100%; object-fit: cover; }

    .sku-badge { background: rgba(99, 102, 241, 0.1); color: var(--indigo-glow); padding: 4px 10px; border-radius: 6px; font-size: 12px; }
    
    .stock-badge { padding: 6px 12px; border-radius: 30px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; }
    .stock-badge.in-stock { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .stock-badge.low-stock { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stock-badge.out-of-stock { background: rgba(239, 68, 68, 0.1); color: #ef4444; }

    .price-nexus { font-size: 16px; font-weight: 800; color: white; }

    /* Action Button */
    .btn-pulse-sync {
        background: var(--indigo-primary); color: white; border: none; padding: 10px 20px;
        border-radius: 12px; font-weight: 800; font-size: 13px; transition: 0.3s;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
    }
    .btn-pulse-sync:hover { background: var(--indigo-glow); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4); }
    .btn-pulse-sync:disabled { opacity: 0.5; transform: none; box-shadow: none; }

    /* Toast */
    .nexus-toast {
        position: fixed; bottom: 30px; right: 30px; z-index: 9999;
        padding: 15px 25px; border-radius: 15px; background: var(--glass-bg);
        border: 1px solid var(--indigo-glow); backdrop-filter: blur(10px);
        color: white; font-weight: 700; display: none;
    }

    /* Pagination Styling */
    .nexus-pagination .pagination { gap: 10px; margin-bottom: 0; }
    .nexus-pagination .page-link { 
        background: rgba(255,255,255,0.03) !important; 
        border: 1px solid var(--glass-border) !important; 
        color: #94a3b8 !important; 
        border-radius: 12px !important; 
        padding: 10px 18px !important;
        transition: 0.3s;
    }
    .nexus-pagination .page-item.active .page-link { 
        background: var(--indigo-primary) !important; 
        border-color: var(--indigo-primary) !important; 
        color: white !important; 
    }
    .nexus-pagination .page-link:hover {
        background: rgba(255,255,255,0.1) !important;
        color: white !important;
    }
</style>

<script>
    function pulseSync(btn) {
        const productId = btn.getAttribute('data-id');
        const text = btn.querySelector('.btn-text');
        const loader = btn.querySelector('.btn-loading');
        const toast = document.getElementById('nexusToast');

        // State: Loading
        btn.disabled = true;
        text.classList.add('d-none');
        loader.classList.remove('d-none');

        fetch(`/woocommerce/sync/single/${productId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                btn.classList.replace('btn-pulse-sync', 'btn-success');
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Synced';
            } else {
                resetBtn(btn, text, loader);
            }
        })
        .catch(error => {
            showToast('Handshake Interrupted. Check network.', 'error');
            resetBtn(btn, text, loader);
        });
    }

    function resetBtn(btn, text, loader) {
        btn.disabled = false;
        text.classList.remove('d-none');
        loader.classList.add('d-none');
    }

    function showToast(msg, type) {
        const toast = document.getElementById('nexusToast');
        toast.innerText = msg;
        toast.style.display = 'block';
        toast.classList.add('animate__fadeInRight');
        toast.style.borderColor = type === 'success' ? '#10b981' : '#ef4444';
        
        setTimeout(() => {
            toast.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
            setTimeout(() => {
                toast.style.display = 'none';
                toast.classList.remove('animate__fadeOutRight');
            }, 500);
        }, 4000);
    }

    // Basic live search
    document.getElementById('nexusSearch').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.nexus-table tbody tr');
        
        rows.forEach(row => {
            const name = row.querySelector('.fw-800').innerText.toLowerCase();
            const sku = row.querySelector('.sku-badge').innerText.toLowerCase();
            if (name.includes(query) || sku.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endsection
