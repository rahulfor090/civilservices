@extends('layouts.admin')

@section('title', 'Centres & Addresses Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">National Centres &amp; Addresses</h1>
            <p class="text-xs text-slate-500 mt-1">Manage offline classroom centre addresses, helplines, map links, and display order across the website.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('centres.index') }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5 transition">
                <span>View Public Directory</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
            <a href="{{ route('admin.centres.create') }}"
               class="px-4 py-2.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-md flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add New Centre</span>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Centres</div>
            <div class="text-2xl font-black text-brand-navy mt-1">{{ $stats['total_centres'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Active Campuses</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['active_centres'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Inactive / Hidden</div>
            <div class="text-2xl font-black text-slate-700 mt-1">{{ $stats['inactive_centres'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Cities Covered</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $stats['cities_count'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.centres.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by centre name, city, state, address, or pincode..."
                       class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
            </div>

            <div>
                <select name="status" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold transition">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.centres.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Centres Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Order</th>
                        <th class="py-3.5 px-4">Centre &amp; Slug</th>
                        <th class="py-3.5 px-4">City / State</th>
                        <th class="py-3.5 px-4">Address</th>
                        <th class="py-3.5 px-4">Contact</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($centres as $centre)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-mono text-slate-400">
                                #{{ $centre->sort_order }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span>{{ $centre->name }}</span>
                                    @if($centre->isHeadquarters())
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">HQ</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">/centres/{{ $centre->slug }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $centre->city }}</div>
                                <div class="text-[11px] text-slate-400">{{ $centre->state }}</div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <div class="text-slate-700 leading-relaxed">{{ $centre->address }}</div>
                                @if($centre->pincode)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">PIN: {{ $centre->pincode }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-mono font-semibold text-slate-800">{{ $centre->phone }}</div>
                                @if($centre->email)
                                    <div class="text-[11px] text-slate-400">{{ $centre->email }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $centre->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $centre->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.centres.edit', $centre) }}"
                                       class="px-3 py-1.5 rounded-lg bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold transition">
                                        Edit Address
                                    </a>
                                    @if($centre->is_active)
                                        <a href="{{ route('centres.show', $centre->slug) }}"
                                           target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                            View
                                        </a>
                                    @endif
                                    <form action="{{ route('admin.centres.destroy', $centre) }}" method="POST" onsubmit="return confirm('Delete centre {{ addslashes($centre->name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete Centre">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                No centres found. Click "+ Add New Centre" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($centres->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $centres->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

