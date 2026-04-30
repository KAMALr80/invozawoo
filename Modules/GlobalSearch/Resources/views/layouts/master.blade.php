<style>
    :root {
        --primary: #0045ff;
        --primary-hover: #0037cc;
        --secondary: #71717a;
        --secondary-hover: #52525b;
        --success: #16a34a;
        --success-hover: #15803d;
        --danger: #dc2626;
        --danger-hover: #b91c1c;
        --warning: #eab308;
        --warning-hover: #ca8a04;
        --info: #0ea5e9;
        --info-hover: #0284c7;
        --background: #ffffff;
        --text: #1f2937;
    }

    #erp-global-search:focus {
        border-color: #5a52e8 !important;
        box-shadow: 0 0 0 4px rgba(90, 82, 232, 0.08) !important;
    }

    #erp-global-search-results,
    .erp-results-container {
        max-height: 420px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.12) transparent;
    }

    #erp-global-search-results::-webkit-scrollbar,
    .erp-results-container::-webkit-scrollbar {
    width: 4px;
}
    #erp-global-search-results::-webkit-scrollbar-track,
    .erp-results-container::-webkit-scrollbar-track {
    background: transparent;
}

    #erp-global-search-results::-webkit-scrollbar-thumb,
   .erp-results-container::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.12);
    border-radius: 999px;
}
#erp-global-search-results::-webkit-scrollbar-thumb:hover,
.erp-results-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.22);
}

    .erp-search-trigger {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.92);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 10px;
    padding: 10px 16px;
    cursor: pointer;
    color: #6b6b7e;
    font-size: 14px;
    font-weight: 500;
    transition: all .2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
    margin: 12px 0 6px 0;
}

.erp-search-trigger:hover,
.erp-search-trigger:focus {
    border-color: rgba(0, 0, 0, 0.16);
    color: #111118;
    transform: translateY(-1px);
    outline: none;
}

.erp-search-trigger-icon {
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.erp-search-trigger-icon svg {
    width: 16px;
    height: 16px;
    opacity: .7;
}

.erp-search-trigger-shortcut {
    display: flex;
    gap: 3px;
    margin-left: 12px;
    align-items: center;
}

.erp-search-trigger-shortcut kbd {
    background: #f3f4f6;
    border: 1px solid #d1d5db;
    border-bottom-color: #bfc5cf;
    border-radius: 5px;
    color: #4b5563;
    font-size: 10px;
    font-weight: 600;
    line-height: 1;
    padding: 4px 6px;
    box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.08);
}

.erp-search-trigger:hover .erp-search-trigger-shortcut kbd {
    background: #ffffff;
    color: #111118;
}
    .erp-kbd{
        background:rgba(0,0,0,0.05);
        border:1px solid rgba(0,0,0,0.10);
        border-radius:5px;
        padding:2px 6px;
        font-size:11px;
        color:#888;
    }

    .erp-search-overlay{
        position:fixed;
        inset:0;
        background:rgba(160,160,185,0.35);
        backdrop-filter:blur(16px);
        -webkit-backdrop-filter:blur(16px);
        z-index:99999;
        display:flex;
        align-items:flex-start;
        justify-content:center;
        padding-top:min(12vh,120px);
        opacity:0;
        pointer-events:none;
        transition:opacity .25s ease;
    }
    .erp-search-overlay.active{
        opacity:1;
        pointer-events:auto;
    }

    .erp-search-modal{
        width:min(760px, calc(100vw - 32px));
        background:rgba(255,255,255,0.97);
        border:1px solid rgba(0,0,0,0.08);
        border-radius:16px;
        box-shadow:0 24px 64px rgba(0,0,0,0.14), 0 4px 16px rgba(0,0,0,0.06);
        overflow:hidden;
        transform:scale(.96) translateY(-10px);
        opacity:0;
        transition:transform .25s ease, opacity .25s ease;
    }
    .erp-search-overlay.active .erp-search-modal{
        transform:scale(1) translateY(0);
        opacity:1;
    }

    .erp-search-input-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    position: relative;
    background: #ffffff;
}
  .erp-search-icon-wrap {
    flex-shrink: 0;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5a52e8;
    transition: transform 0.2s;
}

   .erp-search-input {
    flex: 1;
    background: none;
    border: none;
    outline: none;
    font-family: 'DM Sans', sans-serif;
    font-size: 17px;
    font-weight: 400;
    color: #111118;
    caret-color: #5a52e8;
    padding: 0;
    margin: 0;
    box-shadow: none !important;
}

