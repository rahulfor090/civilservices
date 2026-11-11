@extends('layouts.app')

@section('title', $locale === 'hi' ? 'सिविल सेवा गुरुकुल - भारत का प्रमुख सिविल सेवा संस्थान' : 'Civil Service Gurukul - India\'s Premier Civil Services Learning Platform')
@section('meta_description', $locale === 'hi' ? 'यूपीएससी सिविल सेवा परीक्षा (आईएएस/आईपीएस) की संपूर्ण तैयारी। राष्ट्रीय मुख्यालय: ओल्ड राजेंद्र नगर, नई दिल्ली। दैनिक समाचार विश्लेषण, मुख्य परीक्षा उत्तर लेखन और 11 राष्ट्रीय केंद्र।' : 'Comprehensive preparation for UPSC Civil Services Examination (IAS/IPS). National HQ: Old Rajinder Nagar, New Delhi. Daily news analysis, mains answer writing, and 11 offline learning centres.')

@section('content')
<div class="space-y-12">
    <!-- Hero Section with Daily Focus & Highlights -->
    <section class="relative bg-gradient-to-br from-slate-950 via-[#0B132B] to-slate-900 text-white rounded-3xl p-8 md:p-12 shadow-2xl overflow-hidden border border-slate-800">
        <!-- Glow accents -->
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-brand-orange/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-7 space-y-6">
                <!-- Status Badges -->
                <div class="flex flex-wrap items-center gap-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-orange/20 border border-brand-orange/30 text-brand-orange text-xs font-semibold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-brand-orange animate-ping"></span>
                        {{ $locale === 'hi' ? 'यूपीएससी 2026-27 लक्ष्य' : 'UPSC CSE 2026-27 Mission' }}
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-slate-200 text-xs font-medium backdrop-blur-sm">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        Old Rajinder Nagar HQ
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    {{ $locale === 'hi' ? 'सटीक मार्गदर्शन, निर्विवाद सफलता' : 'Master Civil Services with Authoritative Pedagogy' }}
                </h1>
                <p class="text-slate-300 text-base sm:text-lg leading-relaxed max-w-2xl">
                    {{ $locale === 'hi' 
                        ? 'भारत के सर्वश्रेष्ठ शिक्षकों द्वारा तैयार दैनिक समसामयिकी विश्लेषण, उत्तर-लेखन अभ्यास और ओल्ड राजेंद्र नगर राष्ट्रीय मुख्यालय सहित 11 केंद्रों पर प्रत्यक्ष कक्षा कार्यक्रम।' 
                        : 'Rigorous Daily News Analysis, anchored question-style editorials, evaluated mains answer writing, and classroom coaching across 11 national learning centres headquartered in Old Rajinder Nagar, Delhi.' }}
                </p>

                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ $locale === 'hi' ? url('/hindi/daily-updates/daily-news-analysis') : url('/daily-updates/daily-news-analysis') }}" 
                       class="px-6 py-3.5 bg-brand-orange hover:bg-brand-orangeHover text-white text-sm font-semibold rounded-xl shadow-lg shadow-brand-orange/25 transition-all flex items-center gap-2 group">
                        <span>{{ $locale === 'hi' ? 'आज का डीएनए पढ़ें' : 'Read Today\'s DNA' }}</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="#headquarters" 
                       class="px-6 py-3.5 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold rounded-xl border border-white/15 backdrop-blur-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>{{ $locale === 'hi' ? 'मुख्यालय परिसर विवरण' : 'National HQ Details' }}</span>
                    </a>
                    <a href="#counselling" 
                       class="px-5 py-3.5 text-slate-300 hover:text-white text-sm font-semibold transition-colors flex items-center gap-1.5">
                        <span>{{ $locale === 'hi' ? 'मुफ्त परामर्श' : 'Free Counselling' }} &darr;</span>
                    </a>
                </div>

                <!-- Live Quick Stats Strip -->
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-4 pt-6 border-t border-slate-800 text-slate-300">
                    <div>
                        <div class="text-2xl font-bold text-white font-mono">11</div>
                        <div class="text-xs text-slate-400">{{ $locale === 'hi' ? 'राष्ट्रीय अध्ययन केंद्र' : 'National Centres' }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-white font-mono">100%</div>
                        <div class="text-xs text-slate-400">{{ $locale === 'hi' ? 'द्विभाषी पाठ्यक्रम' : 'Bilingual Pedagogy' }}</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-brand-orange font-mono">Top 50</div>
                        <div class="text-xs text-slate-400">{{ $locale === 'hi' ? 'अखिल भारतीय रैंक' : 'Consistent AIR Selections' }}</div>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-2xl font-bold text-emerald-400 font-mono">24x7</div>
                        <div class="text-xs text-slate-400">{{ $locale === 'hi' ? 'लाइब्रेरी व डाउट सपोर्ट' : 'HQ Study Hall & Doubt Desk' }}</div>
                    </div>
                </div>
            </div>

            <!-- Featured Daily Capsule Card -->
            <div class="lg:col-span-5 bg-slate-800/80 backdrop-blur-md rounded-2xl p-6 border border-slate-700/80 shadow-xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                    <span class="text-xs font-semibold uppercase tracking-wider text-brand-orange">
                        {{ $locale === 'hi' ? 'आज का संपादकीय विश्लेषण' : 'Today\'s Lead Editorial' }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ now()->format('d M Y') }}</span>
                </div>

                @if($featuredArticles->isNotEmpty())
                    @php $lead = $featuredArticles->first(); @endphp
                    <div class="space-y-3">
                        @if($lead->feature_image_url)
                            <a href="{{ $lead->url }}" class="block overflow-hidden rounded-xl border border-slate-700/80 mb-3 aspect-[16/9] bg-slate-900 group">
                                <img src="{{ $lead->feature_image_url }}" alt="{{ $lead->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>
                        @endif
                        <span class="inline-block px-2.5 py-0.5 rounded text-xs font-medium bg-blue-900/60 text-blue-300 border border-blue-700/50">
                            {{ $lead->contentType ? $lead->contentType->name : 'DNA' }}
                        </span>
                        <h2 class="text-lg font-bold text-white leading-snug hover:text-brand-orange transition-colors">
                            <a href="{{ $lead->url }}">{{ $lead->title }}</a>
                        </h2>
                        <p class="text-sm text-slate-300 line-clamp-3 leading-relaxed">
                            {{ $lead->excerpt }}
                        </p>
                        <div class="pt-2 flex items-center justify-between text-xs text-slate-400">
                            <span>{{ $lead->reading_minutes }} min read</span>
                            <a href="{{ $lead->url }}" class="text-brand-orange hover:underline font-medium inline-flex items-center gap-1">
                                {{ $locale === 'hi' ? 'पूरा पढ़ें' : 'Read in full' }} &rarr;
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-slate-400 text-sm py-8 text-center">{{ $locale === 'hi' ? 'आज का विश्लेषण शीघ्र उपलब्ध होगा।' : 'Daily analysis being updated.' }}</p>
                @endif
            </div>
        </div>
    </section>

    <!-- Tabbed Recent Updates (English / Hindi Live Sync) -->
    <section class="space-y-6" x-data="{ activeTab: '{{ $locale === 'hi' ? 'hi' : 'en' }}' }">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">
                    {{ $locale === 'hi' ? 'नवीनतम समसामयिक अपडेट्स' : 'Latest Current Affairs Updates' }}
                </h2>
                <p class="text-sm text-slate-500">
                    {{ $locale === 'hi' ? 'परीक्षा केंद्रित दैनिक समाचार और संपादकीय' : 'Exam-calibrated daily updates refreshed in real-time' }}
                </p>
            </div>

            <!-- Tab Switcher -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200 self-start sm:self-auto">
                <button type="button" 
                        @click="activeTab = 'en'" 
                        :class="activeTab === 'en' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all">
                    English Medium ({{ $recentUpdatesEn->count() }})
                </button>
                <button type="button" 
                        @click="activeTab = 'hi'" 
                        :class="activeTab === 'hi' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                        class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all">
                    हिन्दी माध्यम ({{ $recentUpdatesHi->count() }})
                </button>
            </div>
        </div>

        <!-- English Tab Content -->
        <div x-show="activeTab === 'en'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($recentUpdatesEn as $index => $item)
                <article class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-card hover:shadow-cardHover hover:border-brand-navy/30 transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        @if($item->feature_image_url)
                            <a href="{{ $item->url }}" class="block w-full h-36 rounded-xl overflow-hidden mb-2 bg-slate-100 border border-slate-100">
                                <img src="{{ $item->feature_image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            </a>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-brand-orange">
                                {{ sprintf('%02d', $index + 1) }}.
                            </span>
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                {{ $item->published_at ? $item->published_at->format('d M') : 'Today' }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-navy transition-colors line-clamp-2 leading-snug">
                            <a href="{{ $item->url }}">{{ $item->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                            {{ $item->excerpt }}
                        </p>
                    </div>
                    <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>{{ $item->reading_minutes }} min</span>
                        <a href="{{ $item->url }}" class="text-brand-navy font-semibold hover:underline">Read &rarr;</a>
                    </div>
                </article>
            @empty
                <p class="col-span-4 text-slate-500 text-sm text-center py-8">No English articles available.</p>
            @endforelse
        </div>

        <!-- Hindi Tab Content -->
        <div x-show="activeTab === 'hi'" x-cloak class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($recentUpdatesHi as $index => $item)
                <article class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-card hover:shadow-cardHover hover:border-brand-navy/30 transition-all flex flex-col justify-between group">
                    <div class="space-y-3">
                        @if($item->feature_image_url)
                            <a href="{{ $item->url }}" class="block w-full h-36 rounded-xl overflow-hidden mb-2 bg-slate-100 border border-slate-100">
                                <img src="{{ $item->feature_image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
                            </a>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-mono font-bold text-brand-orange">
                                {{ sprintf('%02d', $index + 1) }}.
                            </span>
                            <span class="text-[11px] font-medium px-2 py-0.5 rounded bg-slate-100 text-slate-600">
                                {{ $item->published_at ? $item->published_at->format('d M') : 'Today' }}
                            </span>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-brand-navy transition-colors line-clamp-2 leading-snug">
                            <a href="{{ $item->url }}">{{ $item->title }}</a>
                        </h3>
                        <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed">
                            {{ $item->excerpt }}
                        </p>
                    </div>
                    <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                        <span>{{ $item->reading_minutes }} मिनट</span>
                        <a href="{{ $item->url }}" class="text-brand-navy font-semibold hover:underline">पढ़ें &rarr;</a>
                    </div>
                </article>
            @empty
                <p class="col-span-4 text-slate-500 text-sm text-center py-8">हिन्दी में कोई लेख उपलब्ध नहीं है।</p>
            @endforelse
        </div>
    </section>

    <!-- Academic Features Grid -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50/50 rounded-2xl p-6 border border-blue-100 space-y-3 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-md shadow-blue-500/20">1</div>
            <h3 class="text-base font-bold text-slate-900">{{ $locale === 'hi' ? 'दैनिक समाचार विश्लेषण (DNA)' : 'Daily News Analysis (DNA)' }}</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $locale === 'hi' 
                    ? 'द हिंदू और द इंडियन एक्सप्रेस से परीक्षा उपयोगी खबरों का विस्तृत विश्लेषण एवं प्रारंभिक-मुख्य परीक्षा के प्रश्न।' 
                    : 'The Hindu & Indian Express news curated rigorously for GS Papers I to IV with integrated PYQ mapping.' }}
            </p>
            <a href="{{ $locale === 'hi' ? url('/hindi/daily-updates/daily-news-analysis') : url('/daily-updates/daily-news-analysis') }}" class="text-xs font-semibold text-blue-700 hover:underline inline-block pt-1">
                {{ $locale === 'hi' ? 'दैनिक डीएनए देखें' : 'Explore DNA Archive' }} &rarr;
            </a>
        </div>

        <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-2xl p-6 border border-amber-100 space-y-3 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-brand-orange text-white flex items-center justify-center font-bold shadow-md shadow-brand-orange/20">2</div>
            <h3 class="text-base font-bold text-slate-900">{{ $locale === 'hi' ? 'संपादकीय विश्लेषण' : 'Editorial Analysis' }}</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $locale === 'hi' 
                    ? 'प्रमुख राष्ट्रीय एवं अंतर्राष्ट्रीय मुद्दों पर विश्लेषणात्मक आलेख, मुख्य परीक्षा के दृष्टिकोण से उपयोगी बिंदु।' 
                    : 'Balanced perspectives on multidimensional policy issues, ethical dilemmas, and administrative viewpoints.' }}
            </p>
            <a href="{{ $locale === 'hi' ? url('/hindi/daily-updates/editorials') : url('/daily-updates/editorials') }}" class="text-xs font-semibold text-brand-orange hover:underline inline-block pt-1">
                {{ $locale === 'hi' ? 'संपादकीय पढ़ें' : 'Read Editorials' }} &rarr;
            </a>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-teal-50/50 rounded-2xl p-6 border border-emerald-100 space-y-3 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold shadow-md shadow-emerald-500/20">3</div>
            <h3 class="text-base font-bold text-slate-900">{{ $locale === 'hi' ? 'मुफ्त अध्ययन सामग्री व पीडीएफ' : 'Free Downloads & Resources' }}</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $locale === 'hi' 
                    ? 'मासिक पत्रिकाएं, टॉपर्स कॉपियां, पाठ्यक्रम और पूर्व वर्षों के प्रश्न पत्र तुरंत डाउनलोड करें।' 
                    : 'Download verified syllabus breakups, monthly current affairs digests, and past year question compilations.' }}
            </p>
            <a href="{{ $locale === 'hi' ? url('/hindi/downloads') : url('/downloads') }}" class="text-xs font-semibold text-emerald-700 hover:underline inline-block pt-1">
                {{ $locale === 'hi' ? 'डाउनलोड पोर्टल' : 'Browse Downloads' }} &rarr;
            </a>
        </div>
    </section>

    <!-- National Headquarters & Main Campus Spotlight (Modern Layout) -->
    <section id="headquarters" class="relative bg-gradient-to-br from-slate-950 via-[#0B132B] to-slate-900 text-white rounded-3xl p-8 sm:p-10 shadow-2xl overflow-hidden border border-slate-800 scroll-mt-8">
        <!-- Ambient Glowing Backdrops -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info Column -->
            <div class="lg:col-span-7 space-y-6">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/15 border border-amber-400/30 text-amber-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        {{ $locale === 'hi' ? 'राष्ट्रीय मुख्यालय एवं मुख्य शिक्षण परिसर' : 'National Headquarters & Main Learning Campus' }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">{{ $hqCentre?->city ?? 'New Delhi' }}</span>
                </div>

                <div class="space-y-2">
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                        {{ $locale === 'hi' ? 'ओल्ड राजेंद्र नगर, नई दिल्ली' : ($hqCentre?->name ?? 'Old Rajinder Nagar, New Delhi') }}
                    </h2>
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        {{ $locale === 'hi'
                            ? 'यूपीएससी सिविल सेवा परीक्षा की तैयारी का राष्ट्रीय केंद्र। अत्याधुनिक वातानुकूलित कक्षाएं, 24x7 अध्ययन वाचनालय, और वरिष्ठ शिक्षकों के साथ व्यक्तिगत मेंटरशिप।'
                            : 'The epicenter of UPSC CSE preparation in India. State-of-the-art audiovisual classrooms, 24x7 study library, evaluated mains answer writing lab, and face-to-face faculty mentorship.' }}
                    </p>
                </div>

                <!-- Verified Official Address Card -->
                <div class="p-5 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-md space-y-3">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-400/15 border border-amber-400/30 flex items-center justify-center text-amber-400 flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="space-y-1">
                            <div class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">{{ $locale === 'hi' ? 'आधिकारिक परिसर एवं कार्यालय' : 'Official Registered Campus' }}</div>
                            <div class="text-sm sm:text-base font-bold text-white leading-snug">
                                {{ $hqCentre?->fullAddress() ?: '57, 16, Block 47, Old Rajinder Nagar, Rajinder Nagar, Delhi, 110060' }}
                            </div>
                            <div class="text-xs text-slate-400 flex items-center gap-1.5 pt-0.5">
                                <svg class="w-3.5 h-3.5 text-slate-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                                <span>{{ $locale === 'hi' ? 'लैंडमार्क: निकट करोल बाग / राजेंद्र प्लेस मेट्रो स्टेशन' : 'Landmark: Near Rajinder Place / Karol Bagh Metro Station' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campus Features Badges -->
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700 text-xs text-slate-300 flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">✓</span> {{ $locale === 'hi' ? '24x7 वातानुकूलित वाचनालय' : '24x7 AC Study Hall' }}
                    </span>
                    <span class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700 text-xs text-slate-300 flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">✓</span> {{ $locale === 'hi' ? 'दैनिक संशय निवारण काउंटर' : 'Daily Faculty Doubt Counter' }}
                    </span>
                    <span class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700 text-xs text-slate-300 flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">✓</span> {{ $locale === 'hi' ? 'ऑफलाइन टेस्ट सीरीज केंद्र' : 'Offline Test Series Centre' }}
                    </span>
                    <span class="px-3 py-1.5 rounded-lg bg-slate-800/80 border border-slate-700 text-xs text-slate-300 flex items-center gap-1.5">
                        <span class="text-emerald-400 font-bold">✓</span> {{ $locale === 'hi' ? 'स्मार्ट क्लासरूम' : 'Smart Digital Classrooms' }}
                    </span>
                </div>
            </div>

            <!-- Right Interactive Contact Hub Column -->
            <div class="lg:col-span-5 bg-gradient-to-b from-slate-800/90 to-slate-900/90 rounded-2xl p-6 border border-slate-700/80 shadow-2xl space-y-4">
                <div class="pb-3 border-b border-slate-700/80">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">
                        {{ $locale === 'hi' ? 'सीधे संपर्क करें व परामर्श लें' : 'Direct Helpline & Walk-in Desk' }}
                    </span>
                    <h3 class="text-lg font-bold text-white mt-1">
                        {{ $locale === 'hi' ? 'प्रवेश एवं परामर्श सहायता' : 'Admissions & Campus Inquiries' }}
                    </h3>
                </div>

                @php
                    $hqPhone = $hqCentre?->phone ?? '+91 9911020626';
                    $hqPhoneDigits = preg_replace('/[^0-9]/', '', $hqPhone) ?: '919911020626';
                    $hqEmail = $hqCentre?->email ?? 'info@civilservicegurukul.com';
                    $hqMapsUrl = $hqCentre?->mapsUrl() ?? 'https://maps.google.com/?q=57,+16,+Block+47,+Old+Rajinder+Nagar,+Rajinder+Nagar,+Delhi,+110060';
                    $centresTotal = ($activeCentresCount ?? 0) ?: 11;
                @endphp

                <!-- One-click Contact Options -->
                <div class="space-y-3">
                    <!-- Phone Call -->
                    <a href="tel:{{ preg_replace('/\s+/', '', $hqPhone) }}" 
                       class="group p-3 rounded-xl bg-slate-800 border border-slate-700/80 hover:border-amber-400/50 hover:bg-slate-750 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-amber-400/10 text-amber-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">{{ $locale === 'hi' ? 'फोन हेल्पलाइन' : 'Direct Admissions Desk' }}</div>
                                <div class="text-base font-extrabold text-white font-mono group-hover:text-amber-400 transition-colors">{{ $hqPhone }}</div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-amber-400 px-2.5 py-1 rounded bg-amber-400/10 group-hover:bg-amber-400 group-hover:text-slate-950 transition-all">
                            {{ $locale === 'hi' ? 'कॉल करें' : 'Call Now' }}
                        </span>
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://wa.me/{{ $hqPhoneDigits }}?text={{ urlencode('Hello Civil Service Gurukul, I would like to inquire about UPSC classroom batches and counselling at Old Rajinder Nagar HQ.') }}" 
                       target="_blank" rel="noopener noreferrer"
                       class="group p-3 rounded-xl bg-slate-800 border border-slate-700/80 hover:border-emerald-500/50 hover:bg-slate-750 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.311.045-.698.073-2.155-.544-1.862-.788-3.04-2.73-3.133-2.855-.093-.125-.754-1.002-.754-1.91 0-.907.476-1.354.646-1.537.17-.183.371-.228.496-.228.124 0 .248.001.356.006.113.006.264-.043.413.315.155.371.53 1.291.577 1.385.047.094.078.204.016.328-.063.125-.094.204-.187.312-.093.109-.196.243-.28.326-.094.093-.192.195-.083.382.109.187.484.798 1.04 1.292.717.637 1.32.834 1.508.927.188.094.298.078.407-.047.11-.125.469-.547.594-.734.125-.187.25-.156.422-.094.172.062 1.094.516 1.281.609.188.094.312.141.359.219.047.078.047.453-.097.858z"/></svg>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">WhatsApp Desk</div>
                                <div class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">{{ $hqPhone }}</div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-emerald-400 px-2.5 py-1 rounded bg-emerald-500/10 group-hover:bg-emerald-500 group-hover:text-white transition-all">
                            Chat &rarr;
                        </span>
                    </a>

                    <!-- Email -->
                    <a href="mailto:{{ $hqEmail }}" 
                       class="group p-3 rounded-xl bg-slate-800 border border-slate-700/80 hover:border-sky-400/50 hover:bg-slate-750 transition-all flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-sky-400/10 text-sky-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Official Email</div>
                                <div class="text-xs sm:text-sm font-semibold text-white group-hover:text-sky-300 transition-colors truncate max-w-[190px]">{{ $hqEmail }}</div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-sky-400 px-2.5 py-1 rounded bg-sky-400/10 group-hover:bg-sky-400 group-hover:text-slate-950 transition-all">
                            Write
                        </span>
                    </a>
                </div>

                <!-- Navigation & Visit CTAs -->
                <div class="pt-2 flex items-center gap-3">
                    <a href="{{ $hqMapsUrl }}" 
                       target="_blank" rel="noopener noreferrer"
                       class="flex-1 py-2.5 px-3 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-xl text-center border border-white/10 transition-all flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                        <span>{{ $locale === 'hi' ? 'गूगल मैप्स रूट' : 'Directions' }}</span>
                    </a>
                    <a href="{{ route('centres.index') }}" 
                       class="flex-1 py-2.5 px-3 bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold rounded-xl text-center shadow-lg shadow-brand-orange/25 transition-all">
                        {{ $locale === 'hi' ? "सभी {$centresTotal} केंद्र देखें" : "View All {$centresTotal} Centres" }} &rarr;
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Embedded Free Counselling Lead Capture Section -->
    <section id="counselling" class="scroll-mt-8">
        @include('components.lead-capture-form', [
            'headline' => $locale === 'hi' ? 'सिविल सेवा परीक्षा 2026-27 के लिए विशेषज्ञ परामर्श' : 'Schedule 1-on-1 Academic Guidance for UPSC 2026-27',
            'subhead' => $locale === 'hi' ? 'ओल्ड राजेंद्र नगर मुख्यालय अथवा नजदीकी केंद्र पर वरिष्ठ मेंटर्स से निःशुल्क परामर्श प्राप्त करें।' : 'Discuss preparation strategy, optional subject selection, and batch details with our senior faculty mentors in Old Rajinder Nagar or your nearest centre.',
            'sourcePage' => url()->current()
        ])
    </section>
</div>
@endsection
