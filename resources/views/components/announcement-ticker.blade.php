@props(['items' => collect()])

<div x-data="{ 
        closed: localStorage.getItem('ticker_closed') === 'true' 
     }" 
     x-show="!closed" 
     class="bg-amber-50/90 border-b border-amber-200/80 text-xs py-2 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
        <!-- Label Badge -->
        <div class="shrink-0 flex items-center bg-amber-600 text-white font-bold px-2.5 py-1 rounded text-xs mr-3 shadow-xs tracking-wide uppercase">
            <span class="inline-block w-2 h-2 rounded-full bg-white animate-pulse mr-1.5"></span>
            {{ app()->getLocale() === 'hi' ? 'ताज़ा अपडेट' : 'This Just In' }}:
        </div>

        <!-- Scrolling Ticker Track -->
        <div class="overflow-hidden whitespace-nowrap w-full relative flex items-center">
            <div class="inline-flex space-x-8 animate-marquee">
                @forelse($items as $item)
                    <a href="{{ $item->cta_url }}" class="text-slate-800 hover:text-amber-700 font-medium inline-flex items-center transition">
                        <span class="text-amber-600 mr-1">&#9679;</span>
                        {{ $item->headline }}
                    </a>
                @empty
                    <span class="text-slate-600">Daily Current Affairs and Examination Solved Papers Available Now</span>
                @endforelse
            </div>
        </div>

        <!-- Close Button -->
        <button @click="closed = true; localStorage.setItem('ticker_closed', 'true')" 
                class="shrink-0 text-slate-400 hover:text-slate-700 ml-2 p-1" 
                title="Dismiss ticker">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

<style>
@keyframes marquee {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
}
.animate-marquee {
    display: inline-flex;
    animation: marquee 35s linear infinite;
}
.animate-marquee:hover {
    animation-play-state: paused;
}
</style>
