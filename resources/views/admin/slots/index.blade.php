@extends('layouts.admin')

@section('title', 'Homepage Announcement Ticker & Banners')

@section('content')
<div class="space-y-8" x-data="{
    showNewTickerModal: false,
    editTickerModal: false,
    editItem: { id: null, headline: '', cta_url: '', sort_order: 1, is_active: true }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Homepage Announcements & Banners</h1>
            <p class="text-xs text-slate-500">Manage breaking announcement tickers and top promotional banner strips</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showNewTickerModal = true" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Ticker Headline</span>
            </button>
        </div>
    </div>

    <!-- Language Switcher Tabs -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div class="flex items-center gap-2">
            @foreach($languages as $lang)
                <a href="{{ route('admin.slots.index', ['language_id' => $lang->id]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $selectedLangId == $lang->id ? 'bg-brand-navy text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    {{ $lang->name }} ({{ strtoupper($lang->code) }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- 1. Ticker Announcements Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <span class="w-3 h-3 rounded-full bg-red-500 animate-pulse"></span>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Announcement Ticker ("This Just In")</h2>
                    <p class="text-[11px] text-slate-400">Headlines scrolling horizontally across the top of the homepage</p>
                </div>
            </div>
            <button @click="showNewTickerModal = true" class="px-3 py-1.5 rounded-lg bg-brand-navy text-white text-xs font-semibold hover:bg-brand-navyLight transition">
                + New Headline
            </button>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($tickerItems as $item)
                <div class="p-4 flex items-center justify-between gap-4 hover:bg-slate-50/60 transition">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="text-xs font-mono text-slate-400 bg-slate-100 px-2 py-1 rounded-md">#{{ $item->sort_order }}</span>
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 truncate">
                                {{ $item->headline }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono truncate">
                                Link: {{ $item->cta_url ?: 'None' }}
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $item->is_active ? 'Active' : 'Hidden' }}
                        </span>
                        <button @click="editItem = { id: {{ $item->id }}, headline: '{{ addslashes($item->headline) }}', cta_url: '{{ addslashes($item->cta_url) }}', sort_order: {{ $item->sort_order }}, is_active: {{ $item->is_active ? 'true' : 'false' }} }; editTickerModal = true;" 
                                class="p-1.5 text-slate-400 hover:text-brand-navy transition rounded-md hover:bg-slate-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('admin.slots.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this announcement?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 transition rounded-md hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    No ticker headlines active for this language. Click "+ New Headline" to add one.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. Top Strip Promo Banner Section -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-1">Top Promo Strip Banner</h2>
        <p class="text-xs text-slate-500 mb-6">Highlighted announcement strip placed above or within the homepage header</p>

        <form action="{{ route('admin.slots.banner.update') }}" method="POST" class="space-y-4 max-w-3xl">
            @csrf
            <input type="hidden" name="language_id" value="{{ $selectedLangId }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Banner Headline *</label>
                <input type="text" name="headline" value="{{ $promoBanner?->headline }}" required 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sub-headline / Detail</label>
                <input type="text" name="subheadline" value="{{ $promoBanner?->subheadline }}" 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Button / CTA Label</label>
                    <input type="text" name="cta_label" value="{{ $promoBanner?->cta_label ?? 'Book Free Counselling' }}" 
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Action URL</label>
                    <input type="text" name="url" value="{{ $promoBanner?->url ?? '#leadsform' }}" 
                           class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ ($promoBanner?->is_active ?? true) ? 'checked' : '' }} class="rounded border-slate-300 text-brand-navy focus:ring-brand-navy">
                    <span class="text-xs font-semibold text-slate-700">Display this Banner</span>
                </label>
                <button type="submit" class="px-5 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow-sm transition">
                    Save Banner Settings
                </button>
            </div>
        </form>
    </div>

    <!-- MODAL: Add Ticker Item -->
    <div x-show="showNewTickerModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showNewTickerModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Add Ticker Headline</h3>
            <p class="text-xs text-slate-500 mb-4">Displays in the live breaking announcement ticker</p>

            <form action="{{ route('admin.slots.items.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="slot_id" value="{{ $tickerSlot->id }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Headline Text *</label>
                    <input type="text" name="headline" required placeholder="e.g., UPSC CSE 2026 Prelims Notification Released" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Link URL</label>
                    <input type="text" name="cta_url" placeholder="/daily-updates/daily-news-analysis" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ $tickerItems->count() + 1 }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-brand-navy focus:ring-brand-navy">
                            <span class="text-xs font-semibold text-slate-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showNewTickerModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm">
                        Add Headline
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Ticker Item -->
    <div x-show="editTickerModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="editTickerModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Edit Ticker Item</h3>
            <p class="text-xs text-slate-500 mb-4">Modify headline text or target URL</p>

            <form :action="'{{ url('admin/homepage-slots/items') }}/' + editItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Headline Text *</label>
                    <input type="text" name="headline" required x-model="editItem.headline" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Link URL</label>
                    <input type="text" name="cta_url" x-model="editItem.cta_url" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sort Order</label>
                        <input type="number" name="sort_order" x-model="editItem.sort_order" 
                               class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>
                    <div class="flex items-center pt-5">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" :checked="editItem.is_active" class="rounded border-slate-300 text-brand-navy focus:ring-brand-navy">
                            <span class="text-xs font-semibold text-slate-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="editTickerModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow-sm">
                        Update Headline
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
