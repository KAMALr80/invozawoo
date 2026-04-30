@if(auth()->check())
<style>
:root {
    --erp-search-bg: #f0f0f5;
    --erp-search-surface: rgba(255, 255, 255, 0.96);
    --erp-search-border: rgba(0, 0, 0, 0.08);
    --erp-search-border-focus: rgba(0, 0, 0, 0.16);
    --erp-search-text: #111118;
    --erp-search-secondary: #6b6b7e;
    --erp-search-muted: #a0a0b0;
    --erp-search-accent: #5a52e8;
    --erp-search-accent-hover: rgba(90, 82, 232, 0.10);
    --erp-search-highlight: rgba(90, 82, 232, 0.07);
    --erp-search-radius: 16px;
    --erp-search-shadow: 0 24px 64px rgba(0,0,0,0.14), 0 4px 16px rgba(0,0,0,0.06), 0 0 0 1px rgba(0,0,0,0.07);
}

#erp-search-trigger {
    position: fixed;
    right: 22px;
    bottom: 22px;
    z-index: 99997;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid var(--erp-search-border);
    border-radius: 10px;
    padding: 10px 16px;
    cursor: pointer;
    color: var(--erp-search-secondary);
    font-family: 'DM Sans', Arial, sans-serif;
    font-size: 14px;
    font-weight: 500;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
}
#erp-search-trigger:hover,
#erp-search-trigger:focus {
    border-color: var(--erp-search-border-focus);
    color: var(--erp-search-text);
    transform: translateY(-1px);
    outline: none;
}
.erp-search-trigger-icon {
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    opacity: .65;
}
.erp-search-trigger-shortcut {
    display: inline-flex;
    gap: 3px;
    margin-left: 10px;
    align-items: center;
}
.erp-search-trigger-shortcut kbd,
.erp-kbd {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 22px;
    padding: 0 7px;
    background: rgba(0,0,0,0.05);
    border: 1px solid rgba(0,0,0,0.10);
    border-radius: 6px;
    color: var(--erp-search-muted);
    font-size: 11px;
    line-height: 1;
    font-weight: 600;
}

#erp-global-search-overlay {
    position: fixed;
    inset: 0;
    background: rgba(160, 160, 185, 0.35);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    z-index: 99998;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: min(18vh, 160px);
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease;
}
#erp-global-search-overlay.active {
    opacity: 1;
    pointer-events: auto;
}
.erp-search-modal {
    width: min(760px, calc(100vw - 32px));
    background: var(--erp-search-surface);
    border: 1px solid var(--erp-search-border);
    border-radius: var(--erp-search-radius);
    box-shadow: var(--erp-search-shadow);
    overflow: hidden;
    transform: scale(.94) translateY(-12px);
    opacity: 0;
    transition: transform .30s cubic-bezier(.34, 1.56, .64, 1), opacity .25s ease;
}
#erp-global-search-overlay.active .erp-search-modal {
    transform: scale(1) translateY(0);
    opacity: 1;
}
.erp-search-input-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 20px;
    border-bottom: 1px solid var(--erp-search-border);
    background: #fff;
}
.erp-search-icon-wrap {
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--erp-search-accent);
    flex-shrink: 0;
}
.erp-search-input {
    flex: 1;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
    font-family: 'DM Sans', Arial, sans-serif;
    font-size: 17px;
    font-weight: 400;
    color: var(--erp-search-text);
    caret-color: var(--erp-search-accent);
    padding: 0 !important;
    margin: 0 !important;
}
.erp-search-input::placeholder { color: var(--erp-search-muted); }
.erp-loading-ring {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(90,82,232,0.15);
    border-top-color: var(--erp-search-accent);
    border-radius: 50%;
    animation: erpSearchSpin .7s linear infinite;
    opacity: 0;
    transition: opacity .2s;
    flex-shrink: 0;
}
.erp-loading-ring.visible { opacity: 1; }
.erp-search-clear-btn {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(0,0,0,0.07);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--erp-search-secondary);
    opacity: 0;
    transform: scale(.7);
    transition: all .2s;
    flex-shrink: 0;
    padding: 0;
}
.erp-search-clear-btn.visible { opacity: 1; transform: scale(1); }
.erp-search-clear-btn:hover { background: rgba(0,0,0,0.12); color: var(--erp-search-text); }

