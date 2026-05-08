{{-- resources/views/components/pos/sidebar.blade.php --}}
<div class="bg-white border-l border-gray-200 w-64 flex flex-col h-full shadow-lg z-20">
    <div class="p-2 border-b border-gray-100 bg-[#f8fafc]">
        <h3 class="text-[9px] font-black text-gray-800 uppercase tracking-tighter mb-2 flex items-center">
            <i class="fas fa-th-large mr-1.5 text-blue-500 text-[10px]"></i>
            Quick Select
        </h3>
        <div class="flex flex-wrap gap-1">
            <button class="px-2 py-0.5 text-[9px] font-black bg-blue-600 text-white rounded shadow-sm">All</button>
            @foreach($categories ?? [] as $category)
                <button class="px-2 py-0.5 text-[9px] font-black bg-white text-gray-400 border border-gray-200 rounded hover:border-blue-400 hover:text-blue-500 transition-all">{{ $category->category }}</button>
            @endforeach
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-2 scrollbar-thin scrollbar-thumb-gray-200 bg-gray-50/30">
        <div id="productGrid" class="grid grid-cols-2 gap-2">
            <!-- Products populated by JS -->
        </div>
    </div>
</div>
