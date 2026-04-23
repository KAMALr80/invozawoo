@extends('layouts.app')

@section('page-title', 'Add New Product')

@section('content')
<style>
    /* ================= PROFESSIONAL DESIGN SYSTEM ================= */
    :root {
        --primary: #10b981;
        --primary-dark: #059669;
        --secondary: #8b5cf6;
        --secondary-dark: #6d28d9;
        --info: #3b82f6;
        --info-dark: #1d4ed8;
        --danger: #ef4444;
        --warning: #f59e0b;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --border: #e5e7eb;
        --bg-light: #f9fafb;
        --bg-white: #ffffff;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 15px 50px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 20px;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        color: var(--text-main);
        line-height: 1.5;
    }

    /* ================= MAIN CONTAINER ================= */
    .product-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(16px, 3vw, 20px);
        margin-left: 260px;
        margin-top: 70px;
        transition: margin 0.3s ease;
        width: calc(100% - 260px);
    }

    /* ================= UNAUTHORIZED CARD ================= */
    .unauthorized-card {
        max-width: 500px;
        width: 100%;
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        padding: clamp(30px, 5vw, 40px);
        box-shadow: var(--shadow-xl);
        text-align: center;
        border: 1px solid var(--border);
    }

    .unauthorized-icon {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        width: clamp(60px, 10vw, 80px);
        height: clamp(60px, 10vw, 80px);
        border-radius: var(--radius-xl);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px auto;
        font-size: clamp(24px, 5vw, 32px);
    }

    .unauthorized-title {
        margin: 0 0 10px 0;
        font-size: clamp(24px, 5vw, 28px);
        font-weight: 800;
        color: var(--text-main);
        word-break: break-word;
    }

    .unauthorized-text {
        color: var(--text-muted);
        font-size: clamp(14px, 3vw, 16px);
        line-height: 1.6;
        margin-bottom: 30px;
        word-break: break-word;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, var(--info) 0%, var(--info-dark) 100%);
        color: white;
        text-decoration: none;
        padding: clamp(12px, 2.5vw, 14px) clamp(24px, 4vw, 28px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 16px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25);
    }

    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
    }

    /* ================= FORM CARD ================= */
    .form-card {
        max-width: 1000px;
        width: 100%;
        background: var(--bg-white);
        border-radius: var(--radius-2xl);
        overflow: hidden;
        box-shadow: var(--shadow-xl);
        border: 1px solid var(--border);
    }

    /* ================= FORM HEADER ================= */
    .form-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        padding: clamp(24px, 4vw, 30px);
        color: white;
        position: relative;
        overflow: hidden;
    }

    .header-pattern {
        position: absolute;
        right: -40px;
        top: -40px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: rotate(45deg);
    }

    .header-pattern-2 {
        position: absolute;
        left: -60px;
        bottom: -60px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: clamp(16px, 4vw, 20px);
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
        flex-wrap: wrap;
    }

    .header-icon {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        width: clamp(60px, 10vw, 70px);
        height: clamp(60px, 10vw, 70px);
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 5vw, 32px);
        flex-shrink: 0;
    }

    .header-text {
        flex: 1;
        min-width: 250px;
    }

    .header-title {
        margin: 0;
        font-size: clamp(24px, 5vw, 32px);
        font-weight: 800;
        word-break: break-word;
    }

    .header-subtitle {
        margin: 5px 0 0 0;
        opacity: 0.9;
        font-size: clamp(14px, 3vw, 16px);
        word-break: break-word;
    }

    /* ================= FORM CONTENT ================= */
    .form-content {
        padding: clamp(24px, 5vw, 40px);
    }

    /* ================= FORM GRID ================= */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 30px;
    }

    /* ================= SECTION TITLES ================= */
    .section-title {
        margin: 0 0 20px 0;
        font-size: clamp(18px, 3.5vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .title-underline {
        background: var(--primary);
        width: 6px;
        height: 20px;
        border-radius: 3px;
        display: inline-block;
    }

    .title-underline.purple {
        background: var(--secondary);
    }

    /* ================= FORM GROUPS ================= */
    .form-group {
        margin-bottom: 25px;
    }

    .form-label {
        display: block;
        margin-bottom: 10px;
        font-weight: 600;
        color: #374151;
        font-size: clamp(14px, 2.5vw, 15px);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .label-icon {
        background: #dbeafe;
        width: 24px;
        height: 24px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        color: var(--info);
    }

    .label-icon.warning {
        background: #fef3c7;
        color: var(--warning);
    }

    .label-icon.pink {
        background: #fce7f3;
        color: #db2777;
    }

    .label-icon.green {
        background: #d1fae5;
        color: var(--primary);
    }

    .label-icon.purple {
        background: #e0e7ff;
        color: var(--secondary);
    }

    /* ================= FORM INPUTS ================= */
    .form-input {
        width: 100%;
        padding: clamp(14px, 2.5vw, 16px) clamp(16px, 3vw, 20px);
        border: 2px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 2.5vw, 16px);
        color: var(--text-main);
        background: white;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--info);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-input.error {
        border-color: var(--danger);
    }

    /* ================= INPUT WITH ICON ================= */
    .input-with-icon {
        position: relative;
    }

    .input-with-icon .form-input {
        padding-left: clamp(45px, 8vw, 50px);
    }

    .input-prefix {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 600;
        font-size: 14px;
        pointer-events: none;
    }

    .input-prefix.pink {
        color: #db2777;
    }

    .input-prefix.green {
        color: var(--primary);
    }

    /* ================= ERROR MESSAGE ================= */
    .error-message {
        color: var(--danger);
        font-size: clamp(12px, 2vw, 13px);
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* ================= SELECT WRAPPER ================= */
    .select-wrapper {
        margin-bottom: 10px;
    }

    .form-select {
        width: 100%;
        padding: clamp(14px, 2.5vw, 16px) clamp(16px, 3vw, 20px);
        border: 2px solid var(--border);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 2.5vw, 16px);
        color: var(--text-main);
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
        background-position: right 16px center;
        background-repeat: no-repeat;
        background-size: 20px;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--secondary);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    /* ================= CUSTOM CATEGORY INPUT ================= */
    .custom-category-container {
        margin-top: 10px;
        display: none;
    }

    .custom-category-input {
        width: 100%;
        padding: clamp(14px, 2.5vw, 16px) clamp(16px, 3vw, 20px);
        border: 2px solid var(--secondary);
        border-radius: var(--radius-lg);
        font-size: clamp(14px, 2.5vw, 16px);
        color: var(--text-main);
        background: #f5f3ff;
        transition: all 0.3s ease;
    }

    .custom-category-input:focus {
        outline: none;
        border-color: var(--secondary-dark);
        box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
    }

    .custom-category-hint {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 5px;
    }

    .custom-category-hint span {
        color: var(--secondary);
    }

    /* ================= IMAGE UPLOAD SECTION ================= */
    .image-section {
        margin-bottom: 30px;
        background: var(--bg-light);
        border-radius: var(--radius-lg);
        padding: clamp(20px, 4vw, 30px);
        border: 2px dashed var(--border);
    }

    .image-header {
        margin: 0 0 20px 0;
        font-size: clamp(18px, 3.5vw, 20px);
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .image-icon {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }

    /* ================= IMAGE TYPE TOGGLE ================= */
    .image-toggle {
        display: flex;
        gap: 30px;
        margin-bottom: 25px;
        background: white;
        padding: 15px 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .toggle-option {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    .toggle-radio {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    .toggle-label {
        font-weight: 500;
        color: #374151;
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: clamp(13px, 2.5vw, 14px);
    }

    /* ================= UPLOAD AREA ================= */
    .upload-area {
        background: white;
        border-radius: var(--radius-lg);
        padding: 25px;
        text-align: center;
        border: 2px dashed #cbd5e1;
        transition: all 0.3s;
        cursor: pointer;
    }

    .upload-area:hover {
        border-color: var(--secondary);
        background: #f5f3ff;
    }

    .upload-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .upload-text {
        font-weight: 600;
        color: #374151;
        margin: 0 0 5px 0;
        font-size: clamp(14px, 2.5vw, 16px);
    }

    .upload-hint {
        font-size: clamp(12px, 2vw, 13px);
        color: var(--text-muted);
        margin: 0;
    }

    .file-input {
        display: none;
    }

    /* ================= URL INPUT ================= */
    .url-input {
        position: relative;
    }

    .url-input .form-input {
        padding-right: 40px;
    }

    .url-hint {
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 8px;
    }

    .url-hint span {
        background: #e0e7ff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

    /* ================= IMAGE PREVIEW ================= */
    .image-preview {
        margin-top: 25px;
        display: none;
        background: white;
        border-radius: var(--radius-lg);
        padding: 20px;
        border: 1px solid var(--border);
    }

    .preview-title {
        font-weight: 600;
        color: #374151;
        margin: 0 0 15px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .preview-title span {
        background: var(--secondary);
        width: 4px;
        height: 18px;
        border-radius: 2px;
        display: inline-block;
    }

    .preview-image {
        max-width: 100%;
        max-height: 250px;
        border-radius: var(--radius-lg);
        border: 2px solid var(--border);
        padding: 5px;
        background: var(--bg-light);
    }

    /* ================= ACTION BUTTONS ================= */
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        padding-top: 30px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: white;
        color: #4b5563;
        text-decoration: none;
        padding: clamp(14px, 2.5vw, 16px) clamp(24px, 4vw, 30px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 16px);
        transition: all 0.3s ease;
        border: 2px solid var(--border);
    }

    .btn-cancel:hover {
        background: var(--bg-light);
        border-color: #9ca3af;
    }

    .btn-submit {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        padding: clamp(14px, 2.5vw, 16px) clamp(24px, 4vw, 30px);
        border-radius: var(--radius-lg);
        font-weight: 600;
        font-size: clamp(14px, 2.5vw, 16px);
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
    }

    /* ================= SCROLLBAR STYLING ================= */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* ================= LOADING SPINNER ================= */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--secondary);
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

    /* ================= RESPONSIVE BREAKPOINTS ================= */
    
    /* Large Desktop (1200px and above) */
    @media (min-width: 1200px) {
        .form-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Desktop (992px to 1199px) */
    @media (max-width: 1199px) {
        .product-page {
            margin-left: 220px;
            width: calc(100% - 220px);
        }
    }

    /* Tablet (768px to 991px) */
    @media (max-width: 991px) {
        .product-page {
            margin-left: 0;
            margin-top: 60px;
            width: 100%;
            padding: 15px;
        }

        .form-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .header-icon {
            margin: 0 auto;
        }
    }

    /* Mobile Landscape (576px to 767px) */
    @media (max-width: 767px) {
        .product-page {
            padding: 10px;
        }

        .form-content {
            padding: 20px;
        }

        .image-toggle {
            flex-direction: column;
            gap: 10px;
        }

        .toggle-option {
            width: 100%;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn-cancel,
        .btn-submit {
            width: 100%;
            justify-content: center;
        }

        .image-section {
            padding: 20px;
        }

        .upload-area {
            padding: 20px;
        }

        .upload-icon {
            font-size: 32px;
        }
    }

    /* Mobile Portrait (up to 575px) */
    @media (max-width: 575px) {
        .product-page {
            padding: 8px;
        }

        .form-content {
            padding: 16px;
        }

        .header-title {
            font-size: 22px;
        }

        .header-subtitle {
            font-size: 13px;
        }

        .section-title {
            font-size: 18px;
        }

        .form-label {
            font-size: 13px;
        }

        .form-input,
        .form-select {
            padding: 12px 14px;
            font-size: 14px;
        }

        .input-with-icon .form-input {
            padding-left: 40px;
        }

        .input-prefix {
            left: 16px;
            font-size: 13px;
        }

        .image-section {
            padding: 16px;
        }

        .image-header {
            font-size: 18px;
        }

        .image-icon {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }

        .upload-text {
            font-size: 14px;
        }

        .upload-hint {
            font-size: 11px;
        }

        .preview-image {
            max-height: 200px;
        }
    }

    /* Extra Small Devices (up to 360px) */
    @media (max-width: 360px) {
        .product-page {
            padding: 5px;
        }

        .form-content {
            padding: 12px;
        }

        .header-icon {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .header-title {
            font-size: 20px;
        }

        .header-subtitle {
            font-size: 12px;
        }

        .section-title {
            font-size: 16px;
        }

        .form-label {
            font-size: 12px;
        }

        .form-input,
        .form-select {
            padding: 10px 12px;
            font-size: 13px;
        }

        .image-section {
            padding: 12px;
        }

        .image-header {
            font-size: 16px;
        }

        .image-icon {
            width: 28px;
            height: 28px;
            font-size: 14px;
        }

        .upload-area {
            padding: 16px;
        }

        .upload-icon {
            font-size: 28px;
        }

        .upload-text {
            font-size: 13px;
        }

        .btn-cancel,
        .btn-submit {
            padding: 12px 20px;
            font-size: 13px;
        }
    }

    /* Print Styles */
    @media print {
        .product-page {
            margin: 0;
            padding: 0;
            background: white;
        }

        .btn-cancel,
        .btn-submit,
        .image-toggle,
        .upload-area,
        .url-input {
            display: none !important;
        }

        .form-header {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<div class="product-page">
    @if (auth()->user()->role !== 'admin')
        <!-- Unauthorized Access Card -->
        <div class="unauthorized-card">
            <div class="unauthorized-icon">
                🚫
            </div>
            <h2 class="unauthorized-title">Access Restricted</h2>
            <p class="unauthorized-text">
                Only administrators have permission to add new products to the inventory.
                Please contact your system administrator for access.
            </p>
            <a href="{{ route('inventory.index') }}" class="btn-back">
                <span>←</span>
                Back to Inventory
            </a>
        </div>
    @else
        <!-- Create Product Form -->
        <div class="form-card">
            <!-- Form Header -->
            <div class="form-header">
                <div class="header-pattern"></div>
                <div class="header-pattern-2"></div>
                <div class="header-content">
                    <div class="header-icon">
                        ➕
                    </div>
                    <div class="header-text">
                        <h2 class="header-title">Add New Product</h2>
                        <p class="header-subtitle">Add a new item to your inventory with image</p>
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form method="POST" action="{{ route('inventory.store') }}" enctype="multipart/form-data" class="form-content">
                @csrf

                <!-- Two Column Layout -->
                <div class="form-grid">
                    <!-- Left Column - Basic Info -->
                    <div>
                        <h3 class="section-title">
                            <span class="title-underline"></span>
                            Basic Information
                        </h3>

                        <!-- Product Code -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">
                                    🔢
                                </span>
                                Product Code *
                            </label>
                            <input type="text" name="product_code" value="{{ old('product_code') }}" required
                                placeholder="Enter unique product code"
                                class="form-input {{ $errors->has('product_code') ? 'error' : '' }}">
                            @error('product_code')
                                <p class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Product Name -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">
                                    📝
                                </span>
                                Product Name *
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                placeholder="Enter product name"
                                class="form-input {{ $errors->has('name') ? 'error' : '' }}">
                            @error('name')
                                <p class="error-message">
                                    <span>⚠️</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Category Field with ALL Categories -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon purple">
                                    🏷️
                                </span>
                                Category
                            </label>

                            <!-- Category Dropdown with ALL Categories -->
                            <div class="select-wrapper">
                                <select id="category_select" name="category" onchange="toggleCategoryInput(this)" class="form-select">
                                    <option value="">Select Category</option>

                                    <!-- Original Categories -->
                                    <option value="Electronics" {{ old('category') == 'Electronics' ? 'selected' : '' }}>
                                        📱 Electronics
                                    </option>
                                    <option value="Clothing" {{ old('category') == 'Clothing' ? 'selected' : '' }}>
                                        👕 Clothing
                                    </option>
                                    <option value="Home & Kitchen" {{ old('category') == 'Home & Kitchen' ? 'selected' : '' }}>
                                        🏠 Home & Kitchen
                                    </option>
                                    <option value="Books" {{ old('category') == 'Books' ? 'selected' : '' }}>
                                        📚 Books
                                    </option>
                                    <option value="Sports" {{ old('category') == 'Sports' ? 'selected' : '' }}>
                                        ⚽ Sports
                                    </option>
                                    <option value="Beauty" {{ old('category') == 'Beauty' ? 'selected' : '' }}>
                                        💄 Beauty
                                    </option>
                                    <option value="Toys" {{ old('category') == 'Toys' ? 'selected' : '' }}>
                                        🧸 Toys
                                    </option>

                                    <!-- NEW CATEGORIES ADDED HERE -->
                                    <option value="Stationery" {{ old('category') == 'Stationery' ? 'selected' : '' }}>
                                        ✏️ Stationery
                                    </option>
                                    <option value="Household" {{ old('category') == 'Household' ? 'selected' : '' }}>
                                        🧹 Household
                                    </option>
                                    <option value="Misc" {{ old('category') == 'Misc' ? 'selected' : '' }}>
                                        📌 Misc
                                    </option>
                                    <option value="Grocery" {{ old('category') == 'Grocery' ? 'selected' : '' }}>
                                        🛒 Grocery
                                    </option>

                                    <!-- Other (Custom) -->
                                    <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>
                                        ✨ Other (Custom)
                                    </option>
                                </select>
                            </div>

                            <!-- Custom Category Input (Hidden by default) -->
                            <div id="custom_category_container" class="custom-category-container">
                                <input type="text" id="custom_category" name="custom_category"
                                    value="{{ old('custom_category') }}" placeholder="Enter custom category name"
                                    class="custom-category-input">
                                <p class="custom-category-hint">
                                    <span>✨</span> Enter your custom category name
                                </p>
                            </div>

                            @error('category')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description (Optional) -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon warning">
                                    📝
                                </span>
                                Description
                            </label>
                            <textarea name="description" rows="4" placeholder="Enter product description (optional)"
                                class="form-input">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Right Column - Stock & Pricing -->
                    <div>
                        <h3 class="section-title">
                            <span class="title-underline purple"></span>
                            Stock & Pricing
                        </h3>

                        <!-- Quantity -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon pink">
                                    📦
                                </span>
                                Initial Quantity *
                            </label>
                            <div class="input-with-icon">
                                <input type="number" name="quantity" value="{{ old('quantity', 0) }}" required
                                    min="0" placeholder="0"
                                    class="form-input {{ $errors->has('quantity') ? 'error' : '' }}">
                                <span class="input-prefix pink">Qty</span>
                            </div>
                            @error('quantity')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon green">
                                    💰
                                </span>
                                Price (₹) *
                            </label>
                            <div class="input-with-icon">
                                <input type="number" name="price" value="{{ old('price', 0) }}" required
                                    step="0.01" min="0" placeholder="0.00"
                                    class="form-input {{ $errors->has('price') ? 'error' : '' }}">
                                <span class="input-prefix green">₹</span>
                            </div>
                            @error('price')
                                <p class="error-message">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Image Upload Section - Full Width -->
                <div class="image-section">
                    <h3 class="image-header">
                        <span class="image-icon">
                            🖼️
                        </span>
                        Product Image
                    </h3>

                    <!-- Image Type Toggle -->
                    <div class="image-toggle">
                        <label class="toggle-option">
                            <input type="radio" name="image_type" value="upload" checked
                                onclick="toggleImageInput('upload')" class="toggle-radio">
                            <span class="toggle-label">
                                <span>📤</span> Upload File
                            </span>
                        </label>
                        <label class="toggle-option">
                            <input type="radio" name="image_type" value="url" onclick="toggleImageInput('url')"
                                class="toggle-radio">
                            <span class="toggle-label">
                                <span>🔗</span> Image URL
                            </span>
                        </label>
                    </div>

                    <!-- File Upload Input -->
                    <div id="upload-input" style="display: block;">
                        <div class="upload-area"
                            onclick="document.getElementById('fileInput').click()">
                            <div class="upload-icon">📸</div>
                            <p class="upload-text">Click to upload or drag and drop</p>
                            <p class="upload-hint">JPG, PNG, GIF (Max: 2MB)</p>
                            <input type="file" name="image" id="fileInput" accept="image/*"
                                class="file-input" onchange="previewImage(this)">
                        </div>
                    </div>

                    <!-- URL Input -->
                    <div id="url-input" style="display: none;">
                        <div class="url-input">
                            <input type="url" name="image_url" id="imageUrl"
                                placeholder="https://example.com/image.jpg"
                                class="form-input"
                                oninput="previewUrl(this.value)">
                            <p class="url-hint">
                                <span>🔗</span> Enter direct image URL (must start with http:// or https://)
                            </p>
                        </div>
                    </div>

                    <!-- Image Preview Section -->
                    <div id="image-preview" class="image-preview">
                        <p class="preview-title">
                            <span></span>
                            Image Preview
                        </p>
                        <div style="text-align: center;">
                            <img id="preview-img" src="" alt="Preview"
                                class="preview-image"
                                onerror="this.onerror=null; this.src=''; document.getElementById('image-preview').style.display='none';">
                        </div>
                    </div>

                    @error('image')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                    @error('image_url')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden input to handle custom category -->
                <input type="hidden" name="use_custom_category" id="use_custom_category" value="0">

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('inventory.index') }}" class="btn-cancel">
                        <span>←</span>
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit">
                        <span>💾</span>
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<!-- JavaScript for Category and Image Handling -->
<script>
    // ========== CATEGORY HANDLING ==========
    function toggleCategoryInput(selectElement) {
        const customContainer = document.getElementById('custom_category_container');
        const customInput = document.getElementById('custom_category');
        const useCustomHidden = document.getElementById('use_custom_category');

        if (selectElement.value === 'Other') {
            // Show custom input
            customContainer.style.display = 'block';
            useCustomHidden.value = '1';

            // If coming from old input, keep the value
            @if (old('custom_category'))
                customInput.value = '{{ old('custom_category') }}';
            @endif
        } else {
            // Hide custom input
            customContainer.style.display = 'none';
            useCustomHidden.value = '0';
            customInput.value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category_select');

        // Check if old category is 'Other'
        @if (old('category') === 'Other')
            categorySelect.value = 'Other';
            toggleCategoryInput(categorySelect);
        @endif

        // ========== IMAGE HANDLING ==========
        @if (old('image_type') === 'url' && old('image_url'))
            toggleImageInput('url');
            document.querySelector('input[name="image_type"][value="url"]').checked = true;
            document.getElementById('imageUrl').value = '{{ old('image_url') }}';
            previewUrl('{{ old('image_url') }}');
        @endif
    });

    // ========== IMAGE HANDLING FUNCTIONS ==========
    function toggleImageInput(type) {
        const uploadInput = document.getElementById('upload-input');
        const urlInput = document.getElementById('url-input');
        const preview = document.getElementById('image-preview');

        if (type === 'upload') {
            uploadInput.style.display = 'block';
            urlInput.style.display = 'none';
        } else {
            uploadInput.style.display = 'none';
            urlInput.style.display = 'block';
        }

        // Hide preview when switching
        preview.style.display = 'none';
        document.getElementById('preview-img').src = '';
    }

    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewUrl(url) {
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');

        if (url && (url.startsWith('http://') || url.startsWith('https://'))) {
            previewImg.src = url;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
            previewImg.src = '';
        }
    }

    // Form submit handling to ensure custom category is sent correctly
    document.querySelector('form').addEventListener('submit', function(e) {
        const categorySelect = document.getElementById('category_select');
        const customInput = document.getElementById('custom_category');
        const useCustomHidden = document.getElementById('use_custom_category');

        if (categorySelect.value === 'Other' && customInput.value.trim() !== '') {
            // Set the category select value to custom input value
            // Create a hidden input to override the select
            const hiddenCategory = document.createElement('input');
            hiddenCategory.type = 'hidden';
            hiddenCategory.name = 'category';
            hiddenCategory.value = customInput.value.trim();

            // Disable the original select so it doesn't get submitted
            categorySelect.disabled = true;

            // Add the hidden input
            this.appendChild(hiddenCategory);
        }
    });

    // Handle window resize for sidebar
    window.addEventListener('resize', function() {
        const productPage = document.querySelector('.product-page');
        if (window.innerWidth <= 991) {
            productPage.style.marginLeft = '0';
            productPage.style.width = '100%';
        } else {
            productPage.style.marginLeft = '260px';
            productPage.style.width = 'calc(100% - 260px)';
        }
    });
</script>
@endsection