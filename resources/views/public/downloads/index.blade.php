@extends('layouts.app')

@section('title', $locale === 'hi' ? 'मुफ्त अध्ययन सामग्री व डाउनलोड्स - सिविल सेवा गुरुकुल' : 'Free UPSC Study Material & Downloads - Civil Service Gurukul')
@section('meta_description', $locale === 'hi' ? 'यूपीएससी पाठ्यक्रम, पूर्व वर्षों के प्रश्न पत्र, टॉपर्स कॉपियां और मासिक समसामयिकी पत्रिकाएं मुफ्त डाउनलोड करें।' : 'Download verified UPSC Civil Services syllabus, previous year question papers, toppers test copies, and monthly current affairs compilations.')

@section('content')
<div class="space-y-10">
    <!-- Header -->
    <div class="bg-gradient-to-r from-slate-900 to-brand-navy rounded-3xl p-8 text-white">
        <div class="max-w-3xl space-y-3">
            <span class="inline-block px-3 py-1 rounded-full bg-white/10 text-xs font-semibold uppercase tracking-wider text-brand-orange border border-white/10">
                {{ $locale === 'hi' ? 'ई-संसाधन केंद्र' : 'E-Resource Centre' }}
            </span>
            <h1 class="text-3xl font-extrabold tracking-tight">
                {{ $locale === 'hi' ? 'मुफ्त अध्ययन सामग्री व पीडीएफ डाउनलोड्स' : 'Free UPSC Study Material & PDF Downloads' }}
            </h1>
            <p class="text-slate-300 text-sm leading-relaxed">
                {{ $locale === 'hi' 
                    ? 'सिविल सेवा अभ्यर्थियों के लिए प्रमाणित पाठ्यक्रम, पूर्व वर्षों के हल प्रश्न पत्र, और मासिक पत्रिकाएं।' 
                    : 'Curated PDFs, officially calibrated syllabus breakdowns, past 10 years prelims and mains question papers.' }}
            </p>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($categories as $cat)
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-card hover:shadow-cardHover transition-all flex flex-col justify-between">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-navy/10 text-brand-navy flex items-center justify-center font-bold text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">{{ $cat->name }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2">{{ $cat->description }}</p>
                    <span class="inline-block text-[11px] font-medium text-slate-400">
                        {{ $cat->downloads->count() }} {{ $locale === 'hi' ? 'दस्तावेज' : 'documents' }}
                    </span>
                </div>
                <div class="pt-4 mt-2 border-t border-slate-100">
                    <a href="{{ $locale === 'hi' ? url('/hindi/downloads/' . $cat->slug) : url('/downloads/' . $cat->slug) }}" 
                       class="text-xs font-semibold text-brand-orange hover:underline inline-flex items-center gap-1">
                        {{ $locale === 'hi' ? 'श्रेणी देखें' : 'View Category' }} &rarr;
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Recent Downloads List -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-card space-y-4">
        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
            {{ $locale === 'hi' ? 'हालिया प्रकाशित डाउनलोड्स' : 'Recently Uploaded Documents' }}
        </h2>

        <div class="divide-y divide-slate-100">
            @forelse($recentDownloads as $item)
                <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 hover:bg-slate-50/60 px-3 rounded-xl transition-colors">
                    <div class="flex items-start gap-3">
                        <span class="p-2 rounded-lg bg-red-50 text-red-600 shrink-0">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">{{ $item->title }}</h3>
                            <div class="flex items-center gap-3 text-xs text-slate-400 mt-0.5">
                                <span>{{ $item->category ? $item->category->name : 'General' }}</span>
                                <span>&bull;</span>
                                <span>{{ $item->file_size_formatted ?? 'PDF' }}</span>
                                <span>&bull;</span>
                                <span>{{ $item->download_count }} downloads</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ $item->file_url ?? '#' }}" 
                           download
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download PDF</span>
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 text-sm py-6 text-center">No downloads available at this time.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
