{{-- resources/views/components/pos/footer.blade.php --}}
<div class="bg-[#f8fafc] border-t border-gray-200 px-4 py-1.5 flex items-center justify-between shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
    <div class="flex items-center space-x-4">
        <label class="flex items-center group cursor-pointer">
            <input type="checkbox" id="noPickPack" class="w-3.5 h-3.5 text-blue-600 border-gray-300 rounded focus:ring-blue-500/10">
            <span class="ml-1.5 text-[9px] font-black text-gray-400 uppercase group-hover:text-gray-600 transition-colors">No Pick & Pack</span>
        </label>
        
        <div class="flex items-baseline space-x-2">
            <span class="text-[9px] font-black text-gray-400 uppercase tracking-tighter">Total Payable</span>
            <span class="text-xl font-black text-blue-600 tracking-tighter" id="totalPayable">₹ 0.00</span>
        </div>
    </div>

    <div class="flex items-center space-x-2">
        <button type="button" id="cancelBtn" class="px-4 py-1.5 text-[9px] font-black text-gray-400 uppercase tracking-tighter hover:bg-gray-100 rounded transition-all active:scale-95">
            Cancel
        </button>
        <button type="button" id="draftBtn" class="px-4 py-1.5 text-[9px] font-black text-blue-600 uppercase tracking-tighter bg-blue-50 hover:bg-blue-100 border border-blue-100 rounded transition-all active:scale-95 shadow-sm">
            Draft
        </button>
        <button type="button" id="finalizeBtn" class="px-6 py-1.5 text-[9px] font-black text-white uppercase tracking-tighter bg-blue-600 hover:bg-blue-700 rounded transition-all active:scale-95 shadow-md shadow-blue-200">
            Finalize (F12)
        </button>
    </div>
</div>