#erp-global-search:focus {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}

   .erp-search-input::placeholder {
    color: #a0a0b0;
}
    .erp-loading-ring{
        width:18px;
        height:18px;
        border:2px solid rgba(90,82,232,0.15);
        border-top-color:#5a52e8;
        border-radius:50%;
        animation:erpSpin .7s linear infinite;
        opacity:0;
        transition:opacity .2s;
        flex-shrink:0;
    }
    .erp-loading-ring.visible{
        opacity:1;
    }
    .erp-search-clear-btn{
        width:22px;
        height:22px;
        border-radius:50%;
        background:rgba(0,0,0,0.07);
        border:none;
        cursor:pointer;
        display:flex;
        align-items:center;
        justify-content:center;
        color:#666;
        opacity:0;
        transform:scale(.7);
        transition:all .2s;
        flex-shrink:0;
    }
    .erp-search-clear-btn.visible{
        opacity:1;
        transform:scale(1);
    }
    .erp-search-clear-btn:hover{
        background:rgba(0,0,0,0.12);
        color:#111118;
    }

    .erp-filter-row {
    display: flex;
    gap: 6px;
    padding: 10px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    overflow-x: auto;
    overflow-y: hidden;
    background: #fafafa;
    scrollbar-width: thin;
    scrollbar-color: rgba(0,0,0,0.10) transparent;
}
    .erp-filter-row::-webkit-scrollbar {
    height: 3px;
}
.erp-filter-row::-webkit-scrollbar-track {
    background: transparent;
}
.erp-filter-row::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.10);
    border-radius: 999px;
}
.erp-filter-row::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.18);
}

    .erp-filter-pill{
        display:inline-flex;
        align-items:center;
        gap:5px;
        padding:5px 12px;
        border-radius:20px;
        background:transparent;
        border:1px solid rgba(0,0,0,0.08);
        color:#666;
        font-size:12px;
        font-weight:500;
        cursor:pointer;
        white-space:nowrap;
        transition:all .18s ease;
    }
    .erp-filter-pill:hover{
        border-color:rgba(0,0,0,0.16);
        background:rgba(0,0,0,0.03);
        color:#111118;
    }
    .erp-filter-pill.active{
        background:rgba(90,82,232,0.07);
        border-color:rgba(90,82,232,0.30);
        color:#5a52e8;
    }
    .erp-pill-dot{
        width:6px;
        height:6px;
        border-radius:50%;
        background:currentColor;
    }

    .erp-result-group{
        padding:10px 0 4px;
        animation:erpFadeSlideIn .22s ease forwards;
    }

    .erp-group-label{
        padding:0 20px 6px;
        font-size:10.5px;
        font-weight:600;
        letter-spacing:.1em;
        text-transform:uppercase;
        color:#999;
    }

    .erp-group-separator{
        height:1px;
        background:rgba(0,0,0,0.06);
        margin:4px 0;
    }

    .erp-result-item{
        display:flex;
        align-items:center;
        gap:12px;
        padding:9px 20px;
        cursor:pointer;
        transition:background .12s ease;
        position:relative;
        text-decoration:none !important;
        color:#111118 !important;
    }

    .erp-result-item:hover,
    .erp-result-item.focused{
        background:rgba(90,82,232,0.10);
    }

    .erp-result-item.focused:before{
        content:'';
        position:absolute;
        left:0;
        top:0;
        bottom:0;
        width:2px;
        background:#5a52e8;
        border-radius:0 2px 2px 0;
    }
    .erp-result-item.focused::after {
    content: '';
    position: absolute;
    left: 12px;
    right: 12px;
    bottom: 0;
    height: 2px;
    border-radius: 999px;
    background: rgba(90,82,232,0.22);
}

    .erp-item-icon{
        width:34px;
        height:34px;
        border-radius:9px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
        font-size:15px;
    }
    .erp-item-icon.products{ background:rgba(90,82,232,0.10); }
    .erp-item-icon.customers{ background:rgba(29,184,184,0.10); }
    .erp-item-icon.invoices{ background:rgba(240,160,40,0.12); }
    .erp-item-icon.reports{ background:rgba(232,82,122,0.10); }
    .erp-item-icon.settings{ background:rgba(100,170,80,0.12); }
    .erp-item-icon.orders{ background:rgba(255,140,60,0.12); }

    .erp-item-body{
        flex:1;
        min-width:0;
    }

    .erp-item-title{
        font-size:14px;
        font-weight:500;
        color:#111118;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .erp-item-title mark{
        background:none;
        color:#5a52e8;
        font-weight:600;
    }

    .erp-item-subtitle{
        font-size:12px;
        color:#888;
        margin-top:1px;
        white-space:nowrap;
        overflow:hidden;
        text-overflow:ellipsis;
    }

    .erp-item-badge{
        flex-shrink:0;
        font-size:11px;
        padding:3px 8px;
        border-radius:5px;
        font-weight:500;
        line-height:1.4;
    }
    .badge-green{
        background:rgba(20,180,100,0.1);
        color:#0fa864;
    }
    .badge-yellow{
        background:rgba(220,160,10,0.1);
        color:#b8860b;
    }
    .badge-red{
        background:rgba(220,60,60,0.1);
        color:#d43535;
    }
    .badge-blue{
        background:rgba(60,130,240,0.1);
        color:#2a82e0;
    }
    .badge-gray{
        background:rgba(140,140,160,0.1);
        color:#8a8a9a;
    }

    .erp-item-arrow{
        color:#aaa;
        opacity:0;
        transform:translateX(-4px);
        transition:all .15s ease;
        margin-left:auto;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .erp-result-item:hover .erp-item-arrow,
    .erp-result-item.focused .erp-item-arrow{
        opacity:1;
        transform:translateX(0);
    }

    .erp-empty-state{
        padding:48px 20px;
        text-align:center;
        animation:erpFadeSlideIn .2s ease forwards;
    }
    .erp-empty-icon{
        font-size:32px;
        margin-bottom:12px;
    }
    .erp-empty-title{
        font-size:15px;
        font-weight:500;
        color:#666;
        margin-bottom:6px;
    }
    .erp-empty-sub{
        font-size:13px;
        color:#999;
    }

    .erp-search-footer{
        display:flex;
        align-items:center;
        justify-content:space-between;
        padding:10px 20px;
        border-top:1px solid rgba(0,0,0,0.08);
        background:#fafafa;
    }
    .erp-footer-hints{
        display:flex;
        gap:14px;
    }
    .erp-hint,
    .erp-footer-esc{
        font-size:11px;
        color:#999;
    }

    .erp-quick-action-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        border-bottom: 1px solid #f2f2f2;
        cursor: pointer;
        transition: background .12s ease;
    }
    .erp-quick-action-item:last-child {
        border-bottom: none;
    }
    .erp-quick-action-item:hover {
        background: rgba(90,82,232,0.07);
    }

    .quick-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        flex-shrink: 0;
    }

    .quick-icon-emoji {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        line-height: 1;
        transform: translateY(-1px);
    }

    .quick-icon.product  { background: rgba(90,82,232,0.10); }
    .quick-icon.invoice  { background: rgba(240,160,40,0.12); }
    .quick-icon.customer { background: rgba(29,184,184,0.10); }
    .quick-icon.credit   { background: rgba(232,82,122,0.10); }
    .quick-icon.vendor   { background: rgba(60,130,240,0.10); }

    .quick-text {
    font-size: 14px;
    color: #6b6b7e;
    flex: 1;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


    .quick-text strong,
.quick-text span,
.quick-text.invoice-text {
    white-space: nowrap;
}

    .quick-text.invoice-text span {
        display: block;
        /* white-space: nowrap; */
    }
    .quick-text.invoice-text {
    display: flex;
    align-items: center;
    gap: 4px;
    white-space: nowrap;
}

    @keyframes erpSpin{
        to{ transform:rotate(360deg); }
    }

    @keyframes erpFadeSlideIn{
        from{
            opacity:0;
            transform:translateY(6px);
        }
        to{
            opacity:1;
            transform:translateY(0);
        }
    }
    .invoice {
        
        margin: 0px 0px !important;
    }
    .erp-search-overlay {
    position: fixed;
    inset: 0;
    background: rgba(180, 184, 200, 0.35);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    z-index: 99999;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 18px;
    opacity: 0;
    pointer-events: none;
    transition: opacity .25s ease;
}

.erp-search-overlay.active {
    opacity: 1;
    pointer-events: auto;
}

.erp-search-modal {
    width: 800px;
    max-width: calc(100vw - 24px);
    background: rgba(255,255,255,0.96);
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 18px 45px rgba(0,0,0,0.12);
    transform: translateY(-10px) scale(.98);
    opacity: 0;
    transition: all .25s ease;
}

.erp-search-overlay.active .erp-search-modal {
    transform: translateY(0) scale(1);
    opacity: 1;
}

.erp-search-input-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px 26px;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    background: #fff;
}

