{{-- resources/views/sales/create.blade.php --}}
@extends('layouts.app')

@section('page-title', 'Create New Invoice')

@section('content')
    @php
        use Illuminate\Support\Str;
    @endphp

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="google-maps-api-key" content="{{ config('services.google.maps_api_key') }}">

    <style>
        /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --success-dark: #059669;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --warning: #f59e0b;
            --info: #0ea5e9;
            --purple: #8b5cf6;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e5e7eb;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 60px rgba(0, 0, 0, 0.08);
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --radius-2xl: 20px;
            --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            font-family: var(--font-sans);
            color: var(--text-main);
            line-height: 1.5;
        }

        .invoice-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
            padding: clamp(16px, 3vw, 30px);
            width: 100%;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .invoice-card {
            background: var(--bg-white);
            border-radius: var(--radius-2xl);
            padding: clamp(24px, 5vw, 40px);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--border);
            width: 100%;
        }

        .invoice-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
            min-width: 280px;
        }

        .header-icon {
            width: clamp(50px, 8vw, 60px);
            height: clamp(50px, 8vw, 60px);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.25);
            flex-shrink: 0;
        }

        .header-icon span {
            font-size: clamp(24px, 4vw, 28px);
            color: white;
        }

        .header-title {
            font-size: clamp(24px, 5vw, 32px);
            font-weight: 800;
            margin: 0;
            color: var(--text-main);
            letter-spacing: -0.5px;
            word-break: break-word;
        }

        .header-subtitle {
            color: var(--text-muted);
            margin: 5px 0 0;
            font-size: clamp(13px, 2.5vw, 15px);
            word-break: break-word;
        }

        .btn-clear-customer {
            background: #fef2f2;
            color: var(--danger);
            border: 1.5px solid #fecaca;
            padding: 10px 20px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
            white-space: nowrap;
        }

        .btn-clear-customer:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 38, 38, 0.15);
        }

        .section-card {
            background: white;
            padding: clamp(20px, 4vw, 25px);
            border-radius: var(--radius-lg);
            margin-bottom: 30px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            width: 100%;
        }

        .section-title {
            font-size: clamp(16px, 3vw, 18px);
            font-weight: 700;
            color: #374151;
            margin: 0 0 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            word-break: break-word;
        }

        .step-badge {
            display: inline-flex;
            width: 24px;
            height: 24px;
            color: white;
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .step-1 {
            background: var(--primary);
        }

        .step-2 {
            background: var(--warning);
        }

        .step-3 {
            background: var(--success);
        }

        .step-shipping {
            background: var(--purple);
        }

        .form-group {
            position: relative;
        }

        .form-label {
            display: block;
            color: #374151;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: clamp(13px, 2.5vw, 14px);
            word-break: break-word;
        }

        .required-star {
            color: var(--danger);
            font-weight: bold;
        }

        .status-text {
            font-size: 12px;
            margin-left: 8px;
            font-weight: normal;
            word-break: break-word;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            border: 1.5px solid #d1d5db;
            background: white;
            font-size: clamp(14px, 2.5vw, 15px);
            color: #374151;
            transition: all 0.2s;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-input-disabled {
            background: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        .form-textarea {
            width: 100%;
            padding: 14px 16px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            background: white;
            font-size: clamp(14px, 2.5vw, 15px);
            color: #374151;
            outline: none;
            resize: vertical;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-textarea:focus {
            border-color: var(--success);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .customer-search-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-wrapper {
            position: relative;
            flex: 1;
            min-width: 250px;
        }

        .search-results {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            width: 100%;
            background: white;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-height: 350px;
            overflow-y: auto;
            z-index: 1000;
        }

        .search-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 18px;
            pointer-events: none;
        }

        .hint-text {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 6px;
            word-break: break-word;
        }

        .btn-add-customer {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
            border: none;
            padding: 0 24px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s;
        }

        .btn-add-customer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        .btn-share-location {
            background: linear-gradient(135deg, var(--info) 0%, #0284c7 100%);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 14px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
            transition: all 0.2s;
        }

        .btn-share-location:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(14, 165, 233, 0.3);
        }

        .btn-share-location:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border: none;
            padding: 18px 40px;
            border-radius: var(--radius-lg);
            font-weight: 700;
            font-size: clamp(14px, 2.5vw, 16px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .btn-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .selected-customer-card {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1.5px solid var(--info);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        .selected-customer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }

        .customer-avatar {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .customer-avatar span {
            font-size: 22px;
            color: white;
        }

        .customer-details {
            flex: 1;
            min-width: 200px;
        }

        .customer-label {
            font-size: 12px;
            color: #0c4a6e;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .customer-name {
            font-weight: 700;
            color: var(--text-main);
            font-size: 16px;
            word-break: break-word;
        }

        .customer-contact {
            display: flex;
            gap: 10px;
            font-size: 13px;
            color: #475569;
            flex-wrap: wrap;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Shipping Card Styles */
        .shipping-card {
            background: linear-gradient(135deg, #f3e8ff 0%, #ede9fe 100%);
            border: 1.5px solid var(--purple);
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-bottom: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        .shipping-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .shipping-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .shipping-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
        }

        .shipping-title {
            font-weight: 700;
            color: #5b21b6;
            font-size: 16px;
        }

        .shipping-hint {
            font-size: 12px;
            color: #6d28d9;
            margin-bottom: 15px;
        }

        /* Address Autocomplete Styles */
        .address-input-wrapper {
            position: relative;
            width: 100%;
        }

        .address-suggestions {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            right: 0;
            background: white;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .address-suggestion-item {
            padding: 12px 16px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }

        .address-suggestion-item:hover {
            background: #f8fafc;
        }

        .suggestion-main {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }

        .suggestion-secondary {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .location-loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--info);
        }

        .location-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid #e5e7eb;
            border-top-color: var(--info);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .location-success {
            color: var(--success);
        }

        .location-error {
            color: var(--danger);
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }

        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        .invoice-table th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 700;
            font-size: clamp(12px, 2vw, 14px);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #374151;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-bottom: 2px solid #dbeafe;
            white-space: nowrap;
        }

        .invoice-table td {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .empty-state td {
            background: #f8fafc;
            text-align: center;
            color: var(--text-muted);
            font-style: italic;
        }

        .empty-state-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            color: #94a3b8;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 48px;
        }

        .product-input {
            width: 100%;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border);
            background: white;
            font-size: clamp(13px, 2.2vw, 15px);
            color: #374151;
            text-align: right;
            outline: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .product-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .product-input.qty {
            text-align: center;
        }

        .product-input.readonly {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
        }

        .product-input.mrp-readonly {
            background: #f3f4f6;
            color: #6b7280;
            font-weight: 600;
            cursor: not-allowed;
        }

        .product-input.discount-badge {
            background: #ecfdf5;
            border-color: #10b981;
            color: #059669;
            font-weight: 700;
        }

        .btn-remove {
            background: #fef2f2;
            color: var(--danger);
            border: 1.5px solid #fecaca;
            padding: 8px 16px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: clamp(12px, 2.2vw, 13px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-remove:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .totals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .total-item {
            position: relative;
        }

        .total-label {
            display: block;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: clamp(12px, 2.5vw, 14px);
            word-break: break-word;
        }

        .grand-total-label {
            color: var(--text-main);
            font-weight: 700;
        }

        .input-prefix {
            position: relative;
        }

        .prefix {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #374151;
            font-weight: 600;
            font-size: clamp(14px, 2.5vw, 18px);
            pointer-events: none;
        }

        .grand-prefix {
            color: var(--text-main);
            font-weight: 700;
        }

        .total-input {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            background: #f8fafc;
            font-size: clamp(16px, 3vw, 18px);
            font-weight: 700;
            color: #374151;
            text-align: right;
            outline: none;
        }

        .total-input-editable {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: var(--radius-lg);
            border: 1.5px solid var(--border);
            background: white;
            font-size: clamp(14px, 2.5vw, 16px);
            color: #374151;
            text-align: right;
            outline: none;
            transition: all 0.2s;
        }

        .total-input-editable:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .grand-total-input {
            width: 100%;
            padding: 14px 16px 14px 42px;
            border-radius: var(--radius-lg);
            border: 2px solid #1e293b;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            font-size: clamp(18px, 3.5vw, 20px);
            font-weight: 800;
            color: #1e293b;
            text-align: right;
            outline: none;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
        }

        .form-actions {
            text-align: right;
            margin-top: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 500px;
            padding: clamp(20px, 4vw, 30px);
            border-radius: var(--radius-2xl);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalSlideIn 0.3s ease-out;
            border: 1px solid var(--border);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .modal-icon span {
            font-size: 24px;
            color: white;
        }

        .modal-title {
            font-size: clamp(20px, 4vw, 22px);
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            letter-spacing: -0.5px;
            word-break: break-word;
        }

        .modal-subtitle {
            color: var(--text-muted);
            margin: 4px 0 0;
            font-size: clamp(12px, 2.5vw, 14px);
            word-break: break-word;
        }

        .modal-body {
            display: grid;
            gap: 16px;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border);
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
            border: 1.5px solid var(--border);
            padding: 12px 24px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 15px);
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-save-customer {
            background: linear-gradient(135deg, var(--success) 0%, var(--success-dark) 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: var(--radius-md);
            font-weight: 600;
            font-size: clamp(13px, 2.5vw, 15px);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
            transition: all 0.2s;
        }

        .btn-save-customer:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        }

        .btn-save-customer:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .product-image {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            object-fit: cover;
            border: 2px solid var(--border);
            background: #f8fafc;
        }

        .product-image-sm {
            width: 30px;
            height: 30px;
            border-radius: var(--radius-sm);
            object-fit: cover;
            border: 1px solid var(--border);
            background: #f8fafc;
        }

        .product-image-placeholder {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .product-image-placeholder.sm {
            width: 30px;
            height: 30px;
            font-size: 12px;
        }

        .product-mrp-price {
            background: var(--primary);
            color: white;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
            margin-right: 8px;
        }

        .product-selling-price {
            background: var(--success);
            color: white;
            padding: 6px 12px;
            border-radius: var(--radius-md);
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
        }

        .toast-notification {
            position: fixed;
            top: 30px;
            right: 30px;
            padding: 16px 24px;
            border-radius: var(--radius-lg);
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideInRight 0.3s ease-out, fadeOut 0.3s ease-out 2.7s forwards;
        }

        .toast-success {
            background: var(--success);
        }

        .toast-error {
            background: var(--danger);
        }

        .toast-info {
            background: var(--primary);
        }

        .toast-warning {
            background: var(--warning);
        }

        .toast-icon {
            font-size: 20px;
        }

        .toast-message {
            flex: 1;
            color: white;
            word-break: break-word;
        }

        .barcode-scanner-input {
            position: absolute;
            opacity: 0;
            height: 0;
            width: 0;
            pointer-events: none;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(10px);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        @keyframes buttonSpin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        @media (max-width: 768px) {
            .invoice-card {
                padding: 20px;
            }

            .header-left {
                flex-direction: column;
                text-align: center;
            }

            .header-icon {
                margin: 0 auto;
            }

            .selected-customer-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .customer-contact {
                flex-direction: column;
                width: 100%;
            }

            .totals-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                text-align: center;
            }

            .btn-submit {
                width: 100%;
                justify-content: center;
            }

            .shipping-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-share-location {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .invoice-card {
                padding: 16px;
            }

            .header-title {
                font-size: 24px;
            }

            .section-title {
                font-size: 16px;
            }

            .invoice-table {
                min-width: 600px;
            }
        }
    </style>

    <div class="invoice-page">
        <div class="container">
            <div class="invoice-card">
                <div class="invoice-header">
                    <div class="header-left">
                        <div class="header-icon">
                            <span>🧾</span>
                        </div>
                        <div>
                            <h1 class="header-title">Create New Invoice</h1>
                            <p class="header-subtitle">Step 1: Select customer → Step 2: Add products → Step 3: Shipping</p>
                        </div>
                    </div>
                    <div id="clearCustomerContainer" style="display: none;">
                        <button type="button" onclick="InvoiceManager.clearCustomerSelection()" class="btn-clear-customer">
                            <span>✕</span> Clear Customer
                        </button>
                    </div>
                </div>

                <form method="POST" action="{{ route('sales.store') }}" id="invoiceForm"
                    onsubmit="return InvoiceManager.handleSubmit(event)">
                    @csrf
                    <input type="text" id="barcodeInput" autocomplete="off" class="barcode-scanner-input">
                    <input type="hidden" name="invoice_token" value="{{ Str::uuid() }}">

                    {{-- CUSTOMER SECTION --}}
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-1">1</span>
                            Step 1: Select Customer (Required)
                        </h3>

                        <div id="selectedCustomerInfo" class="selected-customer-card" style="display: none;">
                            <div class="selected-customer-content">
                                <div class="customer-avatar"><span>👤</span></div>
                                <div class="customer-details">
                                    <div class="customer-label">CUSTOMER SELECTED</div>
                                    <div id="selectedCustomerName" class="customer-name"></div>
                                </div>
                                <div class="customer-contact">
                                    <div class="contact-item"><span>📱</span><span id="selectedCustomerMobileText"></span>
                                    </div>
                                    <div class="contact-item"><span>✉️</span><span id="selectedCustomerEmailText"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid-2">
                            <div class="form-group">
                                <label class="form-label">Select Customer <span class="required-star">*</span><span
                                        id="customerStatus" class="status-text"></span></label>
                                <div class="customer-search-group">
                                    <div class="search-wrapper">
                                        <input type="text" id="customerSearch"
                                            placeholder="Type customer name or mobile to search..." autocomplete="off"
                                            class="form-input">
                                        <input type="hidden" name="customer_id" id="customer_id">
                                        <div id="customerResults" class="search-results"></div>
                                    </div>
                                    <button type="button" onclick="InvoiceManager.openCustomerModal()"
                                        class="btn-add-customer"><span>+</span> Add New</button>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Step 2: Search Products <span id="productStatus"
                                        class="status-text"></span></label>
                                <div class="search-wrapper">
                                    <input type="text" id="productSearch" disabled
                                        placeholder="First select a customer above..."
                                        class="form-input form-input-disabled">
                                    <span class="search-icon">🔍</span>
                                    <div id="productResults" class="search-results"></div>
                                </div>
                                <div class="hint-text"><span id="productSearchHint">Select a customer first to enable
                                        product search</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- SHIPPING SECTION WITH WORKING ADDRESS AUTOCOMPLETE --}}
                    @if(\Illuminate\Support\Facades\Cache::get('logistics_system_enabled', true))
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-shipping">📦</span>
                            Step 3: Shipping Information (Optional)
                        </h3>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label class="form-label">
                                <input type="checkbox" name="requires_shipping" value="1" id="requiresShipping"
                                    class="form-checkbox">
                                <span style="margin-left: 8px; font-weight: 600;">This order requires shipping</span>
                            </label>
                            <p class="hint-text" style="margin-left: 28px;">Check this if products need to be shipped to
                                customer</p>
                        </div>

                        <div id="shippingFields" style="display: none;">
                            <div class="shipping-card">
                                <div class="shipping-header">
                                    <div class="shipping-header-left">
                                        <div class="shipping-icon"><span>📍</span></div>
                                        <div class="shipping-title">Delivery Address</div>
                                    </div>
                                    <button type="button" id="shareLiveLocationBtn" class="btn-share-location"
                                        onclick="InvoiceManager.getLiveLocation()">
                                        <span>📍</span> Share Live Location
                                    </button>
                                </div>
                                <p class="shipping-hint">Enter shipping details (leave blank to use customer address)</p>

                                {{-- Address with Autocomplete --}}
                                <div class="form-group" style="margin-bottom: 15px;">
                                    <label class="form-label">Shipping Address <span class="required-star"
                                            id="addressRequired" style="display: none;">*</span></label>
                                    <div class="address-input-wrapper">
                                        <input type="text" id="shipping_address_autocomplete" class="form-input"
                                            placeholder="Start typing address or click Share Live Location..."
                                            autocomplete="off">
                                        <div id="addressSuggestions" class="address-suggestions"></div>
                                    </div>
                                    <textarea name="shipping_address" id="shipping_address" class="form-textarea"
                                        placeholder="Complete shipping address will appear here" rows="2" style="margin-top: 10px;"></textarea>
                                </div>

                                <div class="grid-3">
                                    <div class="form-group">
                                        <label class="form-label">City <span class="required-star" id="cityRequired"
                                                style="display: none;">*</span></label>
                                        <input type="text" name="city" id="city" class="form-input"
                                            placeholder="Auto-fetches from address">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">State <span class="required-star" id="stateRequired"
                                                style="display: none;">*</span></label>
                                        <input type="text" name="state" id="state" class="form-input"
                                            placeholder="Auto-fetches from address">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Pincode <span class="required-star"
                                                id="pincodeRequired" style="display: none;">*</span></label>
                                        <input type="text" name="pincode" id="pincode" class="form-input"
                                            placeholder="Auto-fetches from address">
                                    </div>
                                </div>

                                {{-- ✅ HIDDEN FIELDS FOR COORDINATES --}}
                                <input type="hidden" name="destination_latitude" id="destination_latitude">
                                <input type="hidden" name="destination_longitude" id="destination_longitude">
                                <input type="hidden" name="place_id" id="place_id">

                                <div class="grid-2" style="margin-top: 10px;">
                                    <div class="form-group">
                                        <label class="form-label">Receiver Name (if different)</label>
                                        <input type="text" name="receiver_name" id="receiver_name" class="form-input"
                                            placeholder="Leave blank to use customer name">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Receiver Phone (if different)</label>
                                        <input type="text" name="receiver_phone" id="receiver_phone"
                                            class="form-input" placeholder="Leave blank to use customer phone">
                                    </div>
                                </div>

                                <div class="form-group" style="margin-top: 10px;">
                                    <label class="form-label">Delivery Instructions (Optional)</label>
                                    <textarea name="delivery_instructions" id="delivery_instructions" class="form-textarea"
                                        placeholder="Any special instructions for delivery" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ITEMS TABLE --}}
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-2">2</span>
                            Invoice Items
                            <span id="itemsStatus" class="status-text"></span>
                        </h3>

                        <div class="table-responsive">
                            <table class="invoice-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>MRP (₹)</th>
                                        <th>Selling Price (₹)</th>
                                        <th>Discount (₹)</th>
                                        <th>Quantity</th>
                                        <th>Total (₹)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="itemsTable">
                                    <tr id="emptyState" class="empty-state">
                                        <td colspan="7">
                                            <div class="empty-state-content">
                                                <span class="empty-icon">📦</span>
                                                <p>Select a customer first, then search and add products</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TOTALS --}}
                    <div class="section-card">
                        <h3 class="section-title">
                            <span class="step-badge step-3">3</span>
                            Invoice Summary
                        </h3>

                        <div class="totals-grid">
                            <div class="total-item">
                                <label class="total-label">Sub Total (MRP)</label>
                                <div class="input-prefix">
                                    <span class="prefix">₹</span>
                                    <input id="sub_total_mrp" name="sub_total_mrp" readonly value="0.00"
                                        class="total-input">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label">Total Discount</label>
                                <div class="input-prefix">
                                    <span class="prefix">₹</span>
                                    <input id="total_discount" name="total_discount" readonly value="0.00"
                                        class="total-input">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label">Sub Total (Selling)</label>
                                <div class="input-prefix">
                                    <span class="prefix">₹</span>
                                    <input id="sub_total" name="sub_total" readonly value="0.00" class="total-input">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label">Tax (%)</label>
                                <div class="input-prefix">
                                    <span class="prefix">%</span>
                                    <input id="tax" name="tax" value="0"
                                        oninput="InvoiceManager.calculate()" class="total-input-editable">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label">Tax Amount</label>
                                <div class="input-prefix">
                                    <span class="prefix">₹</span>
                                    <input id="tax_amount" name="tax_amount" readonly value="0.00"
                                        class="total-input">
                                </div>
                            </div>
                            <div class="total-item">
                                <label class="total-label grand-total-label">Grand Total</label>
                                <div class="input-prefix">
                                    <span class="prefix grand-prefix">₹</span>
                                    <input id="grand_total" name="grand_total" readonly value="0.00"
                                        class="grand-total-input">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" id="saveBtn" class="btn-submit">
                            <span class="btn-icon">💾</span>
                            Save & Generate Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- CUSTOMER MODAL --}}
    <div id="customerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon"><span>👤</span></div>
                <div>
                    <h3 class="modal-title">Add New Customer</h3>
                    <p class="modal-subtitle">Fill customer details below</p>
                </div>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Full Name <span class="required-star">*</span></label>
                    <input id="c_name" placeholder="Enter customer name" class="form-input">
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Mobile <span class="required-star">*</span></label>
                        <input id="c_mobile" placeholder="Enter mobile number" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input id="c_email" placeholder="Enter email address" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea id="c_address" placeholder="Enter customer address" rows="3" class="form-textarea"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="InvoiceManager.closeCustomerModal()" class="btn-cancel">Cancel</button>
                <button onclick="InvoiceManager.saveCustomer()" id="saveCustomerBtn"
                    class="btn-save-customer"><span>✓</span> Save Customer</button>
            </div>
        </div>
    </div>

    {{-- Google Maps API Script --}}
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initAddressAutocomplete"
        async defer></script>

    <script>
        const InvoiceManager = (function() {
            let state = {
                products: @json($products),
                isCustomerSelected: false,
                isSavingCustomer: false,
                isScannerEnabled: false,
                barcodeBuffer: '',
                barcodeTimeout: null,
                customerTimer: null,
                autocompleteService: null,
                placesService: null,
                isGettingLocation: false
            };

            const elements = {
                customerSearch: document.getElementById('customerSearch'),
                customerResults: document.getElementById('customerResults'),
                customerIdInput: document.getElementById('customer_id'),
                customerStatus: document.getElementById('customerStatus'),
                selectedCustomerInfo: document.getElementById('selectedCustomerInfo'),
                selectedCustomerName: document.getElementById('selectedCustomerName'),
                selectedCustomerMobileText: document.getElementById('selectedCustomerMobileText'),
                selectedCustomerEmailText: document.getElementById('selectedCustomerEmailText'),
                clearCustomerContainer: document.getElementById('clearCustomerContainer'),
                productSearch: document.getElementById('productSearch'),
                productResults: document.getElementById('productResults'),
                productStatus: document.getElementById('productStatus'),
                productSearchHint: document.getElementById('productSearchHint'),
                itemsTable: document.getElementById('itemsTable'),
                emptyState: document.getElementById('emptyState'),
                itemsStatus: document.getElementById('itemsStatus'),
                barcodeInput: document.getElementById('barcodeInput'),
                customerModal: document.getElementById('customerModal'),
                saveCustomerBtn: document.getElementById('saveCustomerBtn'),
                requiresShipping: document.getElementById('requiresShipping'),
                shippingFields: document.getElementById('shippingFields'),
                shippingAddressAutocomplete: document.getElementById('shipping_address_autocomplete'),
                shippingAddress: document.getElementById('shipping_address'),
                city: document.getElementById('city'),
                state: document.getElementById('state'),
                pincode: document.getElementById('pincode'),
                addressSuggestions: document.getElementById('addressSuggestions'),
                destinationLatitude: document.getElementById('destination_latitude'),
                destinationLongitude: document.getElementById('destination_longitude'),
                placeId: document.getElementById('place_id')
            };

            // ========== GOOGLE MAPS AUTOCOMPLETE ==========
            function initAddressAutocomplete() {
                if (!window.google || !window.google.maps || !window.google.maps.places) {
                    console.log('Google Maps API not loaded yet');
                    return;
                }

                state.autocompleteService = new google.maps.places.AutocompleteService();
                state.placesService = new google.maps.places.PlacesService(document.createElement('div'));

                if (elements.shippingAddressAutocomplete) {
                    elements.shippingAddressAutocomplete.addEventListener('input', handleAddressInput);
                    elements.shippingAddressAutocomplete.addEventListener('focus', () => {
                        if (elements.shippingAddressAutocomplete.value.length > 2) handleAddressInput();
                    });
                }
            }

            let addressDebounceTimer = null;

            function handleAddressInput() {
                const input = elements.shippingAddressAutocomplete.value;
                if (input.length < 3) {
                    elements.addressSuggestions.style.display = 'none';
                    return;
                }

                clearTimeout(addressDebounceTimer);
                addressDebounceTimer = setTimeout(() => {
                    state.autocompleteService.getPlacePredictions({
                        input: input,
                        types: ['address'],
                        componentRestrictions: {
                            country: 'in'
                        }
                    }, (predictions, status) => {
                        if (status === 'OK' && predictions) {
                            displaySuggestions(predictions);
                        } else {
                            elements.addressSuggestions.style.display = 'none';
                        }
                    });
                }, 300);
            }

            function displaySuggestions(predictions) {
                elements.addressSuggestions.innerHTML = '';
                predictions.forEach(prediction => {
                    const item = document.createElement('div');
                    item.className = 'address-suggestion-item';
                    item.innerHTML = `
                        <div class="suggestion-main">${escapeHTML(prediction.structured_formatting.main_text)}</div>
                        <div class="suggestion-secondary">${escapeHTML(prediction.structured_formatting.secondary_text || '')}</div>
                    `;
                    item.onclick = () => selectPlace(prediction.place_id);
                    elements.addressSuggestions.appendChild(item);
                });
                elements.addressSuggestions.style.display = 'block';
            }

            function selectPlace(placeId) {
                elements.addressSuggestions.style.display = 'none';
                elements.shippingAddressAutocomplete.value = 'Loading address...';

                state.placesService.getDetails({
                    placeId: placeId,
                    fields: ['formatted_address', 'address_components', 'geometry']
                }, (place, status) => {
                    if (status === 'OK' && place) {
                        elements.shippingAddressAutocomplete.value = place.formatted_address;
                        if (elements.shippingAddress) {
                            elements.shippingAddress.value = place.formatted_address;
                        }

                        // ✅ SAVE COORDINATES
                        if (place.geometry && place.geometry.location) {
                            if (elements.destinationLatitude) {
                                elements.destinationLatitude.value = place.geometry.location.lat();
                            }
                            if (elements.destinationLongitude) {
                                elements.destinationLongitude.value = place.geometry.location.lng();
                            }
                        }

                        // ✅ SAVE PLACE ID
                        if (elements.placeId) {
                            elements.placeId.value = placeId;
                        }

                        extractAddressComponents(place.address_components);
                        showToast('Address fetched successfully!', 'success');
                    } else {
                        elements.shippingAddressAutocomplete.value = '';
                        showToast('Could not load address details', 'error');
                    }
                });
            }

            function extractAddressComponents(components) {
                let city = '',
                    stateName = '',
                    pincode = '';
                components.forEach(component => {
                    const types = component.types;
                    if (types.includes('locality')) {
                        city = component.long_name;
                    } else if (types.includes('sublocality') && !city) {
                        city = component.long_name;
                    } else if (types.includes('administrative_area_level_1')) {
                        stateName = component.long_name;
                    } else if (types.includes('postal_code')) {
                        pincode = component.long_name;
                    }
                });

                if (elements.city) elements.city.value = city;
                if (elements.state) elements.state.value = stateName;
                if (elements.pincode) elements.pincode.value = pincode;

                if (city || stateName || pincode) {
                    showToast('City, State, and Pincode auto-filled', 'success');
                }
            }

            // ========== LIVE LOCATION ==========
            async function getLiveLocation() {
                if (state.isGettingLocation) return;

                if (!navigator.geolocation) {
                    showToast('Geolocation is not supported by your browser', 'error');
                    return;
                }

                state.isGettingLocation = true;
                const btn = document.getElementById('shareLiveLocationBtn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="location-spinner"></span> Getting location...';
                btn.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    async (position) => {
                            const {
                                latitude,
                                longitude
                            } = position.coords;
                            await reverseGeocode(latitude, longitude);
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            state.isGettingLocation = false;
                        },
                        (error) => {
                            let errorMessage = 'Unable to get location. ';
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    errorMessage += 'Please allow location access.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    errorMessage += 'Location information unavailable.';
                                    break;
                                case error.TIMEOUT:
                                    errorMessage += 'Location request timed out.';
                                    break;
                                default:
                                    errorMessage += 'Please try again.';
                            }
                            showToast(errorMessage, 'error');
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            state.isGettingLocation = false;
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                );
            }

            async function reverseGeocode(lat, lng) {
                showToast('Fetching address from location...', 'info');

                const geocoder = new google.maps.Geocoder();
                geocoder.geocode({
                    location: {
                        lat,
                        lng
                    }
                }, (results, status) => {
                    if (status === 'OK' && results[0]) {
                        const address = results[0].formatted_address;
                        if (elements.shippingAddressAutocomplete) {
                            elements.shippingAddressAutocomplete.value = address;
                        }
                        if (elements.shippingAddress) {
                            elements.shippingAddress.value = address;
                        }

                        // ✅ SAVE COORDINATES
                        if (elements.destinationLatitude) {
                            elements.destinationLatitude.value = lat;
                        }
                        if (elements.destinationLongitude) {
                            elements.destinationLongitude.value = lng;
                        }

                        let city = '',
                            stateName = '',
                            pincode = '';
                        results[0].address_components.forEach(component => {
                            const types = component.types;
                            if (types.includes('locality')) {
                                city = component.long_name;
                            } else if (types.includes('sublocality') && !city) {
                                city = component.long_name;
                            } else if (types.includes('administrative_area_level_1')) {
                                stateName = component.long_name;
                            } else if (types.includes('postal_code')) {
                                pincode = component.long_name;
                            }
                        });

                        if (elements.city) elements.city.value = city;
                        if (elements.state) elements.state.value = stateName;
                        if (elements.pincode) elements.pincode.value = pincode;

                        showToast('Location fetched and address auto-filled!', 'success');
                    } else {
                        showToast('Could not get address from location', 'error');
                    }
                });
            }

            // ========== SHIPPING TOGGLE ==========
            if (elements.requiresShipping && elements.shippingFields) {
                elements.requiresShipping.addEventListener('change', function() {
                    elements.shippingFields.style.display = this.checked ? 'block' : 'none';
                    const requiredStars = ['addressRequired', 'cityRequired', 'stateRequired',
                        'pincodeRequired'
                    ];
                    requiredStars.forEach(id => {
                        const star = document.getElementById(id);
                        if (star) star.style.display = this.checked ? 'inline' : 'none';
                    });
                    if (this.checked && state.isCustomerSelected) {
                        autoFillShippingFromCustomer();
                    }
                });
            }

            function autoFillShippingFromCustomer() {
                const customerId = elements.customerIdInput?.value;
                if (customerId) {
                    fetch(`/customers/${customerId}/details`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.customer) {
                                const receiverName = document.getElementById('receiver_name');
                                const receiverPhone = document.getElementById('receiver_phone');
                                if (receiverName && !receiverName.value && data.customer.name) receiverName
                                    .value = data.customer.name;
                                if (receiverPhone && !receiverPhone.value && data.customer.mobile) receiverPhone
                                    .value = data.customer.mobile;
                                if (data.customer.address && elements.shippingAddress && !elements
                                    .shippingAddress.value) {
                                    elements.shippingAddress.value = data.customer.address;
                                    if (elements.shippingAddressAutocomplete) elements
                                        .shippingAddressAutocomplete.value = data.customer.address;
                                }
                                if (data.customer.city && elements.city && !elements.city.value) elements.city
                                    .value = data.customer.city;
                                if (data.customer.state && elements.state && !elements.state.value) elements
                                    .state.value = data.customer.state;
                                if (data.customer.pincode && elements.pincode && !elements.pincode.value)
                                    elements.pincode.value = data.customer.pincode;
                            }
                        })
                        .catch(err => console.log('Could not fetch customer details'));
                }
            }

            // ========== CUSTOMER FUNCTIONS ==========
            function loadCustomerFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);
                const customerId = urlParams.get('customer_id');
                const customerName = urlParams.get('customer_name');
                if (!customerId || !customerName || state.isCustomerSelected) return;

                if (elements.customerSearch) {
                    elements.customerSearch.value = 'Loading customer...';
                    elements.customerSearch.disabled = true;
                }

                const customer = {
                    id: customerId,
                    name: decodeURIComponent(customerName),
                    mobile: 'Fetching...',
                    email: 'Fetching...'
                };
                selectCustomer(customer);
                showToast('Loading customer details...', 'info');

                fetch(`/customers/${customerId}/details`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.customer) {
                            if (elements.selectedCustomerMobileText) elements.selectedCustomerMobileText
                                .textContent = data.customer.mobile || 'Not provided';
                            if (elements.selectedCustomerEmailText) elements.selectedCustomerEmailText
                                .textContent = data.customer.email || 'Not provided';
                            if (elements.customerSearch) {
                                elements.customerSearch.value = data.customer.name;
                                elements.customerSearch.disabled = false;
                            }
                            showToast(`Customer "${data.customer.name}" loaded successfully`, 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (elements.customerSearch) elements.customerSearch.disabled = false;
                        showToast('Failed to load customer details', 'error');
                    });
            }

            function init() {
                disableBarcodeScanner();
                attachEventListeners();
                loadCustomerFromUrl();
                updateUIState();
                window.initAddressAutocomplete = initAddressAutocomplete;
            }

            function attachEventListeners() {
                if (elements.customerSearch) {
                    elements.customerSearch.addEventListener('input', handleCustomerSearch);
                    elements.customerSearch.addEventListener('focus', disableBarcodeScanner);
                    elements.customerSearch.addEventListener('blur', handleCustomerBlur);
                }
                if (elements.productSearch) {
                    elements.productSearch.addEventListener('input', handleProductSearch);
                    elements.productSearch.addEventListener('focus', disableBarcodeScanner);
                    elements.productSearch.addEventListener('blur', handleProductBlur);
                }
                if (elements.barcodeInput) {
                    elements.barcodeInput.addEventListener('input', handleBarcodeInput);
                    elements.barcodeInput.addEventListener('keydown', handleBarcodeKeydown);
                }
                document.addEventListener('click', handleDocumentClick);
                document.addEventListener('mousedown', handleDocumentMousedown);
            }

            let customerTimer = null;

            function handleCustomerSearch() {
                const query = this.value.trim();
                clearTimeout(customerTimer);
                if (query.length < 2) {
                    elements.customerResults.style.display = 'none';
                    return;
                }
                elements.customerResults.innerHTML = getSearchLoadingHTML();
                elements.customerResults.style.display = 'block';
                customerTimer = setTimeout(() => performCustomerSearch(query), 500);
            }

            function performCustomerSearch(query) {
                fetch(`{{ route('customers.ajax.search') }}?search=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(customers => {
                        elements.customerResults.innerHTML = '';
                        if (customers.length === 0) {
                            elements.customerResults.innerHTML = getNoCustomersHTML();
                            elements.customerResults.style.display = 'block';
                            return;
                        }
                        customers.forEach((customer, index) => {
                            elements.customerResults.appendChild(createCustomerElement(customer, index,
                                customers.length));
                        });
                        elements.customerResults.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        elements.customerResults.innerHTML = getSearchErrorHTML();
                        elements.customerResults.style.display = 'block';
                    });
            }

            function createCustomerElement(customer, index, total) {
                const item = document.createElement('div');
                item.style.cssText =
                    `padding: 14px 16px; cursor: pointer; border-bottom: ${index === total - 1 ? 'none' : '1px solid #f1f5f9'}; transition: all 0.2s; display: flex; justify-content: space-between; align-items: center;`;
                item.innerHTML = `
                    <div style="flex:1">
                        <div style="font-weight:600; color:#374151; margin-bottom:4px;">${escapeHTML(customer.name)}</div>
                        <div style="display:flex; gap:12px; font-size:13px; color:#64748b;"><span>📱 ${escapeHTML(customer.mobile || 'No phone')}</span>${customer.email ? `<span>✉️ ${escapeHTML(customer.email)}</span>` : ''}</div>
                    </div>
                    <div style="background:#3b82f6; color:white; padding:6px 12px; border-radius:8px; font-weight:600; font-size:13px;">Select</div>
                `;
                item.onmouseover = () => item.style.background = '#f8fafc';
                item.onmouseout = () => item.style.background = 'white';
                item.onclick = () => selectCustomer(customer);
                return item;
            }

            function selectCustomer(customer) {
                if (elements.customerIdInput) elements.customerIdInput.value = customer.id;
                if (elements.customerSearch) {
                    elements.customerSearch.value = customer.name;
                    elements.customerSearch.style.borderColor = '#10b981';
                }
                if (elements.selectedCustomerName) elements.selectedCustomerName.textContent = customer.name;
                if (elements.selectedCustomerMobileText) elements.selectedCustomerMobileText.textContent = customer
                    .mobile || 'Not provided';
                if (elements.selectedCustomerEmailText) elements.selectedCustomerEmailText.textContent = customer
                    .email || 'Not provided';
                if (elements.selectedCustomerInfo) elements.selectedCustomerInfo.style.display = 'block';
                if (elements.clearCustomerContainer) elements.clearCustomerContainer.style.display = 'block';
                if (elements.customerResults) elements.customerResults.style.display = 'none';
                state.isCustomerSelected = true;
                enableProductSearch();
                if (elements.customerStatus) elements.customerStatus.textContent = '';
                setTimeout(() => {
                    if (elements.productSearch) elements.productSearch.focus();
                }, 100);
                setTimeout(() => {
                    enableBarcodeScanner();
                }, 500);
                showToast(`Customer "${customer.name}" selected. Now you can add products.`, 'success');
                updateUIState();
                if (elements.requiresShipping && elements.requiresShipping.checked) autoFillShippingFromCustomer();
            }

            function clearCustomerSelection() {
                if (elements.customerSearch) {
                    elements.customerSearch.value = '';
                    elements.customerSearch.style.borderColor = '#d1d5db';
                }
                if (elements.customerIdInput) elements.customerIdInput.value = '';
                state.isCustomerSelected = false;
                if (elements.selectedCustomerInfo) elements.selectedCustomerInfo.style.display = 'none';
                if (elements.clearCustomerContainer) elements.clearCustomerContainer.style.display = 'none';
                disableProductSearch();
                clearAllProducts();
                disableBarcodeScanner();
                if (elements.customerSearch) elements.customerSearch.focus();
                if (elements.customerStatus) {
                    elements.customerStatus.textContent = 'Please select a customer first';
                    elements.customerStatus.style.color = '#dc2626';
                }
                const url = new URL(window.location.href);
                url.searchParams.delete('customer_id');
                url.searchParams.delete('customer_name');
                window.history.replaceState({}, '', url.toString());
                updateUIState();
                showToast('Customer selection cleared', 'info');
            }

            function handleCustomerBlur() {
                setTimeout(() => {
                    if (state.isCustomerSelected && !isInputFieldActive()) enableBarcodeScanner();
                }, 200);
            }

            function enableProductSearch() {
                if (elements.productSearch) {
                    elements.productSearch.disabled = false;
                    elements.productSearch.placeholder = "Type product name to search...";
                    elements.productSearch.style.background = 'white';
                    elements.productSearch.style.color = '#374151';
                    elements.productSearch.style.cursor = 'text';
                }
                if (elements.productSearchHint) {
                    elements.productSearchHint.textContent = 'Start typing to search products';
                    elements.productSearchHint.style.color = '#059669';
                }
                if (elements.productStatus) elements.productStatus.textContent = '';
            }

            function disableProductSearch() {
                if (elements.productSearch) {
                    elements.productSearch.disabled = true;
                    elements.productSearch.value = '';
                    elements.productSearch.placeholder = "First select a customer above...";
                    elements.productSearch.style.background = '#f3f4f6';
                    elements.productSearch.style.color = '#9ca3af';
                    elements.productSearch.style.cursor = 'not-allowed';
                }
                if (elements.productSearchHint) {
                    elements.productSearchHint.textContent = 'Select a customer first to enable product search';
                    elements.productSearchHint.style.color = '#6b7280';
                }
                if (elements.productStatus) elements.productStatus.textContent = 'Customer required';
            }

            function handleProductSearch() {
                if (!state.isCustomerSelected) {
                    showToast('Please select a customer first', 'error');
                    if (elements.customerSearch) elements.customerSearch.focus();
                    this.value = '';
                    return;
                }
                const val = this.value.toLowerCase().trim();
                if (elements.productResults) elements.productResults.innerHTML = '';
                if (!val) {
                    if (elements.productResults) elements.productResults.style.display = 'none';
                    return;
                }
                const filteredProducts = state.products.filter(p => (p.name && p.name.toLowerCase().includes(
                    val)) || (p.product_code && p.product_code.toString().toLowerCase().includes(val)));
                const exactMatch = state.products.find(p => p.product_code && p.product_code.toString()
                .toLowerCase() === val);
                if (exactMatch) {
                    addProduct(exactMatch);
                    if (elements.productResults) elements.productResults.style.display = 'none';
                    if (elements.productSearch) elements.productSearch.value = '';
                    showToast(`Product added: ${exactMatch.name}`, 'success');
                    return;
                }
                if (filteredProducts.length === 0) {
                    if (elements.productResults) {
                        elements.productResults.innerHTML =
                            `<div style="padding:20px; text-align:center; color:#94a3b8;">No products found matching "${escapeHTML(val)}"</div>`;
                        elements.productResults.style.display = 'block';
                    }
                    return;
                }
                filteredProducts.forEach((p, index) => {
                    const item = document.createElement('div');
                    item.style.cssText =
                        `padding: 14px 16px; cursor: pointer; border-bottom: ${index === filteredProducts.length - 1 ? 'none' : '1px solid #f1f5f9'}; transition: all 0.2s;`;
                    const imageUrl = getProductImageUrl(p);
                    const mrp = parseFloat(p.mrp || p.price || 0);
                    const sellingPrice = parseFloat(p.price || p.mrp || 0);
                    item.innerHTML = `
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            ${imageUrl ? `<img src="${imageUrl}" alt="${escapeHTML(p.name)}" class="product-image" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
                            <div class="product-image-placeholder" style="${imageUrl ? 'display:none;' : 'display:flex;'}">${escapeHTML(p.name?.charAt(0) || 'P')}</div>
                            <div style="flex:1; min-width:150px;"><div style="font-weight:600; color:#374151;">${escapeHTML(p.name)}</div><div style="font-size:12px; color:#64748b;">Code: ${escapeHTML(p.product_code || 'N/A')}</div></div>
                            <div style="display:flex; gap:8px;"><div class="product-mrp-price">MRP: ₹${mrp.toFixed(2)}</div><div class="product-selling-price">Sell: ₹${sellingPrice.toFixed(2)}</div></div>
                        </div>
                    `;
                    item.onmouseover = () => item.style.background = '#f8fafc';
                    item.onmouseout = () => item.style.background = 'white';
                    item.onclick = () => addProduct(p);
                    elements.productResults.appendChild(item);
                });
                if (elements.productResults) elements.productResults.style.display = 'block';
            }

            function getProductImageUrl(product) {
                if (!product.image) return null;
                if (product.image.startsWith('http://') || product.image.startsWith('https://')) return product
                    .image;
                return `/storage/${product.image}`;
            }

            function addProduct(p) {
                if (!state.isCustomerSelected) {
                    showToast('Please select a customer first', 'error');
                    if (elements.customerSearch) elements.customerSearch.focus();
                    return;
                }
                if (elements.productResults) elements.productResults.style.display = 'none';
                if (elements.productSearch) elements.productSearch.value = '';
                if (elements.emptyState) elements.emptyState.style.display = 'none';
                let existingRow = null;
                if (elements.itemsTable) {
                    document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => {
                        if (row.dataset.pid == p.id) existingRow = row;
                    });
                }
                if (existingRow) {
                    const qtyInput = existingRow.querySelector('.qty');
                    if (qtyInput) {
                        qtyInput.value = parseInt(qtyInput.value || 0) + 1;
                        existingRow.style.background = '#f0f9ff';
                        setTimeout(() => existingRow.style.background = '', 300);
                    }
                } else {
                    const rowId = `product-row-${p.id}`;
                    if (elements.itemsTable) {
                        elements.itemsTable.insertAdjacentHTML('beforeend', getProductRowHTML(p, rowId));
                        setTimeout(() => {
                            const newRow = document.getElementById(rowId);
                            if (newRow) newRow.style.background = '';
                        }, 300);
                    }
                }
                calculate();
                updateUIState();
            }

            function getProductRowHTML(p, rowId) {
                const imageUrl = getProductImageUrl(p);
                const mrp = parseFloat(p.mrp || p.price || 0);
                const sellingPrice = parseFloat(p.price || p.mrp || 0);
                const discount = mrp - sellingPrice;
                return `
                    <tr data-pid="${p.id}" id="${rowId}" style="border-bottom:1px solid #e5e7eb; animation:slideIn 0.3s ease-out; background:#f8fafc;">
                        <td style="padding:20px;">
                            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                ${imageUrl ? `<img src="${imageUrl}" alt="${escapeHTML(p.name)}" class="product-image-sm" onerror="this.onerror=null; this.src=''; this.style.display='none'; this.nextElementSibling.style.display='flex';">` : ''}
                                <div class="product-image-placeholder sm" style="${imageUrl ? 'display:none;' : 'display:flex;'}">${escapeHTML(p.name?.charAt(0) || 'P')}</div>
                                <div><div style="font-weight:600; color:#374151;">${escapeHTML(p.name || 'Product')}</div><div style="font-size:12px; color:#64748b;">Code: ${escapeHTML(p.product_code || 'N/A')}</div></div>
                            </div>
                            <input type="hidden" name="items[product_id][]" value="${escapeHTML(p.id)}">
                        </td>
                        <td style="padding:20px;"><input type="number" step="0.01" class="mrp-input product-input mrp-readonly" value="${mrp.toFixed(2)}" readonly data-mrp="${mrp}"></td>
                        <td style="padding:20px;"><input type="number" step="0.01" name="items[price][]" value="${sellingPrice.toFixed(2)}" oninput="InvoiceManager.calculate()" onchange="InvoiceManager.updateDiscount(this)" class="product-input selling-price-input"></td>
                        <td style="padding:20px;"><input type="text" class="discount-input product-input discount-badge" value="${discount.toFixed(2)}" readonly></td>
                        <td style="padding:20px;"><input type="number" class="qty product-input qty" name="items[quantity][]" value="1" min="1" oninput="InvoiceManager.calculate()"></td>
                        <td style="padding:20px;"><input type="text" name="items[total][]" readonly value="${sellingPrice.toFixed(2)}" class="product-input readonly"></td>
                        <td style="padding:20px;"><button type="button" onclick="InvoiceManager.removeProduct('${rowId}')" class="btn-remove"><span>🗑️</span>Remove</button></td>
                    </tr>
                `;
            }

            function updateDiscount(input) {
                const row = input.closest('tr');
                if (row) {
                    const mrpInput = row.querySelector('.mrp-input');
                    const discountInput = row.querySelector('.discount-input');
                    if (mrpInput && discountInput) {
                        const mrp = parseFloat(mrpInput.value) || 0;
                        const sellingPrice = parseFloat(input.value) || 0;
                        let discount = sellingPrice < mrp ? (mrp - sellingPrice) : 0;
                        discountInput.value = discount.toFixed(2);
                        if (discount > 0) {
                            discountInput.style.background = '#ecfdf5';
                            discountInput.style.color = '#059669';
                        } else {
                            discountInput.style.background = '#f1f5f9';
                            discountInput.style.color = '#1e293b';
                        }
                        calculate();
                    }
                }
            }

            function removeProduct(rowId) {
                const row = document.getElementById(rowId);
                if (row) {
                    row.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => {
                        row.remove();
                        if (elements.itemsTable && elements.itemsTable.children.length === 1 && elements
                            .emptyState) {
                            elements.emptyState.style.display = '';
                        }
                        calculate();
                        updateUIState();
                    }, 300);
                }
            }

            function clearAllProducts() {
                if (elements.itemsTable) {
                    document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => row.remove());
                }
                if (elements.emptyState) elements.emptyState.style.display = '';
                calculate();
                if (elements.itemsStatus) elements.itemsStatus.textContent =
                'Add products after selecting customer';
            }

            function handleProductBlur() {
                setTimeout(() => {
                    if (state.isCustomerSelected && !isInputFieldActive()) enableBarcodeScanner();
                }, 200);
            }

            function enableBarcodeScanner() {
                state.isScannerEnabled = true;
                if (elements.barcodeInput) {
                    elements.barcodeInput.disabled = false;
                    elements.barcodeInput.value = '';
                }
                state.barcodeBuffer = '';
                if (!isInputFieldActive()) {
                    setTimeout(() => {
                        if (elements.barcodeInput) elements.barcodeInput.focus();
                    }, 100);
                }
            }

            function disableBarcodeScanner() {
                state.isScannerEnabled = false;
                if (elements.barcodeInput) {
                    elements.barcodeInput.disabled = true;
                    elements.barcodeInput.value = '';
                }
                state.barcodeBuffer = '';
                if (state.barcodeTimeout) clearTimeout(state.barcodeTimeout);
            }

            function handleBarcodeInput(e) {
                if (!state.isScannerEnabled || !state.isCustomerSelected) return;
                state.barcodeBuffer += e.target.value;
                e.target.value = '';
                if (state.barcodeTimeout) clearTimeout(state.barcodeTimeout);
                state.barcodeTimeout = setTimeout(() => {
                    if (state.barcodeBuffer.length > 0) {
                        processBarcode(state.barcodeBuffer.trim());
                        state.barcodeBuffer = '';
                    }
                }, 100);
            }

            function handleBarcodeKeydown(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (!state.isScannerEnabled || !state.isCustomerSelected) {
                        showToast('Please select customer first', 'error');
                        e.target.value = '';
                        return;
                    }
                    const scannedCode = e.target.value.trim();
                    if (scannedCode) processBarcode(scannedCode);
                    e.target.value = '';
                }
            }

            function processBarcode(code) {
                if (!code) return;
                const product = state.products.find(p => p.product_code && p.product_code.toString() === code
                    .toString());
                if (!product) {
                    showToast(`Product not found for code: ${code}`, 'error');
                    return;
                }
                addProduct(product);
                showToast(`Product added: ${product.name}`, 'success');
            }

            function calculate() {
                let subTotalMrp = 0,
                    subTotal = 0,
                    totalDiscount = 0;
                document.querySelectorAll('#itemsTable tr[data-pid]').forEach(row => {
                    const mrp = parseFloat(row.querySelector('.mrp-input')?.value) || 0;
                    const sellingPrice = parseFloat(row.querySelector('[name="items[price][]"]')?.value) ||
                        0;
                    const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
                    const itemMrpTotal = mrp * qty;
                    const itemTotal = sellingPrice * qty;
                    const itemDiscount = sellingPrice < mrp ? (mrp - sellingPrice) * qty : 0;
                    subTotalMrp += itemMrpTotal;
                    subTotal += itemTotal;
                    totalDiscount += itemDiscount;
                    const totalInput = row.querySelector('[name="items[total][]"]');
                    if (totalInput) totalInput.value = itemTotal.toFixed(2);
                    const discountInput = row.querySelector('.discount-input');
                    if (discountInput) {
                        const unitDiscount = sellingPrice < mrp ? (mrp - sellingPrice) : 0;
                        discountInput.value = unitDiscount.toFixed(2);
                        if (unitDiscount > 0) {
                            discountInput.style.background = '#ecfdf5';
                            discountInput.style.color = '#059669';
                        } else {
                            discountInput.style.background = '#f1f5f9';
                            discountInput.style.color = '#1e293b';
                        }
                    }
                });
                const subTotalMrpInput = document.getElementById('sub_total_mrp');
                if (subTotalMrpInput) subTotalMrpInput.value = subTotalMrp.toFixed(2);
                const totalDiscountInput = document.getElementById('total_discount');
                if (totalDiscountInput) totalDiscountInput.value = totalDiscount.toFixed(2);
                const subTotalInput = document.getElementById('sub_total');
                if (subTotalInput) subTotalInput.value = subTotal.toFixed(2);
                const taxPercent = parseFloat(document.getElementById('tax')?.value) || 0;
                const taxAmount = (subTotal * taxPercent) / 100;
                const taxAmountInput = document.getElementById('tax_amount');
                if (taxAmountInput) taxAmountInput.value = taxAmount.toFixed(2);
                const grandTotal = subTotal + taxAmount;
                const grandTotalInput = document.getElementById('grand_total');
                if (grandTotalInput) grandTotalInput.value = grandTotal.toFixed(2);
            }

            function handleSubmit(event) {
                event.preventDefault();
                if (!state.isCustomerSelected) {
                    showToast('Please select a customer first', 'error');
                    if (elements.customerSearch) {
                        elements.customerSearch.focus();
                        elements.customerSearch.classList.add('shake');
                        setTimeout(() => elements.customerSearch.classList.remove('shake'), 500);
                    }
                    return false;
                }
                const hasProducts = document.querySelectorAll('#itemsTable tr[data-pid]').length > 0;
                if (!hasProducts) {
                    showToast('Please add at least one product', 'error');
                    if (elements.productSearch) {
                        elements.productSearch.focus();
                        elements.productSearch.classList.add('shake');
                        setTimeout(() => elements.productSearch.classList.remove('shake'), 500);
                    }
                    return false;
                }
                if (elements.requiresShipping && elements.requiresShipping.checked) {
                    const shippingAddress = elements.shippingAddress?.value.trim();
                    const city = elements.city?.value.trim();
                    const stateName = elements.state?.value.trim();
                    const pincode = elements.pincode?.value.trim();
                    if (!shippingAddress) {
                        showToast('Shipping address is required', 'error');
                        elements.shippingAddress?.focus();
                        return false;
                    }
                    if (!city) {
                        showToast('City is required', 'error');
                        elements.city?.focus();
                        return false;
                    }
                    if (!stateName) {
                        showToast('State is required', 'error');
                        elements.state?.focus();
                        return false;
                    }
                    if (!pincode) {
                        showToast('Pincode is required', 'error');
                        elements.pincode?.focus();
                        return false;
                    }
                }
                const btn = document.getElementById('saveBtn');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `<span class="btn-icon spin">⏳</span>Processing...`;
                }
                showToast('Creating invoice...', 'info');
                document.getElementById('invoiceForm').submit();
                return false;
            }

            function openCustomerModal() {
                ['c_name', 'c_mobile', 'c_email', 'c_address'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.value = '';
                });
                if (elements.customerModal) elements.customerModal.style.display = 'flex';
                const nameInput = document.getElementById('c_name');
                if (nameInput) nameInput.focus();
            }

            function closeCustomerModal() {
                if (elements.customerModal) elements.customerModal.style.display = 'none';
            }

            function saveCustomer() {
                if (state.isSavingCustomer) return;
                const name = document.getElementById('c_name')?.value.trim();
                const mobile = document.getElementById('c_mobile')?.value.trim();
                const email = document.getElementById('c_email')?.value.trim();
                const address = document.getElementById('c_address')?.value.trim();
                if (!name) {
                    showToast('Customer name is required', 'error');
                    document.getElementById('c_name')?.focus();
                    return;
                }
                if (!mobile) {
                    showToast('Mobile number is required', 'error');
                    document.getElementById('c_mobile')?.focus();
                    return;
                }
                state.isSavingCustomer = true;
                if (elements.saveCustomerBtn) {
                    elements.saveCustomerBtn.innerHTML =
                        `<span style="display:inline-block; width:16px; height:16px; border:2px solid rgba(255,255,255,0.3); border-top-color:white; border-radius:50%; animation:buttonSpin 0.6s linear infinite;"></span>Saving...`;
                    elements.saveCustomerBtn.disabled = true;
                }
                fetch("{{ route('customers.store.ajax') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({
                            name,
                            mobile,
                            email,
                            address
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.customer) {
                            closeCustomerModal();
                            selectCustomer(data.customer);
                            showToast('Customer added and selected! Now add products.', 'success');
                        } else {
                            showToast(data.message || 'Error saving customer', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Failed to save customer. Please try again.', 'error');
                    })
                    .finally(() => {
                        state.isSavingCustomer = false;
                        if (elements.saveCustomerBtn) {
                            elements.saveCustomerBtn.innerHTML = `<span>✓</span>Save Customer`;
                            elements.saveCustomerBtn.disabled = false;
                        }
                    });
            }

            function updateUIState() {
                const hasProducts = document.querySelectorAll('#itemsTable tr[data-pid]').length > 0;
                if (!state.isCustomerSelected) {
                    if (elements.customerStatus) {
                        elements.customerStatus.textContent = 'Required';
                        elements.customerStatus.style.color = '#dc2626';
                    }
                    if (elements.itemsStatus) {
                        elements.itemsStatus.textContent = 'Select customer first';
                        elements.itemsStatus.style.color = '#dc2626';
                    }
                } else if (!hasProducts) {
                    if (elements.customerStatus) {
                        elements.customerStatus.textContent = '✅ Selected';
                        elements.customerStatus.style.color = '#059669';
                    }
                    if (elements.itemsStatus) {
                        elements.itemsStatus.textContent = 'No products added yet';
                        elements.itemsStatus.style.color = '#f59e0b';
                    }
                } else {
                    if (elements.customerStatus) {
                        elements.customerStatus.textContent = '✅ Selected';
                        elements.customerStatus.style.color = '#059669';
                    }
                    if (elements.itemsStatus) elements.itemsStatus.textContent = '';
                }
            }

            function isInputFieldActive() {
                const activeElement = document.activeElement;
                if (!activeElement) return false;
                const activeTag = activeElement.tagName.toLowerCase();
                const activeId = activeElement.id;
                return activeTag === 'input' || activeTag === 'textarea' || activeTag === 'select' || activeId ===
                    'customerSearch' || activeId === 'productSearch' || activeElement.closest('#customerModal');
            }

            function handleDocumentClick(e) {
                if (elements.customerResults && !elements.customerSearch?.contains(e.target) && !elements
                    .customerResults.contains(e.target)) {
                    elements.customerResults.style.display = 'none';
                }
                if (elements.productResults && !elements.productSearch?.contains(e.target) && !elements
                    .productResults.contains(e.target)) {
                    elements.productResults.style.display = 'none';
                }
                if (elements.addressSuggestions && !elements.shippingAddressAutocomplete?.contains(e.target) && !
                    elements.addressSuggestions.contains(e.target)) {
                    elements.addressSuggestions.style.display = 'none';
                }
            }

            function handleDocumentMousedown(e) {
                if (isInputFieldActive() || e.target.closest('button') || e.target.closest('a')) return;
                if (state.isCustomerSelected && state.isScannerEnabled && !isInputFieldActive()) {
                    setTimeout(() => {
                        if (elements.barcodeInput) elements.barcodeInput.focus();
                    }, 50);
                }
            }

            function escapeHTML(str) {
                if (!str) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;').replace(/'/g, '&#039;');
            }

            function getSearchLoadingHTML() {
                return `<div style="padding:20px; text-align:center; color:#64748b;"><div style="display:inline-block; width:20px; height:20px; border:2px solid #e5e7eb; border-top-color:#3b82f6; border-radius:50%; animation:spin 0.8s linear infinite; margin-right:10px;"></div>Searching customers...</div>`;
            }

            function getNoCustomersHTML() {
                return `<div style="padding:30px 20px; text-align:center; color:#64748b;"><div style="font-size:40px; margin-bottom:10px;">👤</div>No customers found<div style="font-size:13px; margin-top:8px;">Try different keywords or add a new customer</div></div>`;
            }

            function getSearchErrorHTML() {
                return `<div style="padding:20px; text-align:center; color:#ef4444;"><div style="font-size:40px; margin-bottom:10px;">⚠️</div>Search failed. Please try again.</div>`;
            }

            function showToast(message, type = 'success') {
                document.querySelectorAll('.toast-notification').forEach(el => el.remove());
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                const bgColor = type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ?
                    '#f59e0b' : '#3b82f6';
                const icon = type === 'success' ? '✓' : type === 'error' ? '⚠' : type === 'warning' ? '⚠' : 'ℹ';
                toast.style.background = bgColor;
                toast.innerHTML =
                    `<span class="toast-icon">${icon}</span><span class="toast-message">${escapeHTML(message)}</span>`;
                document.body.appendChild(toast);
                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 3000);
            }

            return {
                init,
                selectCustomer,
                clearCustomerSelection,
                addProduct,
                removeProduct,
                updateDiscount,
                calculate,
                handleSubmit,
                openCustomerModal,
                closeCustomerModal,
                saveCustomer,
                showToast,
                autoFillShippingFromCustomer,
                getLiveLocation,
                initAddressAutocomplete
            };
        })();

        document.addEventListener('DOMContentLoaded', function() {
            InvoiceManager.init();
        });
        window.InvoiceManager = InvoiceManager;
    </script>
@endsection
