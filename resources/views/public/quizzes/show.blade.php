@extends('layouts.app')

@section('title', $quiz->title . ' - ' . ($locale === 'hi' ? 'टेस्ट निर्देश व प्रारंभ' : 'Test Briefing & Instructions'))
@section('meta_description', 'Take ' . $quiz->title . ' on Civil Service Gurukul. Online timed UPSC prelims test with instant results and All-India Rank.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-gradient-to-r from-slate-900 to-brand-navy rounded-3xl p-8 text-white space-y-4">
        <a href="{{ $locale === 'hi' ? url('/hindi/quizzes') : url('/quizzes') }}" class="text-xs text-brand-orange hover:underline font-semibold inline-flex items-center gap-1">
            &larr; {{ $locale === 'hi' ? 'सभी टेस्ट' : 'All Tests' }}
        </a>
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-brand-orange/20 border border-brand-orange/30 text-brand-orange text-xs font-semibold uppercase tracking-wider">
                {{ $quiz->category ? $quiz->category->name : 'UPSC Prelims Mock' }}
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $quiz->title }}</h1>
        </div>
    </div>

    <!-- Test Meta Specs Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm text-center">
            <div class="text-xs text-slate-400 uppercase font-semibold">Duration</div>
            <div class="text-2xl font-bold text-slate-900 font-mono mt-1">{{ $quiz->duration_minutes }} min</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Countdown Timer</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm text-center">
            <div class="text-xs text-slate-400 uppercase font-semibold">Total Questions</div>
            <div class="text-2xl font-bold text-slate-900 font-mono mt-1">{{ $quiz->questions->count() }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Objective MCQs</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm text-center">
            <div class="text-xs text-slate-400 uppercase font-semibold">Max Marks</div>
            <div class="text-2xl font-bold text-brand-navy font-mono mt-1">{{ (int)$quiz->total_marks }}</div>
            <div class="text-[11px] text-emerald-600 mt-0.5">+{{ $quiz->marks_per_question }} per correct</div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm text-center">
            <div class="text-xs text-slate-400 uppercase font-semibold">Negative Marking</div>
            <div class="text-2xl font-bold text-red-600 font-mono mt-1">-{{ $quiz->negative_marks_per_question }}</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Per wrong answer</div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-card space-y-6">
        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
            {{ $locale === 'hi' ? 'महत्वपूर्ण परीक्षा निर्देश' : 'Instructions & Exam Rules' }}
        </h2>

        <div class="prose prose-slate text-xs sm:text-sm text-slate-600 space-y-3 leading-relaxed">
            @if($quiz->instructions)
                <p>{!! nl2br(e($quiz->instructions)) !!}</p>
            @else
                <ul class="list-disc list-inside space-y-2">
                    <li>This test consists of <strong>{{ $quiz->questions->count() }} multiple-choice questions</strong>.</li>
                    <li>Each question has four options with only one correct answer.</li>
                    <li>Each correct response fetches <strong>+{{ $quiz->marks_per_question }} marks</strong>, while each incorrect answer attracts a negative deduction of <strong>-{{ $quiz->negative_marks_per_question }} marks</strong>.</li>
                    <li>Unanswered questions carry zero penalty.</li>
                    <li>The countdown timer starts as soon as you click "Start Test". The server automatically submits the exam upon expiry.</li>
                    <li>You can flag questions for review using the <em>"Mark for Review"</em> option and revisit them via the question palette anytime before final submission.</li>
                    <li>Keyboard shortcuts are enabled: use numbers <code>1</code>, <code>2</code>, <code>3</code>, <code>4</code> to pick options, <code>M</code> to mark for review, and <code>S</code> to save.</li>
                </ul>
            @endif
        </div>

        <!-- Previous Attempt summary if already taken -->
        @if($userAttempt)
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-blue-950">
                <div>
                    <span class="font-bold">Previous Attempt Recorded:</span>
                    <span>Scored <strong>{{ $userAttempt->score }} / {{ $userAttempt->max_score }}</strong> (Accuracy: {{ $userAttempt->accuracy_pct }}%) on {{ $userAttempt->submitted_at ? $userAttempt->submitted_at->format('d M Y, H:i') : 'recently' }}</span>
                </div>
                <div>
                    <a href="{{ route('student.quiz.result', $userAttempt) }}" class="px-4 py-1.5 rounded-lg bg-blue-700 text-white font-semibold hover:bg-blue-800 transition">
                        View Previous Scorecard &rarr;
                    </a>
                </div>
            </div>
        @endif

        <!-- Action CTA -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                By starting this test, you agree to adhere to competitive integrity standards.
            </div>

            @auth
                <form action="{{ route('student.quiz.start', $quiz) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-sm font-bold shadow-lg shadow-brand-orange/30 transition-all flex items-center justify-center gap-2">
                        <span>{{ $userAttempt ? 'Re-attempt Test Now' : 'Start Test Now' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-sm font-bold shadow-md transition-all text-center inline-flex items-center justify-center gap-2">
                    <span>Sign in to Start Test</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