.erp-search-icon-wrap {
    color: #5a52e8;
    display: flex;
    align-items: center;
    justify-content: center;
}

.erp-search-input {
    flex: 1;
    border: none !important;
    outline: none !important;
    background: transparent !important;
    font-size: 18px;
    color: #111118;
    box-shadow: none !important;
    padding: 0;
}

.erp-search-input::placeholder {
    color: #9a9aac;
}

.erp-loading-ring {
    width: 24px;
    height: 24px;
    border: 3px solid rgba(90, 82, 232, 0.18);
    border-top-color: #5a52e8;
    border-radius: 50%;
    animation: erpSpin .75s linear infinite;
    opacity: 0;
    transition: opacity .2s ease;
}

.erp-loading-ring.visible {
    opacity: 1;
}

.erp-search-clear-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    background: rgba(0,0,0,0.08);
    color: #666;
    cursor: pointer;
    font-size: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.erp-search-clear-btn:hover {
    background: rgba(0,0,0,0.12);
}

.erp-filter-row {
    display: flex;
    gap: 10px;
    padding: 14px 22px;
    border-bottom: 1px solid rgba(0,0,0,0.08);
    overflow-x: auto;
    background: #fafafa;
    scrollbar-width: thin;
}

.erp-filter-row::-webkit-scrollbar {
    height: 6px;
}

