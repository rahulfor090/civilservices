@extends('layouts.admin')

@section('title', 'Grade Answer Copy #' . $submission->candidate_roll_no)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="text-xs font-semibold text-gray-500 mb-1 space-x-1">
                    <a href="{{ route('admin.mains.index') }}" class="hover:text-gray-700">← Back to Queue</a>
                </nav>
                <h1 class="text-2xl font-extrabold text-gray-900">UPSC Rubric Evaluation Sheet</h1>
                <p class="text-xs text-gray-500 mt-0.5">
                    Candidate: <strong class="text-gray-800">{{ $submission->user->name }}</strong> (Roll No: #{{ $submission->candidate_roll_no }}) • Test: <strong class="text-gray-800">{{ $submission->test->title }}</strong>
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-1 text-xs font-bold rounded {{ $submission->status === 'evaluated' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                    Status: {{ ucfirst($submission->status) }}
                </span>
            </div>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs font-semibold">
            Please fix the errors below before submitting the evaluation.
        </div>
        @endif

        <form action="{{ route('admin.mains.evaluate.store', $submission) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Question by question rubric sheet -->
            <div class="space-y-6">
                @foreach($submission->test->questions as $index => $q)
                @php
                    $existing = $submission->scores->firstWhere('mains_test_question_id', $q->id);
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                    <input type="hidden" name="rubrics[{{ $index }}][question_id]" value="{{ $q->id }}">

                    <div class="flex items-start justify-between pb-3 border-b border-gray-100 gap-4">
                        <div>
                            <span class="text-xs font-bold text-primary-700 uppercase">Question {{ $q->question_number }}</span>
                            <p class="text-sm font-semibold text-gray-900 mt-1">{{ $q->stem }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-xs font-bold text-gray-500 uppercase">Max Marks</span>
                            <span class="block text-base font-extrabold text-gray-800">{{ $q->max_marks }}</span>
                        </div>
                    </div>

                    <!-- 4 Rubrics Input Columns -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Intro & Context (20%)</label>
                            <input type="number" step="0.5" min="0" max="{{ $q->max_marks * 0.3 }}" name="rubrics[{{ $index }}][intro_score]" value="{{ old("rubrics.{$index}.intro_score", $existing?->intro_score ?? 1.5) }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Content & Data (50%)</label>
                            <input type="number" step="0.5" min="0" max="{{ $q->max_marks * 0.6 }}" name="rubrics[{{ $index }}][content_score]" value="{{ old("rubrics.{$index}.content_score", $existing?->content_score ?? 4.0) }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Structure & Diagram (20%)</label>
                            <input type="number" step="0.5" min="0" max="{{ $q->max_marks * 0.3 }}" name="rubrics[{{ $index }}][structure_score]" value="{{ old("rubrics.{$index}.structure_score", $existing?->structure_score ?? 1.5) }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Conclusion (10%)</label>
                            <input type="number" step="0.5" min="0" max="{{ $q->max_marks * 0.2 }}" name="rubrics[{{ $index }}][conclusion_score]" value="{{ old("rubrics.{$index}.conclusion_score", $existing?->conclusion_score ?? 1.0) }}" class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 uppercase mb-1">Specific Evaluator Margin Comment</label>
                        <input type="text" name="rubrics[{{ $index }}][comments]" value="{{ old("rubrics.{$index}.comments", $existing?->evaluator_comments) }}" placeholder="e.g. Good introduction; substantiate arguments with Law Commission recommendations." class="w-full px-3 py-1.5 border border-gray-300 rounded text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Overall Remarks & Evaluated Copy -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Overall Academic Assessment</h3>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Evaluator Comprehensive Remarks *</label>
                    <textarea name="general_remarks" rows="3" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Provide qualitative advice on answer presentation, diagram usage, and time management...">{{ old('general_remarks', $submission->evaluator_remarks ?? 'Good conceptual clarity. Focus on structuring answers into sub-headings with bulleted points.') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Upload Annotated / Evaluated PDF Copy (Optional)</label>
                    <input type="file" name="evaluated_copy_pdf" accept=".pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-xs text-gray-500">Upon submission, candidate will immediately receive notifications and can view scorecard.</span>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow transition-colors">
                        Publish Evaluated Scorecard
                    </button>
                </div>
            </div>

        </form>
</div>
@endsection
