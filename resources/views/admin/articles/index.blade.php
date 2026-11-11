@extends('layouts.admin')

@section('title', 'Articles CMS')

@section('content')
<div class="space-y-6" x-data="{
    selectAll: false,
    selectedIds: [],
    toggleAll() {
        if (this.selectAll) {
            this.selectedIds = Array.from(document.querySelectorAll('.article-checkbox')).map(el => el.value);
        } else {
            this.selectedIds = [];
        }
    }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Articles Management</h1>
            <p class="text-xs text-slate-500">Publish, schedule, edit, and translate IAS syllabus content</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>New Article</span>
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.articles.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div class="lg:col-span-2">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search by title or slug..." 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy focus:bg-white transition-all">
            </div>

            <div>
                <select name="language_id" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy focus:bg-white transition-all">
                    <option value="">All Languages</option>
                    @foreach($languages as $lang)
                        <option value="{{ $lang->id }}" {{ request('language_id') == $lang->id ? 'selected' : '' }}>
                            {{ $lang->name }} ({{ $lang->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="content_type_id" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy focus:bg-white transition-all">
                    <option value="">All Content Types</option>
                    @foreach($contentTypes as $type)
                        <option value="{{ $type->id }}" {{ request('content_type_id') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <select name="status" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy focus:bg-white transition-all">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shrink-0">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Bulk Action Form & Data Table -->
    <form action="{{ route('admin.articles.bulk') }}" method="POST" id="bulkForm">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Bulk Action Bar -->
            <div class="p-3.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-slate-500">Selected:</span>
                    <span class="font-bold text-slate-900 font-mono" x-text="selectedIds.length">0</span>
                    <span class="text-slate-400">&bull;</span>
                    <select name="action" class="px-2.5 py-1 text-xs rounded-lg border border-slate-200 bg-white" :disabled="selectedIds.length === 0">
                        <option value="">Bulk Actions</option>
                        <option value="publish">Publish</option>
                        <option value="unpublish">Move to Draft</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button type="submit" 
                            :disabled="selectedIds.length === 0"
                            onclick="return confirm('Apply this bulk action to selected articles?')"
                            class="px-3 py-1 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-900 disabled:opacity-40 disabled:cursor-not-allowed">
                        Apply
                    </button>
                </div>
                <div class="text-slate-400 text-[11px]">
                    Showing {{ $articles->firstItem() ?? 0 }}-{{ $articles->lastItem() ?? 0 }} of {{ $articles->total() }} articles
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] uppercase bg-slate-50/50 text-slate-400 border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-3 w-10 text-center">
                                <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-slate-300 text-brand-navy focus:ring-brand-navy">
                            </th>
                            <th class="py-3 px-3">Article</th>
                            <th class="py-3 px-3">Locale</th>
                            <th class="py-3 px-3">Type & Section</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Views</th>
                            <th class="py-3 px-3">Published</th>
                            <th class="py-3 px-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($articles as $art)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" 
                                           name="selected_ids[]" 
                                           value="{{ $art->id }}" 
                                           x-model="selectedIds"
                                           class="article-checkbox rounded border-slate-300 text-brand-navy focus:ring-brand-navy">
                                </td>
                                <td class="py-3 px-3 max-w-md">
                                    <div class="flex items-center gap-3">
                                        @if($art->feature_image_url)
                                            <img src="{{ $art->feature_image_url }}" alt="" class="w-11 h-11 rounded-lg object-cover border border-slate-200 shrink-0 bg-slate-100 shadow-2xs">
                                        @else
                                            <div class="w-11 h-11 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 shrink-0" title="No featured image">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-900 line-clamp-1">
                                                <a href="{{ route('admin.articles.edit', $art) }}" class="hover:text-brand-orange">
                                                    {{ $art->title }}
                                                </a>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono truncate">
                                                /{{ $art->slug }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $art->language->code === 'hi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $art->language->code }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="text-slate-800 font-medium">{{ $art->contentType ? $art->contentType->name : '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $art->section ? $art->section->name : 'General' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $art->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($art->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-mono text-[11px]">
                                    {{ number_format($art->view_count) }}
                                </td>
                                <td class="py-3 px-3 text-[11px] text-slate-400">
                                    {{ $art->published_at ? $art->published_at->format('d M Y') : 'Unpublished' }}
                                </td>
                                <td class="py-3 px-3 text-right space-x-2">
                                    <a href="{{ $art->url }}" target="_blank" class="text-slate-400 hover:text-slate-700 inline-block" title="View Public Article">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('admin.articles.edit', $art) }}" class="text-brand-navy hover:text-brand-navyLight font-semibold">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">No articles matched your filter criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($articles->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </form>
</div>
@endsection
