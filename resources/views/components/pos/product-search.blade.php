{{-- resources/views/components/pos/product-search.blade.php --}}
<div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm mb-2">
    <div class="grid grid-cols-12 gap-2">
        <div class="col-span-12 lg:col-span-6 relative">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-[10px]"></i>
            </div>
            <input type="text" id="productSearch" 
                class="block w-full pl-8 pr-2 py-1.5 border border-gray-200 rounded bg-gray-50 text-[11px] placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500/20 focus:border-blue-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                placeholder="Search products..." disabled>
            
            <div id="productResults" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded shadow-xl max-h-80 overflow-y-auto hidden">
                <!-- Results populated by JS -->
            </div>
        </div>

        <div class="col-span-12 lg:col-span-2 relative">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                <i class="fas fa-barcode text-gray-400 text-[10px]"></i>
            </div>
            <input type="text" id="barcodeInput" 
                class="block w-full pl-8 pr-2 py-1.5 border border-gray-200 rounded bg-gray-50 text-[11px] placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                placeholder="Barcode">
        </div>

        <div class="col-span-12 lg:col-span-2">
            <select id="categoryFilter" 
                class="block w-full px-2 py-1.5 border border-gray-200 rounded bg-gray-50 text-[11px] text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option value="">All Categories</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->category }}">{{ $category->category }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-span-12 lg:col-span-2">
            <select id="priceType" 
                class="block w-full px-2 py-1.5 border border-gray-200 rounded bg-gray-50 text-[11px] text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                <option value="retail">Retail Price</option>
                <option value="wholesale">Wholesale</option>
                <option value="dealer">Dealer</option>
            </select>
        </div>
    </div>
    <div id="productSearchHint" class="mt-1 text-[9px] text-gray-400 flex items-center">
        <i class="fas fa-info-circle mr-1"></i>
        <span>Select customer first</span>
    </div>
</div>
