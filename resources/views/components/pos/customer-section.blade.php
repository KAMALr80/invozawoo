{{-- resources/views/components/pos/customer-section.blade.php --}}
<div class="bg-white p-2 rounded-lg border border-gray-100 shadow-sm mb-2">
    <div class="flex items-center justify-between mb-2">
        <h3 class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Customer Details</h3>
        <button type="button" class="text-[8px] font-black text-blue-600 hover:text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded transition-colors uppercase">
            Customer View
        </button>
    </div>

    <div class="relative mb-2">
        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
            <i class="fas fa-user text-gray-400 text-[10px]"></i>
        </div>
        <input type="text" id="customerSearch" 
            class="block w-full pl-8 pr-2 py-1.5 border border-gray-200 rounded bg-gray-50 text-[11px] placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
            placeholder="Search or Select Customer...">
        
        <div id="customerResults" class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded shadow-xl max-h-60 overflow-y-auto hidden">
            <!-- Results populated by JS -->
        </div>
    </div>

    <div id="selectedCustomerInfo" class="space-y-1 hidden">
        <div class="flex items-start p-2 bg-blue-50/50 rounded border border-blue-100/50">
            <div class="flex-1">
                <p class="text-[10px] font-black text-gray-800 leading-tight" id="selectedCustomerName">---</p>
                <div class="flex flex-col mt-0.5 space-y-0.5">
                    <span class="text-[9px] text-gray-500 flex items-center">
                        <i class="fas fa-phone-alt w-3.5 text-[8px]"></i>
                        <span id="selectedCustomerMobileText">---</span>
                    </span>
                    <span class="text-[9px] text-gray-500 flex items-center">
                        <i class="fas fa-envelope w-3.5 text-[8px]"></i>
                        <span id="selectedCustomerEmailText">---</span>
                    </span>
                    <span class="text-[9px] text-gray-500 flex items-start">
                        <i class="fas fa-map-marker-alt w-3.5 mt-0.5 text-[8px]"></i>
                        <span id="selectedCustomerAddressText" class="flex-1 truncate">---</span>
                    </span>
                </div>
            </div>
            <button type="button" id="clearCustomerBtn" class="text-red-400 hover:text-red-600 p-0.5">
                <i class="fas fa-times-circle text-[10px]"></i>
            </button>
        </div>
    </div>

    <div id="customerStatus" class="text-[9px] text-gray-400 italic text-center py-1">
        Please select a customer to start
    </div>
</div>
