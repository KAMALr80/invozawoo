{{-- resources/views/components/pos/summary.blade.php --}}
<div class="bg-white border-t border-gray-200 p-2">
    <div class="grid grid-cols-12 gap-3">
        <!-- Totals Column -->
        <div class="col-span-12 lg:col-span-4 space-y-1">
            <div class="flex justify-between items-center text-gray-400">
                <span class="text-[9px] font-black uppercase">Items</span>
                <span class="text-[10px] font-black" id="itemCount">0</span>
            </div>
            <div class="flex justify-between items-center text-gray-400">
                <span class="text-[9px] font-black uppercase">Subtotal</span>
                <span class="text-[10px] font-black" id="subTotal">₹ 0.00</span>
            </div>
            <div class="grid grid-cols-3 gap-x-2 py-0.5 border-y border-gray-50 my-1">
                <div class="flex justify-between text-[8px] text-gray-400"><span>GST</span><span id="taxGST">0</span></div>
                <div class="flex justify-between text-[8px] text-gray-400"><span>HST</span><span id="taxHST">0</span></div>
                <div class="flex justify-between text-[8px] text-gray-400"><span>PST</span><span id="taxPST">0</span></div>
                <div class="flex justify-between text-[8px] text-gray-400"><span>Fed</span><span id="taxFed">0</span></div>
                <div class="flex justify-between text-[8px] text-gray-400"><span>Prov</span><span id="taxProv">0</span></div>
            </div>
            <div class="flex justify-between items-center text-blue-600">
                <span class="text-[10px] font-black uppercase tracking-tighter">Total Amount</span>
                <span class="text-base font-black" id="totalAmount">₹ 0.00</span>
            </div>
        </div>

        <!-- Delivery Column -->
        <div class="col-span-12 lg:col-span-5">
            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-tighter mb-2">Delivery</h4>
            <div class="grid grid-cols-3 gap-1.5">
                @php
                    $options = [
                        ['id' => 'walkin_self', 'label' => 'Walk-in (Self)', 'icon' => 'fa-walking'],
                        ['id' => 'walkin_delivery', 'label' => 'Walk-in (Del)', 'icon' => 'fa-truck-loading'],
                        ['id' => 'walkin_shipping', 'label' => 'Walk-in (Ship)', 'icon' => 'fa-box'],
                        ['id' => 'pickup', 'label' => 'Pickup', 'icon' => 'fa-hand-holding-box'],
                        ['id' => 'delivery', 'label' => 'Delivery', 'icon' => 'fa-shipping-fast'],
                        ['id' => 'shipping', 'label' => 'Shipping', 'icon' => 'fa-plane-departure'],
                    ];
                @endphp
                @foreach($options as $opt)
                <button type="button" class="delivery-option flex flex-col items-center justify-center p-1 border border-gray-100 rounded hover:border-blue-400 hover:bg-blue-50 transition-all group" data-id="{{ $opt['id'] }}">
                    <i class="fas {{ $opt['icon'] }} text-gray-300 group-hover:text-blue-500 mb-0.5 text-[10px]"></i>
                    <span class="text-[8px] font-black text-gray-400 group-hover:text-blue-600 uppercase text-center leading-tight">{{ $opt['label'] }}</span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Controls Column -->
        <div class="col-span-12 lg:col-span-3 flex flex-col justify-center space-y-2">
            <label class="flex items-center group cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="applyTax" class="sr-only peer" checked>
                    <div class="w-6 h-3 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:left-[1px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-blue-600"></div>
                </div>
                <span class="ml-2 text-[9px] font-black text-gray-400 uppercase group-hover:text-gray-600 transition-colors">Apply Tax</span>
            </label>
            <label class="flex items-center group cursor-pointer">
                <div class="relative">
                    <input type="checkbox" id="lockCursor" class="sr-only peer">
                    <div class="w-6 h-3 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[1px] after:left-[1px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-2.5 after:w-2.5 after:transition-all peer-checked:bg-blue-600"></div>
                </div>
                <span class="ml-2 text-[9px] font-black text-gray-400 uppercase group-hover:text-gray-600 transition-colors">Lock Cursor</span>
            </label>
        </div>
    </div>
</div>
