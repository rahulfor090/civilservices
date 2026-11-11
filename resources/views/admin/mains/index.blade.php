@extends('layouts.admin')

@section('title', 'Mains Evaluation & Evaluators Manager')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Mains Evaluation & Evaluators Manager</h1>
            <p class="text-xs text-slate-500 mt-1">Manage subjective UPSC Mains answer copies, faculty evaluators, and rubric grading.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.mains.index', ['tab' => 'evaluators']) }}" 
               class="px-4 py-2 rounded-xl {{ $activeTab === 'evaluators' ? 'bg-brand-orange text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }} text-xs font-bold shadow-2xs flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Evaluator Faculty ({{ $stats['total_evaluators'] }})</span>
            </a>
            <a href="{{ route('admin.mains.index', ['tab' => 'queue']) }}" 
               class="px-4 py-2 rounded-xl {{ $activeTab === 'queue' ? 'bg-brand-navy text-white' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }} text-xs font-bold shadow-2xs flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Answer Copies Queue ({{ $stats['total_submissions'] }})</span>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Copies</div>
            <div class="text-2xl font-black text-brand-navy mt-1">{{ $stats['total_submissions'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Pending Assignment</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $stats['pending_assignment'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Under Evaluation</div>
            <div class="text-2xl font-black text-blue-700 mt-1">{{ $stats['under_evaluation'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Completed / Graded</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $stats['evaluated'] }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs">
            <div class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Faculty Evaluators</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $stats['total_evaluators'] }}</div>
        </div>
    </div>

    @if($activeTab === 'evaluators')
        <!-- ================= EVALUATOR FACULTY ROSTER TAB ================= -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900 tracking-tight">Active Faculty Evaluators Roster</h2>
                    <p class="text-xs text-slate-500">Staff members authorized to evaluate and grade UPSC Mains subjective copies.</p>
                </div>
            </div>

            <!-- Evaluators Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Evaluator Name & Email</th>
                            <th class="py-3.5 px-4">Contact Phone</th>
                            <th class="py-3.5 px-4">Assigned Copies</th>
                            <th class="py-3.5 px-4">Pending Copies</th>
                            <th class="py-3.5 px-4">Completed Copies</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($evaluators as $ev)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                        <span>{{ $ev->name }}</span>
                                        @if($ev->hasRole('super_admin'))
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $ev->email }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-mono text-slate-600">{{ $ev->mobile ?? '—' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800 font-mono">
                                    {{ $ev->assigned_copies_count ?? 0 }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded font-bold font-mono text-[11px] {{ ($ev->pending_copies_count ?? 0) > 0 ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $ev->pending_copies_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded font-bold font-mono text-[11px] bg-emerald-100 text-emerald-900">
                                        {{ $ev->completed_copies_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Active Evaluator
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if(!$ev->hasRole('super_admin'))
                                        <form action="{{ route('admin.mains.evaluators.destroy', $ev) }}" method="POST" onsubmit="return confirm('Revoke Evaluator role for {{ $ev->name }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-700">
                                                Revoke Role
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Primary Admin</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    No evaluators registered yet. Use the form below to add faculty members.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Add New Evaluator Faculty Form -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                        + Register New Evaluator Faculty Member
                    </h3>
                    <p class="text-xs text-slate-500">Create a login account for faculty members to evaluate student Mains copies with rubrics.</p>
                </div>

                <form action="{{ route('admin.mains.evaluators.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Faculty Full Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Dr. Rajesh Sharma (GS Paper 2 & Essay)" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address (Login ID) *</label>
                            <input type="email" name="email" required placeholder="e.g. rajesh.sharma@civilservicegurukul.com" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Mobile / WhatsApp Number</label>
                            <input type="text" name="mobile" placeholder="e.g. 9811020626" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Initial Password *</label>
                            <input type="password" name="password" required placeholder="Minimum 8 characters" class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-md transition-all">
                            Save & Register Evaluator &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @else
        <!-- ================= ANSWER COPY QUEUE TAB ================= -->
        <div class="space-y-4">
            <!-- Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.mains.index') }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ !request('status') ? 'bg-brand-navy text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    All Submissions ({{ $stats['total_submissions'] }})
                </a>
                <a href="{{ route('admin.mains.index', ['status' => 'submitted']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'submitted' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    Pending Assignment ({{ $stats['pending_assignment'] }})
                </a>
                <a href="{{ route('admin.mains.index', ['status' => 'assigned']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'assigned' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    Under Evaluation ({{ $stats['under_evaluation'] }})
                </a>
                <a href="{{ route('admin.mains.index', ['status' => 'evaluated']) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-semibold {{ request('status') === 'evaluated' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                    Evaluated / Graded ({{ $stats['evaluated'] }})
                </a>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-4">Roll No / Student</th>
                                <th class="py-3.5 px-4">Test Title & Paper</th>
                                <th class="py-3.5 px-4">Submitted At</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4">Assign Evaluator</th>
                                <th class="py-3.5 px-4">Score</th>
                                <th class="py-3.5 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($submissions as $sub)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-900 font-mono">#{{ $sub->candidate_roll_no }}</span>
                                        <div class="text-slate-500 text-[11px] font-semibold">{{ $sub->user->name }}</div>
                                        <div class="text-slate-400 text-[10px] font-mono">{{ $sub->user->email }}</div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="font-bold text-slate-900">{{ $sub->test->title }}</span>
                                        <div class="text-slate-500 text-[11px] uppercase font-mono">{{ $sub->test->gs_paper }} &bull; {{ $sub->test->total_marks }} Marks</div>
                                    </td>
                                    <td class="py-3.5 px-4 text-slate-500">
                                        {{ $sub->submitted_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($sub->status === 'evaluated')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Evaluated</span>
                                        @elseif($sub->status === 'assigned')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">Assigned</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Submitted</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <form action="{{ route('admin.mains.assign', $sub) }}" method="POST" class="flex items-center gap-1.5">
                                            @csrf
                                            <select name="evaluator_id" class="border border-slate-200 rounded-lg text-xs py-1 px-2 focus:outline-none bg-slate-50 font-semibold" onchange="this.form.submit()">
                                                <option value="">Unassigned</option>
                                                @foreach($evaluators as $ev)
                                                    <option value="{{ $ev->id }}" {{ $sub->evaluator_id == $ev->id ? 'selected' : '' }}>
                                                        {{ $ev->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">
                                        {{ $sub->total_score !== null ? $sub->total_score . ' / ' . $sub->test->total_marks : '—' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <a href="{{ route('admin.mains.evaluate', $sub) }}" class="inline-block px-3 py-1.5 bg-brand-navy hover:bg-brand-navyLight text-white rounded-lg font-bold text-xs shadow-xs transition-colors">
                                            {{ $sub->status === 'evaluated' ? 'Review Scoring' : 'Grade Copy &rarr;' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-400 text-xs">
                                        <p class="text-base font-semibold text-slate-600">No answer copies found in this queue</p>
                                        <p class="mt-1">Student submissions from the Mains portal will appear here for grading.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($submissions->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
