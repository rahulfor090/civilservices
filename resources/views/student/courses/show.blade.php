@extends('layouts.app')

@section('title', $course->title . ' - Class Player - Civil Service Gurukul')

@section('content')
<div class="space-y-6" x-data="{
    activeLessonId: {{ $activeLesson ? $activeLesson->id : 0 }},
    isCompleted: {{ (($activeLesson && $activeLesson->progress->where('user_id', auth()->id())->first()?->is_completed) || ($quizAttempt && $quizAttempt->status === 'completed')) ? 'true' : 'false' }},
    markCompleted(id) {
        this.isCompleted = true;
        fetch('{{ route('student.courses.progress') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                lesson_id: id,
                watched_seconds: 1800,
                is_completed: true
            })
        });
    }
}">
    <!-- Navigation Strip -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('student.dashboard') }}" class="text-xs text-brand-orange hover:underline font-semibold inline-flex items-center gap-1">
                &larr; Back to My Learning Dashboard
            </a>
            <span class="text-slate-300">|</span>
            <span class="text-xs text-slate-500 font-mono">
                Batch: <strong>{{ $batch->batch_code ?? 'ONLINE-2026' }}</strong>
            </span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('courses.calendar.ics', $course) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs shadow-2xs">
                <svg class="w-3.5 h-3.5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span>Add Planner to Phone (.ICS)</span>
            </a>
        </div>
    </div>

    <!-- Main Player Grid: Content (Left 8 cols) & Planner Playlist (Right 4 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Player & Lesson details -->
        <div class="lg:col-span-8 space-y-4">
            @if(!$activeLesson)
                <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-xs text-slate-500">
                    <p class="font-bold text-slate-700 text-sm">No session selected</p>
                    <p class="mt-1">Please select an activity from the Course Planner on the right.</p>
                </div>
            @elseif(!$activeLesson->is_unlocked)
                <!-- Locked / Upcoming Scheduled Session Banner -->
                <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-950 rounded-3xl p-8 text-white space-y-6 border border-slate-800 shadow-2xl text-center relative overflow-hidden">
                    <div class="w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 mx-auto flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>

                    <div class="space-y-2 max-w-lg mx-auto">
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold uppercase tracking-wider">
                            {{ $activeLesson->type_badge_label }} &bull; Scheduled Session
                        </span>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-white">{{ $activeLesson->title }}</h3>
                        <p class="text-xs text-slate-300">
                            This session is scheduled to unlock automatically on 
                            <strong class="text-amber-400">{{ $activeLesson->scheduled_at ? $activeLesson->scheduled_at->format('d M Y, h:i A') : $activeLesson->scheduled_date?->format('d M Y') }}</strong>.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-800/80 border border-slate-700/80 max-w-md mx-auto text-xs text-slate-300 space-y-1">
                        <div class="font-bold text-indigo-400 flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span>Pre-Session Alert Notification Scheduled</span>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            You will receive a notification <strong>{{ $activeLesson->alert_minutes_before ?? 30 }} minutes before</strong> this session 
                            @if($activeLesson->alert_time)
                                (at {{ $activeLesson->alert_time->format('h:i A') }})
                            @endif
                            on your registered email and mobile.
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('courses.calendar.ics', $course) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Import Schedule to Google/Apple Calendar</span>
                        </a>
                    </div>
                </div>

            @elseif($activeLesson->effective_type === 'phone_session')
                <!-- Phone Interactive Session Card -->
                <div class="bg-gradient-to-br from-blue-950 via-slate-900 to-brand-navy rounded-3xl p-6 sm:p-8 text-white space-y-6 border border-blue-900/60 shadow-2xl relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Phone Interactive Session &bull; 1-on-1 Direct Mentoring</span>
                            </span>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-white mt-2">{{ $activeLesson->title }}</h3>
                            <p class="text-xs text-slate-300">
                                Call your faculty directly during the dedicated call window to discuss doubts, strategy & answer writing feedback.
                            </p>
                        </div>

                        @if($activeLesson->mentor_phone)
                            <div class="shrink-0">
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $activeLesson->mentor_phone) }}" 
                                   class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>Call Mentor Directly</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Mentor Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Lead Mentor / Faculty</span>
                            <div class="text-base font-bold text-white mt-1">{{ $activeLesson->mentor_name ?? 'Senior Faculty Member' }}</div>
                            <span class="text-[10px] text-blue-300">Civil Services Gurukul</span>
                        </div>

                        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Dedicated Phone Line</span>
                            <div class="text-base font-bold text-emerald-400 font-mono mt-1">{{ $activeLesson->mentor_phone ?? '+91 9911020626' }}</div>
                            <span class="text-[10px] text-slate-400">Direct Telephone Slot</span>
                        </div>

                        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Call Window Timings</span>
                            <div class="text-base font-bold text-amber-400 mt-1">{{ $activeLesson->call_window ?? '06:00 PM - 07:00 PM' }}</div>
                            <span class="text-[10px] text-slate-400">Active Discussion Slot</span>
                        </div>
                    </div>

                    <!-- Instructions & Call Guidelines -->
                    <div class="p-4 rounded-2xl bg-blue-900/30 border border-blue-500/30 space-y-1.5 text-xs">
                        <div class="font-bold text-blue-300 uppercase tracking-wider text-[10px]">Session Preparation Guidelines:</div>
                        <p class="text-slate-300 leading-relaxed">
                            {{ $activeLesson->session_instructions ?: "Please keep your questions and answer copies ready before dialing. To ensure every aspirant gets personalized guidance, please keep your direct call focused on your key doubt areas." }}
                        </p>
                    </div>
                </div>

            @elseif($activeLesson->effective_type === 'live')
                <!-- 1-to-1 Live Class / Interactive Video Meeting Card -->
                <div class="bg-gradient-to-br from-purple-950 via-slate-900 to-slate-950 rounded-3xl p-6 sm:p-8 text-white space-y-6 border border-purple-900/60 shadow-2xl relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Live Interactive Class &bull; {{ $activeLesson->meeting_platform ?? 'Google Meet / Zoom' }}</span>
                            </span>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-white mt-2">{{ $activeLesson->title }}</h3>
                            <p class="text-xs text-slate-300">
                                Live interactive discussion & lecture session with screen sharing, whiteboard & two-way audio.
                            </p>
                        </div>

                        @if($activeLesson->meeting_link)
                            <div class="shrink-0">
                                <a href="{{ $activeLesson->meeting_link }}" 
                                   target="_blank"
                                   class="px-6 py-3.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-purple-600/30 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Join Live Classroom</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Platform</span>
                            <div class="text-base font-bold text-white mt-1">{{ $activeLesson->meeting_platform ?? 'Google Meet' }}</div>
                        </div>

                        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Meeting PIN / Passcode</span>
                            <div class="text-base font-bold text-amber-400 font-mono mt-1">{{ $activeLesson->meeting_password ?? 'None Required' }}</div>
                        </div>

                        <div class="bg-slate-800/80 p-4 rounded-2xl border border-slate-700/80">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Scheduled Time</span>
                            <div class="text-base font-bold text-purple-300 mt-1">{{ $activeLesson->scheduled_time ?? '05:00 PM' }} ({{ $activeLesson->duration_minutes }} mins)</div>
                        </div>
                    </div>
                </div>

            @elseif($activeLesson->effective_type === 'mains')
                <!-- Mains Answer Writing & Copy Submission Arena -->
                <div class="bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 rounded-3xl p-6 sm:p-8 text-white space-y-6 border border-emerald-900/60 shadow-2xl relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>Mains Daily Answer Writing Practice</span>
                            </span>
                            <h3 class="text-xl sm:text-2xl font-extrabold text-white mt-2">{{ $activeLesson->title }}</h3>
                            <p class="text-xs text-slate-300">
                                Practice writing your answer on standard UPSC format sheets and submit for expert mentor evaluation.
                            </p>
                        </div>

                        @if($activeLesson->mains_test_id)
                            <div class="shrink-0">
                                <a href="{{ route('student.mains.show', $activeLesson->mains_test_id) }}" 
                                   class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                                    <span>Open Mains Test & Upload Copy</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Question Stem Box -->
                    <div class="p-5 rounded-2xl bg-slate-800/90 border border-emerald-500/30 space-y-3">
                        <div class="flex items-center justify-between text-xs text-emerald-400 font-semibold border-b border-slate-700/60 pb-2">
                            <span>Question #1</span>
                            <span>{{ $activeLesson->mains_marks ?? 15 }} Marks &bull; {{ $activeLesson->mains_word_limit ?? 250 }} Words</span>
                        </div>
                        <div class="text-sm sm:text-base text-slate-100 font-medium leading-relaxed">
                            {{ $activeLesson->mains_question_text ?: ($activeLesson->mainsTest?->instructions ?: "Discuss the key constitutional tenets covered in today's class. Support your answer with relevant constitutional provisions and Supreme Court landmark judgments.") }}
                        </div>
                    </div>

                    <!-- Submission Status -->
                    @if($mainsSubmission)
                        <div class="p-4 rounded-2xl bg-emerald-950/50 border border-emerald-500/40 flex items-center justify-between gap-3 text-xs">
                            <div>
                                <span class="font-bold text-emerald-400">Answer Sheet Submitted</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">Submitted on {{ $mainsSubmission->submitted_at?->format('d M Y, h:i A') }} &bull; Evaluation Status: {{ ucfirst($mainsSubmission->status) }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full font-bold uppercase text-[10px] bg-emerald-500/20 text-emerald-300">
                                {{ $mainsSubmission->status }}
                            </span>
                        </div>
                    @endif
                </div>

            @elseif($activeLesson->effective_type === 'quiz' || $activeLesson->quiz)
                <!-- Daily Quiz Interactive Arena -->
                <div class="bg-gradient-to-br from-slate-900 via-brand-navy to-slate-950 rounded-3xl p-6 sm:p-8 text-white space-y-6 border border-slate-800 shadow-2xl relative overflow-hidden">
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-5">
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30 text-xs font-semibold uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Daily Practice Quiz &bull; {{ $activeLesson->quiz?->category?->name ?? 'Prelims Test' }}
                            </span>
                            <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight mt-2 text-white">
                                {{ $activeLesson->quiz ? $activeLesson->quiz->title : $activeLesson->title }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                Lesson {{ $activeLesson->sort_order }} &bull; Test your recall and conceptual clarity on today's topics
                            </p>
                        </div>

                        @if($activeLesson->quiz)
                            <div class="shrink-0">
                                <form action="{{ route('student.quiz.start', $activeLesson->quiz) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs sm:text-sm font-bold shadow-lg shadow-brand-orange/30 transition flex items-center justify-center gap-2">
                                        <span>{{ $quizAttempt ? 'Re-attempt Quiz' : 'Start Daily Quiz' }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if($activeLesson->quiz)
                        <!-- Quiz Meta Metrics -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 relative z-10">
                            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/80 text-center">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Duration</div>
                                <div class="text-xl font-bold font-mono text-white mt-1">{{ $activeLesson->quiz->duration_minutes }} Mins</div>
                                <div class="text-[10px] text-slate-400">Timed Exam</div>
                            </div>

                            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/80 text-center">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Questions</div>
                                <div class="text-xl font-bold font-mono text-white mt-1">{{ $activeLesson->quiz->questions->count() }} MCQs</div>
                                <div class="text-[10px] text-slate-400">Objective</div>
                            </div>

                            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/80 text-center">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Max Marks</div>
                                <div class="text-xl font-bold font-mono text-amber-400 mt-1">{{ (int)$activeLesson->quiz->total_marks }}</div>
                                <div class="text-[10px] text-emerald-400">+{{ $activeLesson->quiz->marks_per_question }} Correct</div>
                            </div>

                            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/80 text-center">
                                <div class="text-[10px] uppercase font-bold text-slate-400">Negative Penalty</div>
                                <div class="text-xl font-bold font-mono text-red-400 mt-1">-{{ $activeLesson->quiz->negative_marks_per_question }}</div>
                                <div class="text-[10px] text-slate-400">Per Wrong</div>
                            </div>
                        </div>

                        <!-- Previous Attempt Scorecard Banner -->
                        @if($quizAttempt && $quizAttempt->status === 'completed')
                            <div class="p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs relative z-10">
                                <div>
                                    <div class="font-bold text-emerald-400 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>Completed: Scored {{ $quizAttempt->score }} / {{ (int)$quizAttempt->max_score }} ({{ $quizAttempt->accuracy_pct }}% Accuracy)</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-0.5">
                                        Submitted on {{ $quizAttempt->submitted_at ? $quizAttempt->submitted_at->format('d M Y, H:i') : 'recently' }} &bull; All-India Rank #{{ $quizAttempt->all_india_rank ?? 1 }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ route('student.quiz.result', $quizAttempt) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition inline-flex items-center gap-1">
                                        <span>View Solutions</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

            @else
                <!-- Video Player Box with Anti-Piracy Watermark -->
                <div class="relative bg-black rounded-3xl overflow-hidden shadow-2xl aspect-video flex items-center justify-center border border-slate-800 group">
                    <!-- Watermark Overlay -->
                    <div class="absolute top-4 right-4 z-20 pointer-events-none opacity-40 text-white font-mono text-[10px]">
                        Candidate ID: {{ substr(auth()->user()->uuid, 0, 8) }} &bull; {{ request()->ip() }}
                    </div>

                    @if($activeLesson && $activeLesson->embed_video_url)
                        <iframe class="w-full h-full" 
                                src="{{ $activeLesson->embed_video_url }}" 
                                title="{{ $activeLesson->title }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen></iframe>
                    @else
                        <!-- Fallback / Non-embeddable Link Container -->
                        <div class="text-center p-8 space-y-3">
                            <div class="w-16 h-16 rounded-full bg-brand-orange/20 text-brand-orange mx-auto flex items-center justify-center">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-white text-base font-bold">{{ $activeLesson ? $activeLesson->title : 'Select a lesson to start playback' }}</h3>
                            
                            @if($activeLesson && $activeLesson->video_url)
                                <p class="text-xs text-amber-300 max-w-md mx-auto">
                                    Notice: The URL provided (<code>{{ $activeLesson->video_url }}</code>) is not a specific embeddable video ID. YouTube blocks embedding the general homepage.
                                </p>
                                <div>
                                    <a href="{{ $activeLesson->video_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition">
                                        <span>Open Link in New Tab &rarr;</span>
                                    </a>
                                </div>
                            @else
                                <p class="text-xs text-slate-400">High-Definition Lecture Stream &bull; Video stream will be attached shortly</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <!-- Current Lesson Info Bar -->
            @if($activeLesson)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $activeLesson->effective_type === 'quiz' ? 'bg-amber-100 text-amber-800' : 'bg-brand-orange/10 text-brand-orange' }}">
                                {{ $activeLesson->type_badge_label }} &bull; Session #{{ $activeLesson->sort_order }}
                            </span>
                            <h2 class="text-lg font-bold text-slate-900 mt-1">{{ $activeLesson->title }}</h2>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-mono mt-1">
                                <span>Duration: {{ $activeLesson->duration_minutes }} minutes</span>
                                @if($activeLesson->scheduled_date)
                                    <span>&bull;</span>
                                    <span>📅 {{ $activeLesson->scheduled_date->format('d M Y') }} at {{ $activeLesson->scheduled_time ?? '05:00 PM' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3 flex-wrap">
                            @if($activeLesson->quiz)
                                <form action="{{ route('student.quiz.start', $activeLesson->quiz) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow transition inline-flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        <span>{{ $quizAttempt ? 'Retake Quiz' : 'Take Daily Quiz' }}</span>
                                    </button>
                                </form>
                            @endif

                            <button type="button" 
                                    @click="markCompleted({{ $activeLesson->id }})"
                                    :class="isCompleted ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span x-text="isCompleted ? 'Completed' : 'Mark as Complete'"></span>
                            </button>

                            @if($activeLesson->notes_pdf_url)
                                <a href="{{ $activeLesson->notes_pdf_url }}" 
                                   target="_blank" 
                                   download
                                   class="px-4 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow transition inline-flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Download Notes</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Course Playlist & Day Planner Sidebar (Right 4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-0">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Course Day Planner</h3>
                    <p class="text-[11px] text-slate-500">{{ $lessons->count() }} Sessions &bull; {{ $course->title }}</p>
                </div>
                <a href="{{ route('courses.calendar.ics', $course) }}" title="Download Calendar" class="text-slate-400 hover:text-brand-orange p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </a>
            </div>

            <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto">
                @forelse($lessons as $idx => $les)
                    @php
                        $isActive = $activeLesson && $activeLesson->id === $les->id;
                        $prog = $les->progress->where('user_id', auth()->id())->first();
                        $effType = $les->effective_type;
                        $isUnlocked = $les->is_unlocked;
                    @endphp
                    <a href="{{ route('student.courses.show', ['slug' => $course->slug, 'lesson' => $les->id]) }}" 
                       class="p-3.5 flex items-start gap-3 transition-colors block {{ $isActive ? 'bg-amber-50/70 border-l-4 border-brand-orange' : 'hover:bg-slate-50/70' }}">
                        
                        <!-- Status / Type icon -->
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 
                            {{ $prog?->is_completed ? 'bg-emerald-100 text-emerald-800' : (!$isUnlocked ? 'bg-slate-200 text-slate-500' : 'bg-brand-orange/10 text-brand-orange') }}">
                            @if($prog?->is_completed)
                                &check;
                            @elseif(!$isUnlocked)
                                🔒
                            @elseif($effType === 'phone_session')
                                📞
                            @elseif($effType === 'mains')
                                ✍
                            @elseif($effType === 'quiz')
                                📝
                            @elseif($effType === 'live')
                                🎥
                            @else
                                {{ $idx + 1 }}
                            @endif
                        </span>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h4 class="text-xs font-bold truncate {{ $isActive ? 'text-brand-orange' : 'text-slate-900' }} leading-snug">
                                    {{ $les->title }}
                                </h4>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-400 mt-1">
                                <span class="font-semibold text-slate-600">{{ $les->type_badge_label }}</span>
                                <span>&bull;</span>
                                <span>{{ $les->duration_minutes }}m</span>

                                @if($les->scheduled_time)
                                    <span>&bull;</span>
                                    <span class="font-mono text-slate-600">{{ $les->scheduled_time }}</span>
                                @endif

                                @if(!$isUnlocked)
                                    <span class="text-amber-600 font-semibold bg-amber-50 px-1.5 rounded">Locked</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        No sessions scheduled in this batch yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
