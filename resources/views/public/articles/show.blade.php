@extends('layouts.app')

@section('title', $article->meta_title ?: $article->title . ' | Civil Service Gurukul')
@section('meta_description', $article->meta_description ?: $article->excerpt)

@section('meta_tags')
    @php
        $ogImageUrl = $article->feature_image_url ?: asset('images/logo.png');
        if (!preg_match('#^https?://#i', $ogImageUrl)) {
            $ogImageUrl = url($ogImageUrl);
        }
        $hero = $article->heroMedia;
        $ogWidth = $hero?->width ?: 1200;
        $ogHeight = $hero?->height ?: 630;
        $ogMime = $hero?->mime_type ?: 'image/jpeg';
    @endphp
    <!-- Open Graph / WhatsApp / Facebook Share Preview -->
    <meta property="og:site_name" content="Civil Service Gurukul">
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $article->meta_title ?: $article->title }}">
    <meta property="og:description" content="{{ $article->meta_description ?: $article->excerpt }}">
    <meta property="og:url" content="{{ $article->url }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:secure_url" content="{{ $ogImageUrl }}">
    <meta property="og:image:type" content="{{ $ogMime }}">
    <meta property="og:image:width" content="{{ $ogWidth }}">
    <meta property="og:image:height" content="{{ $ogHeight }}">
    <meta property="og:image:alt" content="{{ $hero?->alt_text ?: $article->title }}">

    <!-- Twitter / X Card Preview -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@CivilServiceGurukul">
    <meta name="twitter:title" content="{{ $article->meta_title ?: $article->title }}">
    <meta name="twitter:description" content="{{ $article->meta_description ?: $article->excerpt }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">
@endsection

@section('canonical_url', $article->url)

@section('hreflang_tags')
    @if($twin)
        @if($article->language->code === 'en')
            <link rel="alternate" hreflang="en-in" href="{{ $article->url }}" />
            <link rel="alternate" hreflang="hi-in" href="{{ $twin->url }}" />
            <link rel="alternate" hreflang="x-default" href="{{ $article->url }}" />
        @else
            <link rel="alternate" hreflang="hi-in" href="{{ $article->url }}" />
            <link rel="alternate" hreflang="en-in" href="{{ $twin->url }}" />
            <link rel="alternate" hreflang="x-default" href="{{ $twin->url }}" />
        @endif
    @else
        <link rel="alternate" hreflang="{{ $article->language->code === 'hi' ? 'hi-in' : 'en-in' }}" href="{{ $article->url }}" />
    @endif
@endsection

