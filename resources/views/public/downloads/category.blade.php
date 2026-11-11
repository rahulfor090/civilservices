@extends('layouts.app')

@section('title', $category->name . ' - ' . ($locale === 'hi' ? 'सिविल सेवा गुरुकुल डाउनलोड' : 'Civil Service Gurukul Downloads'))
@section('meta_description', $category->description ?? 'Download verified resources for ' . $category->name)

@section('content')
<div class="space-y-8">
    <div class="bg-gradient-to-r from-slate-900 to-brand-navy rounded-3xl p-8 text-white">
        <div class="space-y-3">
            <a href="{{ $locale === 'hi' ? url('/hindi/downloads') : url('/downloads') }}" class="text-xs text-brand-orange hover:underline font-semibold inline-flex items-center gap-1">
                &larr; {{ $locale === 'hi' ? 'सभी श्रेणियां' : 'All Categories' }}
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">{{ $category->name }}</h1>
            <p class="text-slate-300 text-sm max-w-2xl">{{ $category->description }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-card space-y-4">
        <div class="divide-y divide-slate-100">
            @forelse($category->downloads as $item)
                <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50/60 px-4 rounded-xl transition-colors">
                    <div class="flex items-start gap-3">
                        <span class="p-2.5 rounded-xl bg-red-50 text-red-600 shrink-0">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                        </span>
                        <div>
                            <h2 class="text-base font-bold text-slate-900">{{ $item->title }}</h2>
                            <p class="text-xs text-slate-500 mt-1">{{ $item->description }}</p>
                            <div class="flex items-center gap-3 text-xs text-slate-400 mt-2">
                                <span>{{ $item->file_size_formatted ?? 'PDF' }}</span>
                                <span>&bull;</span>
                                <span>{{ $item->download_count }} downloads</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ $item->file_url ?? '#' }}" 
                           download
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-semibold shadow-sm transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download Now</span>
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 text-sm py-8 text-center">No downloads available in this category yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
