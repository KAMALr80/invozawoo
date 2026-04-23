@extends('layouts.app')

@section('page-title', 'Invoice #' . $sale->invoice_no)

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
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-2xl: 20px;
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f1f5f9;
            font-family: var(--font-sans);
            color: var(--text-main);
            line-height: 1.5;
        }

        /* ================= MAIN CONTAINER ================= */
        .invoice-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            padding: clamp(16px, 3vw, 2rem) clamp(8px, 2vw, 1rem);
            width: 100%;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* ================= INVOICE CARD ================= */
        .invoice-card {
            background: var(--bg-white);
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-xl);
            overflow: hidden;
            width: 100%;
        }

        /* ================= HEADER ================= */
        .invoice-header {
            background: linear-gradient(135deg, #1e293b, #0f172a);
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

        .header-left {
            flex: 1;
            min-width: 250px;
        }

        .invoice-title {
            font-size: clamp(1.5rem, 5vw, 2rem);
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            word-break: break-word;
        }

        .invoice-subtitle {
            margin-top: 0.5rem;
            opacity: 0.9;
            font-size: clamp(0.875rem, 2.5vw, 0.95rem);
            word-break: break-word;
        }

        .header-right {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: clamp(0.85rem, 2vw, 0.9rem);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .status-badge.paid {
            background: var(--success);
            color: white;
        }

        .status-badge.partial {
            background: var(--warning);
            color: white;
        }

        .status-badge.unpaid {
            background: var(--danger);
            color: white;
        }

        .status-badge.emi {
            background: var(--purple);
            color: white;
        }

        .header-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .header-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: clamp(0.8rem, 2vw, 0.85rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            white-space: nowrap;
        }

        .header-btn:hover {
            background: white;
            color: #0f172a;
        }

        /* ================= CUSTOMER SECTION ================= */
        .customer-section {
            padding: clamp(1.25rem, 3vw, 1.5rem) clamp(1.5rem, 4vw, 2rem);
            background: var(--bg-light);
            border-bottom: 1px solid var(--border);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }

        .customer-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }

        .customer-value {
            font-size: clamp(1rem, 2.5vw, 1.125rem);
            font-weight: 600;
            color: var(--text-main);
            word-break: break-word;
        }

        .customer-detail {
            margin-top: 0.5rem;
            color: var(--text-muted);
            font-size: clamp(0.85rem, 2vw, 0.9rem);
            word-break: break-word;
        }

        .customer-detail div {
            margin-bottom: 0.25rem;
        }

        /* ================= SHIPPING SECTION ================= */
        .shipping-section {
            padding: clamp(1.25rem, 3vw, 1.5rem) clamp(1.5rem, 4vw, 2rem);
            background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
            border-bottom: 1px solid var(--border);
            border-left: 4px solid var(--purple);
        }

        .shipping-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .shipping-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
        }

        .shipping-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #5b21b6;
        }

        .shipping-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-left: 3.25rem;
        }

        .shipping-item {
            margin-bottom: 0.5rem;
        }

        .shipping-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #6d28d9;
            font-weight: 600;
            letter-spacing: 0.3px;
            margin-bottom: 0.25rem;
        }

        .shipping-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-main);
            word-break: break-word;
        }

        .shipping-instructions {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.5);
            border-radius: var(--radius-md);
            font-style: italic;
            color: #4b5563;
        }

        .shipping-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-left: 0.5rem;
        }

        .shipping-badge.required {
            background: var(--purple);
            color: white;
        }

        /* ================= WALLET CARDS ================= */
        .wallet-grid {
            padding: clamp(1.25rem, 3vw, 1.5rem) clamp(1.5rem, 4vw, 2rem);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .wallet-card {
            background: var(--bg-light);
            padding: clamp(1rem, 2.5vw, 1.25rem);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .wallet-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .wallet-card.advance {
            background: #f0fdf4;
            border-color: #86efac;
        }

        .wallet-card.due {
            background: #fef2f2;
            border-color: #fecaca;
        }

        .wallet-card.net {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .wallet-card.excess {
            background: #f3e8ff;
            border-color: #c4b5fd;
        }

        .wallet-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            word-break: break-word;
        }

        .wallet-amount {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 700;
            margin-bottom: 0.25rem;
            word-break: break-word;
        }

        .wallet-amount.advance {
            color: var(--success);
        }

        .wallet-amount.due {
            color: var(--danger);
        }

        .wallet-amount.net {
            color: var(--primary);
        }

        .wallet-amount.excess {
            color: var(--purple);
        }

        .wallet-sub {
            font-size: 0.85rem;
            color: var(--text-muted);
            word-break: break-word;
        }

        /* ================= ITEMS SECTION ================= */
        .items-section {
            padding: clamp(1.25rem, 3vw, 1.5rem) clamp(1.5rem, 4vw, 2rem);
        }

        .section-title {
            font-size: clamp(1rem, 2.5vw, 1.125rem);
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            word-break: break-word;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: clamp(0.875rem, 2.2vw, 0.95rem);
            min-width: 900px;
        }

        .items-table th {
            background: var(--bg-light);
            padding: 1rem;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }

        .items-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
            white-space: nowrap;
        }

        .items-table tbody tr:hover {
            background: var(--bg-light);
        }

        /* ================= PRICE STYLES ================= */
        .mrp-price {
            color: #64748b;
            font-size: 0.85rem;
            text-decoration: line-through;
            margin-right: 0.5rem;
        }

        .selling-price {
            font-weight: 700;
            color: var(--success);
        }

        .discount-badge {
            display: inline-block;
            padding: 0.2rem 0.5rem;
            background: #f0fdf4;
            color: var(--success);
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .price-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .price-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* ================= SUMMARY BOX ================= */
        .summary-container {
            display: grid;
            grid-template-columns: 1fr minmax(300px, 350px);
            gap: 2rem;
            margin-top: 1.25rem;
        }

        @media (max-width: 992px) {
            .summary-container {
                grid-template-columns: 1fr;
            }
        }

        .summary-box {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px dashed var(--border);
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--text-muted);
            font-size: 0.95rem;
            word-break: break-word;
        }

        .summary-value {
            font-weight: 600;
            color: var(--text-main);
            word-break: break-word;
        }

        .grand-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border);
        }

        .grand-total .amount {
            color: var(--primary);
        }

        .total-mrp-row {
            color: #64748b;
        }

        .total-discount-row {
            color: var(--success);
        }

        /* ================= PAYMENT SUMMARY CARDS ================= */
        .payment-summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .payment-card {
            background: white;
            padding: 1.25rem;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }

        .payment-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .payment-card.total {
            border-left: 4px solid #1e293b;
        }

        .payment-card.paid {
            border-left: 4px solid var(--success);
        }

        .payment-card.invoice {
            border-left: 4px solid var(--primary);
        }

        .payment-card.wallet {
            border-left: 4px solid var(--purple);
        }

        .payment-card.advance {
            border-left: 4px solid var(--purple);
            background: #f3e8ff;
        }

        .payment-card.remaining {
            border-left: 4px solid var(--warning);
        }

        .payment-label {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            word-break: break-word;
        }

        .payment-value {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 700;
            word-break: break-word;
        }

        .payment-value.total {
            color: #1e293b;
        }

        .payment-value.paid {
            color: var(--success);
        }

        .payment-value.invoice {
            color: var(--primary);
        }

        .payment-value.wallet {
            color: var(--purple);
        }

        .payment-value.advance {
            color: var(--purple);
        }

        .payment-value.remaining {
            color: var(--warning);
        }

        .payment-sub {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 0.25rem;
            word-break: break-word;
        }

        /* ================= PAYMENTS SECTION ================= */
        .payments-section {
            padding: clamp(1.25rem, 3vw, 1.5rem) clamp(1.5rem, 4vw, 2rem);
            background: var(--bg-light);
            border-top: 1px solid var(--border);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .payments-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            font-size: clamp(0.875rem, 2.2vw, 0.95rem);
            min-width: 900px;
        }

        .payments-table th {
            background: #f1f5f9;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }

        .payments-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            white-space: nowrap;
        }

        .payments-table tbody tr:hover {
            background: var(--bg-light);
        }

        /* ================= BADGES ================= */
        .type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .type-badge.invoice {
            background: #dcfce7;
            color: #166534;
        }

        .type-badge.advance-only {
            background: #f3e8ff;
            color: #6d28d9;
        }

        .type-badge.advance-used {
            background: #ede9fe;
            color: #5b21b6;
        }

        .type-badge.excess {
            background: #f3e8ff;
            color: #6d28d9;
        }

        .type-badge.emi {
            background: #ffedd5;
            color: #9a3412;
        }

        /* ================= SHIPMENT BADGES ================= */
        .shipment-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .shipment-status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .shipment-status.picked {
            background: #cffafe;
            color: #0e7490;
        }

        .shipment-status.in_transit {
            background: #dbeafe;
            color: #1e40af;
        }

        .shipment-status.out_for_delivery {
            background: #f3e8ff;
            color: #6b21a5;
        }

        .shipment-status.delivered {
            background: #dcfce7;
            color: #166534;
        }

        .shipment-status.failed {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ================= SHIPMENT CARD ================= */
        .shipment-card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            padding: 1rem;
            transition: all 0.2s;
            margin-bottom: 1rem;
        }

        .shipment-card:hover {
            box-shadow: var(--shadow-md);
        }

        .shipment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .shipment-number {
            font-weight: 700;
            color: var(--primary);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .shipment-tracking {
            font-family: monospace;
            font-size: 0.9rem;
            color: var(--text-main);
            background: var(--bg-light);
            padding: 0.25rem 0.5rem;
            border-radius: var(--radius-sm);
        }

        .shipment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
            font-size: 0.9rem;
        }

        .shipment-detail-item {
            display: flex;
            flex-direction: column;
        }

        .shipment-detail-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 0.3px;
        }

        .shipment-detail-value {
            font-weight: 600;
            color: var(--text-main);
        }

        .shipment-tracking-link {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.85rem;
        }

        .shipment-tracking-link:hover {
            text-decoration: underline;
        }

        /* ================= ACTION BUTTONS ================= */
        .btn-sm {
            padding: 0.4rem 0.75rem;
            border-radius: var(--radius-sm);
            font-size: clamp(0.8rem, 2vw, 0.85rem);
            font-weight: 500;
            border: 1px solid var(--border);
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            white-space: nowrap;
        }

        .btn-sm:hover {
            background: #f1f5f9;
        }

        .btn-danger:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        .btn-primary:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-warning:hover {
            background: var(--warning);
            color: white;
            border-color: var(--warning);
        }

        .btn-purple:hover {
            background: var(--purple);
            color: white;
            border-color: var(--purple);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn-primary-lg {
            background: var(--primary);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-primary-lg:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary-lg {
            background: #f1f5f9;
            color: #475569;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: 1px solid var(--border);
            white-space: nowrap;
        }

        .btn-secondary-lg:hover {
            background: #e2e8f0;
            color: var(--text-main);
        }

        .btn-purple-lg {
            background: var(--purple);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            white-space: nowrap;
        }

        .btn-purple-lg:hover {
            background: #6d28d9;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* ================= EMI SECTION ================= */
        .emi-section {
            margin-top: 1.5rem;
            padding: 1.5rem;
            background: #fffbeb;
            border-radius: var(--radius-lg);
            border: 1px solid #fcd34d;
        }

        .emi-title {
            margin: 0 0 1rem 0;
            color: #92400e;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            word-break: break-word;
        }

        .emi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
        }

        .emi-item {
            background: white;
            padding: 0.75rem;
            border-radius: var(--radius-md);
            border: 1px solid #fed7aa;
        }

        .emi-label {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
            word-break: break-word;
        }

        .emi-value {
            font-weight: 600;
            color: #92400e;
            word-break: break-word;
        }

        .emi-status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .emi-status.running {
            background: #fef3c7;
            color: #92400e;
        }

        .emi-status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        /* ================= SUMMARY NOTE ================= */
        .summary-note {
            margin-top: 1rem;
            padding: 1rem;
            background: white;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            border: 1px solid var(--border);
            word-break: break-word;
        }

        .summary-note .fw-bold {
            font-weight: 600;
        }

        .summary-note .text-success {
            color: var(--success);
        }

        .summary-note .text-primary {
            color: var(--primary);
        }

        .summary-note .text-purple {
            color: var(--purple);
        }

        .summary-note .text-danger {
            color: var(--danger);
        }

        /* ================= EMPTY STATE ================= */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: var(--radius-lg);
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            color: var(--text-main);
            word-break: break-word;
        }

        .empty-text {
            word-break: break-word;
        }

        /* ================= FOOTER ================= */
        .invoice-footer {
            padding: 1.25rem 2rem;
            text-align: center;
            color: #94a3b8;
            font-size: 0.85rem;
            border-top: 1px solid var(--border);
            word-break: break-word;
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

        .text-success {
            color: var(--success);
        }

        .text-danger {
            color: var(--danger);
        }

        .text-primary {
            color: var(--primary);
        }

        .text-purple {
            color: var(--purple);
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mb-2 {
            margin-bottom: 0.5rem;
        }

        .ml-2 {
            margin-left: 0.5rem;
        }

        /* ================= TOAST NOTIFICATION ================= */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border-left: 4px solid;
            display: none;
            z-index: 9999;
            max-width: 400px;
            width: calc(100% - 40px);
            animation: slideIn 0.3s ease;
        }

        .toast.success {
            border-left-color: var(--success);
        }

        .toast.error {
            border-left-color: var(--danger);
        }

        .toast.warning {
            border-left-color: var(--warning);
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* ================= LOADING OVERLAY ================= */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #e2e8f0;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .loading-text {
            color: var(--text-main);
            font-weight: 500;
        }

        /* ================= RESPONSIVE BREAKPOINTS ================= */

        /* Large Desktop (1200px and above) */
        @media (min-width: 1200px) {
            .wallet-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Desktop (992px to 1199px) */
        @media (max-width: 1199px) {
            .wallet-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .payment-summary-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .emi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .shipping-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Tablet (768px to 991px) */
        @media (max-width: 991px) {
            .invoice-header {
                padding: 1.5rem;
            }

            .customer-section {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .wallet-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .payment-summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .shipping-grid {
                margin-left: 0;
            }
        }

        /* Mobile Landscape (576px to 767px) */
        @media (max-width: 767px) {
            .invoice-page {
                padding: 15px;
            }

            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-right {
                width: 100%;
            }

            .status-badge {
                width: 100%;
                text-align: center;
            }

            .header-actions {
                width: 100%;
            }

            .header-btn {
                flex: 1;
                justify-content: center;
            }

            .wallet-grid {
                grid-template-columns: 1fr;
            }

            .payment-summary-grid {
                grid-template-columns: 1fr;
            }

            .items-table {
                min-width: 800px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-primary-lg,
            .btn-secondary-lg,
            .btn-purple-lg {
                width: 100%;
                justify-content: center;
            }

            .emi-grid {
                grid-template-columns: 1fr;
            }

            .toast {
                left: 20px;
                right: 20px;
                max-width: none;
            }

            .shipping-header {
                flex-wrap: wrap;
            }
        }

        /* Mobile Portrait (up to 575px) */
        @media (max-width: 575px) {
            .invoice-page {
                padding: 12px;
            }

            .customer-section,
            .wallet-grid,
            .items-section,
            .payments-section,
            .shipping-section {
                padding: 1.25rem;
            }

            .summary-container {
                gap: 1rem;
            }

            .summary-box {
                padding: 1.25rem;
            }

            .summary-label {
                font-size: 0.875rem;
            }

            .summary-value {
                font-size: 0.875rem;
            }

            .grand-total {
                font-size: 1.125rem;
            }

            .payment-card {
                padding: 1rem;
            }

            .payment-value {
                font-size: 1.25rem;
            }

            .items-table,
            .payments-table {
                min-width: 600px;
            }

            .items-table th,
            .items-table td,
            .payments-table th,
            .payments-table td {
                padding: 0.75rem;
                font-size: 0.8rem;
            }

            .type-badge {
                font-size: 0.75rem;
                padding: 0.2rem 0.5rem;
            }

            .btn-sm {
                padding: 0.3rem 0.5rem;
                font-size: 0.75rem;
            }

            .emi-section {
                padding: 1.25rem;
            }

            .emi-title {
                font-size: 1rem;
            }

            .emi-item {
                padding: 0.5rem;
            }

            .emi-label {
                font-size: 0.75rem;
            }

            .emi-value {
                font-size: 0.875rem;
            }

            .summary-note {
                padding: 0.75rem;
                font-size: 0.8rem;
            }

            .shipment-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .shipment-details {
                grid-template-columns: 1fr;
            }
        }

        /* Extra Small Devices (up to 360px) */
        @media (max-width: 360px) {
            .invoice-page {
                padding: 8px;
            }

            .invoice-title {
                font-size: 1.25rem;
            }

            .invoice-subtitle {
                font-size: 0.8rem;
            }

            .customer-section,
            .wallet-grid,
            .items-section,
            .payments-section,
            .shipping-section {
                padding: 1rem;
            }

            .wallet-amount {
                font-size: 1.125rem;
            }

            .wallet-sub {
                font-size: 0.75rem;
            }

            .payment-value {
                font-size: 1.125rem;
            }

            .items-table,
            .payments-table {
                min-width: 500px;
            }

            .items-table th,
            .items-table td,
            .payments-table th,
            .payments-table td {
                padding: 0.5rem;
                font-size: 0.7rem;
            }

            .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }

            .emi-item {
                padding: 0.4rem;
            }

            .emi-label {
                font-size: 0.7rem;
            }

            .emi-value {
                font-size: 0.8rem;
            }

            .summary-note {
                padding: 0.5rem;
                font-size: 0.75rem;
            }

            .invoice-footer {
                padding: 1rem;
                font-size: 0.75rem;
            }
        }

        /* Print Styles */
        @media print {

            .header-actions,
            .btn-sm,
            .btn-primary-lg,
            .btn-secondary-lg,
            .btn-purple-lg,
            .action-buttons,
            .toast,
            .loading-overlay {
                display: none !important;
            }

            .invoice-card {
                box-shadow: none;
                border: 1px solid #000;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .wallet-card {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                break-inside: avoid;
            }

            .items-table,
            .payments-table {
                border: 1px solid #000;
            }

            .items-table th {
                background: #f0f0f0 !important;
            }

            .shipping-section {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .discount-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    <div class="invoice-page">
        <div class="container">
            <div class="invoice-card">
                {{-- Loading Overlay --}}
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="spinner"></div>
                    <div class="loading-text">Processing...</div>
                </div>

                {{-- ================= INVOICE HEADER ================= --}}
                <div class="invoice-header">
                    <div class="header-content">
                        <div class="header-left">
                            <h1 class="invoice-title">INVOICE</h1>
                            <div class="invoice-subtitle">#{{ $sale->invoice_no }}</div>
                        </div>
                        <div class="header-right">
                            <div class="status-badge {{ $sale->payment_status }}">
                                {{ strtoupper($sale->payment_status) }}
                            </div>
                            <div class="header-actions">
                                <button class="header-btn" onclick="copyInvoiceNo()" title="Copy Invoice Number">
                                    📋 Copy
                                </button>
                                <a href="{{ route('sales.print', $sale->id) }}" class="header-btn" target="_blank"
                                    title="Print Invoice">
                                    🖨️ Print
                                </a>
                                {{-- ✅ MODIFIED: Direct PDF Download Button --}}
                                <a href="{{ route('sales.invoice', $sale->id) }}" class="header-btn"
                                    title="Download Invoice PDF" download>
                                    📥 Invoice Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= CUSTOMER DETAILS ================= --}}
                <div class="customer-section">
                    <div>
                        <div class="customer-label">Bill To</div>
                        <div class="customer-value">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                        @if ($sale->customer)
                            <div class="customer-detail">
                                <div>📱 {{ $sale->customer->mobile ?? 'N/A' }}</div>
                                <div>✉️ {{ $sale->customer->email ?? 'N/A' }}</div>
                                @if ($sale->customer->address)
                                    <div>📍 {{ $sale->customer->address }}</div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="customer-label">Invoice Details</div>
                        <div class="customer-value">
                            Date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}
                        </div>
                        <div class="customer-detail">
                            <div>📅 Created: {{ $sale->created_at->format('d M Y h:i A') }}</div>
                            <div>🆔 Invoice #{{ $sale->invoice_no }}</div>
                        </div>
                    </div>
                </div>

                {{-- ================= SHIPPING SECTION ================= --}}
                @if ($sale->requires_shipping)
                    <div class="shipping-section">
                        <div class="shipping-header">
                            <div class="shipping-icon">
                                <span>📦</span>
                            </div>
                            <div class="shipping-title">
                                Shipping Information
                                <span class="shipping-badge required">REQUIRES SHIPPING</span>
                            </div>
                        </div>

                        <div class="shipping-grid">
                            <div class="shipping-item">
                                <div class="shipping-label">Shipping Address</div>
                                <div class="shipping-value">{{ $sale->full_shipping_address }}</div>
                            </div>

                            @if ($sale->city || $sale->state || $sale->pincode)
                                <div class="shipping-item">
                                    <div class="shipping-label">Location</div>
                                    <div class="shipping-value">
                                        @if ($sale->city)
                                            {{ $sale->city }}
                                        @endif
                                        @if ($sale->state)
                                            {{ $sale->city ? ', ' : '' }}{{ $sale->state }}
                                        @endif
                                        @if ($sale->pincode)
                                            <br>PIN: {{ $sale->pincode }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Shipments for this sale --}}
                        @if ($sale->shipments && $sale->shipments->count() > 0)
                            <div style="margin-top: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                                    <span style="font-size: 1rem;">🚚</span>
                                    <span style="font-weight: 600; color: #5b21b6;">Associated Shipments</span>
                                </div>

                                @foreach ($sale->shipments as $shipment)
                                    <div class="shipment-card">
                                        <div class="shipment-header">
                                            <div class="shipment-number">
                                                <span>📋 Shipment #{{ $shipment->shipment_number }}</span>
                                                <span
                                                    class="shipment-status {{ $shipment->status }}">{{ ucfirst(str_replace('_', ' ', $shipment->status)) }}</span>
                                            </div>
                                            @if ($shipment->tracking_number)
                                                <div class="shipment-tracking">
                                                    Tracking: {{ $shipment->tracking_number }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="shipment-details">
                                            <div class="shipment-detail-item">
                                                <span class="shipment-detail-label">Courier Partner</span>
                                                <span
                                                    class="shipment-detail-value">{{ $shipment->courier_partner ?? 'Not Assigned' }}</span>
                                            </div>
                                            <div class="shipment-detail-item">
                                                <span class="shipment-detail-label">Shipping Method</span>
                                                <span
                                                    class="shipment-detail-value">{{ ucfirst($shipment->shipping_method ?? 'Standard') }}</span>
                                            </div>
                                            <div class="shipment-detail-item">
                                                <span class="shipment-detail-label">Estimated Delivery</span>
                                                <span class="shipment-detail-value">
                                                    {{ $shipment->estimated_delivery_date ? $shipment->estimated_delivery_date->format('d M Y') : 'Not set' }}
                                                </span>
                                            </div>
                                            <div class="shipment-detail-item">
                                                <span class="shipment-detail-label">Delivered On</span>
                                                <span class="shipment-detail-value">
                                                    {{ $shipment->actual_delivery_date ? $shipment->actual_delivery_date->format('d M Y') : 'Not delivered' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($shipment->tracking_number)
                                            <div style="margin-top: 0.5rem; text-align: right;">
                                                <a href="{{ route('logistics.live-track', $shipment->tracking_number) }}"
                                                    target="_blank" class="shipment-tracking-link">
                                                    <span>🔍</span> Track Shipment
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                style="margin-top: 1rem; padding: 0.75rem; background: rgba(255,255,255,0.5); border-radius: var(--radius-md);">
                                <p style="color: #6d28d9; margin: 0;">
                                    <span>⏳</span> No shipments created yet for this invoice.
                                    <a href="{{ route('logistics.shipments.create', ['sale_id' => $sale->id]) }}"
                                        style="color: #5b21b6; font-weight: 600; text-decoration: underline; margin-left: 0.5rem;">
                                        Create Shipment
                                    </a>
                                </p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ================= WALLET BALANCE ================= --}}
                @if ($sale->customer)
                    @php
                        $customer = $sale->customer;
                        $latestWallet = \App\Models\CustomerWallet::where('customer_id', $customer->id)
                            ->orderBy('created_at', 'desc')
                            ->first();
                        $walletBalance = $latestWallet ? $latestWallet->balance : 0;

                        $allPayments = $sale->payments->where('status', 'paid');
                        $totalReceived = $allPayments->sum('amount');
                        $invoicePayments = $allPayments->whereIn('remarks', ['INVOICE', 'EMI_DOWN'])->sum('amount');
                        $walletUsed = $allPayments->where('remarks', 'ADVANCE_USED')->sum('amount');
                        $advancePayments = $allPayments
                            ->whereIn('remarks', ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD'])
                            ->sum('amount');
                        $appliedToInvoice = $invoicePayments + $walletUsed;
                        $remainingDue = max(0, $sale->grand_total - $appliedToInvoice);
                        $excessAmount = max(0, $totalReceived - $sale->grand_total);
                        $netPosition = $totalReceived - $sale->grand_total;
                    @endphp

                    <div class="wallet-grid">
                        <div class="wallet-card advance">
                            <div class="wallet-label">Wallet Balance</div>
                            <div class="wallet-amount advance">₹{{ number_format($walletBalance, 2) }}</div>
                            <div class="wallet-sub">Available advance</div>
                        </div>

                        <div
                            class="wallet-card {{ $excessAmount > 0 ? 'excess' : ($remainingDue > 0 ? 'due' : 'advance') }}">
                            <div class="wallet-label">Invoice Status</div>
                            <div
                                class="wallet-amount {{ $excessAmount > 0 ? 'excess' : ($remainingDue > 0 ? 'due' : 'advance') }}">
                                @if ($excessAmount > 0)
                                    ₹{{ number_format($excessAmount, 2) }} Excess
                                @elseif($remainingDue > 0)
                                    ₹{{ number_format($remainingDue, 2) }} Due
                                @else
                                    Fully Paid
                                @endif
                            </div>
                            <div class="wallet-sub">
                                @if ($excessAmount > 0)
                                    Will be added to wallet
                                @elseif($remainingDue > 0)
                                    Remaining to pay
                                @else
                                    Invoice settled
                                @endif
                            </div>
                        </div>

                        <div class="wallet-card net">
                            <div class="wallet-label">Total Received</div>
                            <div class="wallet-amount net">₹{{ number_format($totalReceived, 2) }}</div>
                            <div class="wallet-sub">
                                @if ($invoicePayments > 0)
                                    Invoice: ₹{{ number_format($invoicePayments, 2) }}
                                @endif
                                @if ($walletUsed > 0)
                                    | Wallet: ₹{{ number_format($walletUsed, 2) }}
                                @endif
                                @if ($advancePayments > 0)
                                    | Advance: ₹{{ number_format($advancePayments, 2) }}
                                @endif
                            </div>
                        </div>

                        <div class="wallet-card {{ $netPosition > 0 ? 'advance' : ($netPosition < 0 ? 'due' : 'net') }}">
                            <div class="wallet-label">Net Position</div>
                            <div
                                class="wallet-amount {{ $netPosition > 0 ? 'advance' : ($netPosition < 0 ? 'due' : 'net') }}">
                                @if ($netPosition > 0)
                                    +₹{{ number_format($netPosition, 2) }} (Advance)
                                @elseif($netPosition < 0)
                                    -₹{{ number_format(abs($netPosition), 2) }} (Due)
                                @else
                                    Clear
                                @endif
                            </div>
                            <div class="wallet-sub">Received vs Invoice</div>
                        </div>
                    </div>
                @endif

                {{-- ================= ITEMS TABLE WITH PROPER MRP DISPLAY ================= --}}
                <div class="items-section">
                    <h3 class="section-title">🛒 Items Purchased</h3>

                    <div class="table-responsive">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Product</th>
                                    <th class="text-right">MRP (₹)</th>
                                    <th class="text-right">Selling Price (₹)</th>
                                    <th class="text-right">Discount (₹)</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-right">Total (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->items as $index => $item)
                                    @php
                                        // Original MRP from sale_items table
                                        $originalMrp = $item->mrp ?? 0;

                                        // Selling price from sale_items
                                        $sellingPrice = $item->price;

                                        // Check if selling price is greater than MRP
                                        $isPriceIncreased = $sellingPrice > $originalMrp && $originalMrp > 0;

                                        // For price increased items: MRP = Selling Price, Discount = 0
                                        // For discounted items: MRP = Original MRP, Discount = Original MRP - Selling Price
                                        if ($isPriceIncreased) {
                                            $displayMrp = $sellingPrice;
                                            $displayDiscount = 0;
                                        } else {
                                            $displayMrp = $originalMrp;
                                            $displayDiscount = max(0, $originalMrp - $sellingPrice);
                                        }

                                        // If MRP is 0, use selling price
                                        if ($originalMrp == 0) {
                                            $displayMrp = $sellingPrice;
                                            $displayDiscount = 0;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div style="font-weight: 600;">{{ $item->product->name ?? 'Product Deleted' }}
                                            </div>
                                            @if ($item->product && $item->product->product_code)
                                                <div style="font-size: 0.75rem; color: var(--text-muted);">Code:
                                                    {{ $item->product->product_code }}</div>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <span class="fw-bold">₹{{ number_format($displayMrp, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            <span class="selling-price">₹{{ number_format($sellingPrice, 2) }}</span>
                                        </td>
                                        <td class="text-right">
                                            @if ($displayDiscount > 0)
                                                <span class="discount-badge">-
                                                    ₹{{ number_format($displayDiscount, 2) }}</span>
                                                @if ($originalMrp > 0)
                                                    <div style="font-size: 0.7rem; color: var(--success);">
                                                        ({{ round(($displayDiscount / $originalMrp) * 100, 1) }}% off)
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-right fw-bold">₹{{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Summary with all totals --}}
                    <div class="summary-container">
                        <div></div>
                        <div class="summary-box">
                            @php
                                $totalMrp = 0;
                                $totalDiscount = 0;
                                foreach ($sale->items as $item) {
                                    $originalMrp = $item->mrp ?? 0;
                                    $sellingPrice = $item->price;

                                    // For price increased items: use selling price as MRP for total
                                    if ($sellingPrice > $originalMrp && $originalMrp > 0) {
                                        $mrpForTotal = $sellingPrice;
                                        $discountForTotal = 0;
                                    } else {
                                        $mrpForTotal = $originalMrp > 0 ? $originalMrp : $sellingPrice;
                                        $discountForTotal = max(0, $originalMrp - $sellingPrice);
                                    }

                                    $totalMrp += $mrpForTotal * $item->quantity;
                                    $totalDiscount += $discountForTotal * $item->quantity;
                                }
                            @endphp

                            <div class="summary-row total-mrp-row">
                                <span class="summary-label">Total MRP:</span>
                                <span class="summary-value">₹{{ number_format($totalMrp, 2) }}</span>
                            </div>
                            @if ($totalDiscount > 0)
                                <div class="summary-row total-discount-row">
                                    <span class="summary-label">Total Discount:</span>
                                    <span class="summary-value">- ₹{{ number_format($totalDiscount, 2) }}</span>
                                </div>
                            @endif
                            <div class="summary-row">
                                <span class="summary-label">Subtotal (After Discount):</span>
                                <span class="summary-value">₹{{ number_format($sale->sub_total, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">Tax ({{ $sale->tax }}%):</span>
                                <span class="summary-value">+ ₹{{ number_format($sale->tax_amount, 2) }}</span>
                            </div>
                            <div class="grand-total">
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Grand Total:</span>
                                    <span class="amount">₹{{ number_format($sale->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- ================= PAYMENT HISTORY ================= --}}
                <div class="payments-section">
                    <div class="section-header">
                        <h3 class="section-title" style="margin-bottom: 0;">💳 Payment History</h3>
                        @if ($sale->payments->count() > 0)
                            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                <span
                                    style="background: #f1f5f9; padding: 0.5rem 1rem; border-radius: 2rem; font-weight: 600;">
                                    Total: ₹{{ number_format($totalReceived, 2) }}
                                </span>
                                @if ($sale->payments->count() > 1)
                                    <button type="button" class="btn-sm btn-danger"
                                        onclick="bulkDeletePayments({{ $sale->id }}, '{{ $sale->invoice_no }}', {{ $totalReceived }})">
                                        🗑️ Delete All ({{ $sale->payments->count() }})
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Payment Summary Cards --}}
                    <div class="payment-summary-grid">
                        <div class="payment-card total">
                            <div class="payment-label">Grand Total</div>
                            <div class="payment-value total">₹{{ number_format($sale->grand_total, 2) }}</div>
                            <div class="payment-sub">Invoice amount</div>
                        </div>

                        <div class="payment-card paid">
                            <div class="payment-label">Total Received</div>
                            <div class="payment-value paid">₹{{ number_format($totalReceived, 2) }}</div>
                            <div class="payment-sub">{{ $sale->payments->count() }} transaction(s)</div>
                        </div>

                        <div class="payment-card invoice">
                            <div class="payment-label">Applied to Invoice</div>
                            <div class="payment-value invoice">₹{{ number_format($appliedToInvoice, 2) }}</div>
                            <div class="payment-sub">Invoice + Wallet used</div>
                        </div>

                        @if ($advancePayments > 0)
                            <div class="payment-card advance">
                                <div class="payment-label">Advance Payments</div>
                                <div class="payment-value advance">₹{{ number_format($advancePayments, 2) }}</div>
                                <div class="payment-sub">Added to wallet</div>
                            </div>
                        @endif

                        @if ($excessAmount > 0)
                            <div class="payment-card advance">
                                <div class="payment-label">Excess Amount</div>
                                <div class="payment-value advance">₹{{ number_format($excessAmount, 2) }}</div>
                                <div class="payment-sub">Will go to wallet</div>
                            </div>
                        @elseif($remainingDue > 0)
                            <div class="payment-card remaining">
                                <div class="payment-label">Remaining Due</div>
                                <div class="payment-value remaining">₹{{ number_format($remainingDue, 2) }}</div>
                                <div class="payment-sub">Balance to pay</div>
                            </div>
                        @else
                            <div class="payment-card paid">
                                <div class="payment-label">Status</div>
                                <div class="payment-value paid">PAID</div>
                                <div class="payment-sub">Invoice settled</div>
                            </div>
                        @endif
                    </div>

                    @if ($sale->payments->count() > 0)
                        <div class="table-responsive">
                            <table class="payments-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Method</th>
                                        <th class="text-right">Amount</th>
                                        <th>Type</th>
                                        <th>Applied To / Source</th>
                                        <th>Reference</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sale->payments->sortByDesc('created_at') as $payment)
                                        @php
                                            $typeClass = match ($payment->remarks) {
                                                'EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD' => 'advance-only',
                                                'ADVANCE_USED' => 'advance-used',
                                                'INVOICE' => 'invoice',
                                                'EMI_DOWN' => 'emi',
                                                default => 'invoice',
                                            };
                                            $typeLabel = match ($payment->remarks) {
                                                'EXCESS_TO_ADVANCE' => '💰 Excess to Wallet',
                                                'ADVANCE_ONLY' => '💰 Advance Only',
                                                'WALLET_ADD' => '💰 Wallet Add',
                                                'ADVANCE_USED' => '🔄 Wallet Used',
                                                'INVOICE' => '📄 Invoice Payment',
                                                'EMI_DOWN' => '📊 EMI Down',
                                                default => $payment->remarks,
                                            };
                                            $appliedText = match ($payment->remarks) {
                                                'EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD' => 'Added to wallet',
                                                'ADVANCE_USED' => 'Applied to invoice',
                                                'INVOICE', 'EMI_DOWN' => 'Applied to invoice',
                                                default => '—',
                                            };

                                            // FIND SOURCE INVOICE FOR ADVANCE_USED
                                            $sourceInvoice = null;
                                            if ($payment->remarks === 'ADVANCE_USED' && $payment->source_wallet_id) {
                                                $sourcePayment = \App\Models\Payment::where(
                                                    'wallet_id',
                                                    $payment->source_wallet_id,
                                                )
                                                    ->whereIn('remarks', ['EXCESS_TO_ADVANCE', 'WALLET_ADD'])
                                                    ->first();
                                                if ($sourcePayment && $sourcePayment->sale) {
                                                    $sourceInvoice = $sourcePayment->sale->invoice_no;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $payment->created_at->format('d M Y') }}<br><small>{{ $payment->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span style="text-transform: uppercase;">{{ $payment->method }}</span>
                                                @if ($payment->transaction_id)
                                                    <br><small
                                                        style="color: var(--text-muted);">{{ $payment->transaction_id }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right fw-bold text-success">+
                                                ₹{{ number_format($payment->amount, 2) }}</td>
                                            <td><span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                                            </td>
                                            <td>
                                                <small
                                                    style="color: {{ in_array($payment->remarks, ['EXCESS_TO_ADVANCE', 'ADVANCE_ONLY', 'WALLET_ADD']) ? '#7c3aed' : '#2563eb' }};">
                                                    {{ $appliedText }}
                                                </small>
                                                @if ($sourceInvoice)
                                                    <br><small style="color: #6d28d9; font-weight:500;">⬅️ From Invoice
                                                        #{{ $sourceInvoice }}</small>
                                                @elseif ($payment->remarks == 'ADVANCE_USED' && $payment->source_wallet_id)
                                                    <br><small style="color: #6d28d9;">From Wallet
                                                        #{{ $payment->source_wallet_id }}</small>
                                                @endif
                                            </td>
                                            <td><small>{{ $payment->transaction_id ?? '—' }}</small></td>
                                            <td class="text-center">
                                                <button class="btn-sm btn-danger"
                                                    onclick="deletePayment({{ $payment->id }}, {{ $payment->amount }})">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary Note --}}
                        <div class="summary-note">
                            <strong>Payment Summary:</strong><br>
                            • Total amount received: <span
                                class="fw-bold text-success">₹{{ number_format($totalReceived, 2) }}</span><br>
                            • Applied to this invoice: <span
                                class="fw-bold text-primary">₹{{ number_format($appliedToInvoice, 2) }}</span><br>
                            @if ($advancePayments > 0)
                                • Advance payments: <span
                                    class="fw-bold text-purple">₹{{ number_format($advancePayments, 2) }}</span> (added to
                                wallet)<br>
                            @endif
                            @if ($excessAmount > 0)
                                • Excess amount: <span
                                    class="fw-bold text-purple">₹{{ number_format($excessAmount, 2) }}</span> will be
                                added to
                                wallet<br>
                            @endif
                            @if ($remainingDue > 0)
                                • Remaining due: <span
                                    class="fw-bold text-danger">₹{{ number_format($remainingDue, 2) }}</span><br>
                            @else
                                • Invoice status: <span class="fw-bold text-success">FULLY PAID</span><br>
                            @endif
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">💰</div>
                            <div class="empty-title">No payments recorded yet</div>
                            <div class="empty-text">Total amount due: ₹{{ number_format($sale->grand_total, 2) }}</div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="action-buttons">
                        @if ($remainingDue > 0 || $sale->payment_status == 'unpaid')
                            <a href="{{ route('payments.create', $sale->id) }}" class="btn-primary-lg">➕ Add Payment</a>
                        @endif
                        @if ($sale->payment_status != 'paid' && $sale->payment_status != 'emi')
                            <a href="{{ route('sales.edit', $sale->id) }}" class="btn-secondary-lg">✏️ Edit Invoice</a>
                        @endif
                        @if ($sale->requires_shipping && $sale->shipments->count() == 0)
                            <a href="{{ route('logistics.shipments.create', ['sale_id' => $sale->id]) }}"
                                class="btn-purple-lg">
                                📦 Create Shipment
                            </a>
                        @endif
                        <a href="{{ route('customers.payments', $sale->customer_id) }}" class="btn-secondary-lg">👤
                            Customer
                            History</a>
                    </div>

                    {{-- EMI Details --}}
                    @if ($sale->payment_status === 'emi' && $sale->emiPlan)
                        <div class="emi-section">
                            <h4 class="emi-title">📆 EMI Details</h4>
                            <div class="emi-grid">
                                <div class="emi-item">
                                    <div class="emi-label">Total Amount</div>
                                    <div class="emi-value">₹{{ number_format($sale->emiPlan->total_amount, 2) }}</div>
                                </div>
                                <div class="emi-item">
                                    <div class="emi-label">Down Payment</div>
                                    <div class="emi-value">₹{{ number_format($sale->emiPlan->down_payment, 2) }}</div>
                                </div>
                                <div class="emi-item">
                                    <div class="emi-label">Monthly EMI</div>
                                    <div class="emi-value">₹{{ number_format($sale->emiPlan->emi_amount, 2) }}</div>
                                </div>
                                <div class="emi-item">
                                    <div class="emi-label">Status</div>
                                    <div class="emi-value">
                                        <span class="emi-status {{ $sale->emiPlan->status }}">
                                            {{ ucfirst($sale->emiPlan->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="emi-item">
                                    <div class="emi-label">Total Months</div>
                                    <div class="emi-value">{{ $sale->emiPlan->months }} Months</div>
                                </div>
                                <div class="emi-item">
                                    <div class="emi-label">Total Payable</div>
                                    <div class="emi-value">
                                        ₹{{ number_format($sale->emiPlan->emi_amount * $sale->emiPlan->months, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ================= FOOTER ================= --}}
                <div class="invoice-footer">
                    This is a computer-generated invoice • Thank you for your business!
                </div>
            </div>
        </div>
    </div>

    {{-- ================= TOAST NOTIFICATION ================= --}}
    <div id="toast" class="toast"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            toast.innerHTML = msg;
            toast.className = 'toast ' + type;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 3000);
        }

        function copyInvoiceNo() {
            navigator.clipboard.writeText('{{ $sale->invoice_no }}')
                .then(() => showToast('✅ Invoice number copied!', 'success'))
                .catch(() => showToast('❌ Failed to copy', 'error'));
        }

        function deletePayment(id, amount) {
            if (!confirm(`Delete payment of ₹${amount}?`)) return;
            showLoading();
            fetch(`/payments/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(r => r.json())
                .then(d => {
                    hideLoading();
                    if (d.success) {
                        showToast('✅ Deleted!');
                        setTimeout(() => location.reload(), 1500);
                    } else showToast('❌ ' + d.message, 'error');
                })
                .catch(() => {
                    hideLoading();
                    showToast('❌ Error', 'error');
                });
        }

        function bulkDeletePayments(id, no, amount) {
            if (!confirm(`Delete all payments for #${no} (₹${amount})?`)) return;
            showLoading();
            fetch(`/payments/bulk/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(r => r.json())
                .then(d => {
                    hideLoading();
                    if (d.success) {
                        showToast('✅ Deleted!');
                        setTimeout(() => location.reload(), 2000);
                    } else showToast('❌ ' + d.message, 'error');
                })
                .catch(() => {
                    hideLoading();
                    showToast('❌ Error', 'error');
                });
        }

        document.addEventListener('keydown', e => {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
@endsection
