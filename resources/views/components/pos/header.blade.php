{{-- resources/views/components/pos/header.blade.php --}}
<div class="bg-[#f8fafc] border-b border-gray-200 px-4 py-1 flex items-center justify-between shadow-sm">
    <div class="flex items-center space-x-4">
        <div class="flex items-center">
            <div class="w-7 h-7 bg-blue-600 rounded flex items-center justify-center text-white mr-2">
                <i class="fas fa-store text-[10px]"></i>
            </div>
            <div>
                <h2 class="text-[11px] font-black text-gray-800 uppercase tracking-tighter leading-none" id="posLocationName">Main Branch</h2>
                <p class="text-[9px] text-gray-400 font-bold leading-none mt-0.5">POS v2.4.0</p>
            </div>
        </div>
        <div class="h-5 w-px bg-gray-200"></div>
        <div class="flex items-center text-gray-500">
            <i class="far fa-calendar-alt mr-1.5 text-[10px]"></i>
            <span class="text-[10px] font-bold" id="posDateTime">{{ now()->format('D, M d, Y - H:i') }}</span>
        </div>
    </div>

    <div class="flex items-center space-x-2">
        <button type="button" class="p-1.5 text-gray-400 hover:bg-gray-100 rounded transition-colors" title="Refresh Data">
            <i class="fas fa-sync-alt text-[10px]"></i>
        </button>
        <button type="button" class="p-1.5 text-gray-400 hover:bg-gray-100 rounded transition-colors" title="Settings">
            <i class="fas fa-cog text-[10px]"></i>
        </button>
        <div class="h-4 w-px bg-gray-200 mx-1"></div>
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="flex items-center space-x-1.5 px-2 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded border border-red-100 transition-all">
                <i class="fas fa-power-off text-[9px]"></i>
                <span class="text-[9px] font-black uppercase tracking-tighter">Logout</span>
            </button>
        </form>
    </div>
</div>