.erp-filter-row::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.10);
    border-radius: 8px;
}

.erp-filter-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 999px;
    border: 1px solid rgba(0,0,0,0.10);
    background: #fff;
    color: #5f6374;
    font-size: 14px;
    cursor: pointer;
    white-space: nowrap;
    transition: all .18s ease;
}

.erp-filter-pill:hover {
    background: #f4f4f9;
}

.erp-filter-pill.active {
    background: rgba(90,82,232,0.08);
    color: #5a52e8;
    border-color: rgba(90,82,232,0.24);
}

.erp-pill-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: currentColor;
}
/* loading skeleton like screenshot */
.erp-searching-state {
    padding: 12px 0 10px;
    background: #fff;
}

.erp-search-skeleton {
    height: 46px;
    margin: 8px 24px;
    border-radius: 12px;
    background: linear-gradient(
        90deg,
        #f2f2f2 0%,
        #ebebeb 35%,
        #f8f8f8 50%,
        #ebebeb 65%,
        #f2f2f2 100%
    );
    background-size: 220% 100%;
    animation: erpSkeletonShimmer 1.2s linear infinite;
}

.erp-search-skeleton:nth-child(1) { width: calc(100% - 48px); }
.erp-search-skeleton:nth-child(2) { width: calc(95% - 48px); }
.erp-search-skeleton:nth-child(3) { width: calc(89% - 48px); }
.erp-search-skeleton:nth-child(4) { width: calc(84% - 48px); }

.erp-result-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid rgba(0,0,0,0.04);
    cursor: pointer;
    transition: background .15s ease;
}

.erp-result-item:hover,
.erp-result-item.active {
    background: rgba(90,82,232,0.06);
}

.erp-item-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: rgba(90,82,232,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5a52e8;
    flex-shrink: 0;
}

.erp-item-body {
    flex: 1;
    min-width: 0;
}

