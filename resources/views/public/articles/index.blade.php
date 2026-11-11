@extends('layouts.app')

@section('title', ($contentType ? $contentType->name : 'Daily Current Affairs & Analysis') . ' | Civil Service Gurukul')
@section('meta_description', 'Archive of ' . ($contentType ? $contentType->name : 'Daily Current Affairs') . ' for civil service exam preparation. Structured day-by-day analysis and PYQ mapping.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Breadcrumb Hierarchy -->
    <nav class="flex items-center text-xs text-slate-500 mb-4" aria-label="Breadcrumb">
        <a href="{{ url($locale === 'hi' ? '/hindi' : '/') }}" class="hover:text-amber-600 transition">
            {{ $locale === 'hi' ? 'होम' : 'Home' }}
        </a>
        <span class="mx-2 text-slate-400">/</span>
        <span class="text-slate-800 font-medium truncate">
            {{ $contentType ? $contentType->name : 'Daily Updates' }}
        </span>
    </nav>

    <!-- Header Description Banner -->
    <div class="bg-white p-6 rounded-xl shadow-xs border border-slate-200 mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2 tracking-tight">
            {{ $contentType ? $contentType->name : ($locale === 'hi' ? 'दैनिक समसामयिकी एवं विश्लेषण' : 'Daily Current Affairs & News Analysis') }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl">
            {{ $locale === 'hi'
                ? 'दैनिक समाचार विश्लेषण (DNA) एवं संपादकीय सारांश। द हिन्दू, इंडियन एक्सप्रेस, पीआईबी, एवं संसद टीवी से संकलित उच्च-प्राथमिकता वाले परीक्षा-उपयोगी तथ्यों का विश्लेषणात्मक संकलन। रविवार एवं राष्ट्रीय अवकाश को छोड़कर प्रतिदिन प्रकाशित।'
                : 'Our daily news analysis consists of two integral parts — Daily News Analysis and Important Editorials curated rigorously from The Hindu, The Indian Express, LiveMint, PIB, and Sansad TV debates. Content is mapped directly to UPSC CSE and State PCS syllabi.' }}
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Column: Numbered Articles List (8 Cols) -->
        <div class="lg:col-span-8 space-y-4">
            
            <!-- Filter & Sort Bar -->
            <div class="bg-white p-4 rounded-lg border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center space-x-2 text-slate-600 font-medium">
                    <span>Showing {{ $articles->firstItem() ?? 0 }}–{{ $articles->lastItem() ?? 0 }} of {{ $articles->total() }} items</span>
                    @if($selectedDate)
                        <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded text-[11px] font-bold">
                            Date: {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                            <a href="{{ url()->current() }}" class="ml-1 text-amber-900 hover:text-black">&times;</a>
                        </span>
                    @endif
                </div>

                <!-- Sort Control -->
                <div class="flex items-center space-x-2">
                    <span class="text-slate-500">Sort by:</span>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'published_at', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                       class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 font-medium text-slate-700 transition">
                        Date {{ request('direction') === 'asc' ? '▲' : '▼' }}
                    </a>
                </div>
            </div>

            <!-- Numbered Article Rows (01.–20.) -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 divide-y divide-slate-100">
                @forelse($articles as $index => $article)
                    @php
                        $itemNumber = str_pad(($articles->firstItem() + $index), 2, '0', STR_PAD_LEFT);
                    @endphp
                    <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex items-start space-x-3.5 sm:space-x-4 group">
                        <!-- Index Number (01.-20. Standard Convention) -->
                        <span class="text-lg sm:text-xl font-black text-amber-600/70 group-hover:text-amber-600 shrink-0 tabular-nums">
                            {{ $itemNumber }}.
                        </span>

                        @if($article->feature_image_url)
                            <a href="{{ $article->url }}" class="shrink-0 w-20 h-16 sm:w-28 sm:h-20 rounded-xl overflow-hidden border border-slate-200 bg-slate-100 block shadow-2xs">
                                <img src="{{ $article->feature_image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            </a>
                        @endif

                        <div class="flex-grow min-w-0">
                            <!-- Section Tag & Date -->
                            <div class="flex items-center space-x-2 text-[11px] text-slate-500 mb-1">
                                <span class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded">
                                    {{ $article->section ? ($article->section->translation()?->name ?? 'GS') : 'General Studies' }}
                                </span>
                                <span>&bull;</span>
                                <time datetime="{{ $article->published_at ? $article->published_at->toIso8601String() : '' }}">
                                    {{ $article->published_at ? $article->published_at->format('d M Y') : now()->format('d M Y') }}
                                </time>
                                @if($article->source_name)
                                    <span>&bull;</span>
                                    <span class="text-slate-400">{{ $article->source_name }}</span>
                                @endif
                            </div>

                            <!-- Title Link -->
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-amber-700 transition leading-snug">
                                <a href="{{ $article->url }}" class="block">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            <!-- Excerpt preview -->
                            @if($article->excerpt)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $article->excerpt }}
                                </p>
                            @endif

                            <!-- Meta Chips -->
                            <div class="flex items-center space-x-2 mt-2 text-[11px] text-slate-400">
                                <span>{{ $article->reading_minutes }} min read</span>
                                @if($article->blocks->where('type', 'pyq')->count() > 0)
                                    <span>&bull;</span>
                                    <span class="text-emerald-700 font-semibold flex items-center">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        PYQ Included
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Read Arrow -->
                        <a href="{{ $article->url }}" class="shrink-0 p-2 text-slate-300 group-hover:text-amber-600 transition" aria-label="Read article">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        <h3 class="text-sm font-bold text-slate-700">No articles found</h3>
                        <p class="text-xs text-slate-500 mt-1">Try selecting a different date or month from the archive filter.</p>
                        <a href="{{ url()->current() }}" class="inline-block mt-3 text-xs font-bold text-amber-600 hover:underline">Reset Filters</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination with rel="prev" / rel="next" -->
            <div class="mt-6">
                {{ $articles->links() }}
            </div>
        </div>

        <!-- Right Column: Year / Month Selectors & Real-Link Calendar (4 Cols) -->
        <aside class="lg:col-span-4 space-y-6">
            
            <!-- Date & Archive Filter Box -->
            <div class="bg-white p-5 rounded-xl shadow-xs border border-slate-200">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100 flex items-center">
                    <svg class="w-4 h-4 text-amber-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Archive Filter
                </h3>

                <!-- Form for Year & Month Dropdowns -->
                <form method="GET" action="{{ url()->current() }}" class="space-y-3 mb-6">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Select Year</label>
                        <select name="year" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:ring-1 focus:ring-amber-500 bg-white">
                            @foreach([2026, 2025, 2024, 2023, 2022] as $y)
                                <option value="{{ $y }}" {{ ($selectedYear == $y || (!$selectedYear && $y == now()->year)) ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1 uppercase">Select Month</label>
                        <select name="month" class="w-full px-3 py-1.5 text-xs rounded border border-slate-300 focus:ring-1 focus:ring-amber-500 bg-white">
                            @foreach(range(1, 12) as $m)
                                @php $mName = date('F', mktime(0, 0, 0, $m, 10)); @endphp
                                <option value="{{ sprintf('%02d', $m) }}" {{ ($selectedMonth == $m || (!$selectedMonth && $m == now()->month)) ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-bold py-1.5 rounded text-xs transition">
                        Filter Archive
                    </button>
                </form>

                <!-- Interactive Calendar Widget (Real Daily Content Links) -->
                <div class="border-t border-slate-100 pt-4">
                    <div class="flex items-center justify-between mb-3 text-xs font-bold text-slate-800">
                        <span>{{ $calendarDate->format('F Y') }}</span>
                        <div class="flex space-x-1">
                            @php
                                $prevM = $calendarDate->copy()->subMonth();
                                $nextM = $calendarDate->copy()->addMonth();
                            @endphp
                            <a href="{{ request()->fullUrlWithQuery(['year' => $prevM->year, 'month' => sprintf('%02d', $prevM->month)]) }}" 
                               class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Previous Month">&larr;</a>
                            <a href="{{ request()->fullUrlWithQuery(['year' => $nextM->year, 'month' => sprintf('%02d', $nextM->month)]) }}" 
                               class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Next Month">&rarr;</a>
                        </div>
                    </div>

                    <!-- Day Names Header -->
                    <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 mb-1">
                        <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                    </div>

                    <!-- Day Grid with Real Links -->
                    <div class="grid grid-cols-7 gap-1 text-center text-xs">
                        <!-- Empty slots before month start -->
                        @for($i = 0; $i < $startDayOfWeek; $i++)
                            <div class="h-7"></div>
                        @endfor

                        <!-- Days of Month -->
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php
                                $dateStr = sprintf('%04d-%02d-%02d', $calendarDate->year, $calendarDate->month, $d);
                                $hasArticles = isset($dailyCounts[$d]) && $dailyCounts[$d] > 0;
                                $isSelected = $selectedDate === $dateStr;
                            @endphp

                            @if($hasArticles)
                                <a href="{{ request()->fullUrlWithQuery(['date' => $dateStr, 'year' => $calendarDate->year, 'month' => sprintf('%02d', $calendarDate->month)]) }}" 
                                   class="h-7 flex items-center justify-center rounded font-bold transition text-xs relative {{ $isSelected ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-100/70 text-amber-900 hover:bg-amber-200' }}"
                                   title="{{ $dailyCounts[$d] }} articles published">
                                    {{ $d }}
                                    <span class="absolute bottom-0.5 right-0.5 w-1 h-1 rounded-full {{ $isSelected ? 'bg-white' : 'bg-amber-600' }}"></span>
                                </a>
                            @else
                                <div class="h-7 flex items-center justify-center text-slate-400 text-xs">
                                    {{ $d }}
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Reusable Lead Form in Sidebar -->
            <x-lead-capture-form 
                title="Get Daily Current Affairs via WhatsApp" 
                subtitle="Join our community of over 500,000 civil services aspirants." 
                layout="compact" 
                source="archive_sidebar" />
        </aside>

    </div>
</div>
@endsection
