@extends('layouts.admin')

@section('title', 'Top Menu & Navigation Management')

@section('content')
<div class="space-y-6" x-data="{
    showNewColumnModal: false,
    showNewLinkModal: false,
    activeParentId: null,
    activeParentLabel: '',
    editItemModal: false,
    editItem: { id: null, label: '', url: '', sort_order: 1, is_active: true }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Top Menu & Mega-Menu Management</h1>
            <p class="text-xs text-slate-500">Configure top navigation categories, sub-links, URLs, and display order dynamically</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showNewColumnModal = true" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Navigation Category / Column</span>
            </button>
        </div>
    </div>

    <!-- Language Switcher Tabs -->
    <div class="flex items-center justify-between border-b border-slate-200 pb-3">
        <div class="flex items-center gap-2">
            @foreach($languages as $lang)
                <a href="{{ route('admin.navigation.index', ['language_id' => $lang->id]) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ $selectedLangId == $lang->id ? 'bg-brand-navy text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    {{ $lang->name }} ({{ strtoupper($lang->code) }})
                </a>
            @endforeach
        </div>
        <span class="text-xs text-slate-400 font-medium">Menu ID: #{{ $menu->id }} &bull; Code: <code class="text-slate-600 font-mono">{{ $menu->code }}</code></span>
    </div>

    <!-- Column Grid -->
    @if($columns->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center shadow-xs">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            <h3 class="text-sm font-bold text-slate-700">No navigation categories found for this medium</h3>
            <p class="text-xs text-slate-500 mt-1">Create your first category column (e.g., Prelims Preparation, Mains & Ethics, Current Affairs).</p>
            <button @click="showNewColumnModal = true" class="mt-4 px-4 py-2 rounded-xl bg-brand-orange text-white text-xs font-semibold hover:bg-brand-orangeHover transition-all inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Create First Category</span>
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            @foreach($columns as $col)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs flex flex-col overflow-hidden">
                    <!-- Column Header -->
                    <div class="p-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $col->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ $col->label }}</h3>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">Order: #{{ $col->sort_order }}</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="editItem = { id: {{ $col->id }}, label: '{{ addslashes($col->label) }}', url: '{{ addslashes($col->url) }}', sort_order: {{ $col->sort_order }}, is_active: {{ $col->is_active ? 'true' : 'false' }} }; editItemModal = true;" 
                                    class="p-1 rounded-md text-slate-400 hover:text-brand-navy hover:bg-slate-100 transition" 
                                    title="Edit Column">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <form action="{{ route('admin.navigation.items.destroy', $col->id) }}" method="POST" onsubmit="return confirm('Delete this entire category column and all its sub-links?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete Column">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Child Links List -->
                    <div class="p-4 flex-1 space-y-2">
                        @forelse($col->children as $child)
                            <div class="p-2.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-amber-50/40 transition group flex items-center justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="text-xs font-semibold text-slate-800 truncate group-hover:text-amber-700">
                                        {{ $child->label }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono truncate">
                                        {{ $child->url }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click="editItem = { id: {{ $child->id }}, label: '{{ addslashes($child->label) }}', url: '{{ addslashes($child->url) }}', sort_order: {{ $child->sort_order }}, is_active: {{ $child->is_active ? 'true' : 'false' }} }; editItemModal = true;" 
                                            class="p-1 text-slate-300 hover:text-slate-700 transition" 
                                            title="Edit link">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form action="{{ route('admin.navigation.items.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Delete this link?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-300 hover:text-red-500 transition" title="Delete link">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-xs text-slate-400 border border-dashed border-slate-200 rounded-xl">
                                No links added yet
                            </div>
                        @endforelse
                    </div>

                    <!-- Add Link to this Column Button -->
                    <div class="p-3 bg-slate-50 border-t border-slate-100">
                        <button @click="activeParentId = {{ $col->id }}; activeParentLabel = '{{ addslashes($col->label) }}'; showNewLinkModal = true;" 
                                class="w-full py-1.5 px-3 rounded-lg border border-dashed border-slate-300 hover:border-brand-orange text-slate-600 hover:text-brand-orange text-xs font-semibold transition-all flex items-center justify-center gap-1.5 bg-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Add Link to {{ $col->label }}</span>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- MODAL: Add New Column -->
    <div x-show="showNewColumnModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showNewColumnModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Create Navigation Category / Column</h3>
            <p class="text-xs text-slate-500 mb-4">Adds a new top-level column heading in the mega-menu</p>

            <form action="{{ route('admin.navigation.items.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category Heading *</label>
                    <input type="text" name="label" required placeholder="e.g., Prelims Preparation, Ethics & Mains" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target URL *</label>
                    <input type="text" name="url" required value="#" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ $columns->count() + 1 }}" 
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
                    <button type="button" @click="showNewColumnModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm">
                        Create Column
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Add New Link to Column -->
    <div x-show="showNewLinkModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showNewLinkModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Add Link Item</h3>
            <p class="text-xs text-slate-500 mb-4">Adding under column: <span class="font-bold text-brand-navy" x-text="activeParentLabel"></span></p>

            <form action="{{ route('admin.navigation.items.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                <input type="hidden" name="parent_id" :value="activeParentId">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Link Title / Label *</label>
                    <input type="text" name="label" required placeholder="e.g., Mind Maps, Solved Papers" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target URL / Route *</label>
                    <input type="text" name="url" required placeholder="e.g., /mind-map, /free-downloads, /daily-news-editorials" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="1" 
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
                    <button type="button" @click="showNewLinkModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm">
                        Save Link
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: Edit Item -->
    <div x-show="editItemModal" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="editItemModal = false">
            <h3 class="text-base font-bold text-slate-900 mb-1">Edit Navigation Item</h3>
            <p class="text-xs text-slate-500 mb-4">Modify label, URL, or active state</p>

            <form :action="'{{ url('admin/navigation/items') }}/' + editItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Label / Title *</label>
                    <input type="text" name="label" required x-model="editItem.label" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL / Path *</label>
                    <input type="text" name="url" required x-model="editItem.url" 
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
                    <button type="button" @click="editItemModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow-sm">
                        Update Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
