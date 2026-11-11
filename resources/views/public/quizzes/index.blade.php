@extends('layouts.app')

@section('title', $locale === 'hi' ? 'दैनिक क्विज़ व टेस्ट सीरीज़ - सिविल सेवा गुरुकुल' : 'Daily Quizzes & Mock Test Series - Civil Service Gurukul')
@section('meta_description', $locale === 'hi' ? 'यूपीएससी और राज्य लोक सेवा आयोग परीक्षाओं के लिए दैनिक प्रारंभिक परीक्षा क्विज़, मॉक टेस्ट और विस्तृत समाधान।' : 'Daily UPSC Prelims test series, current affairs quizzes, and full-length mock tests with instant scorecards and negative marking.')

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 to-brand-navy rounded-3xl p-8 text-white">
        <div class="max-w-3xl space-y-3">
            <span class="inline-block px-3 py-1 rounded-full bg-brand-orange/20 border border-brand-orange/30 text-brand-orange text-xs font-semibold uppercase tracking-wider">
                {{ $locale === 'hi' ? 'प्रारंभिक परीक्षा अभ्यास' : 'Prelims Test Portal' }}
            </span>
            <h1 class="text-3xl font-extrabold tracking-tight">
                {{ $locale === 'hi' ? 'दैनिक क्विज़ व टेस्ट सीरीज़' : 'Daily UPSC Quizzes & Mock Test Series' }}
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed">
                {{ $locale === 'hi' 
                    ? 'दैनिक समसामयिकी और सामान्य अध्ययन विषयों पर आधारित समयबद्ध ऑनलाइन टेस्ट। तुरंत विस्तृत स्कोरकार्ड और अखिल भारतीय रैंक प्राप्त करें।' 
                    : 'Real-time timed tests calibrated to UPSC CSE prelims standard with negative marking, question palette, and comprehensive question-by-question explanations.' }}
            </p>
        </div>
    </div>

    <!-- Category Filter Strip -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs">
        <a href="{{ $locale === 'hi' ? url('/hindi/quizzes') : url('/quizzes') }}" 
           class="px-4 py-2 rounded-xl font-semibold transition-all whitespace-nowrap {{ !request('category') ? 'bg-brand-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
            {{ $locale === 'hi' ? 'सभी टेस्ट' : 'All Tests' }}
        </a>
        @foreach($categories as $cat)
            <a href="{{ ($locale === 'hi' ? url('/hindi/quizzes') : url('/quizzes')) . '?category=' . $cat->slug }}" 
               class="px-4 py-2 rounded-xl font-semibold transition-all whitespace-nowrap {{ request('category') == $cat->slug ? 'bg-brand-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' }}">
                {{ $cat->name }}
            </a>
        @endforeach
    </div>

    <!-- Quizzes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($quizzes as $quiz)
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-card hover:shadow-cardHover transition-all flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-semibold bg-brand-orange/10 text-brand-orange">
                            {{ $quiz->category ? $quiz->category->name : 'General Studies' }}
                        </span>
                        <span class="text-xs text-slate-500 font-mono font-medium flex items-center gap-1">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Time : {{ $quiz->duration_minutes }} min
                        </span>
                    </div>

                    <h2 class="text-base font-bold text-slate-900 group-hover:text-brand-navy transition-colors line-clamp-2 leading-snug">
                        <a href="{{ $locale === 'hi' ? url('/hindi/quizzes/' . $quiz->slug) : url('/quizzes/' . $quiz->slug) }}">
                            {{ $quiz->title }}
                        </a>
                    </h2>

                    <!-- Specs strip -->
                    <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-slate-50 text-[11px] text-slate-600 text-center border border-slate-100 font-mono">
                        <div>
                            <div class="text-slate-400 text-[10px] uppercase">Questions</div>
                            <div class="font-bold text-slate-900 text-xs mt-0.5">{{ $quiz->questions->count() }} Qs</div>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[10px] uppercase">Marks</div>
                            <div class="font-bold text-slate-900 text-xs mt-0.5">{{ (int)$quiz->total_marks }} M</div>
                        </div>
                        <div>
                            <div class="text-slate-400 text-[10px] uppercase">Negative</div>
                            <div class="font-bold text-red-600 text-xs mt-0.5">-{{ $quiz->negative_marks_per_question }}</div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                        {{ $quiz->is_free ? 'Free Test' : 'Premium' }}
                    </span>

                    <a href="{{ $locale === 'hi' ? url('/hindi/quizzes/' . $quiz->slug) : url('/quizzes/' . $quiz->slug) }}" 
                       class="px-4 py-2 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-semibold shadow-sm transition-all inline-flex items-center gap-1">
                        <span>{{ $locale === 'hi' ? 'टेस्ट दें' : 'Take Test' }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200 text-slate-500">
                <p>No tests available in this category currently.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($quizzes->hasPages())
        <div class="p-4 bg-white rounded-xl border border-slate-200">
            {{ $quizzes->links() }}
        </div>
    @endif
</div>
@endsection
