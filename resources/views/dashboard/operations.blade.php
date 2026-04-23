@extends('layouts.app')

@section('page-title', 'Operational Pulse')

@section('content')
<div class="perf-snap-wrapper">
    <!-- Top Row: Critical Alerts & Inventory -->
    <div class="perf-grid-row" style="grid-template-columns: 1fr 1.5fr; gap: 20px;">
        <!-- Low Stock Alert (Compact) -->
        <div class="perf-card" id="low-stock">
            <div class="card-header-mini">
                <div>
                    <h3 class="card-title">Low Stock</h3>
                    <p class="card-subtitle">Critical depletion</p>
                </div>
                <div class="trend-badge negative">
                    <i class="fas fa-exclamation-triangle"></i> {{ count($lowStockProducts ?? []) }}
                </div>
            </div>
            <div class="stock-list" style="max-height: 400px; overflow-y: auto;">
                @forelse($lowStockProducts ?? [] as $product)
                @php /** @var \App\Models\Product $product */ @endphp
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--bg-light); border-radius: 10px; margin-bottom: 10px; border: 1px solid var(--border);">
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div style="font-size: 16px;">📦</div>
                        <div>
                            <div style="font-weight: 700; font-size: 13px;">{{ optional($product)->name ?? 'Unknown' }}</div>
                            <div style="font-size: 11px; color: var(--danger);">Only {{ optional($product)->quantity ?? 0 }} left</div>
                        </div>
                    </div>
                    <a href="{{ (isset($product) && isset($product->id)) ? route('inventory.edit', $product->id) : '#' }}" class="btn-refresh-mini" title="Restock"><i class="fas fa-plus"></i></a>
                </div>
                @empty
                <div style="text-align: center; padding: 40px; color: var(--text-muted); font-size: 13px;">Inventory is healthy.</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions (Moved here, compact) -->
        <div class="perf-card" id="recent-transactions">
            <div class="card-header-mini">
                <div>
                    <h3 class="card-title">Recent Transactions</h3>
                    <p class="card-subtitle">Latest sales audit</p>
                </div>
                <div class="api-status-pill">
                    <span class="status-dot online"></span> Live
                </div>
            </div>
            <div class="mini-table-container">
                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSales ?? [] as $sale)
                        <tr>
                            <td style="font-weight: 700; color: var(--primary);">#{{ optional($sale)->id ?? 'N/A' }}</td>
                            <td style="font-size: 13px; font-weight: 600;">{{ optional(optional($sale)->customer)->name ?? 'Guest' }}</td>
                            <td style="font-weight: 800;">₹{{ number_format(optional($sale)->grand_total ?? 0, 0) }}</td>
                            <td>
                                <span class="status-badge-premium {{ strtolower(optional($sale)->payment_status ?? 'paid') }}" style="font-size: 9px; padding: 2px 6px;">
                                    {{ ucfirst(optional($sale)->payment_status ?? 'Paid') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Momentum & Revenue -->
    <div class="perf-grid-row" style="grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 20px;">
        <!-- Trend Velocity (Compact) -->
        <div class="perf-card" id="trend-velocity">
            <div class="card-header-mini">
                <div>
                    <h3 class="card-title">Trend Velocity</h3>
                    <p class="card-subtitle">Momentum Analysis</p>
                </div>
                <div class="trend-badge positive">
                    <i class="fas fa-rocket"></i> High
                </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 10px; margin: 10px 0;">
                <h2 class="card-value" style="font-size: 28px;">₹{{ number_format($averageSale ?? 0, 0) }}</h2>
                <span style="font-size: 11px; color: var(--success); font-weight: 700;">+14.2%</span>
            </div>
            <div style="height: 120px; margin: 0 -24px -24px -24px;">
                <canvas id="trendMomentumChart"></canvas>
            </div>
        </div>

        <!-- Revenue Cycle (Compact) -->
        <div class="perf-card" id="revenue-cycle">
            <div class="card-header-mini">
                <h3 class="card-title">Revenue Cycle</h3>
                <i class="fas fa-sync-alt" style="color: var(--secondary);"></i>
            </div>
            <div class="donut-area" style="height: 180px;">
                <canvas id="dailySalesDonut"></canvas>
                <div class="donut-center">
                    <span class="label" style="font-size: 9px;">Today</span>
                    <span class="val" style="font-size: 18px;">₹{{ number_format($todaySales ?? 0, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .perf-grid-row { display: grid; gap: 20px; }
    .perf-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
        overflow: hidden;
    }
    .card-header-mini { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
    .card-title { margin: 0; font-size: 16px; font-weight: 800; color: var(--text-main); }
    .card-subtitle { margin: 2px 0 0; font-size: 11px; color: var(--text-muted); }
    
    .mini-table { width: 100%; border-collapse: collapse; }
    .mini-table th { text-align: left; padding: 10px; color: var(--text-muted); font-size: 10px; text-transform: uppercase; border-bottom: 1px solid var(--border); }
    .mini-table td { padding: 10px; border-bottom: 1px solid var(--border); font-size: 12px; }
    
    .donut-area { position: relative; flex: 1; display: flex; align-items: center; justify-content: center; }
    .donut-center { position: absolute; text-align: center; }
    
    .trend-badge { padding: 4px 8px; border-radius: 6px; font-size: 10px; font-weight: 700; display: flex; align-items: center; gap: 4px; }
    .trend-badge.positive { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .trend-badge.negative { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    
    .btn-refresh-mini { width: 28px; height: 28px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-light); color: var(--text-muted); display: flex; align-items: center; justify-content: center; cursor: pointer; text-decoration: none; font-size: 12px; }
    .btn-refresh-mini:hover { background: var(--primary); color: white; border-color: var(--primary); }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    
    // 1. Revenue Cycle Donut
    const salesCtx = document.getElementById('dailySalesDonut').getContext('2d');
    new Chart(salesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sales', 'Target'],
            datasets: [{
                data: [{{ $todaySales ?? 0 }}, {{ max(0, 50000 - ($todaySales ?? 0)) }}],
                backgroundColor: ['#6366f1', isDark ? '#334155' : '#f1f5f9'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '80%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });

    // 2. Trend Velocity Chart
    const tmCtx = document.getElementById('trendMomentumChart').getContext('2d');
    const grad = tmCtx.createLinearGradient(0, 0, 0, 120);
    grad.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
    grad.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(tmCtx, {
        type: 'line',
        data: {
            labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
            datasets: [{
                data: [4200, 5800, 4900, 7200, 6500, 8900, 8200],
                borderColor: '#6366f1', backgroundColor: grad, fill: true, tension: 0.4, borderWidth: 3, pointRadius: 0
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { display: false } }
        }
    });
});
</script>
@endpush
@endsection
