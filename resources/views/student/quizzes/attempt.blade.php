<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $attempt->quiz->title }} - Exam Mode</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700&family=Noto+Sans+Devanagari:wght@400;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#0b1b3d',
                            navyLight: '#142c5f',
                            orange: '#e65100',
                            orangeHover: '#bf360c',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Devanagari', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { user-select: none; }
    </style>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased"
      x-data="quizEngine({
          attemptId: {{ $attempt->id }},
          totalQuestions: {{ $questions->count() }},
          remainingSeconds: {{ $attempt->remaining_seconds }},
          submitUrl: '{{ route('student.quiz.submit', $attempt) }}',
          autosaveUrl: '{{ route('student.quiz.autosave', $attempt) }}',
          csrfToken: '{{ csrf_token() }}',
          questions: {{ Js::from($questions) }},
          initialAnswers: {{ Js::from($answers) }}
      })"
      @keydown.window="handleKeyboard($event)">

    <!-- Top Sticky Exam Header -->
    <header class="bg-brand-navy text-white h-16 shrink-0 sticky top-0 z-40 border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-4">
            <span class="w-8 h-8 rounded-lg bg-brand-orange text-white font-bold flex items-center justify-center text-sm shadow">E</span>
            <div>
                <h1 class="font-bold text-sm sm:text-base tracking-tight truncate max-w-xs sm:max-w-md">{{ $attempt->quiz->title }}</h1>
                <div class="text-[11px] text-slate-300">
                    Question <span class="font-bold text-white" x-text="currentIndex + 1">1</span> of <span class="font-bold text-white">{{ $questions->count() }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 sm:gap-6">
            <!-- Server-authoritative Countdown Timer -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border font-mono font-bold text-sm sm:text-base transition-colors"
                 :class="remainingSeconds <= 300 ? 'bg-red-950/80 border-red-500 text-red-300 animate-pulse' : 'bg-slate-900 border-slate-700 text-white'">
                <svg class="w-4 h-4 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="formatTime(remainingSeconds)">00:00</span>
            </div>

            <!-- Submit Test CTA Button -->
            <button type="button" 
                    @click="showSubmitModal = true"
                    class="px-4 sm:px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold shadow-md transition-all">
                Submit Test
            </button>
        </div>
    </header>

    <!-- Main Exam Workspace Layout -->
    <div class="flex-1 flex flex-col lg:flex-row overflow-hidden">
        <!-- Question pane (Left / Center) -->
        <main class="flex-1 flex flex-col justify-between overflow-y-auto p-4 sm:p-8 space-y-6">
            <div class="max-w-3xl w-full mx-auto space-y-6">
                <!-- Question Title Bar -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-lg bg-brand-navy text-white text-xs font-bold font-mono">
                            Q. <span x-text="currentIndex + 1"></span>
                        </span>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider" x-text="currentQuestion.subject_tag || 'General Studies'"></span>
                    </div>

                    <div class="flex items-center gap-3 text-xs font-mono">
                        <span class="text-emerald-700 font-bold">+{{ $attempt->quiz->marks_per_question }}</span>
                        <span class="text-red-600 font-bold">-{{ $attempt->quiz->negative_marks_per_question }}</span>
                    </div>
                </div>

                <!-- Question Stem -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-sm sm:text-base leading-relaxed text-slate-900 font-medium space-y-4">
                    <div x-html="currentQuestion.stem"></div>
                </div>

                <!-- Options List -->
                <div class="space-y-3">
                    <template x-for="(opt, idx) in currentQuestion.options" :key="opt.id">
                        <label class="flex items-start gap-4 p-4 rounded-2xl border cursor-pointer transition-all"
                               :class="isSelected(opt.id) ? 'bg-amber-50/70 border-brand-orange shadow-sm text-slate-900' : 'bg-white border-slate-200 hover:border-slate-300 text-slate-700'">
                            <div class="pt-0.5">
                                <input type="radio" 
                                       :name="'q_' + currentQuestion.id" 
                                       :value="opt.id"
                                       :checked="isSelected(opt.id)"
                                       @change="selectOption(opt.id)"
                                       class="w-4 h-4 text-brand-orange border-slate-300 focus:ring-brand-orange">
                            </div>
                            <div class="flex-1 text-xs sm:text-sm font-normal leading-relaxed">
                                <span class="font-bold text-slate-900 mr-2 font-mono" x-text="['A', 'B', 'C', 'D', 'E'][idx] + '.'"></span>
                                <span x-html="opt.option_text"></span>
                            </div>
                        </label>
                    </template>
                </div>
            </div>

            <!-- Bottom Action Controls Strip -->
            <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-slate-200 py-3 px-4 sm:px-8 -mx-4 sm:-mx-8 shrink-0 flex flex-wrap items-center justify-between gap-3 shadow-lg">
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="clearResponse()"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold transition-colors">
                        Clear Response
                    </button>
                    <button type="button" 
                            @click="toggleMarkForReview()"
                            :class="isCurrentMarked() ? 'bg-purple-700 text-white' : 'border border-purple-300 text-purple-700 hover:bg-purple-50'"
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors">
                        <span x-text="isCurrentMarked() ? 'Unmark Review' : 'Mark for Review'"></span>
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" 
                            @click="prevQuestion()"
                            :disabled="currentIndex === 0"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 text-xs font-semibold disabled:opacity-40 disabled:cursor-not-allowed">
                        &larr; Previous
                    </button>
                    <button type="button" 
                            @click="saveAndNext()"
                            class="px-5 py-2 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow transition-all">
                        Save & Next &rarr;
                    </button>
                </div>
            </div>
        </main>

        <!-- Question Palette Sidebar (Right) -->
        <aside class="w-full lg:w-80 bg-white border-t lg:border-t-0 lg:border-l border-slate-200 p-4 sm:p-6 flex flex-col justify-between overflow-y-auto shrink-0 space-y-6">
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                    Question Palette
                </h3>

                <!-- 5 Legend Counters -->
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div class="flex items-center gap-2 p-1.5 rounded-lg bg-emerald-50 text-emerald-800">
                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center font-mono" x-text="counts.answered">0</span>
                        <span>Answered</span>
                    </div>
                    <div class="flex items-center gap-2 p-1.5 rounded-lg bg-red-50 text-red-800">
                        <span class="w-4 h-4 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center font-mono" x-text="counts.notAnswered">0</span>
                        <span>Not Answered</span>
                    </div>
                    <div class="flex items-center gap-2 p-1.5 rounded-lg bg-purple-50 text-purple-800">
                        <span class="w-4 h-4 rounded-full bg-purple-600 text-white text-[10px] font-bold flex items-center justify-center font-mono" x-text="counts.review">0</span>
                        <span>Marked Review</span>
                    </div>
                    <div class="flex items-center gap-2 p-1.5 rounded-lg bg-slate-100 text-slate-700">
                        <span class="w-4 h-4 rounded-full bg-slate-300 text-slate-700 text-[10px] font-bold flex items-center justify-center font-mono" x-text="counts.notVisited">0</span>
                        <span>Not Visited</span>
                    </div>
                </div>

                <!-- Numbered Palette Grid -->
                <div class="grid grid-cols-5 gap-2 pt-2">
                    <template x-for="(q, idx) in questions" :key="q.id">
                        <button type="button" 
                                @click="jumpToQuestion(idx)"
                                :class="getPaletteClass(idx)"
                                class="h-9 w-full rounded-xl font-bold font-mono text-xs flex items-center justify-center transition-all shadow-sm">
                            <span x-text="idx + 1"></span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Keyboard shortcut guide -->
            <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl text-[10px] text-slate-400 space-y-1">
                <span class="font-bold text-slate-600 uppercase">Shortcuts:</span>
                <div class="flex flex-wrap gap-2 text-slate-500">
                    <span><kbd class="px-1 bg-white border rounded">1-4</kbd> Pick Option</span>
                    <span><kbd class="px-1 bg-white border rounded">M</kbd> Review</span>
                    <span><kbd class="px-1 bg-white border rounded">S</kbd> Save</span>
                </div>
            </div>
        </aside>
    </div>

    <!-- Submit Confirmation Modal -->
    <div x-show="showSubmitModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full space-y-6 shadow-2xl border border-slate-100 text-center">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-brand-orange mx-auto flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>

            <div class="space-y-2">
                <h3 class="text-lg font-bold text-slate-900">Are you sure you want to submit?</h3>
                <p class="text-xs text-slate-500">You will not be able to change your responses after submitting.</p>
            </div>

            <!-- Summary counts in modal -->
            <div class="grid grid-cols-3 gap-2 p-3 bg-slate-50 rounded-xl text-xs font-mono">
                <div>
                    <div class="text-slate-400 text-[10px]">Answered</div>
                    <div class="font-bold text-emerald-700 text-sm mt-0.5" x-text="counts.answered">0</div>
                </div>
                <div>
                    <div class="text-slate-400 text-[10px]">Review</div>
                    <div class="font-bold text-purple-700 text-sm mt-0.5" x-text="counts.review">0</div>
                </div>
                <div>
                    <div class="text-slate-400 text-[10px]">Unanswered</div>
                    <div class="font-bold text-red-600 text-sm mt-0.5" x-text="counts.notAnswered + counts.notVisited">0</div>
                </div>
            </div>

            <form :action="submitUrl" method="POST" class="flex items-center gap-3">
                <input type="hidden" name="_token" :value="csrfToken">
                <button type="button" 
                        @click="showSubmitModal = false"
                        class="flex-1 py-3 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-bold">
                    Continue Test
                </button>
                <button type="submit" 
                        class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md">
                    Yes, Submit Now
                </button>
            </form>
        </div>
    </div>

    <!-- Alpine.js Test Engine Logic -->
    <script>
        function quizEngine(config) {
            return {
                attemptId: config.attemptId,
                totalQuestions: config.totalQuestions,
                remainingSeconds: config.remainingSeconds,
                submitUrl: config.submitUrl,
                autosaveUrl: config.autosaveUrl,
                csrfToken: config.csrfToken,
                questions: config.questions,
                currentIndex: 0,
                showSubmitModal: false,
                visited: [0], // Set of visited indices
                answers: {}, // question_id => { selected: [id], review: bool, isAnswered: bool }

                init() {
                    // Initialize state from existing records
                    for (let q of this.questions) {
                        let prev = config.initialAnswers[q.id];
                        this.answers[q.id] = {
                            selected: prev && prev.selected_option_ids ? prev.selected_option_ids.map(Number) : [],
                            review: prev ? !!prev.is_marked_for_review : false,
                            isAnswered: prev ? !!prev.is_answered : false
                        };
                    }

                    // Start timer countdown
                    setInterval(() => {
                        if (this.remainingSeconds > 0) {
                            this.remainingSeconds--;
                        } else {
                            // Auto submit when time expires
                            window.location.href = this.submitUrl;
                        }
                    }, 1000);
                },

                get currentQuestion() {
                    return this.questions[this.currentIndex] || {};
                },

                formatTime(sec) {
                    let m = Math.floor(sec / 60);
                    let s = sec % 60;
                    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                },

                isSelected(optId) {
                    let cur = this.answers[this.currentQuestion.id];
                    return cur && cur.selected.includes(Number(optId));
                },

                selectOption(optId) {
                    let qId = this.currentQuestion.id;
                    this.answers[qId].selected = [Number(optId)];
                    this.answers[qId].isAnswered = true;
                    this.autosave(qId);
                },

                clearResponse() {
                    let qId = this.currentQuestion.id;
                    this.answers[qId].selected = [];
                    this.answers[qId].isAnswered = false;
                    this.autosave(qId);
                },

                isCurrentMarked() {
                    let cur = this.answers[this.currentQuestion.id];
                    return cur && cur.review;
                },

                toggleMarkForReview() {
                    let qId = this.currentQuestion.id;
                    this.answers[qId].review = !this.answers[qId].review;
                    this.autosave(qId);
                },

                saveAndNext() {
                    if (this.currentIndex < this.totalQuestions - 1) {
                        this.jumpToQuestion(this.currentIndex + 1);
                    } else {
                        this.showSubmitModal = true;
                    }
                },

                prevQuestion() {
                    if (this.currentIndex > 0) {
                        this.jumpToQuestion(this.currentIndex - 1);
                    }
                },

                jumpToQuestion(idx) {
                    this.currentIndex = idx;
                    if (!this.visited.includes(idx)) {
                        this.visited.push(idx);
                    }
                },

                autosave(qId) {
                    let ans = this.answers[qId];
                    fetch(this.autosaveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            question_id: qId,
                            selected_option_ids: ans.selected,
                            is_marked_for_review: ans.review,
                            time_spent_seconds: 5
                        })
                    }).catch(console.error);
                },

                get counts() {
                    let answered = 0, review = 0, notAnswered = 0, notVisited = 0;
                    this.questions.forEach((q, idx) => {
                        let ans = this.answers[q.id];
                        if (ans && ans.isAnswered) {
                            answered++;
                        } else if (ans && ans.review) {
                            review++;
                        } else if (this.visited.includes(idx)) {
                            notAnswered++;
                        } else {
                            notVisited++;
                        }
                    });
                    return { answered, review, notAnswered, notVisited };
                },

                getPaletteClass(idx) {
                    let q = this.questions[idx];
                    let ans = this.answers[q.id];
                    let isCurrent = this.currentIndex === idx;

                    let base = isCurrent ? 'ring-2 ring-brand-orange ring-offset-2 ' : '';

                    if (ans && ans.isAnswered && ans.review) {
                        return base + 'bg-purple-700 text-white border-2 border-emerald-400';
                    } else if (ans && ans.isAnswered) {
                        return base + 'bg-emerald-600 text-white';
                    } else if (ans && ans.review) {
                        return base + 'bg-purple-600 text-white';
                    } else if (this.visited.includes(idx)) {
                        return base + 'bg-red-500 text-white';
                    }
                    return base + 'bg-slate-100 text-slate-700 border border-slate-200';
                },

                handleKeyboard(e) {
                    if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
                    if (e.key >= '1' && e.key <= '4') {
                        let idx = parseInt(e.key) - 1;
                        if (this.currentQuestion.options && this.currentQuestion.options[idx]) {
                            this.selectOption(this.currentQuestion.options[idx].id);
                        }
                    } else if (e.key.toLowerCase() === 'm') {
                        this.toggleMarkForReview();
                    } else if (e.key.toLowerCase() === 's') {
                        this.saveAndNext();
                    }
                }
            };
        }
    </script>
</body>
</html>
