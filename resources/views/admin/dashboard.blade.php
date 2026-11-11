@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Staff & Editorial Dashboard</h1>
            <p class="text-xs text-slate-500">Real-time overview of editorial throughput, student admissions, and translation coverage</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.articles.create') }}" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Write New Article</span>
            </a>
        </div>
    </div>

    <!-- Metric Counters Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-2">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Articles</div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ $totalArticles }}</div>
                <div class="text-xs text-emerald-600 font-medium">+{{ $publishedToday }} today</div>
            </div>
            <div class="text-[11px] text-slate-400">{{ $draftArticles }} pending drafts</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-2">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Counselling Leads</div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ $totalLeads }}</div>
                <div class="text-xs text-emerald-600 font-medium">+{{ $newLeadsToday }} today</div>
            </div>
            <div class="text-[11px] text-slate-400">Captured via UTM & honeypot</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-2">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Translation Parity</div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-bold text-brand-navy font-mono">{{ $translationCoverage }}%</div>
                <div class="text-xs text-slate-400 font-medium">HI / EN ratio</div>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                <div class="bg-brand-orange h-1.5 rounded-full" style="width: {{ min($translationCoverage, 100) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm space-y-2">
            <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Registered Users</div>
            <div class="flex items-baseline justify-between">
                <div class="text-2xl font-bold text-slate-900 font-mono">{{ $registeredStudents }}</div>
                <div class="text-xs text-blue-600 font-medium">RBAC 12 Roles</div>
            </div>
            <div class="text-[11px] text-slate-400">Staff & student accounts</div>
        </div>
    </div>

    <!-- Two-column: Recent Content & Audit Trail -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Recent Articles -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h2 class="text-base font-bold text-slate-900">Recent Articles</h2>
                <a href="{{ route('admin.articles.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">View All &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-2.5 px-3">Title</th>
                            <th class="py-2.5 px-3">Language</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3">Published</th>
                            <th class="py-2.5 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($recentArticles as $art)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-3">
                                    <div class="font-semibold text-slate-900 line-clamp-1 max-w-sm">{{ $art->title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $art->contentType ? $art->contentType->name : 'N/A' }}</div>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $art->language->code === 'hi' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ $art->language->code }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold {{ $art->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ ucfirst($art->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-slate-400 text-[11px]">
                                    {{ $art->published_at ? $art->published_at->format('d M, H:i') : 'Unpublished' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('admin.articles.edit', $art) }}" class="text-brand-navy hover:underline font-semibold">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-slate-400">No articles created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Trail -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <h2 class="text-base font-bold text-slate-900">Audit Ledger</h2>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">Ledger &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentAudits as $log)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-800">{{ $log->action }}</span>
                            <span class="text-[10px] text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="text-[11px] text-slate-500">
                            By <span class="font-medium text-slate-700">{{ $log->user ? $log->user->name : 'System' }}</span> in <span class="font-mono text-slate-600">{{ $log->module }}</span>
                        </div>
                        <div class="text-[10px] font-mono text-slate-400 truncate" title="SHA-256 Hash: {{ $log->record_hash }}">
                            #{{ substr($log->record_hash, 0, 16) }}...
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No audit entries yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
