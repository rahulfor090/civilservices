@extends('layouts.admin')

@section('title', 'Edit Quiz - ' . $quiz->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.quizzes.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to All Quizzes</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">{{ $quiz->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-slate-500">
                <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-900 font-semibold">{{ $quiz->category->name ?? 'General' }}</span>
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold font-mono">{{ $quiz->questions->count() }} Questions</span>
                <span class="font-mono text-slate-700 font-bold">Total: {{ number_format($quiz->total_marks, 0) }} Marks</span>
                <span>&bull;</span>
                <span class="font-mono text-slate-700">{{ $quiz->duration_minutes }} Mins</span>
                <span>&bull;</span>
                <span class="font-mono text-slate-400">/quizzes/{{ $quiz->slug }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('quizzes.show', $quiz->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5">
                <span>Preview Student Test</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Quiz Settings Form -->
    <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                    Quiz Settings & Scoring Rules
                </h2>
                <span class="text-xs text-slate-400">Updated: {{ $quiz->updated_at->diffForHumans() }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category *</label>
                    <select name="quiz_category_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('quiz_category_id', $quiz->quiz_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Language *</label>
                    <select name="language_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id', $quiz->language_id) == $lang->id ? 'selected' : '' }}>
                                {{ $lang->name }} ({{ $lang->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Quiz Title *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $quiz->title) }}" 
                       required 
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug *</label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug', $quiz->slug) }}" 
                       required
                       class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Duration (Mins) *</label>
                    <input type="number" 
                           name="duration_minutes" 
                           value="{{ old('duration_minutes', $quiz->duration_minutes) }}" 
                           required 
                           min="1" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-bold focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Marks / Q *</label>
                    <input type="number" 
                           step="0.01" 
                           name="marks_per_question" 
                           value="{{ old('marks_per_question', $quiz->marks_per_question) }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Negative Marks *</label>
                    <input type="number" 
                           step="0.01" 
                           name="negative_marks_per_question" 
                           value="{{ old('negative_marks_per_question', $quiz->negative_marks_per_question) }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono text-red-600 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="published" {{ old('status', $quiz->status) == 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="draft" {{ old('status', $quiz->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $quiz->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instructions for Candidates</label>
                <textarea name="instructions" 
                          rows="2" 
                          class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed">{{ old('instructions', $quiz->instructions) }}</textarea>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-sm transition-all">
                    Update Quiz Settings
                </button>
            </div>
        </div>
    </form>

    <!-- Questions Management Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                    MCQ Question Bank ({{ $quiz->questions->count() }} Questions)
                </h2>
                <p class="text-xs text-slate-500">Each question is automatically evaluated against the official answer key with instant explanation.</p>
            </div>
        </div>

        <!-- Excel & CSV Bulk Question Import Card -->
        <div class="bg-gradient-to-br from-slate-900 via-brand-navy to-slate-950 rounded-2xl p-6 text-white border border-slate-800 shadow-xl space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0 mt-0.5 sm:mt-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold tracking-tight text-white flex items-center gap-2">
                            <span>Bulk Import Questions via Excel Sheet</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Fast Upload</span>
                        </h3>
                        <p class="text-xs text-slate-300">Upload multiple questions with correct answers (A, B, C, D) and explanations using our standardized Excel template.</p>
                    </div>
                </div>

                <div>
                    <a href="{{ route('admin.quizzes.template.download') }}" 
                       class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-sm transition shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Download Excel Template (.xlsx)</span>
                    </a>
                </div>
            </div>

            <!-- Upload Form -->
            <form action="{{ route('admin.quizzes.questions.import', $quiz) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                    <div class="sm:col-span-8">
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Select Filled Excel (.xlsx / .xls) or CSV Sheet</label>
                        <input type="file" 
                               name="excel_file" 
                               required 
                               accept=".xlsx,.xls,.csv" 
                               class="w-full text-xs text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700 bg-slate-950/70 border border-slate-700 rounded-xl p-1.5 focus:outline-none focus:ring-2 focus:ring-brand-orange">
                    </div>
                    <div class="sm:col-span-4 sm:self-end">
                        <button type="submit" 
                                class="w-full py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow-lg shadow-amber-600/30 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <span>Import Questions to Quiz</span>
                        </button>
                    </div>
                </div>

                <!-- Template Format Column Guide -->
                <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 text-[11px] text-slate-400 space-y-1.5">
                    <div class="font-bold text-slate-300 flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            <span>Excel Column Structure (10 Columns):</span>
                        </div>
                        <span class="text-[10px] text-slate-400">Supports up to 500 questions per sheet</span>
                    </div>
                    <div class="font-mono text-[10px] text-amber-300/90 overflow-x-auto whitespace-nowrap bg-slate-900/80 p-2 rounded-lg border border-slate-800">
                        question_text &bull; option_a &bull; option_b &bull; option_c &bull; option_d &bull; correct_option (A, B, C, D) &bull; explanation &bull; subject &bull; topic &bull; difficulty
                    </div>
                </div>
            </form>
        </div>

        <!-- Existing Questions List -->
        @forelse($quiz->questions as $index => $q)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="w-7 h-7 rounded-full bg-brand-navy text-white text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                            {{ $index + 1 }}
                        </span>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 uppercase">
                                    {{ $q->subject_tag ?? 'Current Affairs' }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold capitalize {{ $q->difficulty === 'hard' ? 'bg-red-100 text-red-800' : ($q->difficulty === 'easy' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $q->difficulty }}
                                </span>
                            </div>

                            <div class="text-slate-900 text-sm font-medium leading-relaxed">
                                {!! $q->stem !!}
                            </div>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('admin.quizzes.questions.detach', [$quiz, $q]) }}" method="POST" onsubmit="return confirm('Remove question from this quiz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-slate-400 hover:text-red-600 text-xs font-semibold p-1" title="Remove from quiz">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pl-10">
                    @foreach($q->options as $optIdx => $opt)
                        @php
                            $labels = ['A', 'B', 'C', 'D', 'E', 'F'];
                            $lbl = $labels[$optIdx] ?? ($optIdx + 1);
                        @endphp
                        <div class="p-2.5 rounded-xl text-xs flex items-center justify-between gap-2 border {{ $opt->is_correct ? 'bg-emerald-50/70 border-emerald-300 text-emerald-950 font-semibold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-bold {{ $opt->is_correct ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $lbl }}
                                </span>
                                <span>{{ $opt->option_text }}</span>
                            </div>
                            @if($opt->is_correct)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-200 text-emerald-800 shrink-0">
                                    Correct
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($q->explanation)
                    <div class="pl-10">
                        <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200 text-xs text-amber-950 leading-relaxed">
                            <strong class="text-amber-900 block mb-1">Official Solution & Explanation:</strong>
                            {!! nl2br(e($q->explanation)) !!}
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-xs text-slate-400">
                <p class="text-sm font-semibold text-slate-600">No questions added yet</p>
                <p class="mt-1">Add your first MCQ question using the form below.</p>
            </div>
        @endforelse

        <!-- Add New MCQ Question Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                    + Add New MCQ Question to Quiz
                </h3>
                <p class="text-xs text-slate-500">Provide the question stem, 4 options, mark the correct answer, and write the explanation.</p>
            </div>

            <form action="{{ route('admin.quizzes.questions.store', $quiz) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Subject / Dimension</label>
                        <input type="text" name="subject_tag" value="Current Affairs" placeholder="e.g. Current Affairs, Polity, Economy" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Topic / Specific Keyword</label>
                        <input type="text" name="topic_tag" placeholder="e.g. Electoral Bonds, Monetary Policy" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Difficulty *</label>
                        <select name="difficulty" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                            <option value="medium">Medium (UPSC Standard)</option>
                            <option value="easy">Easy (Direct Concept)</option>
                            <option value="hard">Hard (Advanced / Multi-statement)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Question Stem (Text / Statements) *</label>
                    <textarea name="stem" 
                              rows="3" 
                              required 
                              placeholder="With reference to..., consider the following statements:&#10;1. Statement one...&#10;2. Statement two...&#10;Which of the statements given above is/are correct?"
                              class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed"></textarea>
                </div>

                <!-- 4 Options with Radio Button for Correct Answer -->
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Answer Options (Select the Radio for Correct Answer) *
                    </label>

                    <div class="space-y-2.5">
                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <input type="radio" name="correct_option" value="0" checked id="opt_0" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <label for="opt_0" class="w-6 font-bold text-slate-600 text-xs shrink-0">A.</label>
                            <input type="text" name="options[0]" required placeholder="Option A text (e.g. 1 only)" class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        </div>

                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <input type="radio" name="correct_option" value="1" id="opt_1" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <label for="opt_1" class="w-6 font-bold text-slate-600 text-xs shrink-0">B.</label>
                            <input type="text" name="options[1]" required placeholder="Option B text (e.g. 2 only)" class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        </div>

                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <input type="radio" name="correct_option" value="2" id="opt_2" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <label for="opt_2" class="w-6 font-bold text-slate-600 text-xs shrink-0">C.</label>
                            <input type="text" name="options[2]" required placeholder="Option C text (e.g. Both 1 and 2)" class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        </div>

                        <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <input type="radio" name="correct_option" value="3" id="opt_3" class="h-4 w-4 text-emerald-600 focus:ring-emerald-500">
                            <label for="opt_3" class="w-6 font-bold text-slate-600 text-xs shrink-0">D.</label>
                            <input type="text" name="options[3]" required placeholder="Option D text (e.g. Neither 1 nor 2)" class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Detailed Explanation & Source Reference</label>
                    <textarea name="explanation" 
                              rows="3" 
                              placeholder="Explain why the marked answer is correct and why other statements are incorrect. Include relevant constitutional articles, Supreme Court rulings, or PIB sources..."
                              class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed"></textarea>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-md transition-all">
                        + Save & Add Question to Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
        <div>
            <h4 class="text-xs font-bold text-slate-700 uppercase">Danger Zone</h4>
            <p class="text-[11px] text-slate-500">Permanently delete this quiz and all student attempt records.</p>
        </div>

        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this quiz?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors">
                Delete Quiz
            </button>
        </form>
    </div>
</div>
@endsection
