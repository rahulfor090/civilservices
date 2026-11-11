@extends('layouts.app')

@section('title', $locale === 'hi' ? 'सिविल सेवा पाठ्यक्रम व टेस्ट सीरीज़ - सिविल सेवा गुरुकुल' : 'Civil Services Courses, Batches & Test Series - Civil Service Gurukul')
@section('meta_description', $locale === 'hi' ? 'यूपीएससी एवं राज्य लोक सेवा आयोग (UPPCS, BPSC, RAS) परीक्षाओं के लिए प्रामाणिक फाउंडेशन, प्रीलिम्स और मेन्स कक्षा कार्यक्रम।' : 'Comprehensive UPSC IAS & State PCS foundation batches, prelims crash courses, and mains mentorship programmes by Civil Service Gurukul.')

@section('content')
<div class="space-y-10">
    <!-- Hero Banner -->
    <div class="bg-gradient-to-br from-slate-950 via-[#0B132B] to-slate-900 text-white rounded-3xl p-8 sm:p-12 border border-slate-800 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-xs font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                {{ $locale === 'hi' ? 'प्रवेश प्रारंभ 2026-27' : 'Admissions Open 2026-27' }}
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight">
                {{ $locale === 'hi' ? 'प्रामाणिक सिविल सेवा पाठ्यक्रम व कक्षा कार्यक्रम' : 'Authoritative Civil Services Programmes & Batches' }}
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                {{ $locale === 'hi' 
                    ? 'अखिल भारतीय स्तर पर सर्वश्रेष्ठ शिक्षकों द्वारा संरचित लाइव इंटरैक्टिव कक्षाएं, विषयवार माइंड मैप्स, दैनिक उत्तर लेखन और व्यापक अध्ययन सामग्री।' 
                    : 'Classroom and online hybrid batches taught by distinguished civil service faculty. Includes HD lecture library, evaluated mains answer copies, and personal mentorship desk.' }}
            </p>
        </div>
    </div>

    <!-- Exam Filter Tabs -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 mr-2">Filter by Exam:</span>
        <a href="{{ route('courses.index') }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('exam') ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
            All Programmes
        </a>
        <a href="{{ route('courses.index', ['exam' => 'upsc_cse']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('exam') === 'upsc_cse' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
            UPSC CSE (IAS/IPS)
        </a>
        <a href="{{ route('courses.index', ['exam' => 'uppcs']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('exam') === 'uppcs' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
            UPPCS (UP)
        </a>
        <a href="{{ route('courses.index', ['exam' => 'bpsc']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('exam') === 'bpsc' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
            BPSC (Bihar)
        </a>
        <a href="{{ route('courses.index', ['exam' => 'ras']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('exam') === 'ras' ? 'bg-amber-600 text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
            RAS (Rajasthan)
        </a>
    </div>

    <!-- Courses Grid -->
    @if($courses->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($courses as $course)
                @php
                    $discount = ($course->mrp > $course->price && $course->mrp > 0)
                        ? round((($course->mrp - $course->price) / $course->mrp) * 100)
                        : 0;
                    $firstBatch = $course->batches->first();
                @endphp
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden group">
                    <!-- Top Gradient Header / Badge -->
                    <div class="bg-gradient-to-r from-slate-900 to-[#0B132B] p-6 text-white relative">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                {{ strtoupper(str_replace('_', ' ', $course->target_exam)) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/10 text-slate-200 uppercase">
                                {{ ucfirst($course->level) }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold font-heading group-hover:text-amber-400 transition-colors leading-snug">
                            <a href="{{ route('courses.public.show', $course->slug) }}">{{ $course->title }}</a>
                        </h3>
                    </div>

                    <!-- Body Content -->
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed line-clamp-3">
                            {{ $course->excerpt ?: strip_tags($course->description) }}
                        </p>

                        <!-- Batch & Feature Meta -->
                        <div class="space-y-2 py-3 border-y border-slate-100 text-xs text-slate-600">
                            @if($firstBatch)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Batch Mode:</span>
                                    <span class="font-bold text-slate-800 capitalize">{{ $firstBatch->delivery_mode }} &bull; {{ $firstBatch->batch_code }}</span>
                                </div>
                                @if($firstBatch->faculty_names)
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-slate-400 shrink-0">Mentors:</span>
                                        <span class="font-semibold text-slate-700 text-right truncate">{{ $firstBatch->faculty_names }}</span>
                                    </div>
                                @endif
                            @endif
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Course Validity:</span>
                                <span class="font-bold text-slate-800 font-mono">{{ $course->validity_days }} Days Access</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Study Medium:</span>
                                <span class="font-semibold text-slate-700">{{ $course->language->name ?? 'English / Hindi' }}</span>
                            </div>
                        </div>

                        <!-- Pricing & CTA Block -->
                        <div>
                            <div class="flex items-baseline justify-between mb-4">
                                <div>
                                    <div class="text-2xl font-extrabold text-slate-900 font-mono">
                                        @if($course->price > 0)
                                            ₹{{ number_format($course->price, 0) }}
                                        @else
                                            <span class="text-emerald-600 font-sans">FREE ENROLLMENT</span>
                                        @endif
                                    </div>
                                    @if($course->mrp > $course->price && $course->price > 0)
                                        <div class="text-xs text-slate-400">
                                            MRP <span class="line-through">₹{{ number_format($course->mrp, 0) }}</span>
                                            <span class="text-emerald-600 font-bold ml-1">({{ $discount }}% OFF)</span>
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400">GST Inclusive</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <a href="{{ route('courses.public.show', $course->slug) }}" 
                                   class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold text-center transition">
                                    View Syllabus
                                </a>

                                <form action="{{ route('courses.enroll', $course) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full py-2.5 px-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold text-center shadow-md shadow-amber-600/20 transition flex items-center justify-center gap-1">
                                        <span>Enroll Now</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pt-6">
            {{ $courses->links() }}
        </div>
    @else
        <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 text-slate-500 space-y-3">
            <svg class="w-12 h-12 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <h3 class="text-base font-bold text-slate-700">No Courses Found Matching Filter</h3>
            <p class="text-xs text-slate-400">Try selecting another exam category or browse all available programmes.</p>
            <a href="{{ route('courses.index') }}" class="inline-block px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold">
                Reset Filter
            </a>
        </div>
    @endif
</div>
@endsection
