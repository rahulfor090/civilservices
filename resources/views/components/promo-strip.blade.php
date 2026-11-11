@props(['banner' => null])

@if($banner)
<div x-data="{ dismissed: false }" 
     x-show="!dismissed" 
     class="bg-gradient-to-r from-amber-600 via-amber-500 to-orange-600 text-white text-xs sm:text-sm py-2 px-4 shadow-sm">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
        <div class="flex items-center space-x-2 truncate">
            <span class="bg-white/20 text-white font-bold px-2 py-0.5 rounded text-xs uppercase tracking-wider">Announcement</span>
            <span class="font-medium truncate">{{ $banner->headline }}</span>
        </div>
        <div class="flex items-center space-x-3 shrink-0">
            @if($banner->cta_label)
                <a href="{{ $banner->url }}" class="bg-slate-900 hover:bg-black text-amber-300 font-semibold px-3 py-1 rounded text-xs transition">
                    {{ $banner->cta_label }} &rarr;
                </a>
            @endif
            <button @click="dismissed = true" class="text-white/80 hover:text-white p-1" title="Close announcement">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
</div>
@endif
