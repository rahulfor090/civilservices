@extends('layouts.app')

@section('title', 'CRM Sales & Counselling Pipeline | Admin Console')

@section('content')
<div class="bg-gray-100 py-8 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-navy-900 uppercase tracking-widest bg-amber-200 px-2.5 py-1 rounded">Admissions CRM</span>
                <h1 class="text-2xl font-extrabold text-gray-900 mt-2">Candidate Counselling & Lead Pipeline</h1>
                <p class="text-xs text-gray-500 mt-1">Track public inquiries, assign to sales counsellors, and convert aspirants into enrolled students.</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.reports.export.leads') }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 shadow-sm flex items-center space-x-1">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 bg-navy-900 text-white text-xs font-bold rounded-lg hover:bg-navy-800 shadow-sm">
                    Admin Home
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold">
            {{ session('success') }}
        </div>
        @endif

        <!-- Pipeline Stage KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <a href="{{ route('admin.crm.index') }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center hover:border-primary-500">
                <span class="text-[11px] font-bold text-gray-400 uppercase">All Inquiries</span>
                <span class="block text-xl font-extrabold text-gray-900 mt-1">{{ $stageCounts['all'] }}</span>
            </a>
            <a href="{{ route('admin.crm.index', ['status' => 'new']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center hover:border-blue-500">
                <span class="text-[11px] font-bold text-blue-600 uppercase">New Leads</span>
                <span class="block text-xl font-extrabold text-blue-700 mt-1">{{ $stageCounts['new'] }}</span>
            </a>
            <a href="{{ route('admin.crm.index', ['status' => 'contacted']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center hover:border-amber-500">
                <span class="text-[11px] font-bold text-amber-600 uppercase">Contacted</span>
                <span class="block text-xl font-extrabold text-amber-700 mt-1">{{ $stageCounts['contacted'] }}</span>
            </a>
            <a href="{{ route('admin.crm.index', ['status' => 'counselling_scheduled']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center hover:border-purple-500">
                <span class="text-[11px] font-bold text-purple-600 uppercase">Counselling</span>
                <span class="block text-xl font-extrabold text-purple-700 mt-1">{{ $stageCounts['counselling_scheduled'] }}</span>
            </a>
            <a href="{{ route('admin.crm.index', ['status' => 'won']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center hover:border-emerald-500">
                <span class="text-[11px] font-bold text-emerald-600 uppercase">Enrolled (Won)</span>
                <span class="block text-xl font-extrabold text-emerald-700 mt-1">{{ $stageCounts['won'] }}</span>
            </a>
            <a href="{{ route('admin.crm.index', ['status' => 'lost']) }}" class="bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-center hover:border-red-500">
                <span class="text-[11px] font-bold text-red-500 uppercase">Closed / Lost</span>
                <span class="block text-xl font-extrabold text-red-600 mt-1">{{ $stageCounts['lost'] }}</span>
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('admin.crm.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, email..." class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">

                <select name="centre_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none">
                    <option value="">All Centres / Online</option>
                    @foreach($centres as $c)
                    <option value="{{ $c->id }}" {{ request('centre_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="qualified" {{ request('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="counselling_scheduled" {{ request('status') === 'counselling_scheduled' ? 'selected' : '' }}>Counselling Scheduled</option>
                    <option value="won" {{ request('status') === 'won' ? 'selected' : '' }}>Enrolled (Won)</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>

                <div class="flex items-center space-x-2">
                    <button type="submit" class="w-full py-2 bg-primary-700 hover:bg-primary-800 text-white font-bold rounded-lg transition-colors">
                        Filter Leads
                    </button>
                    <a href="{{ route('admin.crm.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Leads Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 text-xs">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Aspirant / Contact</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Target & Interest</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Source / Page</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Assigned Counsellor</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-600 uppercase">Current Stage</th>
                        <th class="px-4 py-3 text-right font-bold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-900">{{ $lead->name }}</span>
                            <div class="text-gray-500 text-[11px]">{{ $lead->mobile }} • {{ $lead->email }}</div>
                            <div class="text-gray-400 text-[10px]">{{ $lead->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-800">{{ $lead->interest ?? 'UPSC General' }}</span>
                            <div class="text-gray-500 text-[11px]">{{ $lead->centre?->name ?? 'Online Campus' }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <span class="px-2 py-0.5 bg-gray-100 rounded text-[11px] font-semibold">{{ $lead->source }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.crm.assign', $lead) }}" method="POST">
                                @csrf
                                <select name="assigned_to" class="border border-gray-300 rounded text-[11px] py-1 px-1.5 focus:outline-none" onchange="this.form.submit()">
                                    <option value="">Unassigned</option>
                                    @foreach($counsellors as $c)
                                    <option value="{{ $c->id }}" {{ $lead->assigned_to == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.crm.stage', $lead) }}" method="POST">
                                @csrf
                                <select name="status" class="border border-gray-300 rounded text-[11px] font-bold py-1 px-1.5 focus:outline-none {{ $lead->status === 'won' ? 'text-emerald-700' : ($lead->status === 'lost' ? 'text-red-600' : 'text-gray-800') }}" onchange="this.form.submit()">
                                    <option value="new" {{ $lead->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $lead->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="qualified" {{ $lead->status === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="counselling_scheduled" {{ $lead->status === 'counselling_scheduled' ? 'selected' : '' }}>Counselling</option>
                                    <option value="won" {{ $lead->status === 'won' ? 'selected' : '' }}>Enrolled</option>
                                    <option value="lost" {{ $lead->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                            </form>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="tel:{{ $lead->mobile }}" class="inline-block px-2.5 py-1 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 rounded font-bold text-[11px]">
                                Call
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No leads found matching the filter criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $leads->links() }}
            </div>
        </div>

    </div>
</div>
@endsection