.erp-item-title {
    font-size: 14px;
    font-weight: 600;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.erp-item-subtitle {
    font-size: 12px;
    color: #8a8a99;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.erp-item-arrow {
    color: #a2a2ad;
    font-size: 16px;
}

.erp-empty-state {
    padding: 44px 20px;
    text-align: center;
    color: #8f8fa1;
}

.erp-search-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 24px;
    border-top: 1px solid rgba(0,0,0,0.08);
    background: #fafafa;
}

.erp-footer-hints {
    display: flex;
    align-items: center;
    gap: 18px;
}

.erp-hint,
.erp-footer-esc {
    font-size: 13px;
    color: #8b8b9b;
    display: flex;
    align-items: center;
    gap: 7px;
}

.erp-kbd {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    height: 24px;
    padding: 0 8px;
    border-radius: 8px;
    background: #f0f0f4;
    border: 1px solid rgba(0,0,0,0.08);
    color: #8a8a99;
    font-size: 12px;
    line-height: 1;
}

@keyframes erpSpin {
    to { transform: rotate(360deg); }
}

@keyframes erpSkeletonShimmer {
    0% { background-position: 220% 0; }
    100% { background-position: -220% 0; }
}

@media (max-width: 768px) {
    .erp-search-modal {
        width: calc(100vw - 12px);
        border-radius: 16px;
    }

    .erp-footer-hints {
        display: none;
    }
}
.invoice {
        
        margin: 0px 0px !important;
    }
</style>

<div class="@if(!$pos_layout) content-wrapper @endif">

    @if(!$pos_layout)
        <button type="button" id="erp-search-trigger" class="erp-search-trigger no-print">
            <span class="erp-search-trigger-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.35-4.35"></path>
                </svg>
            </span>

            <span>Search</span>

            <span class="erp-search-trigger-shortcut">
                <kbd>Alt</kbd>
                <kbd>Shift</kbd>
                <kbd>S</kbd>
            </span>
        </button>

        <div id="erp-global-search-overlay" class="erp-search-overlay no-print" style="display:none;">
            <div class="erp-search-modal" role="dialog" aria-modal="true" aria-label="Universal search">

                <div class="erp-search-input-row">
                    <div class="erp-search-icon-wrap">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="8.5" cy="8.5" r="5.5"></circle>
                            <path d="M15 15l-3-3" stroke-linecap="round"></path>
                        </svg>
                    </div>

                    <input
                        type="text"
                        id="erp-global-search"
                        class="erp-search-input"
                        placeholder="Search products, customers, invoices…"
                        autocomplete="off"
                        spellcheck="false"
                    />

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
                        <div class="quick-icon product">
                            <span class="quick-icon-emoji">➕</span>
                        </div>

                        <div class="quick-text">
                            <strong>Create</strong>
                            <span>a new product</span>
                        </div>

                        <div class="erp-item-arrow">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M2 7h10M8 3l4 4-4 4"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="erp-quick-action-item" data-action="invoice_create">
                        <div class="quick-icon invoice">
                            <span class="quick-icon-emoji">🧾</span>
                        </div>

                        <div class="quick-text invoice-text">
                            <strong>Generate</strong>
                            <span>an invoice</span>
                        </div>

                        <div class="erp-item-arrow">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M2 7h10M8 3l4 4-4 4"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="erp-quick-action-item" data-action="customer_add">
                        <div class="quick-icon customer">
                            <span class="quick-icon-emoji">👤</span>
                        </div>

                        <div class="quick-text">
                            <strong>Add</strong>
                            <span>a customer</span>
                        </div>

                        <div class="erp-item-arrow">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                <path d="M2 7h10M8 3l4 4-4 4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div id="erp-search-shortcuts" class="erp-search-footer">
                    <div class="erp-footer-hints">
                        <span class="erp-hint"><span class="erp-kbd">↑↓</span> Navigate</span>
                        <span class="erp-hint"><span class="erp-kbd">↵</span> Open</span>
                        <span class="erp-hint"><span class="erp-kbd">Tab</span> Filter</span>
                    </div>

                    <div class="erp-footer-esc">
                        <span class="erp-kbd">Esc</span> to close
                    </div>
                </div>

            </div>
        </div>
    @endif

    @yield('content')