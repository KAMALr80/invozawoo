{{-- resources/views/components/pos/table.blade.php --}}
<div class="bg-white rounded-lg border border-gray-100 shadow-sm overflow-hidden flex flex-col h-full">
    <div class="overflow-x-auto flex-1 scrollbar-thin scrollbar-thumb-gray-200">
        <table class="w-full text-left border-collapse min-w-[1000px]">
            <thead class="bg-[#f8fafc] sticky top-0 z-10 border-b border-gray-200">
                <tr>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-12 text-center">Img</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter">Item Name</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-24">Price</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-16">Qty</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-24 text-right">Total</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-24">Tax</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-20 text-center">Cat</th>
                    <th class="px-2 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter w-10 text-center"></th>
                </tr>
            </thead>
            <tbody id="itemsTable" class="divide-y divide-gray-50">
                <tr id="emptyState">
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-shopping-cart text-gray-200 text-lg mb-2"></i>
                            <p class="text-[11px] font-black text-gray-300 uppercase tracking-tighter">Cart Empty</p>
                        </div>
                    </td>
                </tr>
                <!-- Items populated by JS -->
            </tbody>
        </table>
    </div>
</div>
