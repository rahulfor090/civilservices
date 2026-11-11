@extends('layouts.admin')

@section('title', 'Audit Trail Ledger')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 ring-4 ring-emerald-100 animate-pulse"></span>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tamper-Evident Audit Trail</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Cryptographically chained SHA-256 ledger recording every staff action, content mutation, and authentication event</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" 
                       name="module" 
                       value="{{ request('module') }}" 
                       placeholder="Filter by module (e.g. content, auth)..." 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>
            <div>
                <input type="text" 
                       name="action" 
                       value="{{ request('action') }}" 
                       placeholder="Filter by action (e.g. create_article, login_success)..." 
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>
            <div>
                <button type="submit" class="w-full py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold transition-all">
                    Filter Ledger
                </button>
            </div>
        </form>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="text-[11px] uppercase bg-slate-50 text-slate-400 border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-3">ID</th>
                        <th class="py-3 px-3">Timestamp</th>
                        <th class="py-3 px-3">Actor</th>
                        <th class="py-3 px-3">Module & Action</th>
                        <th class="py-3 px-3">Cryptographic SHA-256 Hash Chain</th>
                        <th class="py-3 px-3">Payload Summary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-3 font-mono text-[11px] text-slate-400">
                                #{{ $log->id }}
                            </td>
                            <td class="py-3 px-3 text-[11px] text-slate-500 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-semibold text-slate-900">{{ $log->user ? $log->user->name : 'System' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $log->ip_address ?? '127.0.0.1' }}</div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                    {{ $log->module }}
                                </span>
                                <div class="font-mono text-slate-900 text-[11px] mt-0.5">{{ $log->action }}</div>
                            </td>
                            <td class="py-3 px-3 max-w-xs font-mono text-[10px]">
                                <div class="flex items-center gap-1.5 text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span class="truncate" title="Current Hash: {{ $log->record_hash }}">
                                        CUR: {{ substr($log->record_hash, 0, 16) }}...
                                    </span>
                                </div>
                                <div class="text-slate-400 truncate pl-3" title="Previous Hash: {{ $log->prev_hash }}">
                                    PREV: {{ $log->prev_hash ? substr($log->prev_hash, 0, 14) . '...' : 'GENESIS' }}
                                </div>
                            </td>
                            <td class="py-3 px-3 text-[11px]">
                                @if($log->changes_summary)
                                    <div class="text-slate-700 font-medium">{{ $log->changes_summary }}</div>
                                @elseif($log->auditable_type)
                                    <div class="text-slate-500 truncate max-w-xs">
                                        {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">No extra metadata</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No audit log entries recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
