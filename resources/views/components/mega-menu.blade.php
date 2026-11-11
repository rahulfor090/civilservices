@php
    $locale = app()->getLocale();

    $menuEn = \Illuminate\Support\Facades\Cache::remember('mega_menu_en', 3600, function () {
        $lang = \App\Models\Language::where('code', 'en')->first();
        if (!$lang) return null;
        return \App\Models\NavigationMenu::where('code', 'mega_menu')
            ->where('language_id', $lang->id)
            ->with([
                'items' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'items.children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')
            ])
            ->first();
    });

    $menuHi = \Illuminate\Support\Facades\Cache::remember('mega_menu_hi', 3600, function () {
        $lang = \App\Models\Language::where('code', 'hi')->first();
        if (!$lang) return null;
        return \App\Models\NavigationMenu::where('code', 'mega_menu')
            ->where('language_id', $lang->id)
            ->with([
                'items' => fn($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'items.children' => fn($q) => $q->where('is_active', true)->orderBy('sort_order')
            ])
            ->first();
    });

    $colsEn = $menuEn?->items;
    $colsHi = $menuHi?->items;
@endphp

<div x-data="{ 
        isOpen: false,
        activeTab: '{{ $locale === 'hi' ? 'hindi' : 'english' }}'
     }" 
     @keydown.escape.window="isOpen = false"
     class="relative">

    <!-- Primary Nav Header Bar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand Logo -->
                <div class="flex items-center space-x-3 shrink-0">
                    <button @click="isOpen = !isOpen" 
                            type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-slate-700 hover:text-amber-600 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-amber-500 transition shrink-0"
                            aria-expanded="false"
                            :aria-expanded="isOpen.toString()"
                            aria-label="Toggle Mega Menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="ml-1.5 text-xs font-bold uppercase tracking-wider hidden sm:inline">Menu</span>
                    </button>

                    <a href="{{ url($locale === 'hi' ? '/hindi' : '/') }}" class="flex items-center space-x-2.5 shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Civil Service Gurukul Emblem" class="w-10 h-10 rounded-full object-contain shadow-xs border border-slate-200 shrink-0">
                        <span class="text-lg sm:text-xl font-black text-slate-900 tracking-tight whitespace-nowrap">
                            Civil Service <span class="text-amber-600 ml-1">Gurukul</span>
                        </span>
                    </a>
                </div>

                <!-- Persistent Top Navigation Links (Crawlable Standard <nav>) -->
                <nav class="hidden lg:flex items-center space-x-5 text-sm font-medium text-slate-700" aria-label="Main Navigation">
                    <a href="{{ url($locale === 'hi' ? '/hindi/courses' : '/courses') }}" 
                       class="text-amber-600 hover:text-amber-700 font-bold transition py-1 border-b-2 border-transparent hover:border-amber-600 flex items-center gap-1">
                        <span>{{ $locale === 'hi' ? 'पाठ्यक्रम' : 'Courses' }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">New</span>
                    </a>
                    <a href="{{ url($locale === 'hi' ? '/hindi/daily-updates/daily-news-analysis' : '/daily-updates/daily-news-analysis') }}" 
                       class="hover:text-amber-600 transition py-1 border-b-2 border-transparent hover:border-amber-600">
                        {{ $locale === 'hi' ? 'दैनिक समाचार विश्लेषण' : 'Daily News Analysis' }}
                    </a>
                    <a href="{{ url($locale === 'hi' ? '/hindi/daily-news-editorials' : '/daily-news-editorials') }}" 
                       class="hover:text-amber-600 transition py-1 border-b-2 border-transparent hover:border-amber-600">
                        {{ $locale === 'hi' ? 'संपादकीय' : 'Daily Editorials' }}
                    </a>
                    <a href="{{ url($locale === 'hi' ? '/hindi/mind-map' : '/mind-map') }}" 
                       class="hover:text-amber-600 transition py-1 border-b-2 border-transparent hover:border-amber-600">
                        {{ $locale === 'hi' ? 'माइंड मैप्स' : 'Mind Maps' }}
                    </a>
                    <a href="{{ url($locale === 'hi' ? '/hindi/free-downloads' : '/free-downloads') }}" 
                       class="hover:text-amber-600 transition py-1 border-b-2 border-transparent hover:border-amber-600">
                        {{ $locale === 'hi' ? 'डाउनलोड्स' : 'Free Downloads' }}
                    </a>
                    <a href="{{ url($locale === 'hi' ? '/hindi/state-pcs-current-affairs' : '/state-pcs-current-affairs') }}" 
                       class="hover:text-amber-600 transition py-1 border-b-2 border-transparent hover:border-amber-600">
                        {{ $locale === 'hi' ? 'राज्य पीसीएस' : 'State PCS' }}
                    </a>
                </nav>

                <!-- Search & Quick CTA -->
                <div class="flex items-center space-x-3">
                    <a href="{{ url('/search') }}" 
                       class="p-2 text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 transition" 
                       title="Site-wide Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </a>
                    <a href="#leadsform" 
                       class="hidden sm:inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white font-semibold px-4 py-2 rounded-lg text-xs sm:text-sm shadow-sm transition">
                        {{ $locale === 'hi' ? 'परामर्श बुक करें' : 'Book Counselling' }}
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Mega-Menu Slide-Down / Full-Screen Overlay (Initial Server HTML is Crawlable) -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="absolute top-full left-0 w-full bg-white border-b border-slate-300 shadow-2xl z-50 max-h-[85vh] overflow-y-auto"
         style="display: none;">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Language Switch Tabs inside Mega-Menu -->
            <div class="flex items-center justify-between border-b border-slate-200 pb-3 mb-6">
                <div class="flex space-x-3">
                    <button @click="activeTab = 'english'" 
                            :class="activeTab === 'english' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-4 py-1.5 rounded-md text-xs font-bold transition uppercase tracking-wider">
                        English Menu
                    </button>
                    <button @click="activeTab = 'hindi'" 
                            :class="activeTab === 'hindi' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            class="px-4 py-1.5 rounded-md text-xs font-bold transition tracking-wider">
                        हिन्दी मेनू (Hindi Medium)
                    </button>
                </div>
                <button @click="isOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md" aria-label="Close menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- English Columns View -->
            <div x-show="activeTab === 'english'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                @if($colsEn && $colsEn->isNotEmpty())
                    @foreach($colsEn as $col)
                        <div>
                            <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">{{ $col->label }}</h4>
                            <ul class="space-y-2 text-sm text-slate-700">
                                @foreach($col->children as $child)
                                    <li><a href="{{ url($child->url) }}" class="hover:text-amber-600 transition block">{{ $child->label }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @else
                    <!-- Col 1: Prelims -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">Prelims Preparation</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">About Civil Services (CSE)</a></li>
                            <li><a href="{{ url('/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">Prelims Examination Syllabus</a></li>
                            <li><a href="{{ url('/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">GS Prelims Strategy</a></li>
                            <li><a href="{{ url('/mind-map') }}" class="hover:text-amber-600 transition block">GS Paper I–IV Mind Maps</a></li>
                            <li><a href="{{ url('/to-the-points') }}" class="hover:text-amber-600 transition block">To The Point Revision Notes</a></li>
                            <li><a href="{{ url('/free-downloads') }}" class="hover:text-amber-600 transition block">Prelims Previous Year Papers (GS & CSAT)</a></li>
                        </ul>
                    </div>

                    <!-- Col 2: Mains & Ethics -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">Mains & Ethics</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/daily-news-editorials') }}" class="hover:text-amber-600 transition block">Mains GS Syllabus & Structure</a></li>
                            <li><a href="{{ url('/daily-news-editorials') }}" class="hover:text-amber-600 transition block">Mains Strategy & Answer Writing</a></li>
                            <li><a href="{{ url('/ethics/case-studies') }}" class="hover:text-amber-600 transition block">Ethics (GS Paper IV) Case Studies</a></li>
                            <li><a href="{{ url('/daily-news-editorials') }}" class="hover:text-amber-600 transition block">Model Essays & Fodder Bank</a></li>
                            <li><a href="{{ url('/free-downloads') }}" class="hover:text-amber-600 transition block">UPSC Mains Solved Papers</a></li>
                            <li><a href="{{ url('/free-downloads') }}" class="hover:text-amber-600 transition block">Toppers' Evaluated Answer Copies</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Current Affairs & Specials -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">Current Affairs Hub</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">Daily News Analysis (DNA)</a></li>
                            <li><a href="{{ url('/daily-news-editorials') }}" class="hover:text-amber-600 transition block">Daily Editorial Breakdown</a></li>
                            <li><a href="{{ url('/pib-current-affairs') }}" class="hover:text-amber-600 transition block">PIB Official Releases</a></li>
                            <li><a href="{{ url('/loksabha-rajyasabha-discussions') }}" class="hover:text-amber-600 transition block">Sansad TV Parliamentary Debates</a></li>
                            <li><a href="{{ url('/summary-of-important-reports') }}" class="hover:text-amber-600 transition block">Gist of NITI Aayog & Govt Reports</a></li>
                            <li><a href="{{ url('/daily-updates/monthly-ca-consolidation') }}" class="hover:text-amber-600 transition block">Monthly CA Consolidation PDFs</a></li>
                        </ul>
                    </div>

                    <!-- Col 4: State PCS & Programmes -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">State PCS & Centres</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/state-pcs-current-affairs') }}" class="hover:text-amber-600 transition block">UPPCS Current Affairs</a></li>
                            <li><a href="{{ url('/state-pcs-current-affairs') }}" class="hover:text-amber-600 transition block">BPSC (Bihar) Special Coverage</a></li>
                            <li><a href="{{ url('/state-pcs-current-affairs') }}" class="hover:text-amber-600 transition block">RAS (Rajasthan) Current Affairs</a></li>
                            <li><a href="{{ url('/free-downloads') }}" class="hover:text-amber-600 transition block">Classroom Programme Centres</a></li>
                            <li><a href="{{ url('/free-downloads') }}" class="hover:text-amber-600 transition block">Free NCERT & NIOS Books Download</a></li>
                            <li><a href="{{ url('/hindi') }}" class="text-amber-600 font-semibold hover:underline block pt-2">Switch to हिन्दी वेबसाइट &rarr;</a></li>
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Hindi Columns View -->
            <div x-show="activeTab === 'hindi'" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                @if($colsHi && $colsHi->isNotEmpty())
                    @foreach($colsHi as $col)
                        <div>
                            <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">{{ $col->label }}</h4>
                            <ul class="space-y-2 text-sm text-slate-700">
                                @foreach($col->children as $child)
                                    <li><a href="{{ url($child->url) }}" class="hover:text-amber-600 transition block">{{ $child->label }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @else
                    <!-- Col 1: प्रारंभिक परीक्षा -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">प्रारंभिक परीक्षा</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/hindi/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">सिविल सेवा परीक्षा परिचय</a></li>
                            <li><a href="{{ url('/hindi/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">प्रारंभिक परीक्षा पाठ्यक्रम</a></li>
                            <li><a href="{{ url('/hindi/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">सामान्य अध्ययन रणनीति</a></li>
                            <li><a href="{{ url('/hindi/mind-map') }}" class="hover:text-amber-600 transition block">माइंड मैप्स (प्रश्नपत्र 1-4)</a></li>
                            <li><a href="{{ url('/hindi/to-the-points') }}" class="hover:text-amber-600 transition block">टू द पॉइंट संक्षिप्त नोट्स</a></li>
                            <li><a href="{{ url('/hindi/free-downloads') }}" class="hover:text-amber-600 transition block">विगत वर्ष के प्रश्नपत्र (GS & CSAT)</a></li>
                        </ul>
                    </div>

                    <!-- Col 2: मुख्य परीक्षा एवं निबंध -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">मुख्य परीक्षा</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/hindi/daily-news-editorials') }}" class="hover:text-amber-600 transition block">मुख्य परीक्षा पाठ्यक्रम एवं रणनीति</a></li>
                            <li><a href="{{ url('/hindi/ethics/case-studies') }}" class="hover:text-amber-600 transition block">नीतिशास्त्र केस स्टडीज़</a></li>
                            <li><a href="{{ url('/hindi/daily-news-editorials') }}" class="hover:text-amber-600 transition block">मॉडल निबंध एवं फॉडर सामग्री</a></li>
                            <li><a href="{{ url('/hindi/free-downloads') }}" class="hover:text-amber-600 transition block">यूपीएससी मुख्य परीक्षा हल प्रश्नपत्र</a></li>
                            <li><a href="{{ url('/hindi/free-downloads') }}" class="hover:text-amber-600 transition block">टॉपर्स की जांची हुई उत्तर पुस्तिकाएँ</a></li>
                            <li><a href="{{ url('/hindi/daily-news-editorials') }}" class="hover:text-amber-600 transition block">दैनिक मुख्य परीक्षा उत्तर लेखन</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: समसामयिकी (Current Affairs) -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">करेंट अफेयर्स</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/hindi/daily-updates/daily-news-analysis') }}" class="hover:text-amber-600 transition block">दैनिक समाचार विश्लेषण (DNA)</a></li>
                            <li><a href="{{ url('/hindi/daily-news-editorials') }}" class="hover:text-amber-600 transition block">संपादकीय विश्लेषण</a></li>
                            <li><a href="{{ url('/hindi/pib-current-affairs') }}" class="hover:text-amber-600 transition block">पीआईबी समाचार सार</a></li>
                            <li><a href="{{ url('/hindi/loksabha-rajyasabha-discussions') }}" class="hover:text-amber-600 transition block">संसद टीवी चर्चा एवं परिचर्चा</a></li>
                            <li><a href="{{ url('/hindi/summary-of-important-reports') }}" class="hover:text-amber-600 transition block">महत्वपूर्ण सरकारी रिपोर्ट सारांश</a></li>
                            <li><a href="{{ url('/hindi/daily-updates/monthly-ca-consolidation') }}" class="hover:text-amber-600 transition block">मासिक समसामयिकी पत्रिका (PDF)</a></li>
                        </ul>
                    </div>

                    <!-- Col 4: राज्य पीसीएस एवं अध्ययन सामग्री -->
                    <div>
                        <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 border-b border-amber-200 pb-1">राज्य पीसीएस व केंद्र</h4>
                        <ul class="space-y-2 text-sm text-slate-700">
                            <li><a href="{{ url('/hindi/state-pcs-current-affairs') }}" class="hover:text-amber-600 transition block">यूपीपीसीएस (UPPCS) विशेषांक</a></li>
                            <li><a href="{{ url('/hindi/state-pcs-current-affairs') }}" class="hover:text-amber-600 transition block">बीपीएससी (BPSC) विशेषांक</a></li>
                            <li><a href="{{ url('/hindi/state-pcs-current-affairs') }}" class="hover:text-amber-600 transition block">आरएएस (RAS) विशेषांक</a></li>
                            <li><a href="{{ url('/hindi/free-downloads') }}" class="hover:text-amber-600 transition block">कक्षा कार्यक्रम केंद्र विवरण</a></li>
                            <li><a href="{{ url('/hindi/free-downloads') }}" class="hover:text-amber-600 transition block">निशुल्क एनसीईआरटी ई-बुक्स डाउनलोड</a></li>
                            <li><a href="{{ url('/') }}" class="text-amber-600 font-semibold hover:underline block pt-2">Visit English Website &rarr;</a></li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
