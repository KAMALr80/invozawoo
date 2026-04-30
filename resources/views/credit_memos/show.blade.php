@extends('layouts.app')

@section('page-title', 'Credit Memo #' . $creditMemo->cm_number)

@section('content')
<div class="bg-orbs">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<div class="container py-4 px-4 position-relative">
    {{-- Header Actions --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('credit-memos.index') }}" class="btn btn-white shadow-sm rounded-pill px-4">
            <i class="fas fa-arrow-left me-2 text-primary"></i> Back to Hub
        </a>
        <div class="d-flex gap-2">
            <button class="btn btn-white shadow-sm rounded-pill px-4" onclick="window.print()">
                <i class="fas fa-print me-2 text-success"></i> Print CM
            </button>
            <button class="btn btn-primary shadow-sm rounded-pill px-4">
                <i class="fas fa-download me-2"></i> Download PDF
            </button>
        </div>
    </div>

    {{-- Main CM Card --}}
    <div class="glass-card overflow-hidden">
        {{-- CM Header Banner --}}
        <div class="p-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
            <div class="position-relative z-index-1 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-uppercase fw-bold opacity-75 mb-1 tracking-widest">Official Credit Memo</h5>
                    <h1 class="display-5 fw-bold mb-0">#{{ $creditMemo->cm_number }}</h1>
                </div>
                <div class="text-end">
                    <span class="badge bg-white text-primary rounded-pill px-4 py-2 fw-bold shadow-sm mb-2" style="font-size: 1rem;">
                        {{ strtoupper($creditMemo->status) }}
                    </span>
                    <div class="small opacity-75 fw-medium">Issued on {{ $creditMemo->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
            {{-- Decorative background icon --}}
            <i class="fas fa-undo-alt position-absolute" style="font-size: 200px; right: -20px; bottom: -50px; opacity: 0.1; transform: rotate(-15deg);"></i>
        </div>

        <div class="card-body p-5">
            {{-- Info Grid --}}
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Customer Details</label>
                    <div class="fw-bold text-dark h5 mb-1">{{ $creditMemo->customer->name }}</div>
                    <div class="text-muted small">
                        <i class="fas fa-phone-alt me-1"></i> {{ $creditMemo->customer->mobile }}<br>
                        <i class="fas fa-envelope me-1"></i> {{ $creditMemo->customer->email ?? 'No email provided' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Original Reference</label>
                    <div class="fw-bold text-dark mb-1">
                        Invoice <a href="{{ route('sales.show', $creditMemo->sale_id) }}" class="text-primary text-decoration-none">#{{ $creditMemo->sale->invoice_no }}</a>
                    </div>
                    <div class="text-muted small">Dated: {{ $creditMemo->sale->sale_date->format('d M Y') }}</div>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Return Configuration</label>
                    <div class="fw-bold text-dark mb-1">Type: {{ ucfirst($creditMemo->type) }}</div>
                    <div class="text-muted small">Restocking: <span class="badge {{ $creditMemo->restock ? 'bg-success-soft text-success' : 'bg-light text-muted' }} px-2">{{ $creditMemo->restock ? 'Yes' : 'No' }}</span></div>
                </div>
                <div class="col-md-3 text-md-end">
                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block">Refund Reconciliation</label>
                    <div class="h5 fw-bold text-primary mb-1">{{ strtoupper($creditMemo->refund_method) }}</div>
                    <div class="text-muted small">Processed by {{ $creditMemo->creator->name ?? 'System' }}</div>
                </div>
            </div>

            {{-- Reason Section --}}
            @if($creditMemo->reason)
            <div class="p-4 rounded-4 mb-5" style="background: rgba(79, 70, 229, 0.03); border-left: 4px solid #4f46e5;">
                <h6 class="fw-bold text-primary mb-2"><i class="fas fa-comment-alt me-2"></i> Reason for Issuance</h6>
                <p class="mb-0 text-dark opacity-75">{{ $creditMemo->reason }}</p>
            </div>
            @endif

            {{-- Items Table --}}
            <div class="table-responsive mb-5">
                <table class="table table-hover align-middle border-0">
                    <thead>
                        <tr class="bg-light text-muted small text-uppercase fw-bold">
                            <th class="ps-4 border-0">Product / Item Description</th>
                            <th class="text-end border-0">Unit Price</th>
                            <th class="text-center border-0">Ret. Qty</th>
                            <th class="text-end border-0">Tax Reversal</th>
                            <th class="text-end pe-4 border-0">Refund Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditMemo->items as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                <div class="small text-muted">{{ $item->product->product_code }}</div>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center"><span class="badge bg-light text-dark border px-3">{{ $item->quantity }}</span></td>
                            <td class="text-end text-muted small">₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-primary">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Summary Reversal --}}
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="p-4 rounded-4" style="background: #f8fafc;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal Reversal</span>
                            <span class="fw-bold">₹{{ number_format($creditMemo->sub_total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span class="text-muted">Tax Reversal</span>
                            <span class="fw-bold">₹{{ number_format($creditMemo->tax_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 fw-bold text-dark">Total Refund</span>
                            <span class="h2 mb-0 fw-bold text-danger">₹{{ number_format($creditMemo->refund_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="card-footer bg-light border-0 p-4 text-center">
            <p class="text-muted small mb-0">This is a system-generated document. No signature is required.</p>
        </div>
    </div>
</div>

<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
    }
    .bg-orbs {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: -1; overflow: hidden;
    }
    .orb {
        position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15;
        animation: float 20s infinite alternate;
    }
    .orb-1 { width: 400px; height: 400px; background: #4f46e5; top: -100px; right: -100px; }
    .orb-2 { width: 300px; height: 300px; background: #10b981; bottom: -50px; left: -50px; animation-delay: -5s; }
    .orb-3 { width: 250px; height: 250px; background: #f59e0b; top: 40%; left: 10%; animation-delay: -10s; }
    
    @keyframes float {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }

    @media print {
        .btn, .bg-orbs { display: none !important; }
        .glass-card { box-shadow: none; border: 1px solid #eee; }
        .container { max-width: 100%; padding: 0; }
    }
</style>
@endsection