@section('structured_data')
    <script type="application/ld+json">
        {!! $jsonLd !!}
    </script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
    <!-- Breadcrumb Hierarchy -->
    <nav class="flex items-center text-xs text-slate-500 mb-4" aria-label="Breadcrumb">
        <a href="{{ url($article->language->code === 'hi' ? '/hindi' : '/') }}" class="hover:text-amber-600 transition">
            {{ $article->language->code === 'hi' ? 'होम' : 'Home' }}
        </a>
        <span class="mx-2 text-slate-400">/</span>
        <a href="{{ url(($article->language->code === 'hi' ? '/hindi/' : '/') . ($article->contentType?->url_prefix ?? 'daily-updates/daily-news-analysis')) }}" class="hover:text-amber-600 transition">
            {{ $article->contentType ? $article->contentType->name : 'Daily Updates' }}
        </a>
        <span class="mx-2 text-slate-400">/</span>
        <span class="text-slate-800 font-medium truncate max-w-xs sm:max-w-md">{{ $article->title }}</span>
    </nav>

    <!-- Language Twin Alert Switcher Banner -->
    @if($twin)
        <div class="mb-6 p-3 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-between text-xs">
            <span class="text-amber-900 font-medium flex items-center">
                <svg class="w-4 h-4 mr-1.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                @if($article->language->code === 'en')
                    यह लेख हिन्दी में भी उपलब्ध है:
                @else
                    This article is also available in English:
                @endif
            </span>
            <a href="{{ $twin->url }}" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-3 py-1 rounded transition text-xs">
                @if($article->language->code === 'en')
                    हिन्दी संस्करण पढ़ें &rarr;
                @else
                    Read English Version &rarr;
                @endif
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Main Article Body & Question Sections (8 Cols) -->
        <article class="lg:col-span-8 bg-white p-6 sm:p-10 rounded-xl shadow-xs border border-slate-200">
            
            <!-- Section Badge & Reading Minutes -->
            <div class="flex items-center space-x-3 text-xs text-slate-500 mb-3">
                <span class="bg-amber-100 text-amber-800 font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                    {{ $article->section ? ($article->section->translation()?->name ?? 'General Studies') : ($article->contentType?->name ?? 'Current Affairs') }}
                </span>
                <span>&bull;</span>
                <span class="flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $article->reading_minutes }} {{ $article->language->code === 'hi' ? 'मिनट पठन' : 'min read' }}
                </span>
            </div>

            <!-- Title (H1) -->
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-4 tracking-tight">
                {{ $article->title }}
            </h1>

            <!-- Publication Meta Strip -->
            <div class="flex flex-wrap items-center justify-between gap-4 py-3 border-y border-slate-100 text-xs text-slate-500 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-700">
                        {{ substr($article->author?->name ?? 'E', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800">{{ $article->author?->name ?? 'Civil Service Gurukul Faculty' }}</div>
                        <div>{{ $article->published_at ? $article->published_at->format('d M Y, h:i A') : now()->format('d M Y') }}</div>
                    </div>
                </div>

                <!-- Source Citation -->
                @if($article->source_name)
                    <div class="text-right">
                        <span class="text-slate-400">Source:</span>
                        <span class="font-medium text-slate-700 ml-1">{{ $article->source_name }}</span>
                    </div>
                @endif
            </div>

            <!-- Share Rail (Horizontal Top) -->
            <div class="flex items-center space-x-2 pb-6 mb-6 border-b border-slate-100 text-xs text-slate-600">
                <span class="font-bold mr-2 text-slate-800">Share:</span>
                <a href="{{ $shareLinks['whatsapp'] }}" target="_blank" rel="noopener" class="p-2 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition" title="WhatsApp">WhatsApp</a>
                <a href="{{ $shareLinks['telegram'] }}" target="_blank" rel="noopener" class="p-2 rounded bg-sky-50 text-sky-700 hover:bg-sky-100 transition" title="Telegram">Telegram</a>
                <a href="{{ $shareLinks['twitter'] }}" target="_blank" rel="noopener" class="p-2 rounded bg-slate-100 text-slate-800 hover:bg-slate-200 transition" title="X / Twitter">X</a>
                <!-- Print & PDF Download Action Group -->
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('articles.pdf', ['slug' => $article->slug]) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-700 font-bold border border-red-200 transition text-xs shadow-2xs"
                       title="Download Article PDF with Civil Service Gurukul Watermark">
                        <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ $article->has_uploaded_pdf ? 'Download Official PDF' : 'Download PDF' }}</span>
                    </a>

                    <a href="{{ route('articles.pdf', ['slug' => $article->slug, 'action' => 'stream']) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold border border-slate-200 transition text-xs"
                       title="Print or View Watermarked PDF">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Print</span>
                    </a>
                </div>
            </div>

            <!-- Featured Hero Image -->
            @if($article->feature_image_url)
                <div class="mb-8 rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs bg-slate-100">
                    <img src="{{ $article->feature_image_url }}" 
                         alt="{{ $article->heroMedia?->alt_text ?: $article->title }}" 
                         class="w-full h-auto max-h-[500px] object-cover" 
                         loading="eager">
                    @if($article->heroMedia?->caption || $article->heroMedia?->credit)
                        <div class="px-4 py-2 bg-slate-50 text-[11px] text-slate-500 italic border-t border-slate-100 flex items-center justify-between">
                            <span>{{ $article->heroMedia->caption }}</span>
                            @if($article->heroMedia->credit)
                                <span class="font-medium text-slate-600 not-italic">Source: {{ $article->heroMedia->credit }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endif

            <!-- In-Page Table of Contents (Mobile & Tablet) -->
            @if($article->sections->count() > 0)
                <div class="lg:hidden mb-6 p-4 rounded-lg bg-amber-50/50 border border-amber-200/60">
                    <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-2">Table of Contents</h3>
                    <ul class="space-y-1.5 text-xs text-amber-800">
                        @foreach($article->sections as $sec)
                            <li>
                                <a href="#{{ $sec->anchor }}" class="hover:underline flex items-center">
                                    <span class="mr-1.5 text-amber-600">&bull;</span> {{ $sec->heading }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Excerpt / Intro -->
            @if($article->excerpt)
                <div class="text-base text-slate-700 font-medium leading-relaxed mb-6 bg-slate-50 p-4 rounded-lg border-l-4 border-amber-500">
                    {{ $article->excerpt }}
                </div>
            @endif

            <!-- Question-Style Anchored Sections -->
            <div class="prose prose-slate max-w-none space-y-8">
                @if($article->sections->count() > 0)
                    @foreach($article->sections as $sec)
                        <section id="{{ $sec->anchor }}" class="scroll-mt-24">
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 pb-2 border-b border-slate-200 mb-4 flex items-center">
                                <span class="w-2 h-2 rounded-full bg-amber-600 mr-2.5 inline-block"></span>
                                {{ $sec->heading }}
                            </h2>
                            <div class="text-slate-800 leading-relaxed text-sm sm:text-base space-y-4">
                                {!! $sec->body_html !!}
                            </div>
                        </section>
                    @endforeach
                @else
                    <div class="text-slate-800 leading-relaxed text-sm sm:text-base space-y-4">
                        {!! $article->body_html !!}
                    </div>
                @endif
            </div>

            <!-- Associated Practice Quiz for Students -->
            @if($article->quiz)
                <div class="mt-10 rounded-2xl bg-gradient-to-br from-amber-500/10 via-white to-orange-500/10 border-2 border-amber-300 shadow-sm overflow-hidden">
                    <!-- Top Banner -->
                    <div class="bg-gradient-to-r from-amber-700 via-amber-600 to-orange-600 text-white px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center space-x-2.5">
                            <span class="p-1.5 rounded-lg bg-white/20">
                                <svg class="w-5 h-5 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </span>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-amber-200">
                                    {{ $locale === 'hi' ? 'संबंधित दैनिक परीक्षा' : 'Associated Editorial Prelims Test' }}
                                </span>
                                <h3 class="text-base sm:text-lg font-black text-white leading-tight">
                                    {{ $article->quiz->title }}
                                </h3>
                            </div>
                        </div>

                        <!-- Status or Attempt Badge -->
                        @if($studentQuizAttempt && $studentQuizAttempt->status === 'completed')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/30 border border-emerald-300 text-white text-xs font-bold">
                                <span>Score: {{ $studentQuizAttempt->score }} / {{ $article->quiz->total_marks }}</span>
                                <span>({{ round(($studentQuizAttempt->score / max(1, $article->quiz->total_marks)) * 100) }}%)</span>
                            </div>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold">
                                {{ $article->quiz->is_free ? 'Free for Aspirants' : 'Enrolled Students' }}
                            </span>
                        @endif
                    </div>

                    <!-- Test Metrics Matrix -->
                    <div class="p-6 space-y-6">
                        @if($article->quiz->instructions)
                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                {{ $article->quiz->instructions }}
                            </p>
                        @endif

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                            <div class="p-3 rounded-xl bg-white border border-amber-200 shadow-xs">
                                <span class="block text-xl font-black text-amber-800">{{ $article->quiz->questions->count() }}</span>
                                <span class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Questions</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-amber-200 shadow-xs">
                                <span class="block text-xl font-black text-amber-800">{{ $article->quiz->duration_minutes }}m</span>
                                <span class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Duration</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-amber-200 shadow-xs">
                                <span class="block text-xl font-black text-amber-800">{{ $article->quiz->total_marks }}</span>
                                <span class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Total Marks</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white border border-amber-200 shadow-xs">
                                <span class="block text-xl font-black text-rose-700">-{{ $article->quiz->negative_marks_per_question }}</span>
                                <span class="text-[11px] text-slate-500 font-semibold uppercase tracking-wider">Negative Mark</span>
                            </div>
                        </div>

                        <!-- CTA Actions -->
                        <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                            @auth
                                @if($studentQuizAttempt && $studentQuizAttempt->status === 'completed')
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <a href="{{ route('student.quiz.result', $studentQuizAttempt) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                                            <span>View Detailed Scorecard & Analysis</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                        @if($article->quiz->allow_reattempt)
                                            <a href="{{ route('student.quiz.start', $article->quiz) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-amber-400 text-amber-800 hover:bg-amber-100 font-bold text-xs transition">
                                                <span>Re-attempt Quiz</span>
                                            </a>
                                        @endif
                                    </div>
                                @elseif($studentQuizAttempt && $studentQuizAttempt->status === 'in_progress')
                                    <a href="{{ route('student.quiz.attempt', $studentQuizAttempt) }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                                        <span>Resume Active Test</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                @else
                                    <a href="{{ route('student.quiz.start', $article->quiz) }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                                        <span>Start Interactive Test ({{ $article->quiz->duration_minutes }} Mins)</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                @endif
                            @else
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ route('login', ['redirect' => route('quizzes.show', $article->quiz->slug)]) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                                        <span>Login to Attempt & Track Score</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                    <a href="{{ route('register') }}" class="text-xs text-amber-800 hover:text-amber-900 font-bold underline">
                                        New Student? Register Free
                                    </a>
                                </div>
                            @endauth

                            <a href="{{ route('quizzes.show', $article->quiz->slug) }}" class="text-xs font-bold text-slate-600 hover:text-amber-700 transition flex items-center gap-1">
                                <span>Quiz Info Page</span> &rarr;
                            </a>
                        </div>

                        <!-- Instant Interactive Questions Preview -->
                        @if($article->quiz->questions->count() > 0)
                            <div class="pt-6 border-t border-amber-200">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Instant Practice Preview (Sample Questions)
                                    </h4>
                                    <span class="text-[11px] text-slate-500 font-medium">Test yourself below</span>
                                </div>

                                <div class="space-y-4">
                                    @foreach($article->quiz->questions->take(2) as $idx => $sampleQ)
                                        <div x-data="{ revealed: false, selectedOpt: null }" class="p-4 rounded-xl bg-white border border-amber-200 text-xs">
                                            <div class="font-semibold text-slate-900 mb-2.5 leading-relaxed">
                                                <span class="font-bold text-amber-700 mr-1">Q{{ $idx + 1 }}.</span> {!! $sampleQ->stem !!}
                                            </div>

                                            <div class="space-y-1.5 mb-3">
                                                @foreach($sampleQ->options as $optIdx => $opt)
                                                    <button type="button"
                                                            @click="selectedOpt = {{ $opt->id }}"
                                                            :class="{
                                                                'border-amber-500 bg-amber-50 text-amber-900': selectedOpt === {{ $opt->id }} && !revealed,
                                                                'border-emerald-500 bg-emerald-50 text-emerald-900 font-bold': revealed && {{ $opt->is_correct ? 'true' : 'false' }},
                                                                'border-rose-300 bg-rose-50 text-rose-900': revealed && selectedOpt === {{ $opt->id }} && !{{ $opt->is_correct ? 'true' : 'false' }},
                                                                'border-slate-200 bg-white hover:bg-slate-50': selectedOpt !== {{ $opt->id }} && !revealed
                                                            }"
                                                            class="w-full text-left p-2 rounded-lg border transition flex items-center justify-between text-xs">
                                                        <span><strong class="mr-1.5">({{ chr(65 + $optIdx) }})</strong> {{ $opt->option_text }}</span>
                                                        <template x-if="revealed && {{ $opt->is_correct ? 'true' : 'false' }}">
                                                            <span class="text-emerald-600 font-bold text-[10px] uppercase tracking-wider">Correct</span>
                                                        </template>
                                                    </button>
                                                @endforeach
                                            </div>

                                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                                <button @click="revealed = !revealed" class="text-xs font-bold text-amber-700 hover:text-amber-800 underline">
                                                    <span x-text="revealed ? 'Hide Explanation' : 'Check Correct Answer & Explanation'"></span> &rarr;
                                                </button>
                                                <span class="text-[10px] text-slate-400 font-medium">{{ $sampleQ->topic_tag ?? 'General Studies' }}</span>
                                            </div>

                                            <div x-show="revealed" class="mt-2.5 p-3 rounded-lg bg-emerald-50/60 border border-emerald-200 text-xs text-slate-700 leading-relaxed" style="display: none;">
                                                <div class="font-bold text-emerald-800 mb-1">Detailed Explanation:</div>
                                                <div>{!! $sampleQ->explanation !!}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Terminal PYQ (Previous Year Questions) Block -->
            @foreach($article->blocks->where('type', 'pyq') as $block)
                @php $pyq = $block->payload_json; @endphp
                <div class="mt-10 p-6 rounded-xl bg-amber-50/70 border border-amber-300">
                    <div class="flex items-center space-x-2 text-amber-900 font-black text-sm uppercase tracking-wider mb-3">
                        <svg class="w-5 h-5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>UPSC Civil Services Examination — Previous Year Question (PYQ)</span>
                    </div>

                    <p class="text-sm font-semibold text-slate-900 mb-3 leading-relaxed">
                        {{ $pyq['question'] ?? '' }}
                    </p>

                    @if(!empty($pyq['options']))
                        <div class="space-y-1.5 text-xs sm:text-sm text-slate-700 mb-4">
                            @foreach($pyq['options'] as $idx => $opt)
                                <div class="flex items-center p-2 rounded bg-white border border-amber-200">
                                    <span class="font-bold mr-2 text-amber-800">({{ chr(65 + $idx) }})</span>
                                    <span>{{ $opt }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div x-data="{ showAnswer: false }" class="pt-2 border-t border-amber-200 text-xs">
                        <button @click="showAnswer = !showAnswer" class="text-amber-800 hover:text-amber-900 font-bold underline">
                            <span x-text="showAnswer ? 'Hide Answer & Explanation' : 'View Correct Answer & Explanation'"></span> &rarr;
                        </button>
                        <div x-show="showAnswer" class="mt-3 p-3 bg-white rounded border border-amber-200 text-xs leading-relaxed" style="display: none;">
                            <p class="font-bold text-emerald-700 mb-1">Answer: {{ $pyq['answer'] ?? '' }}</p>
                            <p class="text-slate-600">{{ $pyq['explanation'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Tags Chips -->
            @if($article->tags->count() > 0)
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5">Topic Tags:</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($article->tags as $tag)
                            <a href="{{ url('/tag/' . $tag->slug) }}" class="inline-block bg-slate-100 hover:bg-amber-100 text-slate-700 hover:text-amber-800 text-xs px-3 py-1 rounded-full transition">
                                # {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Previous / Next Navigation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-8 pt-6 border-t border-slate-200 text-xs">
                @if($prevArticle)
                    <a href="{{ $prevArticle->url }}" class="p-3 rounded-lg border border-slate-200 hover:border-amber-500 transition block">
                        <span class="text-slate-400 block mb-1">&larr; Previous Article</span>
                        <span class="font-bold text-slate-800 line-clamp-1">{{ $prevArticle->title }}</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextArticle)
                    <a href="{{ $nextArticle->url }}" class="p-3 rounded-lg border border-slate-200 hover:border-amber-500 transition block text-right">
                        <span class="text-slate-400 block mb-1">Next Article &rarr;</span>
                        <span class="font-bold text-slate-800 line-clamp-1">{{ $nextArticle->title }}</span>
                    </a>
                @endif
            </div>

            <!-- Trailing Lead Capture Block -->
            <div class="mt-10">
                <x-lead-capture-form 
                    title="Prepare with Mentors Who Cleared the Exam" 
                    subtitle="Get personalized study plans, evaluated answer models, and test series access." 
                    layout="card" 
                    source="article_footer" />
            </div>
        </article>

        <!-- Right Column: Sticky Table of Contents & Related Rail (4 Cols) -->
        <aside class="lg:col-span-4 space-y-6">
            
            <!-- Sticky Table of Contents (Desktop) -->
            @if($article->sections->count() > 0)
                <div class="hidden lg:block bg-white p-5 rounded-xl shadow-xs border border-slate-200 sticky top-20">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-3 pb-2 border-b border-slate-100 flex items-center">
                        <svg class="w-4 h-4 text-amber-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        Table of Contents
                    </h3>
                    <nav class="space-y-2 text-xs">
                        @foreach($article->sections as $sec)
                            <a href="#{{ $sec->anchor }}" class="text-slate-600 hover:text-amber-600 hover:underline block leading-snug transition py-0.5">
                                {{ $sec->heading }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif

            <!-- Associated Quiz Card in Sidebar -->
            @if($article->quiz)
                <div class="bg-gradient-to-br from-amber-600 to-orange-700 text-white p-5 rounded-xl shadow-md space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded bg-white/20 text-white font-extrabold text-[10px] uppercase tracking-wider">
                            Editorial Test
                        </span>
                        <span class="text-amber-200 text-xs font-semibold">
                            {{ $article->quiz->duration_minutes }} Mins
                        </span>
                    </div>

                    <div>
                        <h4 class="font-bold text-sm text-white leading-snug">
                            {{ $article->quiz->title }}
                        </h4>
                        <p class="text-xs text-amber-100 mt-1">
                            {{ $article->quiz->questions->count() }} Questions &bull; {{ $article->quiz->total_marks }} Marks
                        </p>
                    </div>

                    @auth
                        @if($studentQuizAttempt && $studentQuizAttempt->status === 'completed')
                            <a href="{{ route('student.quiz.result', $studentQuizAttempt) }}" class="block w-full text-center bg-white hover:bg-amber-50 text-amber-900 font-bold py-2 px-3 rounded-lg text-xs shadow-xs transition">
                                Review Score ({{ $studentQuizAttempt->score }}/{{ $article->quiz->total_marks }}) &rarr;
                            </a>
                        @else
                            <a href="{{ route('student.quiz.start', $article->quiz) }}" class="block w-full text-center bg-white hover:bg-amber-50 text-amber-900 font-bold py-2 px-3 rounded-lg text-xs shadow-xs transition">
                                Start Quiz &rarr;
                            </a>
                        @endif
                    @else
                        <a href="{{ route('quizzes.show', $article->quiz->slug) }}" class="block w-full text-center bg-white hover:bg-amber-50 text-amber-900 font-bold py-2 px-3 rounded-lg text-xs shadow-xs transition">
                            Take Quiz Free &rarr;
                        </a>
                    @endauth
                </div>
            @endif

            <!-- Article PDF Document Download Card -->
            <div class="bg-gradient-to-br from-red-50 to-orange-50 border border-red-200/80 rounded-xl p-5 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 font-extrabold text-[10px] uppercase tracking-wider flex items-center gap-1">
                        <svg class="w-3 h-3 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        <span>PDF Edition</span>
                    </span>
                    <span class="text-[11px] font-semibold text-slate-500">
                        {{ $article->has_uploaded_pdf ? 'Official File' : 'Watermarked PDF' }}
                    </span>
                </div>

                <div>
                    <h4 class="font-bold text-sm text-slate-900 leading-snug">
                        Download Print-Ready Revision PDF
                    </h4>
                    <p class="text-xs text-slate-600 mt-1">
                        {{ $article->has_uploaded_pdf ? 'Official document uploaded by Civil Service Gurukul faculty.' : 'Complete article with practice MCQs & Civil Service Gurukul watermark.' }}
                    </p>
                </div>

                <div class="pt-1 flex flex-col gap-2">
                    <a href="{{ route('articles.pdf', ['slug' => $article->slug]) }}" 
                       class="w-full text-center bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-3 rounded-lg text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        <span>Download PDF Document</span>
                    </a>
                    <a href="{{ route('articles.pdf', ['slug' => $article->slug, 'action' => 'stream']) }}" 
                       target="_blank"
                       class="w-full text-center bg-white hover:bg-slate-50 text-slate-700 font-semibold py-1.5 px-3 rounded-lg text-xs border border-slate-200 transition">
                        Open & Print in Browser &rarr;
                    </a>
                </div>
            </div>

            <!-- Related Articles Rail -->
            <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">
                    Related Analysis
                </h3>
                <div class="space-y-4">
                    @forelse($relatedArticles as $rel)
                        <div class="text-xs group">
                            <span class="text-[10px] text-amber-600 font-bold block mb-0.5">
                                {{ $rel->published_at ? $rel->published_at->format('d M Y') : '' }}
                            </span>
                            <a href="{{ $rel->url }}" class="font-semibold text-slate-800 group-hover:text-amber-600 leading-snug transition line-clamp-2">
                                {{ $rel->title }}
                            </a>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No related items currently available.</p>
                    @endforelse
                </div>
            </div>

            <!-- Free Downloads Highlight Widget -->
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white p-5 rounded-xl shadow-sm text-xs">
                <span class="bg-amber-500 text-slate-950 font-black px-2 py-0.5 rounded text-[10px] uppercase tracking-wider">Free PDF</span>
                <h4 class="font-bold text-sm text-white mt-2 mb-1">Monthly Current Affairs Compendium</h4>
                <p class="text-slate-400 mb-3 leading-relaxed">High-yield revision compendium compiled for Prelims & Mains 2026-27.</p>
                <a href="{{ url('/free-downloads') }}" class="inline-block bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-3 py-1.5 rounded transition">
                    Download Free PDF &rarr;
                </a>
            </div>
        </aside>

    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    /* Continuous Watermark when printing directly from browser */
    body::before {
        content: "CIVIL SERVICE GURUKUL";
        position: fixed;
        top: 40%;
        left: -10%;
        width: 120%;
        text-align: center;
        transform: rotate(-32deg);
        font-size: 50pt;
        font-weight: 900;
        color: rgba(180, 83, 9, 0.09) !important;
        letter-spacing: 6px;
        text-transform: uppercase;
        pointer-events: none;
        z-index: 99999;
    }
    nav, aside, footer, header, .no-print, button {
        display: none !important;
    }
    article {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
}
</style>
@endpush