.erp-filter-row {
    display: flex;
    gap: 6px;
    padding: 10px 20px;
    border-bottom: 1px solid var(--erp-search-border);
    overflow-x: auto;
    overflow-y: hidden;
    background: #fafafa;
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.10) transparent;
}
.erp-filter-row::-webkit-scrollbar { height: 4px; }
.erp-filter-row::-webkit-scrollbar-track { background: transparent; }
.erp-filter-row::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.10); border-radius: 999px; }
.erp-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    background: transparent;
    border: 1px solid var(--erp-search-border);
    color: var(--erp-search-secondary);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: all .18s ease;
    flex-shrink: 0;
}
.erp-filter-pill:hover {
    border-color: var(--erp-search-border-focus);
    color: var(--erp-search-text);
    background: rgba(0,0,0,0.03);
}
.erp-filter-pill.active {
    background: var(--erp-search-highlight);
    border-color: rgba(90,82,232,0.30);
    color: var(--erp-search-accent);
}
.erp-pill-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

.erp-results-container {
    max-height: 420px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.12) transparent;
    background: #fff;
}
.erp-results-container::-webkit-scrollbar { width: 4px; }
.erp-results-container::-webkit-scrollbar-track { background: transparent; }
.erp-results-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 999px; }
.erp-result-group { padding: 10px 0 4px; animation: erpSearchFadeSlide .22s ease forwards; }
.erp-group-label {
    padding: 0 20px 6px;
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--erp-search-muted);
}
.erp-group-separator { height: 1px; background: var(--erp-search-border); margin: 4px 0; }
.erp-result-item,
.erp-quick-action-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 20px;
    cursor: pointer;
    transition: background .12s ease;
    position: relative;
    color: var(--erp-search-text) !important;
    text-decoration: none !important;
}
.erp-result-item:hover,
.erp-result-item.focused,
.erp-result-item.active,
.erp-quick-action-item:hover,
.erp-quick-action-item.focused {
    background: var(--erp-search-accent-hover);
}
.erp-result-item.focused:before,
.erp-result-item.active:before,
.erp-quick-action-item.focused:before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--erp-search-accent);
    border-radius: 0 2px 2px 0;
}
.erp-item-icon,
.quick-icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 15px;
    background: rgba(90,82,232,0.10);
    color: var(--erp-search-accent);
}
.erp-item-icon.customer,
.erp-item-icon.supplier,
.quick-icon.customer { background: rgba(29,184,184,0.10); color: #1db8b8; }
.erp-item-icon.invoice,
.erp-item-icon.purchase_order,
.erp-item-icon.vendor_bill,
.quick-icon.invoice { background: rgba(240,160,40,0.12); color: #d98b12; }
.erp-item-icon.credit_memo,
.erp-item-icon.vendor_credit_memo { background: rgba(232,82,122,0.10); color: #e8527a; }
.erp-item-icon.menu,
.erp-item-icon.report { background: rgba(100,170,80,0.12); color: #64aa50; }
.erp-item-body,
.quick-text { flex: 1; min-width: 0; }
.erp-item-title {
    font-size: 14px;
    font-weight: 500;
    color: var(--erp-search-text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.erp-item-title mark { background: none; color: var(--erp-search-accent); font-weight: 600; }
.erp-item-subtitle {
    font-size: 12px;
    color: var(--erp-search-muted);
    margin-top: 1px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.erp-item-badge {
    flex-shrink: 0;
    font-size: 11px;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: 500;
    line-height: 1.4;
    background: rgba(60,130,240,0.1);
    color: #2a82e0;
}
.erp-item-arrow {
    color: var(--erp-search-muted);
    opacity: 0;
    transform: translateX(-4px);
    transition: all .15s ease;
    margin-left: auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.erp-result-item:hover .erp-item-arrow,
.erp-result-item.focused .erp-item-arrow,
.erp-result-item.active .erp-item-arrow,
.erp-quick-action-item:hover .erp-item-arrow { opacity: 1; transform: translateX(0); }
.erp-empty-state {
    padding: 48px 20px;
    text-align: center;
    animation: erpSearchFadeSlide .2s ease forwards;
    color: var(--erp-search-muted);
}
.erp-empty-icon { font-size: 32px; margin-bottom: 12px; }
.erp-empty-title { font-size: 15px; font-weight: 500; color: var(--erp-search-secondary); margin-bottom: 6px; }
.erp-empty-sub { font-size: 13px; color: var(--erp-search-muted); }
.erp-quick-section { padding: 8px 0; background: #fff; }
.quick-text {
    font-size: 14px;
    color: var(--erp-search-secondary);
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.quick-text strong { color: var(--erp-search-text); font-weight: 500; white-space: nowrap; }
.quick-text span { color: var(--erp-search-secondary); white-space: nowrap; }
.erp-searching-state { padding: 12px 0 10px; background: #fff; }
.erp-search-skeleton {
    height: 38px;
    margin: 4px 20px;
    border-radius: 8px;
    background: linear-gradient(90deg, rgba(0,0,0,0.04) 25%, rgba(0,0,0,0.07) 50%, rgba(0,0,0,0.04) 75%);
    background-size: 200% 100%;
    animation: erpSearchShimmer 1.2s infinite;
}
.erp-search-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    border-top: 1px solid var(--erp-search-border);
    background: #fafafa;
}
.erp-footer-hints { display: flex; gap: 14px; }
.erp-hint,
.erp-footer-esc {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    color: var(--erp-search-muted);
}
@keyframes erpSearchSpin { to { transform: rotate(360deg); } }
@keyframes erpSearchFadeSlide { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
@keyframes erpSearchShimmer { 0% { background-position: -200% center; } 100% { background-position: 200% center; } }
@media (max-width: 480px) {
    .erp-footer-hints { display: none; }
    #erp-search-trigger { right: 14px; bottom: 14px; }
    .erp-search-modal { width: calc(100vw - 18px); }
}
.invoice {
        
        margin: 0px 0px !important;
    }
</style>

<button type="button" id="erp-search-trigger" class="no-print" aria-label="Open global search">
    <span class="erp-search-trigger-icon">
        <svg viewBox="0 0 20 20" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="8.5" cy="8.5" r="5.5"></circle>
            <path d="M15 15l-3-3" stroke-linecap="round"></path>
        </svg>
    </span>
    <span>Search</span>
    <span class="erp-search-trigger-shortcut"><kbd>Alt</kbd><kbd>Shift</kbd><kbd>S</kbd></span>
</button>

<div id="erp-global-search-overlay" class="no-print" role="dialog" aria-modal="true" aria-label="Universal search">
    <div class="erp-search-modal">
        <div class="erp-search-input-row">
            <div class="erp-search-icon-wrap">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="8.5" cy="8.5" r="5.5"></circle>
                    <path d="M15 15l-3-3" stroke-linecap="round"></path>
                </svg>
            </div>
            <input type="text" id="erp-global-search" class="erp-search-input" placeholder="Search products, customers, invoices…" autocomplete="off" spellcheck="false">
            <div id="erp-search-loading" class="erp-loading-ring"></div>
            <button type="button" id="erp-search-clear" class="erp-search-clear-btn" aria-label="Clear search">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor">
                    <path d="M1 1l8 8M9 1L1 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                </svg>
            </button>
        </div>

        <div id="erp-global-search-filters" class="erp-filter-row">
            <button type="button" class="erp-filter-pill active" data-filter="all"><span class="erp-pill-dot"></span> All</button>
            <button type="button" class="erp-filter-pill" data-filter="customer"><span class="erp-pill-dot"></span> Customer</button>
            <button type="button" class="erp-filter-pill" data-filter="supplier"><span class="erp-pill-dot"></span> Supplier</button>
            <button type="button" class="erp-filter-pill" data-filter="invoice"><span class="erp-pill-dot"></span> Invoice</button>
            <button type="button" class="erp-filter-pill" data-filter="purchase_order"><span class="erp-pill-dot"></span> Purchase Order</button>
            <button type="button" class="erp-filter-pill" data-filter="vendor_bill"><span class="erp-pill-dot"></span> Vendor Bill</button>
            <button type="button" class="erp-filter-pill" data-filter="credit_memo"><span class="erp-pill-dot"></span> Credit Memo</button>
            <button type="button" class="erp-filter-pill" data-filter="vendor_credit_memo"><span class="erp-pill-dot"></span> Vendor Credit Memo</button>
            <button type="button" class="erp-filter-pill" data-filter="expense"><span class="erp-pill-dot"></span> Expense</button>
            <button type="button" class="erp-filter-pill" data-filter="followup"><span class="erp-pill-dot"></span> Followup</button>
            <button type="button" class="erp-filter-pill" data-filter="product"><span class="erp-pill-dot"></span> Product</button>
            <button type="button" class="erp-filter-pill" data-filter="menu"><span class="erp-pill-dot"></span> Menu</button>
            <button type="button" class="erp-filter-pill" data-filter="report"><span class="erp-pill-dot"></span> Report</button>
        </div>

        <div id="erp-global-search-results" class="erp-results-container"></div>

        <div id="erp-search-quick-actions" class="erp-quick-section">
            <div class="erp-quick-action-item" data-action="product_create">
                <div class="quick-icon product">➕</div>
                <div class="quick-text"><strong>Create</strong><span>a new product</span></div>
                <div class="erp-item-arrow"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 7h10M8 3l4 4-4 4"></path></svg></div>
            </div>
            <div class="erp-quick-action-item" data-action="invoice_create">
                <div class="quick-icon invoice">🧾</div>
                <div class="quick-text"><strong>Generate</strong><span>an invoice</span></div>
                <div class="erp-item-arrow"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 7h10M8 3l4 4-4 4"></path></svg></div>
            </div>
            <div class="erp-quick-action-item" data-action="customer_add">
                <div class="quick-icon customer">👤</div>
                <div class="quick-text"><strong>Add</strong><span>a customer</span></div>
                <div class="erp-item-arrow"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 7h10M8 3l4 4-4 4"></path></svg></div>
            </div>
        </div>

        <div id="erp-search-shortcuts" class="erp-search-footer">
            <div class="erp-footer-hints">
                <span class="erp-hint"><span class="erp-kbd">↑↓</span> Navigate</span>
                <span class="erp-hint"><span class="erp-kbd">↵</span> Open</span>
                <span class="erp-hint"><span class="erp-kbd">Tab</span> Filter</span>
            </div>
            <div class="erp-footer-esc"><span class="erp-kbd">Esc</span> to close</div>
        </div>
    </div>
</div>

<script>
(function(){
    if (window.__GlobalSearchModuleLoaded) return;
    window.__GlobalSearchModuleLoaded = true;

    var overlay = document.getElementById('erp-global-search-overlay');
    var trigger = document.getElementById('erp-search-trigger');
    var input = document.getElementById('erp-global-search');
    var clearBtn = document.getElementById('erp-search-clear');
    var resultsBox = document.getElementById('erp-global-search-results');
    var quickBox = document.getElementById('erp-search-quick-actions');
    var loading = document.getElementById('erp-search-loading');
    var category = 'all';
    var activeIndex = -1;
    var searchTimer = null;
    var lastController = null;

    if (!overlay || !trigger || !input || !resultsBox || !quickBox) return;

    var searchUrl = "{{ url('/global-search') }}";
    var shortcuts = {
        '/all':'all','/cus':'customer','/customer':'customer','/sup':'supplier','/supplier':'supplier',
        '/inv':'invoice','/invoice':'invoice','/po':'purchase_order','/purchaseorder':'purchase_order',
        '/vb':'vendor_bill','/vendorbill':'vendor_bill','/cm':'credit_memo','/creditmemo':'credit_memo',
        '/vcm':'vendor_credit_memo','/vendorcreditmemo':'vendor_credit_memo','/exp':'expense','/expense':'expense',
        '/fol':'followup','/followup':'followup','/pro':'product','/product':'product','/menu':'menu','/rep':'report','/report':'report'
    };
    var labels = {
        all:'All', customer:'Customer', supplier:'Supplier', invoice:'Invoice', purchase_order:'Purchase Order',
        vendor_bill:'Vendor Bill', credit_memo:'Credit Memo', vendor_credit_memo:'Vendor Credit Memo', expense:'Expense',
        followup:'Followup', product:'Product', menu:'Menu', report:'Report'
    };
    var icons = {
        Product:'📦', Customer:'👤', Supplier:'🏢', Invoice:'🧾', 'Purchase Order':'🛒', 'Vendor Bill':'📄',
        'Credit Memo':'↩️', 'Vendor Credit Memo':'↩️', Expense:'💸', Followup:'📞', Menu:'⚙️', Report:'📊'
    };

    function openSearch(){
        overlay.classList.add('active');
        setTimeout(function(){ input.focus(); input.select(); }, 60);
        if (!input.value.trim()) showQuick();
    }
    function closeSearch(){
        overlay.classList.remove('active');
        activeIndex = -1;
    }
    function setCategory(cat){
        category = cat || 'all';
        document.querySelectorAll('.erp-filter-pill').forEach(function(btn){
            btn.classList.toggle('active', btn.getAttribute('data-filter') === category);
        });
    }
    function showQuick(){ resultsBox.innerHTML = ''; quickBox.style.display = 'block'; activeIndex = -1; }
    function hideQuick(){ quickBox.style.display = 'none'; }
    function rows(){
        return Array.prototype.slice.call(resultsBox.querySelectorAll('.erp-result-item'))
            .concat(Array.prototype.slice.call(quickBox.querySelectorAll('.erp-quick-action-item')).filter(function(){ return quickBox.style.display !== 'none'; }));
    }
    function setActive(i){
        var r = rows();
        r.forEach(function(x){ x.classList.remove('active'); x.classList.remove('focused'); });
        if (!r.length) { activeIndex = -1; return; }
        activeIndex = Math.max(0, Math.min(i, r.length - 1));
        r[activeIndex].classList.add('focused');
        r[activeIndex].scrollIntoView({block:'nearest'});
    }
    function openRow(row){
        if (!row) return;
        var action = row.getAttribute('data-action');
        if (action) { runQuickAction(action); return; }
        var url = row.getAttribute('data-url');
        if (url) window.location.href = url;
    }
    function escapeHtml(s){ return String(s || '').replace(/[&<>'"]/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c];}); }
    function escapeRegex(s){ return String(s || '').replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
    function highlight(text, term){
        text = escapeHtml(text);
        if (!term) return text;
        return text.replace(new RegExp('(' + escapeRegex(term) + ')', 'ig'), '<mark>$1</mark>');
    }
    function typeToClass(type){
        return String(type || '').toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '');
    }
    function showSkeleton(){
        hideQuick();
        resultsBox.innerHTML = '<div class="erp-searching-state"><div class="erp-search-skeleton"></div><div class="erp-search-skeleton"></div><div class="erp-search-skeleton"></div><div class="erp-search-skeleton"></div></div>';
    }
    function render(items, term){
        hideQuick();
        activeIndex = -1;
        if (!items || !items.length) {
            resultsBox.innerHTML = '<div class="erp-empty-state"><div class="erp-empty-icon">🔍</div><div class="erp-empty-title">No results found</div><div class="erp-empty-sub">Try different keywords or change the filter</div></div>';
            return;
        }

        var groups = {};
        items.forEach(function(item){
            var type = item.type || 'Result';
            if (!groups[type]) groups[type] = [];
            groups[type].push(item);
        });

        var html = '';
        var keys = Object.keys(groups);
        keys.forEach(function(type, gi){
            html += '<div class="erp-result-group"><div class="erp-group-label">' + escapeHtml(type) + '</div>';
            groups[type].forEach(function(item){
                var cls = typeToClass(item.type);
                var icon = icons[item.type] || '🔎';
                html += '<div class="erp-result-item" data-url="' + escapeHtml(item.url) + '">';
                html += '<div class="erp-item-icon ' + escapeHtml(cls) + '">' + icon + '</div>';
                html += '<div class="erp-item-body"><div class="erp-item-title">' + highlight(item.title, term) + '</div><div class="erp-item-subtitle">' + escapeHtml(item.type) + ' · ' + escapeHtml(item.subtitle) + '</div></div>';
                html += '<span class="erp-item-badge">' + escapeHtml(item.type) + '</span>';
                html += '<div class="erp-item-arrow"><svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 7h10M8 3l4 4-4 4"></path></svg></div>';
                html += '</div>';
            });
            html += '</div>';
            if (gi < keys.length - 1) html += '<div class="erp-group-separator"></div>';
        });
        resultsBox.innerHTML = html;
        resultsBox.querySelectorAll('.erp-result-item').forEach(function(row, i){
            row.addEventListener('mouseenter', function(){ setActive(i); });
            row.addEventListener('click', function(){ openRow(row); });
        });
        setActive(0);
    }
    function parseShortcut(value){
        var parts = value.trim().split(/\s+/);
        var first = (parts[0] || '').toLowerCase();
        if (shortcuts[first]) {
            setCategory(shortcuts[first]);
            return parts.slice(1).join(' ');
        }
        return value;
    }
    function searchNow(){
        var raw = input.value || '';
        var term = parseShortcut(raw).trim();
        clearBtn.classList.toggle('visible', raw.trim().length > 0);
        if (term.length < 2) { loading.classList.remove('visible'); showQuick(); return; }
        if (lastController && lastController.abort) lastController.abort();
        if (window.AbortController) lastController = new AbortController();
        loading.classList.add('visible');
        showSkeleton();
        fetch(searchUrl + '?term=' + encodeURIComponent(term) + '&category=' + encodeURIComponent(category), {
            credentials: 'same-origin',
            headers: {'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
            signal: lastController ? lastController.signal : undefined
        }).then(function(res){ return res.json(); })
        .then(function(data){ render(data, term); })
        .catch(function(e){ if (!e || e.name !== 'AbortError') resultsBox.innerHTML = '<div class="erp-empty-state"><div class="erp-empty-icon">⚠️</div><div class="erp-empty-title">Search error</div><div class="erp-empty-sub">Please check the search route or server response</div></div>'; })
        .finally(function(){ loading.classList.remove('visible'); });
    }
    function debounceSearch(){ clearTimeout(searchTimer); searchTimer = setTimeout(searchNow, 280); }
    function cycleFilter(dir){
        var all = Array.prototype.slice.call(document.querySelectorAll('.erp-filter-pill')).map(function(btn){ return btn.getAttribute('data-filter'); });
        var idx = all.indexOf(category);
        var next = all[(idx + dir + all.length) % all.length];
        setCategory(next);
        debounceSearch();
    }
    function runQuickAction(action){
        if (action === 'product_create') window.location.href = '{{ url('/products/create') }}';
        if (action === 'invoice_create') window.location.href = '{{ url('/pos/create') }}';
        if (action === 'customer_add') window.location.href = '{{ url('/contacts?type=customer&open_create=1') }}';
    }

    trigger.addEventListener('click', openSearch);
    overlay.addEventListener('click', function(e){ if (e.target === overlay) closeSearch(); });
    input.addEventListener('input', debounceSearch);
    clearBtn.addEventListener('click', function(){ input.value = ''; clearBtn.classList.remove('visible'); loading.classList.remove('visible'); clearTimeout(searchTimer); input.focus(); showQuick(); });
    document.querySelectorAll('.erp-filter-pill').forEach(function(btn){
        btn.addEventListener('click', function(){ setCategory(btn.getAttribute('data-filter')); debounceSearch(); });
    });
    document.querySelectorAll('.erp-quick-action-item').forEach(function(item, i){
        item.addEventListener('mouseenter', function(){ setActive(i); });
        item.addEventListener('click', function(){ runQuickAction(item.getAttribute('data-action')); });
    });

    document.addEventListener('keydown', function(e){
        var key = (e.key || '').toLowerCase();
        if (e.altKey && e.shiftKey && key === 's') { e.preventDefault(); e.stopPropagation(); openSearch(); return false; }
        if (!overlay.classList.contains('active')) return;
        if (key === 'escape') { e.preventDefault(); closeSearch(); return; }
        if (key === 'arrowdown') { e.preventDefault(); setActive(activeIndex + 1); return; }
        if (key === 'arrowup') { e.preventDefault(); setActive(activeIndex - 1); return; }
        if (key === 'enter') { e.preventDefault(); openRow(rows()[activeIndex]); return; }
        if (key === 'tab') { e.preventDefault(); cycleFilter(e.shiftKey ? -1 : 1); return; }
    }, true);
})();
</script>
@endif
