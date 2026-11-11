@extends('layouts.app')

@section('title', 'Scorecard: ' . $attempt->quiz->title . ' - Civil Service Gurukul')

@section('content')
<div class="max-w-5xl mx-auto space-y-8" x-data="{ filterType: 'all' }">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-brand-navy to-slate-950 rounded-3xl p-6 sm:p-8 text-white space-y-4 border border-slate-800 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-semibold uppercase tracking-wider">
                    Test Completed & Evaluated
                </span>
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight mt-2">{{ $attempt->quiz->title }}</h1>
                <p class="text-xs text-slate-400 mt-1">Submitted on {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition">
                    Dashboard
                </a>
                <form action="{{ route('student.quiz.start', $attempt->quiz) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-md transition">
                        Re-attempt Test
                    </button>
                </form>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-4 border-t border-slate-800">
            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Net Score</div>
                <div class="text-2xl font-bold font-mono text-white mt-1">
                    {{ $attempt->score }} <span class="text-xs text-slate-400 font-normal">/ {{ (int)$attempt->max_score }}</span>
                </div>
                <div class="text-[10px] text-emerald-400 mt-0.5">+{{ $attempt->marks_positive }} raw marks</div>
            </div>

            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Accuracy</div>
                <div class="text-2xl font-bold font-mono text-brand-orange mt-1">
                    {{ $attempt->accuracy_pct }}%
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">{{ $attempt->correct_count }} / {{ $attempt->correct_count + $attempt->incorrect_count }} attempted</div>
            </div>

            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Negative Penalty</div>
                <div class="text-2xl font-bold font-mono text-red-400 mt-1">
                    -{{ $attempt->marks_negative }}
                </div>
                <div class="text-[10px] text-red-300 mt-0.5">{{ $attempt->incorrect_count }} wrong answers</div>
            </div>

            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">All-India Rank</div>
                <div class="text-2xl font-bold font-mono text-blue-300 mt-1">
                    #{{ $attempt->all_india_rank ?? 1 }}
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">Top {{ $attempt->percentile ?? 100 }}% Percentile</div>
            </div>

            <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700 col-span-2 sm:col-span-1">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Time Spent</div>
                <div class="text-2xl font-bold font-mono text-white mt-1">
                    {{ floor($attempt->time_spent_seconds / 60) }}m {{ $attempt->time_spent_seconds % 60 }}s
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">{{ $attempt->unanswered_count }} left unattempted</div>
            </div>
        </div>
    </div>

    <!-- Subject & Topic-wise Breakdown Table -->
    @if(!empty($attempt->section_analytics_json))
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-card space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Subject-wise Performance Breakdown</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[11px] uppercase bg-slate-50 text-slate-400">
                        <tr>
                            <th class="py-2.5 px-3">Subject / Module</th>
                            <th class="py-2.5 px-3">Total Qs</th>
                            <th class="py-2.5 px-3">Correct</th>
                            <th class="py-2.5 px-3">Incorrect</th>
                            <th class="py-2.5 px-3">Unattempted</th>
                            <th class="py-2.5 px-3 text-right">Net Marks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($attempt->section_analytics_json as $subj => $data)
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-900">{{ $subj }}</td>
                                <td class="py-3 px-3 font-mono">{{ $data['total'] }}</td>
                                <td class="py-3 px-3 font-mono text-emerald-700 font-semibold">{{ $data['correct'] }}</td>
                                <td class="py-3 px-3 font-mono text-red-600 font-semibold">{{ $data['incorrect'] }}</td>
                                <td class="py-3 px-3 font-mono text-slate-400">{{ $data['unattempted'] }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-right {{ $data['marks'] >= 0 ? 'text-slate-900' : 'text-red-600' }}">
                                    {{ $data['marks'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Question-by-Question Detailed Review -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-card space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Question-by-Question Solution Review</h2>
                <p class="text-xs text-slate-500">Examine correct explanations, your selections, and model solutions</p>
            </div>

            <!-- Filter tabs -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl text-xs font-semibold">
                <button type="button" 
                        @click="filterType = 'all'" 
                        :class="filterType === 'all' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600'"
                        class="px-3 py-1.5 rounded-lg transition-all">
                    All ({{ $attempt->answers->count() }})
                </button>
                <button type="button" 
                        @click="filterType = 'incorrect'" 
                        :class="filterType === 'incorrect' ? 'bg-white text-red-600 shadow-sm' : 'text-slate-600'"
                        class="px-3 py-1.5 rounded-lg transition-all">
                    Incorrect ({{ $attempt->incorrect_count }})
                </button>
                <button type="button" 
                        @click="filterType = 'marked'" 
                        :class="filterType === 'marked' ? 'bg-white text-purple-700 shadow-sm' : 'text-slate-600'"
                        class="px-3 py-1.5 rounded-lg transition-all">
                    Marked for Review ({{ $attempt->marked_for_review_count }})
                </button>
            </div>
        </div>

        <!-- Answers List -->
        <div class="space-y-6">
            @foreach($attempt->answers as $index => $ans)
                @php
                    $q = $ans->question;
                    $statusClass = $ans->is_correct === true ? 'border-emerald-200 bg-emerald-50/20' : ($ans->is_correct === false ? 'border-red-200 bg-red-50/20' : 'border-slate-200 bg-slate-50/40');
                    $statusBadge = $ans->is_correct === true ? 'Correct (+'.$ans->marks_awarded.')' : ($ans->is_correct === false ? 'Incorrect ('.$ans->marks_awarded.')' : 'Unattempted (0)');
                    $badgeBg = $ans->is_correct === true ? 'bg-emerald-100 text-emerald-800' : ($ans->is_correct === false ? 'bg-red-100 text-red-800' : 'bg-slate-100 text-slate-600');
                @endphp

                <div class="p-5 rounded-2xl border {{ $statusClass }} space-y-4"
                     x-show="filterType === 'all' || (filterType === 'incorrect' && {{ $ans->is_correct === false ? 'true' : 'false' }}) || (filterType === 'marked' && {{ $ans->is_marked_for_review ? 'true' : 'false' }})">
                    <!-- Q header -->
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-800">Q.{{ $index + 1 }}</span>
                            <span class="text-slate-400">&bull;</span>
                            <span class="text-slate-500 font-medium">{{ $q->subject_tag ?? 'General Studies' }}</span>
                            @if($ans->is_marked_for_review)
                                <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px] font-semibold">Marked for Review</span>
                            @endif
                        </div>

                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeBg }}">
                            {{ $statusBadge }}
                        </span>
                    </div>

                    <!-- Stem -->
                    <div class="text-sm font-medium text-slate-900 leading-relaxed">
                        {!! $q->stem !!}
                    </div>

                    <!-- Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        @foreach($q->options as $optIdx => $opt)
                            @php
                                $isChosen = in_array($opt->id, $ans->selected_option_ids ?? []);
                                $isCorrect = $opt->is_correct;

                                $optBorder = 'border-slate-200 bg-white';
                                if ($isCorrect) {
                                    $optBorder = 'border-emerald-500 bg-emerald-50/80 text-emerald-900 font-semibold';
                                } elseif ($isChosen && !$isCorrect) {
                                    $optBorder = 'border-red-400 bg-red-50 text-red-900 font-semibold';
                                }
                            @endphp
                            <div class="p-3 rounded-xl border {{ $optBorder }} flex items-start gap-2">
                                <span class="font-mono font-bold">{{ ['A', 'B', 'C', 'D', 'E'][$optIdx] }}.</span>
                                <div class="flex-1">
                                    <span>{!! $opt->option_text !!}</span>
                                    @if($isChosen && $isCorrect)
                                        <span class="block text-[10px] text-emerald-700 font-bold mt-0.5">&check; Your Answer (Correct)</span>
                                    @elseif($isChosen && !$isCorrect)
                                        <span class="block text-[10px] text-red-600 font-bold mt-0.5">&cross; Your Answer (Wrong)</span>
                                    @elseif($isCorrect)
                                        <span class="block text-[10px] text-emerald-700 font-bold mt-0.5">&check; Correct Answer</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Detailed Solution / Explanation -->
                    @if($q->explanation)
                        <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200/70 text-xs text-amber-950 space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-amber-900">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Model Solution & Explanation:</span>
                            </div>
                            <div class="leading-relaxed pl-5">
                                {!! nl2br(e($q->explanation)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
