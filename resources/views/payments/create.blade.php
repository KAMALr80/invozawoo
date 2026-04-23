@extends('layouts.app')

@section('page-title', 'Record Payment - Invoice #' . $sale->invoice_no)

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #2563eb;
        --primary-dark: #1d4ed8;
        --success: #16a34a;
        --danger: #dc2626;
        --warning: #d97706;
        --purple: #7c3aed;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border: #e2e8f0;
        --bg-light: #f8fafc;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f1f5f9;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= CONTAINER ================= */
    .payment-wrapper {
        min-height: 100vh;
        background: #f1f5f9;
        padding: clamp(16px, 3vw, 2rem) clamp(8px, 2vw, 1rem);
        width: 100%;
    }

    .payment-container {
        max-width: 900px;
        margin: 0 auto;
        width: 100%;
    }

    /* ================= ALERTS ================= */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: var(--radius-md);
        margin-bottom: 1.5rem;
        border-left: 4px solid;
        font-size: clamp(0.875rem, 2vw, 0.95rem);
        word-break: break-word;
    }

    .alert-error {
        background: #fef2f2;
        border-left-color: var(--danger);
    }

    .alert-error ul {
        margin: 0;
        padding-left: 1.5rem;
    }

    .alert-error li {
        color: #991b1b;
    }

    .alert-success {
        background: #f0fdf4;
        border-left-color: var(--success);
    }

    .alert-success p {
        color: #166534;
        margin: 0;
    }

    /* ================= MAIN CARD ================= */
    .card {
        background: var(--bg-white);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        width: 100%;
    }

    /* ================= HEADER ================= */
    .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
        padding: clamp(1.5rem, 4vw, 2rem);
        color: white;
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-title {
        font-size: clamp(1.5rem, 5vw, 1.75rem);
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        word-break: break-word;
    }

    .header-subtitle {
        margin-top: 0.5rem;
        opacity: 0.9;
        font-size: clamp(0.875rem, 2.5vw, 0.95rem);
        word-break: break-word;
    }

    .status-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1.25rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        backdrop-filter: blur(4px);
        white-space: nowrap;
    }

    /* ================= SUMMARY CARDS ================= */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        padding: clamp(1.25rem, 3vw, 1.5rem) clamp(1.5rem, 4vw, 2rem);
        background: var(--bg-light);
        border-bottom: 1px solid var(--border);
    }

    .summary-card {
        background: var(--bg-white);
        padding: clamp(1rem, 2.5vw, 1.25rem);
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .summary-label {
        font-size: clamp(0.8rem, 2vw, 0.85rem);
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-bottom: 0.5rem;
        word-break: break-word;
    }

    .summary-value {
        font-size: clamp(1.25rem, 4vw, 1.5rem);
        font-weight: 700;
        line-height: 1.2;
        word-break: break-word;
    }

    .summary-value.primary {
        color: var(--primary);
    }

    .summary-value.success {
        color: var(--success);
    }

    .summary-value.danger {
        color: var(--danger);
    }

    /* ================= CUSTOMER INFO ================= */
    .customer-info {
        padding: 1.25rem clamp(1.5rem, 4vw, 2rem);
        background: var(--bg-white);
        border-bottom: 1px solid var(--border);
    }

    .customer-details {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .customer-avatar {
        width: clamp(2.5rem, 6vw, 3rem);
        height: clamp(2.5rem, 6vw, 3rem);
        background: var(--bg-light);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(1.25rem, 3vw, 1.5rem);
        flex-shrink: 0;
    }

    .customer-name {
        font-weight: 600;
        color: var(--text-main);
        word-break: break-word;
    }

    .customer-mobile {
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        color: var(--text-muted);
    }

    .balance-badge {
        padding: 0.75rem 1.25rem;
        border-radius: 2rem;
        font-size: clamp(0.85rem, 2vw, 0.95rem);
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }

    .balance-wallet {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .balance-due {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .balance-net {
        background: #f1f5f9;
        color: var(--text-main);
        border: 1px solid var(--border);
    }

    /* ================= FORM SECTION ================= */
    .form-section {
        padding: clamp(1.5rem, 4vw, 2rem);
    }

    /* ================= TABS ================= */
    .tabs {
        display: flex;
        gap: 0.5rem;
        background: var(--bg-light);
        padding: 0.375rem;
        border-radius: var(--radius-lg);
        margin-bottom: 2rem;
        border: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .tab-btn {
        flex: 1;
        min-width: 150px;
        padding: 0.875rem;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: clamp(0.875rem, 2vw, 0.95rem);
        cursor: pointer;
        transition: all 0.2s;
        background: transparent;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .tab-btn.active {
        background: var(--primary);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .tab-btn:hover:not(.active) {
        background: #e2e8f0;
    }

    /* ================= WALLET SECTION ================= */
    .wallet-section {
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: var(--radius-lg);
        padding: clamp(1rem, 3vw, 1.25rem);
        margin-bottom: 1.5rem;
    }

    .wallet-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .wallet-title {
        color: #166534;
        font-weight: 600;
        margin-bottom: 0.25rem;
        font-size: clamp(0.9rem, 2vw, 1rem);
        word-break: break-word;
    }

    .wallet-balance {
        font-size: clamp(1.5rem, 4vw, 1.75rem);
        font-weight: 700;
        color: var(--success);
        word-break: break-word;
    }

    .wallet-toggle {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        border: 1px solid #86efac;
        cursor: pointer;
        user-select: none;
        font-size: clamp(0.85rem, 2vw, 0.95rem);
    }

    .wallet-toggle input {
        width: 1rem;
        height: 1rem;
        cursor: pointer;
    }

    .wallet-input-group {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px dashed #86efac;
    }

    .wallet-input-group.hidden {
        display: none;
    }

    .wallet-input {
        width: 100%;
        padding: 0.875rem;
        border: 1px solid #86efac;
        border-radius: var(--radius-md);
        font-size: clamp(0.875rem, 2vw, 1rem);
        transition: border-color 0.2s;
    }

    .wallet-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .wallet-quick-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }

    .wallet-quick-btn {
        flex: 1;
        min-width: 100px;
        padding: 0.5rem;
        background: #86efac;
        border: none;
        border-radius: var(--radius-sm);
        font-weight: 600;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        cursor: pointer;
        transition: all 0.2s;
    }

    .wallet-quick-btn:hover {
        background: var(--success);
        color: white;
    }

    .wallet-remaining {
        display: flex;
        justify-content: space-between;
        margin-top: 0.75rem;
        background: var(--bg-white);
        padding: 0.75rem;
        border-radius: var(--radius-sm);
        font-size: clamp(0.875rem, 2vw, 0.95rem);
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    /* ================= PAYMENT INPUT ================= */
    .payment-group {
        margin-bottom: 1.5rem;
    }

    .payment-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
        color: var(--text-main);
        font-size: clamp(0.9rem, 2vw, 1rem);
        word-break: break-word;
    }

    .payment-input-wrapper {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .payment-input {
        flex: 1;
        min-width: 200px;
        padding: 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        font-size: clamp(1rem, 2.5vw, 1.125rem);
        transition: border-color 0.2s;
    }

    .payment-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .payment-hint {
        background: #e2e8f0;
        padding: 0.5rem 1rem;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        cursor: pointer;
        display: none;
        white-space: nowrap;
    }

    .payment-hint.visible {
        display: inline-block;
    }

    .quick-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }

    .quick-btn {
        flex: 1;
        min-width: 80px;
        padding: 0.75rem;
        background: #e2e8f0;
        border: none;
        border-radius: var(--radius-md);
        font-weight: 600;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        cursor: pointer;
        transition: all 0.2s;
    }

    .quick-btn:hover {
        background: var(--primary);
        color: white;
    }

    /* ================= PREVIEW ================= */
    .preview-box {
        background: var(--bg-light);
        padding: 1.25rem;
        border-radius: var(--radius-lg);
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
        display: none;
    }

    .preview-box.visible {
        display: block;
    }

    .preview-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        font-size: clamp(1rem, 2.5vw, 1.1rem);
        font-weight: 600;
        word-break: break-word;
    }

    .preview-header.partial {
        color: #92400e;
    }

    .preview-header.full {
        color: var(--success);
    }

    .preview-header.excess {
        color: var(--success);
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .preview-item .label {
        color: var(--text-muted);
        font-size: clamp(0.8rem, 2vw, 0.85rem);
        margin-bottom: 0.25rem;
        word-break: break-word;
    }

    .preview-item .value {
        font-size: clamp(1rem, 2.5vw, 1.125rem);
        font-weight: 700;
        word-break: break-word;
    }

    .preview-item .value.wallet {
        color: var(--success);
    }

    .preview-divider {
        border-top: 2px dashed var(--border);
        padding-top: 1rem;
    }

    .preview-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.25rem;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .preview-row .label {
        font-weight: 600;
    }

    .preview-row .value {
        font-weight: 700;
    }

    .preview-row.due .value {
        color: var(--danger);
    }

    .preview-row.excess .value {
        color: var(--success);
    }

    /* ================= ADVANCE SECTION ================= */
    .advance-section {
        background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        padding: clamp(1.25rem, 3vw, 1.5rem);
        border-radius: var(--radius-lg);
        color: white;
    }

    .advance-title {
        font-size: clamp(1.1rem, 3vw, 1.25rem);
        font-weight: 700;
        margin-bottom: 0.5rem;
        word-break: break-word;
    }

    .advance-description {
        margin-bottom: 1.5rem;
        opacity: 0.9;
        font-size: clamp(0.875rem, 2vw, 0.95rem);
        word-break: break-word;
    }

    .advance-input {
        width: 100%;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: var(--radius-md);
        font-size: clamp(1rem, 2.5vw, 1.125rem);
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transition: all 0.2s;
    }

    .advance-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .advance-input:focus {
        outline: none;
        border-color: white;
        background: rgba(255, 255, 255, 0.15);
    }

    .advance-quick-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .advance-quick-btn {
        flex: 1;
        min-width: 80px;
        padding: 0.75rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: var(--radius-md);
        color: white;
        font-weight: 600;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        cursor: pointer;
        transition: all 0.2s;
    }

    .advance-quick-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .advance-preview {
        background: rgba(255, 255, 255, 0.1);
        padding: 1rem;
        border-radius: var(--radius-md);
        margin-top: 1rem;
        display: none;
        word-break: break-word;
    }

    .advance-preview.visible {
        display: block;
    }

    /* ================= PAYMENT METHODS ================= */
    .methods-title {
        font-size: clamp(1rem, 2.5vw, 1.125rem);
        font-weight: 600;
        margin: 2rem 0 1rem;
        color: var(--text-main);
        word-break: break-word;
    }

    .methods-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .method-card {
        border: 1px solid var(--border);
        padding: 1rem;
        border-radius: var(--radius-md);
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        background: white;
    }

    .method-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .method-card.active {
        border-color: var(--primary);
        background: #eff6ff;
    }

    .method-card.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .method-card.disabled:hover {
        transform: none;
        box-shadow: none;
        border-color: var(--border);
    }

    .method-card input[type="radio"] {
        display: none;
    }

    .method-icon {
        font-size: clamp(1.5rem, 4vw, 1.75rem);
        display: block;
        margin-bottom: 0.25rem;
    }

    .method-name {
        font-weight: 600;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        word-break: break-word;
    }

    /* ================= EMI SECTION ================= */
    .emi-section {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-radius: var(--radius-lg);
        padding: clamp(1.25rem, 3vw, 1.5rem);
        margin-bottom: 1.5rem;
    }

    .emi-title {
        margin: 0 0 1.25rem;
        color: #92400e;
        font-size: clamp(1rem, 2.5vw, 1.1rem);
        font-weight: 600;
        word-break: break-word;
    }

    .emi-field {
        margin-bottom: 1rem;
    }

    .emi-label {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: block;
        color: #92400e;
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        word-break: break-word;
    }

    .emi-input,
    .emi-select {
        width: 100%;
        padding: 0.875rem;
        border: 1px solid #fcd34d;
        border-radius: var(--radius-md);
        transition: border-color 0.2s;
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    .emi-input:focus,
    .emi-select:focus {
        outline: none;
        border-color: var(--warning);
        box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
    }

    .emi-input[readonly] {
        background: #fef9c3;
        font-weight: 600;
    }

    /* ================= FORM FIELDS ================= */
    .form-field {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.25rem;
        display: block;
        color: var(--text-main);
        font-size: clamp(0.85rem, 2vw, 0.9rem);
        word-break: break-word;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 0.875rem;
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        transition: border-color 0.2s;
        font-family: inherit;
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 80px;
    }

    /* ================= SUBMIT BUTTON ================= */
    .submit-btn {
        width: 100%;
        padding: clamp(1rem, 3vw, 1.25rem);
        background: linear-gradient(135deg, var(--primary) 0%, var(--purple) 100%);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        font-size: clamp(1rem, 2.5vw, 1.125rem);
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .submit-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .back-link {
        text-align: center;
        margin-top: 1.25rem;
    }

    .back-link a {
        color: var(--text-muted);
        text-decoration: none;
        font-size: clamp(0.9rem, 2vw, 0.95rem);
        transition: color 0.2s;
    }

    .back-link a:hover {
        color: var(--primary);
    }

    /* ================= UTILITY CLASSES ================= */
    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .fw-bold {
        font-weight: 700;
    }

    .fw-semibold {
        font-weight: 600;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .summary-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .payment-container {
            max-width: 800px;
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .payment-wrapper {
            padding: 1.5rem 0.75rem;
        }

        .customer-details {
            flex-direction: column;
            align-items: flex-start;
        }

        .balance-badge {
            width: 100%;
            justify-content: center;
        }

        .methods-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .payment-wrapper {
            padding: 1rem 0.5rem;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .tabs {
            flex-direction: column;
        }

        .tab-btn {
            width: 100%;
        }

        .wallet-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .wallet-toggle {
            width: 100%;
            justify-content: center;
        }

        .wallet-quick-buttons,
        .quick-buttons,
        .advance-quick-buttons {
            flex-direction: column;
        }

        .wallet-quick-btn,
        .quick-btn,
        .advance-quick-btn {
            width: 100%;
        }

        .methods-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .preview-grid {
            grid-template-columns: 1fr;
        }

        .payment-input-wrapper {
            flex-direction: column;
        }

        .payment-hint {
            width: 100%;
            text-align: center;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .card-header {
            padding: 1.25rem;
        }

        .header-title {
            font-size: 1.35rem;
        }

        .summary-card {
            padding: 1rem;
        }

        .summary-value {
            font-size: 1.25rem;
        }

        .methods-grid {
            grid-template-columns: 1fr;
        }

        .method-card {
            padding: 0.875rem;
        }

        .wallet-balance {
            font-size: 1.5rem;
        }

        .payment-input {
            padding: 0.875rem;
        }

        .form-section {
            padding: 1.25rem;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .card-header {
            padding: 1rem;
        }

        .header-title {
            font-size: 1.25rem;
        }

        .header-subtitle {
            font-size: 0.85rem;
        }

        .status-badge {
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
        }

        .summary-label {
            font-size: 0.75rem;
        }

        .summary-value {
            font-size: 1.1rem;
        }

        .customer-name {
            font-size: 0.95rem;
        }

        .customer-mobile {
            font-size: 0.8rem;
        }

        .wallet-title {
            font-size: 0.9rem;
        }

        .wallet-balance {
            font-size: 1.35rem;
        }

        .payment-input {
            padding: 0.75rem;
        }

        .method-name {
            font-size: 0.8rem;
        }

        .btn-advance,
        .btn-submit {
            padding: 0.875rem;
            font-size: 0.9rem;
        }

        .back-link a {
            font-size: 0.85rem;
        }
    }

    /* Print Styles */
    @media print {
        .card-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .tabs,
        .wallet-section,
        .quick-buttons,
        .wallet-quick-buttons,
        .advance-quick-buttons,
        .method-card,
        .submit-btn,
        .back-link {
            display: none !important;
        }

        .preview-box {
            border: 1px solid #000;
            background: white;
        }

        .balance-badge {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="payment-wrapper">
    <div class="payment-container">

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Success Messages --}}
        @if (session('success'))
            <div class="alert alert-success">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        {{-- Main Card --}}
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <div class="header-content">
                    <div>
                        <h1 class="header-title">💳 Record Payment</h1>
                        <p class="header-subtitle">Invoice #{{ $sale->invoice_no }}</p>
                    </div>
                    <div class="status-badge">
                        {{ strtoupper($sale->payment_status) }}
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="summary-grid">
                <div class="summary-card">
                    <div class="summary-label">Grand Total</div>
                    <div class="summary-value primary">₹{{ number_format($sale->grand_total, 2) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Paid Amount</div>
                    <div class="summary-value success">₹{{ number_format($paidAmount, 2) }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-label">Due Amount</div>
                    <div class="summary-value danger">₹{{ number_format($remaining, 2) }}</div>
                </div>
            </div>

            {{-- Customer Info --}}
            @if ($sale->customer)
                <div class="customer-info">
                    <div class="customer-details">
                        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <div class="customer-avatar">
                                <span>👤</span>
                            </div>
                            <div>
                                <div class="customer-name">{{ $sale->customer->name }}</div>
                                <div class="customer-mobile">{{ $sale->customer->mobile ?? '' }}</div>
                            </div>
                        </div>

                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            @if ($walletBalance > 0)
                                <div class="balance-badge balance-wallet">
                                    <span>💰</span>
                                    <span>Wallet: ₹{{ number_format($walletBalance, 2) }}</span>
                                </div>
                            @endif

                            @if ($dueBalance > 0)
                                <div class="balance-badge balance-due">
                                    <span>⚠️</span>
                                    <span>Due: ₹{{ number_format($dueBalance, 2) }}</span>
                                </div>
                            @endif

                            <div class="balance-badge balance-net">
                                <span>📊</span>
                                <span>
                                    @if ($openBalance > 0)
                                        Due: ₹{{ number_format($openBalance, 2) }}
                                    @elseif($openBalance < 0)
                                        Advance: ₹{{ number_format(abs($openBalance), 2) }}
                                    @else
                                        Clear (₹0)
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Payment Form --}}
            <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
                @csrf
                <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                <input type="hidden" name="payment_type" id="payment_type" value="full">
                <input type="hidden" name="is_advance_only" id="is_advance_only" value="0">

                <div class="form-section">

                    {{-- Tabs --}}
                    <div class="tabs">
                        <button type="button" class="tab-btn active" id="tabInvoice">🧾 Invoice Payment</button>
                        <button type="button" class="tab-btn" id="tabAdvance">👛 Add to Wallet</button>
                    </div>

                    {{-- Invoice Section --}}
                    <div id="invoiceSection">

                        {{-- Wallet Usage --}}
                        @if ($walletBalance > 0)
                            <div class="wallet-section">
                                <div class="wallet-header">
                                    <div>
                                        <div class="wallet-title">💰 Wallet Balance Available</div>
                                        <div class="wallet-balance">₹{{ number_format($walletBalance, 2) }}</div>
                                    </div>
                                    <label class="wallet-toggle">
                                        <input type="checkbox" id="useWalletCheckbox">
                                        <span>Use from Wallet</span>
                                    </label>
                                </div>

                                <div id="walletUseBox" class="wallet-input-group hidden">
                                    <label style="font-weight: 600; margin-bottom: 0.5rem; display: block;">Amount to
                                        use</label>
                                    <input type="number" id="walletAmount" name="wallet_used" min="0"
                                        max="{{ $walletBalance }}" step="0.01" value="0" class="wallet-input"
                                        placeholder="Enter amount">

                                    <div class="wallet-quick-buttons">
                                        <button type="button" class="wallet-quick-btn"
                                            data-wallet-amount="{{ min($walletBalance, $remaining) }}">
                                            Full (₹{{ number_format(min($walletBalance, $remaining), 2) }})
                                        </button>
                                        <button type="button" class="wallet-quick-btn"
                                            data-wallet-amount="{{ min($walletBalance / 2, $remaining) }}">
                                            50%
                                        </button>
                                    </div>

                                    <div class="wallet-remaining">
                                        <span>Remaining in wallet:</span>
                                        <span class="fw-bold"
                                            id="remainingWallet">₹{{ number_format($walletBalance, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Cash Payment --}}
                        <div class="payment-group">
                            <label class="payment-label">💰 Cash/Other Payment</label>
                            <div class="payment-input-wrapper">
                                <input type="number" name="payment_amount" id="paymentAmount" step="0.01"
                                    min="0" value="{{ $remaining > 0 ? $remaining : 0 }}" class="payment-input">
                                <span id="calculatedHint" class="payment-hint"></span>
                            </div>

                            <div class="quick-buttons">
                                <button type="button" class="quick-btn" data-amount="{{ $remaining }}">
                                    Full (₹{{ number_format($remaining, 2) }})
                                </button>
                                <button type="button" class="quick-btn"
                                    data-amount="{{ $remaining / 2 }}">50%</button>
                                <button type="button" class="quick-btn" data-amount="0">Zero</button>
                            </div>
                        </div>

                        {{-- Preview --}}
                        <div id="paymentPreview" class="preview-box"></div>

                        {{-- Wallet Stats --}}
                        <div id="walletStats" class="wallet-section" style="display: none;">
                            <h4 style="margin: 0 0 0.75rem;">Wallet Summary</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                <div>
                                    <span style="color: var(--text-muted);">Balance:</span>
                                    <span class="fw-bold"
                                        id="walletBalanceDisplay">₹{{ number_format($walletBalance, 2) }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-muted);">Due:</span>
                                    <span class="fw-bold"
                                        id="invoiceDueDisplay">₹{{ number_format($remaining, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Advance Section --}}
                    <div id="advanceSection" style="display: none;">
                        <div class="advance-section">
                            <h3 class="advance-title">👛 Add Money to Wallet</h3>
                            <p class="advance-description">This amount will be added as advance</p>

                            <div>
                                <input type="number" name="advance_amount" id="advanceAmount" step="0.01"
                                    min="1" class="advance-input" placeholder="Enter amount">

                                <div class="advance-quick-buttons">
                                    <button type="button" class="advance-quick-btn"
                                        data-advance="1000">₹1,000</button>
                                    <button type="button" class="advance-quick-btn"
                                        data-advance="2000">₹2,000</button>
                                    <button type="button" class="advance-quick-btn"
                                        data-advance="5000">₹5,000</button>
                                    <button type="button" class="advance-quick-btn"
                                        data-advance="10000">₹10,000</button>
                                </div>
                            </div>

                            <div id="advancePreview" class="advance-preview">
                                <!-- Preview -->
                            </div>
                        </div>
                    </div>

                    {{-- Payment Methods --}}
                    <h3 class="methods-title">💳 Payment Method</h3>
                    <div class="methods-grid">

                        <label class="method-card active" data-method="cash">
                            <input type="radio" name="method" value="cash" checked>
                            <span class="method-icon">💵</span>
                            <span class="method-name">Cash</span>
                        </label>

                        <label class="method-card" data-method="upi">
                            <input type="radio" name="method" value="upi">
                            <span class="method-icon">📱</span>
                            <span class="method-name">UPI</span>
                        </label>

                        <label class="method-card" data-method="card">
                            <input type="radio" name="method" value="card">
                            <span class="method-icon">💳</span>
                            <span class="method-name">Card</span>
                        </label>

                        <label class="method-card" data-method="net_banking">
                            <input type="radio" name="method" value="net_banking">
                            <span class="method-icon">🏦</span>
                            <span class="method-name">Net Banking</span>
                        </label>

                        <label class="method-card" data-method="emi">
                            <input type="radio" name="method" value="emi">
                            <span class="method-icon">📆</span>
                            <span class="method-name">EMI</span>
                        </label>

                        <label class="method-card" data-method="wallet">
                            <input type="radio" name="method" value="wallet">
                            <span class="method-icon">👛</span>
                            <span class="method-name">Wallet</span>
                        </label>
                    </div>

                    {{-- EMI Section --}}
                    <div id="emiSection" class="emi-section" style="display: none;">
                        <h4 class="emi-title">📆 EMI Setup</h4>

                        <div class="emi-field">
                            <label class="emi-label">Down Payment</label>
                            <input type="number" id="downPayment" name="down_payment" min="1"
                                max="{{ $remaining - 1 }}" step="0.01" class="emi-input">
                        </div>

                        <div class="emi-field">
                            <label class="emi-label">EMI Months</label>
                            <select id="emiMonths" name="emi_months" class="emi-select">
                                <option value="">Select</option>
                                @foreach ([3, 6, 9, 12, 18, 24] as $month)
                                    <option value="{{ $month }}">{{ $month }} Months</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="emi-field">
                            <label class="emi-label">Monthly EMI</label>
                            <input type="number" id="emiAmount" name="emi_amount" readonly class="emi-input">
                        </div>
                    </div>

                    {{-- Transaction Details --}}
                    <div class="form-field">
                        <label class="form-label">Transaction Reference (Optional)</label>
                        <input type="text" name="transaction_id" class="form-input"
                            placeholder="e.g., UPI Ref No">
                    </div>

                    <div class="form-field">
                        <label class="form-label">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-textarea" placeholder="Add notes..."></textarea>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" id="submitBtn" class="submit-btn">
                        <span id="submitBtnText">💰 Process Payment</span>
                    </button>

                    <div class="back-link">
                        <a href="{{ route('sales.show', $sale->id) }}">← Back to Invoice</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elements
        const invoiceSection = document.getElementById('invoiceSection');
        const advanceSection = document.getElementById('advanceSection');
        const tabInvoice = document.getElementById('tabInvoice');
        const tabAdvance = document.getElementById('tabAdvance');
        const isAdvanceOnly = document.getElementById('is_advance_only');

        const methodCards = document.querySelectorAll('.method-card');
        const emiSection = document.getElementById('emiSection');

        const paymentAmount = document.getElementById('paymentAmount');
        const useWalletCheckbox = document.getElementById('useWalletCheckbox');
        const walletAmount = document.getElementById('walletAmount');
        const walletUseBox = document.getElementById('walletUseBox');
        const remainingWallet = document.getElementById('remainingWallet');
        const paymentPreview = document.getElementById('paymentPreview');
        const paymentType = document.getElementById('payment_type');
        const calculatedHint = document.getElementById('calculatedHint');
        const walletStats = document.getElementById('walletStats');
        const walletBalanceDisplay = document.getElementById('walletBalanceDisplay');
        const invoiceDueDisplay = document.getElementById('invoiceDueDisplay');
        const advancePreview = document.getElementById('advancePreview');
        const advanceAmount = document.getElementById('advanceAmount');

        const downPayment = document.getElementById('downPayment');
        const emiMonths = document.getElementById('emiMonths');
        const emiAmount = document.getElementById('emiAmount');

        const walletBalance = {{ $walletBalance }};
        const dueAmount = {{ $remaining }};
        const openBalance = {{ $openBalance ?? 0 }};

        // Show wallet stats
        if (walletBalance > 0) {
            walletStats.style.display = 'block';
        }

        // Auto-calculate cash
        function autoCalculateCash() {
            if (useWalletCheckbox?.checked) {
                const wallet = parseFloat(walletAmount.value) || 0;
                const remaining = Math.max(0, dueAmount - wallet);
                if (remaining >= 0) {
                    calculatedHint.textContent = `Suggested: ₹${remaining.toFixed(2)}`;
                    calculatedHint.classList.add('visible');
                }
            } else {
                calculatedHint.classList.remove('visible');
            }
        }

        // Apply suggested amount
        function applySuggestedAmount() {
            if (useWalletCheckbox?.checked) {
                const wallet = parseFloat(walletAmount.value) || 0;
                const suggested = Math.max(0, dueAmount - wallet);
                paymentAmount.value = suggested.toFixed(2);
                updatePaymentPreview();
            }
        }

        // Tab Switching
        tabInvoice.addEventListener('click', () => {
            tabInvoice.classList.add('active');
            tabAdvance.classList.remove('active');
            invoiceSection.style.display = 'block';
            advanceSection.style.display = 'none';
            isAdvanceOnly.value = '0';

            document.querySelectorAll('input[name="method"]').forEach(r => r.disabled = false);
            methodCards.forEach(c => c.classList.remove('disabled'));

            const walletRadio = document.querySelector('input[name="method"][value="wallet"]');
            if (walletRadio?.checked) {
                document.querySelector('input[name="method"][value="cash"]').checked = true;
                document.querySelector('[data-method="cash"]').classList.add('active');
                document.querySelector('[data-method="wallet"]').classList.remove('active');
            }
        });

        tabAdvance.addEventListener('click', () => {
            tabAdvance.classList.add('active');
            tabInvoice.classList.remove('active');
            invoiceSection.style.display = 'none';
            advanceSection.style.display = 'block';
            isAdvanceOnly.value = '1';

            document.querySelector('input[name="method"][value="wallet"]').checked = true;
            document.querySelectorAll('input[name="method"]').forEach(r => {
                if (r.value !== 'wallet') {
                    r.disabled = true;
                    r.closest('.method-card').classList.add('disabled');
                }
            });

            methodCards.forEach(c => {
                if (c.dataset.method === 'wallet') {
                    c.classList.add('active');
                } else {
                    c.classList.remove('active');
                }
            });

            emiSection.style.display = 'none';
            updateAdvancePreview();
        });

        // Method Selection
        methodCards.forEach(card => {
            card.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;

                const method = this.dataset.method;
                this.querySelector('input').checked = true;

                methodCards.forEach(c => {
                    c.classList.toggle('active', c === this);
                });

                emiSection.style.display = method === 'emi' ? 'block' : 'none';

                if (method === 'emi' && useWalletCheckbox) {
                    useWalletCheckbox.checked = false;
                    walletUseBox?.classList.add('hidden');
                }
            });
        });

        // Quick Buttons
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                paymentAmount.value = parseFloat(btn.dataset.amount).toFixed(2);
                updatePaymentPreview();
            });
        });

        document.querySelectorAll('.wallet-quick-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                walletAmount.value = parseFloat(btn.dataset.walletAmount).toFixed(2);
                walletAmount.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
            });
        });

        document.querySelectorAll('.advance-quick-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                advanceAmount.value = btn.dataset.advance;
                updateAdvancePreview();
            });
        });

        // Wallet Checkbox
        useWalletCheckbox?.addEventListener('change', function() {
            walletUseBox.classList.toggle('hidden', !this.checked);
            if (!this.checked) {
                walletAmount.value = 0;
                calculatedHint.classList.remove('visible');
                paymentAmount.value = dueAmount.toFixed(2);
            } else {
                const suggested = Math.min(walletBalance, dueAmount);
                walletAmount.value = suggested.toFixed(2);
            }
            walletAmount.dispatchEvent(new Event('input', {
                bubbles: true
            }));
        });

        // Wallet Amount Input
        walletAmount?.addEventListener('input', function() {
            let val = Math.min(parseFloat(this.value) || 0, walletBalance);
            this.value = val;

            if (remainingWallet) {
                remainingWallet.textContent = '₹' + (walletBalance - val).toFixed(2);
            }
            if (walletBalanceDisplay) {
                walletBalanceDisplay.textContent = '₹' + (walletBalance - val).toFixed(2);
            }

            autoCalculateCash();
            updatePaymentPreview();
        });

        // Payment Amount Input
        paymentAmount?.addEventListener('input', updatePaymentPreview);

        // Hint Click
        calculatedHint?.addEventListener('click', applySuggestedAmount);

        // Advance Amount
        advanceAmount?.addEventListener('input', updateAdvancePreview);

        // EMI Calculator
        function calculateEMI() {
            const down = parseFloat(downPayment.value) || 0;
            const months = parseInt(emiMonths.value) || 0;

            if (down > 0 && months > 0 && down < dueAmount) {
                const monthly = (dueAmount - down) / months;
                emiAmount.value = monthly.toFixed(2);
                paymentAmount.value = down;
                updatePaymentPreview();
            }
        }

        downPayment?.addEventListener('input', calculateEMI);
        emiMonths?.addEventListener('change', calculateEMI);

        // Payment Preview
        function updatePaymentPreview() {
            const cash = parseFloat(paymentAmount.value) || 0;
            const wallet = useWalletCheckbox?.checked ? (parseFloat(walletAmount.value) || 0) : 0;
            const total = cash + wallet;

            if (total <= 0) {
                paymentPreview.classList.remove('visible');
                return;
            }

            let html = '';
            let previewClass = '';

            if (total < dueAmount) {
                paymentType.value = 'partial';
                previewClass = 'partial';
                html = `
        <div class="preview-header ${previewClass}">
            <span>⚠️</span> <strong>Partial Payment</strong>
        </div>
        <div class="preview-grid">
            <div class="preview-item"><span class="label">Cash:</span> <span class="value">₹${cash.toFixed(2)}</span></div>
            <div class="preview-item"><span class="label">Wallet:</span> <span class="value wallet">₹${wallet.toFixed(2)}</span></div>
        </div>
        <div class="preview-divider">
            <div class="preview-row"><span>Total Paid:</span> <span>₹${total.toFixed(2)}</span></div>
            <div class="preview-row due"><span>Remaining:</span> <span>₹${(dueAmount - total).toFixed(2)}</span></div>
        </div>
    `;
            } else if (total > dueAmount) {
                paymentType.value = 'excess';
                previewClass = 'excess';
                const excess = total - dueAmount;
                html = `
        <div class="preview-header ${previewClass}">
            <span>💰</span> <strong>Excess Payment</strong>
        </div>
        <div class="preview-grid">
            <div class="preview-item"><span class="label">Cash:</span> <span class="value">₹${cash.toFixed(2)}</span></div>
            <div class="preview-item"><span class="label">Wallet:</span> <span class="value wallet">₹${wallet.toFixed(2)}</span></div>
        </div>
        <div class="preview-divider">
            <div class="preview-row"><span>Total Paid:</span> <span>₹${total.toFixed(2)}</span></div>
            <div class="preview-row"><span>Invoice:</span> <span>₹${dueAmount.toFixed(2)}</span></div>
            <div class="preview-row excess"><span>Excess to Wallet:</span> <span>+₹${excess.toFixed(2)}</span></div>
        </div>
    `;
            } else {
                paymentType.value = 'full';
                previewClass = 'full';
                html = `
        <div class="preview-header ${previewClass}">
            <span>✅</span> <strong>Full Payment</strong>
        </div>
        <div class="preview-grid">
            <div class="preview-item"><span class="label">Cash:</span> <span class="value">₹${cash.toFixed(2)}</span></div>
            <div class="preview-item"><span class="label">Wallet:</span> <span class="value wallet">₹${wallet.toFixed(2)}</span></div>
        </div>
        <div class="preview-divider">
            <div class="preview-row"><span>Total Paid:</span> <span>₹${total.toFixed(2)}</span></div>
            <div class="preview-row"><span>Status:</span> <span class="success">PAID</span></div>
        </div>
    `;
            }

            paymentPreview.innerHTML = html;
            paymentPreview.classList.add('visible');
        }

        // Advance Preview
        function updateAdvancePreview() {
            const amount = parseFloat(advanceAmount.value) || 0;
            if (amount > 0) {
                advancePreview.classList.add('visible');
                advancePreview.innerHTML = `
        <strong>💰 Preview:</strong><br>
        Amount: ₹${amount.toFixed(2)} will be added to wallet
    `;
            } else {
                advancePreview.classList.remove('visible');
            }
        }

        // Initial preview
        if (dueAmount > 0) updatePaymentPreview();

        // Form Submit
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const isAdvance = isAdvanceOnly.value === '1';
            const method = document.querySelector('input[name="method"]:checked')?.value;

            if (isAdvance) {
                const amount = parseFloat(advanceAmount.value) || 0;
                if (amount <= 0) {
                    e.preventDefault();
                    alert('Please enter amount to add to wallet');
                    return false;
                }
                if (!confirm(`Add ₹${amount.toFixed(2)} to wallet?`)) {
                    e.preventDefault();
                    return false;
                }
            } else if (method === 'emi') {
                const down = parseFloat(downPayment.value) || 0;
                const months = emiMonths.value;

                if (down <= 0 || !months) {
                    e.preventDefault();
                    alert('Please complete EMI details');
                    return false;
                }
                if (down >= dueAmount) {
                    e.preventDefault();
                    alert('Down payment must be less than due amount');
                    return false;
                }
            } else {
                const cash = parseFloat(paymentAmount.value) || 0;
                const wallet = useWalletCheckbox?.checked ? (parseFloat(walletAmount.value) || 0) : 0;

                if (cash <= 0 && wallet <= 0) {
                    e.preventDefault();
                    alert('Please enter payment amount');
                    return false;
                }
                if (wallet > walletBalance) {
                    e.preventDefault();
                    alert('Insufficient wallet balance');
                    return false;
                }
            }
        });
    });
</script>
@endpush
@endsection