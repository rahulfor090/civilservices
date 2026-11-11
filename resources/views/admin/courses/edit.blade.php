@extends('layouts.admin')

@section('title', 'Edit Course - ' . $course->title)

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to All Courses</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">{{ $course->title }}</h1>
            <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-slate-500">
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold uppercase">{{ str_replace('_', ' ', $course->target_exam) }}</span>
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold capitalize">{{ $course->level }}</span>
                <span class="font-mono text-slate-700 font-bold">₹{{ number_format($course->price, 0) }}</span>
                <span>&bull;</span>
                <span class="font-mono text-slate-400">/courses/{{ $course->slug }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('student.courses.show', $course->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold flex items-center gap-1.5">
                <span>View LMS Player</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Course Edit Form -->
    <form action="{{ route('admin.courses.update', $course) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">
                    Course Information & Pricing
                </h2>
                <span class="text-xs text-slate-400">Last updated: {{ $course->updated_at->diffForHumans() }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Language *</label>
                    <select name="language_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id', $course->language_id) == $lang->id ? 'selected' : '' }}>
                                {{ $lang->name }} ({{ $lang->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Exam *</label>
                    <select name="target_exam" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="upsc_cse" {{ old('target_exam', $course->target_exam) == 'upsc_cse' ? 'selected' : '' }}>UPSC CSE (IAS / IPS)</option>
                        <option value="uppcs" {{ old('target_exam', $course->target_exam) == 'uppcs' ? 'selected' : '' }}>UPPSC (Uttar Pradesh)</option>
                        <option value="bpsc" {{ old('target_exam', $course->target_exam) == 'bpsc' ? 'selected' : '' }}>BPSC (Bihar PCS)</option>
                        <option value="ras" {{ old('target_exam', $course->target_exam) == 'ras' ? 'selected' : '' }}>RAS (Rajasthan)</option>
                        <option value="mppsc" {{ old('target_exam', $course->target_exam) == 'mppsc' ? 'selected' : '' }}>MPPSC (Madhya Pradesh)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Level *</label>
                    <select name="level" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="foundation" {{ old('level', $course->level) == 'foundation' ? 'selected' : '' }}>Foundation / GS Integrated (Pre+Mains)</option>
                        <option value="prelims" {{ old('level', $course->level) == 'prelims' ? 'selected' : '' }}>Prelims Specialized / Crash Course</option>
                        <option value="mains" {{ old('level', $course->level) == 'mains' ? 'selected' : '' }}>Mains Mentorship & Answer Writing</option>
                        <option value="optional" {{ old('level', $course->level) == 'optional' ? 'selected' : '' }}>Optional Subject Program</option>
                        <option value="interview" {{ old('level', $course->level) == 'interview' ? 'selected' : '' }}>Interview Guidance / DAF</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Title *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $course->title) }}" 
                       required 
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug *</label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug', $course->slug) }}" 
                       required
                       class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Selling Fee (₹) *</label>
                    <input type="number" 
                           step="0.01" 
                           name="price" 
                           value="{{ old('price', $course->price) }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Original MRP (₹)</label>
                    <input type="number" 
                           step="0.01" 
                           name="mrp" 
                           value="{{ old('mrp', $course->mrp) }}" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Validity (Days) *</label>
                    <input type="number" 
                           name="validity_days" 
                           value="{{ old('validity_days', $course->validity_days) }}" 
                           required 
                           min="1" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="published" {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" 
                       id="is_featured" 
                       name="is_featured" 
                       value="1" 
                       {{ old('is_featured', $course->is_featured) ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                <label for="is_featured" class="text-xs font-semibold text-slate-700">
                    Feature this course on public homepage banner cards
                </label>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Short Excerpt / Synopsis</label>
                <textarea name="excerpt" 
                          rows="2" 
                          class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed">{{ old('excerpt', $course->excerpt) }}</textarea>
            </div>

            <!-- Rich HTML Course Description -->
            <div>
                @include('admin.articles._editor', [
                    'inputName' => 'description',
                    'inputLabel' => 'Course Overview & Curriculum Details (Rich HTML)',
                    'initialContent' => old('description', $course->description),
                    'editorRequired' => false
                ])
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-md transition-all">
                    Update Course Information &rarr;
                </button>
            </div>
        </div>
    </form>

    <!-- Batches & Curriculum Manager -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Classroom Batches & Curriculum Lessons</h2>
                <p class="text-xs text-slate-500">Configure batch schedules, faculty, and upload video lessons or PDF notes.</p>
            </div>
        </div>

        <!-- Existing Batches List -->
        @forelse($course->batches as $batch)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- Batch Header Bar -->
                <div class="p-5 bg-slate-50 border-b border-slate-200/80 flex flex-wrap items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2.5">
                            <span class="px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-slate-200 text-slate-800">
                                {{ $batch->batch_code }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $batch->title }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $batch->delivery_mode === 'online' ? 'bg-indigo-100 text-indigo-800' : ($batch->delivery_mode === 'offline' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                                {{ ucfirst($batch->delivery_mode) }} Mode
                            </span>
                            @if($batch->is_enrolment_open)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Admissions Open</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-800">Admissions Closed</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 flex flex-wrap items-center gap-3 pt-0.5">
                            @if($batch->centre)
                                <span><strong>Centre:</strong> {{ $batch->centre->name }} ({{ $batch->centre->city }})</span>
                            @elseif($batch->centre_name)
                                <span><strong>Centre:</strong> {{ $batch->centre_name }}</span>
                            @endif
                            @if($batch->start_date)
                                <span><strong>Starts:</strong> {{ $batch->start_date->format('d M Y') }}</span>
                            @endif
                            @if($batch->class_timing)
                                <span><strong>Timing:</strong> {{ $batch->class_timing }}</span>
                            @endif
                            @if($batch->faculty_names)
                                <span><strong>Faculty:</strong> {{ $batch->faculty_names }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form action="{{ route('admin.courses.batches.destroy', $batch) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this batch and all its lessons?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold">
                                Delete Batch
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lessons in Batch -->
                <div class="p-5 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <span>Curriculum Planner & Scheduled Sessions</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-brand-navy/10 text-brand-navy">
                                    {{ $batch->lessons->count() }} items
                                </span>
                            </h4>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Scheduled sessions auto-unlock at designated date/time (e.g. 5:00 PM video &rarr; 5:30 PM quiz &rarr; Mains question &rarr; Phone discussion window).
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <a href="{{ route('courses.calendar.ics', $course) }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-[11px] shadow-xs">
                                <svg class="w-3.5 h-3.5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Export .ICS Calendar</span>
                            </a>
                        </div>
                    </div>

                    @if($batch->lessons->count() > 0)
                        <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden bg-white shadow-xs">
                            @foreach($batch->lessons->sortBy(['scheduled_at', 'sort_order']) as $lesson)
                                @php
                                    $effType = $lesson->effective_type;
                                    $isLiveOrPhone = in_array($effType, ['live', 'phone_session']);
                                    $isQuiz = $effType === 'quiz';
                                    $isMains = $effType === 'mains';
                                    $isTestSeries = $effType === 'test_series';
                                @endphp
                                <div class="p-3.5 flex flex-col md:flex-row md:items-center justify-between gap-3 hover:bg-slate-50/80 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0
                                            {{ $effType === 'video' ? 'bg-red-50 text-red-700 border border-red-200' : '' }}
                                            {{ $effType === 'live' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                                            {{ $effType === 'quiz' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                            {{ $effType === 'mains' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                            {{ $effType === 'phone_session' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                            {{ $effType === 'test_series' ? 'bg-orange-50 text-orange-700 border border-orange-200' : '' }}
                                            {{ $effType === 'pdf' ? 'bg-slate-100 text-slate-700 border border-slate-200' : '' }}">
                                            <span>#{{ $lesson->sort_order }}</span>
                                        </div>

                                        <div class="space-y-1">
                                            <div class="font-bold text-slate-900 text-xs flex flex-wrap items-center gap-2">
                                                <span>{{ $lesson->title }}</span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                    {{ $effType === 'video' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $effType === 'live' ? 'bg-purple-100 text-purple-800' : '' }}
                                                    {{ $effType === 'quiz' ? 'bg-amber-100 text-amber-800' : '' }}
                                                    {{ $effType === 'mains' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                    {{ $effType === 'phone_session' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $effType === 'test_series' ? 'bg-orange-100 text-orange-800' : '' }}
                                                    {{ $effType === 'pdf' ? 'bg-slate-200 text-slate-800' : '' }}">
                                                    {{ $lesson->type_badge_label }}
                                                </span>

                                                @if($lesson->scheduled_at)
                                                    @if($lesson->is_unlocked)
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            Unlocked & Live
                                                        </span>
                                                    @else
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                            Unlocks: {{ $lesson->scheduled_at->format('d M Y, h:i A') }}
                                                        </span>
                                                    @endif
                                                @endif

                                                @if($lesson->is_free_preview)
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Free Preview</span>
                                                @endif
                                            </div>

                                            <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-3">
                                                <span>⏱ {{ $lesson->duration_minutes }} mins</span>

                                                @if($lesson->scheduled_date)
                                                    <span>&bull;</span>
                                                    <span class="font-medium text-slate-700">
                                                        📅 {{ \Carbon\Carbon::parse($lesson->scheduled_date)->format('D, d M Y') }}
                                                        @if($lesson->scheduled_time)
                                                            at {{ $lesson->scheduled_time }}
                                                        @endif
                                                    </span>
                                                @endif

                                                @if($lesson->alert_minutes_before)
                                                    <span>&bull;</span>
                                                    <span class="text-indigo-600 font-medium">
                                                        🔔 Alert: {{ $lesson->alert_minutes_before }}m before
                                                        @if($lesson->alert_time)
                                                            ({{ $lesson->alert_time->format('h:i A') }})
                                                        @endif
                                                    </span>
                                                @endif

                                                @if($effType === 'phone_session')
                                                    <span>&bull;</span>
                                                    <span class="text-blue-700 font-semibold bg-blue-50 px-2 py-0.5 rounded">
                                                        📞 Mentor: {{ $lesson->mentor_name ?? 'Senior Faculty' }} ({{ $lesson->mentor_phone ?? 'Phone Session' }})
                                                        @if($lesson->call_window)
                                                            &bull; Window: {{ $lesson->call_window }}
                                                        @endif
                                                    </span>
                                                @elseif($effType === 'live')
                                                    <span>&bull;</span>
                                                    <span class="text-purple-700 font-semibold bg-purple-50 px-2 py-0.5 rounded">
                                                        🎥 {{ $lesson->meeting_platform ?? 'Live Video' }}: <a href="{{ $lesson->meeting_link }}" target="_blank" class="underline">{{ Str::limit($lesson->meeting_link, 30) }}</a>
                                                        @if($lesson->meeting_password)
                                                            (Code: {{ $lesson->meeting_password }})
                                                        @endif
                                                    </span>
                                                @elseif($effType === 'quiz' && $lesson->quiz)
                                                    <span>&bull;</span>
                                                    <span class="text-amber-800 font-semibold bg-amber-50 px-2 py-0.5 rounded">
                                                        📝 Quiz: #{{ $lesson->quiz->id }} {{ $lesson->quiz->title }} ({{ $lesson->quiz->questions_count ?? $lesson->quiz->questions()->count() }} MCQs)
                                                    </span>
                                                @elseif($effType === 'mains')
                                                    <span>&bull;</span>
                                                    <span class="text-emerald-800 font-semibold bg-emerald-50 px-2 py-0.5 rounded">
                                                        ✍️ Mains: {{ $lesson->mains_marks ?? 15 }} Marks ({{ $lesson->mains_word_limit ?? 250 }} words)
                                                        @if($lesson->mainsTest)
                                                            &bull; Test: {{ $lesson->mainsTest->title }}
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 self-end md:self-center">
                                        <form action="{{ route('admin.courses.batches.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Remove this session from batch?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition-colors" title="Delete Session">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center text-xs text-slate-500">
                            <p class="font-bold text-slate-700">No scheduled lessons or interactive sessions yet</p>
                            <p class="mt-1">Add pre-recordings, daily quizzes, mains questions, or phone interactive sessions below to build the day-by-day planner.</p>
                        </div>
                    @endif

                    <!-- Add Lesson / Scheduled Activity Form -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/90 shadow-2xs"
                         x-data="{ 
                             activityType: 'video', 
                             selectedQuizId: '', 
                             quizzesList: {{ Js::from($quizzes->map(fn($q) => ['id' => $q->id, 'title' => $q->title, 'duration_minutes' => $q->duration_minutes])) }},
                             onQuizSelected() {
                                 const q = this.quizzesList.find(item => item.id == this.selectedQuizId);
                                 if (q) {
                                     const titleInput = $refs.titleInput;
                                     if (!titleInput.value || titleInput.value.startsWith('Daily Quiz') || titleInput.value.startsWith('Quiz:')) {
                                         titleInput.value = 'Daily Practice Quiz: ' + q.title;
                                     }
                                     $refs.durationInput.value = q.duration_minutes || 20;
                                 }
                             },
                             onTypeChanged() {
                                 const titleInput = $refs.titleInput;
                                 if (this.activityType === 'phone_session' && (!titleInput.value || titleInput.value.includes('Lecture'))) {
                                     titleInput.value = '1-on-1 Mentor Strategy & Doubt Phone Discussion';
                                     $refs.durationInput.value = 60;
                                 } else if (this.activityType === 'mains' && (!titleInput.value || titleInput.value.includes('Lecture'))) {
                                     titleInput.value = 'Mains Daily Answer Writing Practice';
                                     $refs.durationInput.value = 45;
                                 } else if (this.activityType === 'live' && (!titleInput.value || titleInput.value.includes('Lecture'))) {
                                     titleInput.value = 'Live Interactive Doubt Class';
                                     $refs.durationInput.value = 60;
                                 } else if (this.activityType === 'video' && (!titleInput.value || titleInput.value.includes('Discussion'))) {
                                     titleInput.value = 'Lecture: GS Foundation';
                                     $refs.durationInput.value = 90;
                                 }
                             }
                         }">
                        <form action="{{ route('admin.courses.batches.lessons.store', $batch) }}" 
                              method="POST" 
                              class="space-y-4">
                            @csrf

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/80 pb-2.5">
                                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-orange"></span>
                                    <span>Schedule Curriculum Session for {{ $batch->batch_code }}</span>
                                </span>
                                <span class="text-[11px] font-semibold text-slate-500">
                                    All session types: Video, Live, Daily Quiz, Mains, Phone Call Window
                                </span>
                            </div>

                            <!-- Primary Selectors: Title, Activity Type, Duration -->
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                <div class="sm:col-span-5">
                                    <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Session Title *</label>
                                    <input type="text" 
                                           x-ref="titleInput"
                                           name="title" 
                                           required 
                                           placeholder="e.g. Lecture 01: Preamble & Constitutional Philosophy" 
                                           class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                                </div>

                                <div class="sm:col-span-4">
                                    <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Activity / Session Type *</label>
                                    <select name="activity_type" 
                                            x-model="activityType"
                                            @change="onTypeChanged()"
                                            required 
                                            class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-bold">
                                        <option value="video">🎬 Pre-Recorded Video Lecture (Auto-Publish)</option>
                                        <option value="live">🎥 Live Class / 1-to-1 Interactive Video</option>
                                        <option value="quiz">📝 Timed Daily Practice Quiz</option>
                                        <option value="mains">✍️ Mains Question & Copy Evaluation</option>
                                        <option value="phone_session">📞 Phone Interactive Session (Mentor Call Window)</option>
                                        <option value="test_series">🏆 Mock Test Series / Full Length Paper</option>
                                        <option value="pdf">📄 Study Material / PDF Class Handout</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-3">
                                    <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Duration (Mins) *</label>
                                    <input type="number" 
                                           x-ref="durationInput"
                                           name="duration_minutes" 
                                           value="90" 
                                           min="1" 
                                           required 
                                           class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono font-semibold">
                                </div>
                            </div>

                            <!-- Timeline & Planner Scheduling (Date, Time, Alert Notification) -->
                            <div class="p-3.5 bg-indigo-50/60 border border-indigo-100 rounded-xl space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-indigo-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Planner Scheduling & Advance Alerts</span>
                                    </span>
                                    <span class="text-[10px] text-indigo-700 font-medium">Leave empty for instant unlock on enrollment</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Scheduled Date</label>
                                        <input type="date" 
                                               name="scheduled_date" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-indigo-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                                        <span class="text-[9px] text-slate-500">e.g. 2026-12-20 (Course start date)</span>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Scheduled Time (Auto-Unlock)</label>
                                        <input type="time" 
                                               name="scheduled_time" 
                                               value="17:00"
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-indigo-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                                        <span class="text-[9px] text-slate-500">e.g. 17:00 (5:00 PM video, 5:30 PM quiz)</span>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Pre-Session Alert (Mins Before)</label>
                                        <input type="number" 
                                               name="alert_minutes_before" 
                                               value="30" 
                                               min="0"
                                               max="1440"
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-indigo-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                                        <span class="text-[9px] text-indigo-700 font-medium">Alerts at 4:30 PM for 5:00 PM session</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Phone Interactive Session Box -->
                            <div x-show="activityType === 'phone_session'" 
                                 x-transition
                                 class="p-4 bg-blue-50/80 border border-blue-200 rounded-xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-blue-900 uppercase flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span>Phone Interactive Session Details (1-on-1 Direct Discussion)</span>
                                    </span>
                                    <span class="text-[10px] text-blue-700 font-semibold bg-white px-2 py-0.5 rounded border border-blue-200">
                                        Dedicated Short Direct Call Window
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Mentor / Faculty Name *</label>
                                        <input type="text" 
                                               name="mentor_name" 
                                               placeholder="e.g. Dr. Verma (Senior Faculty)" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-blue-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Dedicated Phone Number *</label>
                                        <input type="text" 
                                               name="mentor_phone" 
                                               placeholder="e.g. +91 98765 43210" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-blue-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono font-bold">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Direct Call Window *</label>
                                        <input type="text" 
                                               name="call_window" 
                                               placeholder="e.g. 06:00 PM - 07:00 PM (1 Hour)" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-blue-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-blue-900 uppercase mb-1">Call Guidelines & Instructions for Students</label>
                                    <textarea name="session_instructions" 
                                              rows="2" 
                                              placeholder="e.g. Keep your notes and specific doubt points ready before calling. Call window is limited to 10 minutes per candidate for personalized guidance." 
                                              class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-blue-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>

                            <!-- Live Class / 1-to-1 Interactive Video Box -->
                            <div x-show="activityType === 'live'" 
                                 x-transition
                                 class="p-4 bg-purple-50/80 border border-purple-200 rounded-xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-purple-900 uppercase flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span>Live Class / 1-to-1 Video Meeting Configuration</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Meeting Platform</label>
                                        <select name="meeting_platform" class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-purple-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500 font-medium">
                                            <option value="Google Meet">Google Meet</option>
                                            <option value="Zoom">Zoom Meeting</option>
                                            <option value="YouTube Live">YouTube Live Stream</option>
                                            <option value="MS Teams">Microsoft Teams</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Meeting Join URL *</label>
                                        <input type="text" 
                                               name="meeting_link" 
                                               placeholder="https://meet.google.com/xyz-abc or Zoom link" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-purple-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Passcode / Meeting PIN</label>
                                        <input type="text" 
                                               name="meeting_password" 
                                               placeholder="e.g. CSG2026" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-purple-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500 font-mono">
                                    </div>
                                </div>
                            </div>

                            <!-- Mains Answer Writing Box -->
                            <div x-show="activityType === 'mains'" 
                                 x-transition
                                 class="p-4 bg-emerald-50/80 border border-emerald-200 rounded-xl space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-emerald-900 uppercase flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Mains Subjective Question & Copy Submission</span>
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-emerald-900 uppercase mb-1">Link CMS Mains Test (Optional)</label>
                                        <select name="mains_test_id" class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-emerald-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                            <option value="">-- Or enter custom question below --</option>
                                            @foreach($mainsTests as $mt)
                                                <option value="{{ $mt->id }}">#{{ $mt->id }} &bull; {{ $mt->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-emerald-900 uppercase mb-1">Total Marks</label>
                                        <input type="number" name="mains_marks" value="15" min="5" max="250" class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-emerald-300 text-slate-900 font-mono">
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-emerald-900 uppercase mb-1">Word Limit</label>
                                        <input type="number" name="mains_word_limit" value="250" min="50" max="2000" class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-emerald-300 text-slate-900 font-mono">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-emerald-900 uppercase mb-1">Mains Question Text *</label>
                                    <textarea name="mains_question_text" rows="3" placeholder="e.g. Discuss the constitutional significance of the Basic Structure doctrine with landmark judgments. (15 Marks, 250 Words)" class="w-full px-3 py-2 text-xs rounded-lg bg-white border border-emerald-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                                </div>
                            </div>

                            <!-- Quiz Selection Box -->
                            <div x-show="activityType === 'quiz'" 
                                 x-transition
                                 class="p-4 bg-amber-50/80 border border-amber-200 rounded-xl space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-[10px] font-bold text-amber-900 uppercase">
                                        Select Daily Practice Quiz to Associate *
                                    </label>
                                    <a href="{{ route('admin.quizzes.create') }}" target="_blank" class="text-[10px] font-bold text-amber-700 hover:text-amber-900 underline inline-flex items-center gap-1">
                                        <span>+ Create New Quiz in CMS</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                </div>
                                <select name="quiz_id" 
                                        x-model="selectedQuizId"
                                        @change="onQuizSelected()"
                                        :required="activityType === 'quiz'"
                                        class="w-full px-3 py-2 text-xs rounded-lg bg-white border border-amber-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 font-medium">
                                    <option value="">-- Select a Timed Daily Quiz from Library --</option>
                                    @foreach($quizzes as $q)
                                        <option value="{{ $q->id }}">
                                            #{{ $q->id }} &bull; {{ $q->title }} ({{ strtoupper($q->language?->code ?? 'EN') }}) &bull; {{ $q->duration_minutes }}m ({{ (int)$q->total_marks }} Marks)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-amber-800">
                                    Students can take this timed quiz immediately after watching the pre-recorded lecture.
                                </p>
                            </div>

                            <!-- Video / Document URL Box -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" x-show="activityType === 'video' || activityType === 'pdf' || activityType === 'test_series'">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-600 uppercase mb-0.5">
                                        <span x-show="activityType === 'video'">Video Lecture Stream URL (YouTube, Vimeo, HLS)</span>
                                        <span x-show="activityType === 'pdf'">PDF Document URL / Study Handout</span>
                                        <span x-show="activityType === 'test_series'">Test Series Portal URL / Link</span>
                                    </label>
                                    <input type="text" 
                                           name="video_url" 
                                           placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..." 
                                           class="w-full px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                                    <p class="text-[10px] text-slate-400 mt-1" x-show="activityType === 'video'">
                                        Enter any YouTube link (e.g. <code>watch?v=...</code>, <code>youtu.be/...</code>) — system will automatically convert it to an HD embed player.
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 pt-4">
                                    <input type="checkbox" id="preview_{{ $batch->id }}" name="is_free_preview" value="1" class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                                    <label for="preview_{{ $batch->id }}" class="text-xs font-medium text-slate-700">Free Preview / Open Demo</label>
                                </div>
                            </div>

                            <div class="flex justify-end pt-2 border-t border-slate-200/60">
                                <button type="submit" class="px-5 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                                    <span>+ Add & Schedule Session</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-xs text-slate-400">
                <p class="text-sm font-semibold text-slate-600">No batches configured for this course yet</p>
                <p class="mt-1">Add a batch below to allow students to enroll and access lessons.</p>
            </div>
        @endforelse

        <!-- Create New Batch Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                + Add Another Classroom / Online Batch
            </h3>

            <form action="{{ route('admin.courses.batches.store', $course) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batch Code *</label>
                        <input type="text" 
                               name="batch_code" 
                               required 
                               placeholder="e.g. BATCH-GS-2026-E02" 
                               class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batch Title *</label>
                        <input type="text" 
                               name="title" 
                               required 
                               placeholder="e.g. Evening Batch (Karol Bagh Centre & Live Streaming)" 
                               class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Delivery Mode *</label>
                        <select name="delivery_mode" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                            <option value="online">Online (Live / Recorded)</option>
                            <option value="offline">Offline (Classroom Centre)</option>
                            <option value="hybrid">Hybrid (Offline + Live Stream)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assigned Centre</label>
                        <select name="centre_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                            <option value="">None / National Online</option>
                            @foreach($centres as $centre)
                                <option value="{{ $centre->id }}">{{ $centre->name }} ({{ $centre->city }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batch Start Date</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batch End Date</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Class Timing</label>
                        <input type="text" name="class_timing" placeholder="e.g. 05:00 PM - 07:30 PM (Mon-Sat)" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Faculty Names</label>
                        <input type="text" name="faculty_names" placeholder="e.g. Dr. Verma, Sharma Sir, Tiwari Sir" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_enrolment_open" name="is_enrolment_open" value="1" checked class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                        <label for="is_enrolment_open" class="text-xs font-medium text-slate-700">Admissions & Enrolment Open</label>
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-sm transition-all">
                        + Create Batch
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Course Option -->
    <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
        <div>
            <h4 class="text-xs font-bold text-slate-700 uppercase">Danger Zone</h4>
            <p class="text-[11px] text-slate-500">Permanently delete this course and all associated batch records.</p>
        </div>

        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this course? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 rounded-xl border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold transition-colors">
                Delete Entire Course
            </button>
        </form>
    </div>
</div>
@endsection
