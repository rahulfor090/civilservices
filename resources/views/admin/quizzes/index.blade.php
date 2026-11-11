@extends('layouts.admin')

@section('title', 'Quizzes & Tests Manager')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Quizzes & Mock Tests Manager</h1>
            <p class="text-xs text-slate-500 mt-1">Author Daily Current Affairs Quizzes, GS Prelims Mock Tests, and manage question banks.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.quizzes.create', ['category_slug' => 'daily-current-affairs-quiz']) }}" 
               class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>+ Create Daily CA Quiz</span>
            </a>
            <a href="{{ route('admin.quizzes.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-md flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>+ Add General Quiz</span>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Tests</div>
            <div class="text-2xl font-black text-brand-navy mt-1">{{ $stats['total_quizzes'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Daily CA Quizzes</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $stats['daily_ca_quizzes'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Published & Live</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['published_quizzes'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Student Attempts</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $stats['total_attempts'] }}</div>
        </div>
    </div>

    <!-- Category Filter Quick Pills -->
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.quizzes.index') }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ !request('category_slug') && !request('category_id') ? 'bg-brand-navy text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            All Tests
        </a>
        <a href="{{ route('admin.quizzes.index', ['category_slug' => 'daily-current-affairs-quiz']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('category_slug') === 'daily-current-affairs-quiz' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            📅 Daily Current Affairs Quizzes
        </a>
        <a href="{{ route('admin.quizzes.index', ['category_slug' => 'upsc-prelims-gs-mock']) }}" 
           class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('category_slug') === 'upsc-prelims-gs-mock' ? 'bg-brand-navy text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            🎯 UPSC Prelims GS Mock Tests
        </a>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.quizzes.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search tests by title or slug..." 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>

            <div>
                <select name="category_id" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published (Active)</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'category_slug', 'status', 'language_id']))
                    <a href="{{ route('admin.quizzes.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Quizzes Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Quiz Title & Slug</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Questions & Marks</th>
                        <th class="py-3.5 px-4">Duration & Scoring</th>
                        <th class="py-3.5 px-4">Attempts</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($quizzes as $quiz)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $quiz->title }}</span>
                                    @if($quiz->is_free)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Free</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">/quizzes/{{ $quiz->slug }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-1 rounded-md text-[11px] font-semibold {{ $quiz->category && $quiz->category->slug === 'daily-current-affairs-quiz' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-800' }}">
                                    {{ $quiz->category->name ?? 'General' }}
                                </span>
                                <div class="text-[10px] text-slate-400 uppercase mt-0.5">{{ $quiz->language->code ?? 'en' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-800">{{ $quiz->questions_count }} MCQs</div>
                                <div class="text-[11px] text-slate-500 font-mono">Total: {{ number_format($quiz->total_marks, 0) }} Marks</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ $quiz->duration_minutes }} Mins</div>
                                <div class="text-[11px] text-slate-400 font-mono">+{{ $quiz->marks_per_question }} / -{{ $quiz->negative_marks_per_question }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                    {{ $quiz->attempts_count }} Taken
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($quiz->status === 'published')
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Live
                                    </span>
                                @elseif($quiz->status === 'draft')
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Draft
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ ucfirst($quiz->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" 
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition-colors">
                                        Edit & MCQs ({{ $quiz->questions_count }})
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                <p class="text-base font-semibold text-slate-600">No quizzes found</p>
                                <p class="mt-1">Click the buttons above to author your Daily Current Affairs Quiz or GS Mock.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quizzes->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $quizzes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
