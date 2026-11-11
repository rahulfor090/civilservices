@extends('layouts.admin')

@section('title', 'Create New Quiz')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to Quizzes</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">
                {{ $defaultCategory && $defaultCategory->slug === 'daily-current-affairs-quiz' ? 'Create Daily Current Affairs Quiz' : 'Create New Quiz / Mock Test' }}
            </h1>
            <p class="text-xs text-slate-500">Configure quiz metadata, scoring rules, timer, and instructions.</p>
        </div>
        <div>
            <a href="{{ route('admin.quizzes.template.download') }}" 
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold flex items-center gap-2 shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Download Excel Template (.xlsx)</span>
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs space-y-1">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.quizzes.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                1. Quiz Identity & Classification
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category *</label>
                    <select name="quiz_category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('quiz_category_id', $defaultCategory?->id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Language *</label>
                    <select name="language_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>
                                {{ $lang->name }} ({{ $lang->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Quiz Title *</label>
                @php
                    $defaultTitle = ($defaultCategory && $defaultCategory->slug === 'daily-current-affairs-quiz')
                        ? 'Daily Current Affairs Quiz — ' . date('d F Y')
                        : 'UPSC Prelims GS Mock Test';
                @endphp
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $defaultTitle) }}" 
                       required 
                       placeholder="e.g. Daily Current Affairs Quiz — {{ date('d F Y') }}"
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug (Auto-generated if empty)</label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug') }}" 
                       placeholder="e.g. daily-current-affairs-quiz-{{ date('d-m-Y') }}"
                       class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instructions for Candidates</label>
                <textarea name="instructions" 
                          rows="3" 
                          class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed">{{ old('instructions', '1. Each question carries 2.00 marks.\n2. Penalty of 0.66 marks for every wrong answer (1/3rd negative marking).\n3. Unanswered questions attract zero marks.\n4. Click Submit once you complete all questions.') }}</textarea>
            </div>
        </div>

        <!-- Scoring & Timer Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                2. Timer, Marking Scheme & Settings
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Duration (Minutes) *</label>
                    <input type="number" 
                           name="duration_minutes" 
                           value="{{ old('duration_minutes', 15) }}" 
                           required 
                           min="1" 
                           max="300"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Marks / Question *</label>
                    <input type="number" 
                           step="0.01" 
                           name="marks_per_question" 
                           value="{{ old('marks_per_question', '2.00') }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Negative Marks *</label>
                    <input type="number" 
                           step="0.01" 
                           name="negative_marks_per_question" 
                           value="{{ old('negative_marks_per_question', '0.66') }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono text-red-600 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pass % *</label>
                    <input type="number" 
                           name="pass_percentage" 
                           value="{{ old('pass_percentage', 40) }}" 
                           required 
                           min="1" 
                           max="100"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_free" name="is_free" value="1" checked class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                    <label for="is_free" class="text-xs font-semibold text-slate-700">Free Tier (No payment required)</label>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="allow_reattempt" name="allow_reattempt" value="1" checked class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                    <label for="allow_reattempt" class="text-xs font-semibold text-slate-700">Allow Re-attempts</label>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="shuffle_questions" name="shuffle_questions" value="1" class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                    <label for="shuffle_questions" class="text-xs font-semibold text-slate-700">Shuffle Question Order</label>
                </div>
            </div>

            <div class="pt-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Publication Status *</label>
                <select name="status" required class="w-full sm:w-64 px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                    <option value="published" {{ old('status', 'published') == 'published' ? 'selected' : '' }}>Published (Live immediately)</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (Hide from public)</option>
                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.quizzes.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-md transition-all">
                Save & Start Adding MCQs &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
