@extends('layouts.app')

@section('title', 'UPSC Mains Answer Evaluation Portal | CivilServiceGurukul')

@section('content')
<div class="bg-gray-50 py-10 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-bold text-primary-700 uppercase tracking-widest bg-primary-100 px-3 py-1 rounded-full">Subjective Test Series</span>
                <h1 class="text-3xl font-extrabold text-gray-900 mt-2">Mains Answer Copy Evaluation</h1>
                <p class="text-gray-600 text-sm mt-1">
                    Submit handwritten answer copies in PDF format. Evaluated within 48 hours with UPSC-calibrated 4-pillar rubrics and line-by-line annotations.
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 shadow-sm">
                    ← Back to LMS Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-center space-x-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Active Mains Tests -->
            <div class="lg:col-span-2 space-y-6">
                <h2 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Available Mains Question Papers</span>
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($tests as $test)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2 py-0.5 text-xs font-bold uppercase rounded bg-navy-100 text-navy-800">
                                    {{ strtoupper($test->gs_paper) }}
                                </span>
                                <span class="text-xs font-semibold text-gray-500">
                                    {{ $test->total_marks }} Marks • {{ $test->duration_minutes }} Min
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 leading-snug">
                                {{ $test->title }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-2">
                                Questions: {{ $test->questions_count }} questions
                            </p>
                        </div>

                        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-xs text-emerald-700 font-semibold">Active Submission</span>
                            <a href="{{ route('student.mains.show', $test) }}" class="px-3.5 py-1.5 bg-primary-700 hover:bg-primary-800 text-white rounded text-xs font-bold transition-colors">
                                View & Upload Copy →
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 p-8 text-center bg-white rounded-xl border border-gray-200 text-gray-500 text-sm">
                        No active Mains tests available at this moment.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Col: My Submitted Copies -->
            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center space-x-2">
                    <svg class="w-5 h-5 text-navy-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>My Answer Copies</span>
                </h2>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 divide-y divide-gray-100 overflow-hidden">
                    @forelse($submissions as $sub)
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">#{{ $sub->candidate_roll_no }}</span>
                            @if($sub->status === 'evaluated')
                            <span class="px-2 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800 rounded">Evaluated</span>
                            @elseif($sub->status === 'under_evaluation' || $sub->status === 'assigned')
                            <span class="px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-800 rounded">Under Evaluation</span>
                            @else
                            <span class="px-2 py-0.5 text-xs font-bold bg-blue-100 text-blue-800 rounded">Submitted</span>
                            @endif
                        </div>

                        <h4 class="text-sm font-semibold text-gray-900 leading-snug">
                            {{ $sub->test->title }}
                        </h4>

                        <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                            <span>{{ $sub->submitted_at->format('d M, h:i A') }}</span>
                            @if($sub->status === 'evaluated')
                            <span class="font-bold text-emerald-700">{{ $sub->total_score }} / {{ $sub->test->total_marks }}</span>
                            @endif
                        </div>

                        @if($sub->status === 'evaluated')
                        <div class="pt-2">
                            <a href="{{ route('student.mains.evaluated', $sub) }}" class="block text-center w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-bold transition-colors">
                                View Rubric Scorecard & Remarks
                            </a>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-6 text-center text-xs text-gray-500">
                        You have not submitted any answer copies yet. Select an active test paper to submit your handwritten copy.
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
