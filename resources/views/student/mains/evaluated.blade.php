@extends('layouts.app')

@section('title', 'Evaluated Mains Answer Copy #' . $submission->candidate_roll_no . ' | CivilServiceGurukul')

@section('content')
<div class="bg-gray-50 py-10 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex text-xs font-semibold text-gray-500 mb-6 space-x-2">
            <a href="{{ route('student.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('student.mains.index') }}" class="hover:text-gray-700">Mains Evaluation</a>
            <span>/</span>
            <span class="text-gray-900">Evaluated Report #{{ $submission->candidate_roll_no }}</span>
        </nav>

        <!-- Top Scorecard Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-2.5 py-1 text-xs font-bold uppercase rounded bg-emerald-100 text-emerald-800">
                        Evaluated Scorecard
                    </span>
                    <h1 class="text-2xl font-extrabold text-gray-900 mt-2">{{ $submission->test->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 mt-2">
                        <span>Roll No: <strong class="text-gray-800">{{ $submission->candidate_roll_no }}</strong></span>
                        <span>•</span>
                        <span>Evaluated on: {{ $submission->evaluated_at ? $submission->evaluated_at->format('d M Y, h:i A') : 'Completed' }}</span>
                        <span>•</span>
                        <span>Evaluator: <strong class="text-gray-800">{{ $submission->evaluator?->name ?? 'Senior Academic Evaluator' }}</strong></span>
                    </div>
                </div>

                <!-- Big Score Badge -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center min-w-[160px]">
                    <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider block">Awarded Score</span>
                    <span class="text-4xl font-extrabold text-emerald-700 block mt-1">
                        {{ $submission->total_score }}
                    </span>
                    <span class="text-xs font-medium text-emerald-800">
                        out of {{ $submission->test->total_marks }} Marks
                    </span>
                </div>
            </div>

            <!-- General Remarks -->
            @if($submission->evaluator_remarks)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Evaluator's Overall Feedback & Strategy Advice:</h3>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-800 leading-relaxed italic">
                    "{{ $submission->evaluator_remarks }}"
                </div>
            </div>
            @endif
        </div>

        <!-- Question-wise Rubric Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 space-y-6">
            <h2 class="text-lg font-bold text-gray-900">UPSC Rubric Dimension Breakdown</h2>

            <div class="space-y-6">
                @foreach($submission->test->questions as $q)
                @php
                    $score = $submission->scores->firstWhere('mains_test_question_id', $q->id);
                @endphp
                <div class="border border-gray-200 rounded-xl p-5 bg-white space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-gray-100 gap-2">
                        <div>
                            <span class="text-xs font-bold text-primary-700 uppercase">Question {{ $q->question_number }}</span>
                            <h4 class="text-sm font-semibold text-gray-900 mt-0.5">{{ $q->stem }}</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-base font-extrabold text-primary-700">{{ $score?->total_question_score ?? 0 }}</span>
                            <span class="text-xs text-gray-500 font-medium">/ {{ $q->max_marks }} Marks</span>
                        </div>
                    </div>

                    <!-- 4 Rubrics Pills Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <span class="text-gray-500 block">Intro & Context</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $score?->intro_score ?? 0 }} Marks</span>
                        </div>
                        <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <span class="text-gray-500 block">Content & Examples</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $score?->content_score ?? 0 }} Marks</span>
                        </div>
                        <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <span class="text-gray-500 block">Structure & Diagrams</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $score?->structure_score ?? 0 }} Marks</span>
                        </div>
                        <div class="bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                            <span class="text-gray-500 block">Conclusion & Way Forward</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $score?->conclusion_score ?? 0 }} Marks</span>
                        </div>
                    </div>

                    @if($score?->evaluator_comments)
                    <div class="text-xs text-gray-600 bg-blue-50 border border-blue-100 p-3 rounded-lg">
                        <strong class="text-blue-900">Evaluator Note:</strong> {{ $score->evaluator_comments }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            <!-- Action buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('student.mains.index') }}" class="text-xs font-bold text-gray-600 hover:text-gray-900">
                    ← Back to Mains Test Series
                </a>
                <div class="flex items-center space-x-3">
                    <button onclick="window.print()" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                        Print Scorecard
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
