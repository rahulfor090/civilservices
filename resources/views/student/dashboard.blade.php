@extends('layouts.app')

@section('title', 'Student Portal - My Learning & Test Series - Civil Service Gurukul')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-slate-900 via-brand-navy to-slate-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-2">
            <span class="inline-block px-3 py-1 rounded-full bg-brand-orange/20 border border-brand-orange/30 text-brand-orange text-xs font-semibold uppercase tracking-wider">
                Student Learning Portal
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Welcome back, {{ $user->name }}!</h1>
            <p class="text-xs sm:text-sm text-slate-300">Target Exam: <strong class="text-white">{{ strtoupper(str_replace('_', ' ', $user->target_exam ?? 'UPSC CSE')) }}</strong> &bull; Mode: {{ ucfirst($user->preparation_mode ?? 'Online') }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('quizzes.index') }}" class="px-5 py-2.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-md transition">
                Take Daily Test
            </a>
        </div>
    </div>

    <!-- Ongoing Incomplete Tests Alert -->
    @if($ongoingAttempts->isNotEmpty())
        <div class="p-5 rounded-2xl bg-amber-50 border border-brand-orange/40 text-amber-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="p-2 rounded-xl bg-brand-orange text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <h3 class="font-bold text-sm text-slate-900">You have an active test in progress!</h3>
                    <p class="text-xs text-slate-600">{{ $ongoingAttempts->first()->quiz->title }} &bull; {{ $ongoingAttempts->first()->remaining_seconds }}s remaining</p>
                </div>
            </div>
            <div>
                <a href="{{ route('student.quiz.attempt', $ongoingAttempts->first()) }}" class="px-5 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow transition">
                    Resume Test Now &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- Two-column: Enrolled Courses and Recent Test Scorecards -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Enrolled Courses (LMS) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">My Enrolled Courses & Batches</h2>
            </div>

            <div class="space-y-4">
                @forelse($enrollments as $enr)
                    @php $c = $enr->course; @endphp
                    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-card hover:shadow-cardHover transition-all flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between">
                        <div class="space-y-2 max-w-md">
                            <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-50 text-blue-800">
                                {{ $enr->batch ? $enr->batch->delivery_mode : 'Online' }} Batch
                            </span>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                <a href="{{ route('student.courses.show', $c->slug) }}" class="hover:text-brand-navy">
                                    {{ $c->title }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-1">{{ $c->excerpt }}</p>
                            <div class="text-[11px] text-slate-400">
                                Faculty: <strong class="text-slate-700">{{ $enr->batch->faculty_names ?? 'Senior Faculty Panel' }}</strong>
                            </div>
                        </div>

                        <div class="shrink-0 w-full sm:w-auto">
                            <a href="{{ route('student.courses.show', $c->slug) }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow transition-all block text-center">
                                Open Class Player &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-200 text-center space-y-3">
                        <p class="text-xs text-slate-500">You are not enrolled in any paid classroom or online course batches yet.</p>
                        <a href="{{ url('/') }}" class="inline-block px-4 py-2 rounded-xl bg-brand-orange text-white text-xs font-semibold">
                            Explore UPSC Programmes
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Scorecards & Tests -->
        <div class="lg:col-span-5 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">Recent Test Scorecards</h2>
                <a href="{{ route('quizzes.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">Take More &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($recentAttempts as $att)
                    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-card space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 font-mono">{{ $att->quiz->category ? $att->quiz->category->name : 'Prelims' }}</span>
                                <h3 class="text-xs font-bold text-slate-900 leading-snug line-clamp-1">{{ $att->quiz->title }}</h3>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold font-mono {{ $att->score >= ($att->max_score * 0.4) ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-700' }}">
                                {{ $att->score }} / {{ (int)$att->max_score }} M
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                            <span>Accuracy: <strong class="text-slate-800">{{ $att->accuracy_pct }}%</strong></span>
                            <span>Rank: <strong class="text-brand-navy">#{{ $att->all_india_rank ?? 1 }}</strong></span>
                            <a href="{{ route('student.quiz.result', $att) }}" class="text-brand-orange hover:underline font-semibold">
                                View Solutions &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl p-8 border border-slate-200 text-center space-y-3">
                        <p class="text-xs text-slate-500">No test attempts completed yet.</p>
                        <a href="{{ route('quizzes.index') }}" class="inline-block px-4 py-2 rounded-xl bg-brand-navy text-white text-xs font-semibold">
                            Start First Prelims Quiz
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
