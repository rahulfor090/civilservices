@php
    $locale = app()->getLocale();
    $centres = \App\Models\Centre::activeOrdered();
    $hqCentre = \App\Models\Centre::headquarters();
    $hqCity = $hqCentre?->city ?? 'New Delhi';
    $hqAddress = $hqCentre?->fullAddress() ?: '57, 16, Block 47, Old Rajinder Nagar, Rajinder Nagar, Delhi, 110060';
    $hqPhone = $hqCentre?->phone ?? '+91 9911020626';
    $hqEmail = $hqCentre?->email ?? 'info@civilservicegurukul.com';
    $hqMapsUrl = $hqCentre?->mapsUrl() ?? 'https://maps.google.com/?q=57+16+Block+47+Old+Rajinder+Nagar+Delhi+110060';
    $centresCount = $centres->count() ?: 11;
@endphp

<footer class="bg-[#0A0E17] text-slate-300 pt-16 pb-12 border-t border-slate-800/80 text-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Top Section: Brand Info & Spotlight National Headquarters Card -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Brand & Mission Statement -->
            <div class="lg:col-span-5 space-y-4">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Civil Service Gurukul" class="w-12 h-12 rounded-full object-contain p-0.5 bg-white/5 border border-white/10 shadow-lg shadow-amber-500/10">
                    <div>
                        <span class="text-2xl font-black text-white tracking-tight font-heading">
                            Civil Service <span class="text-amber-400">Gurukul</span>
                        </span>
                    </div>
                </div>

                <p class="text-slate-400 text-xs sm:text-sm leading-relaxed max-w-md">
                    {{ $locale === 'hi'
                        ? 'भारत का अग्रणी सिविल सेवा संस्थान। संघ एवं राज्य लोक सेवा आयोग परीक्षाओं (UPSC CSE, UPPCS, BPSC, RAS, MPPSC) हेतु प्रामाणिक विषयवस्तु, दैनिक विश्लेषण और व्यक्तिगत मार्गदर्शन।'
                        : 'India’s premier civil services academy delivering rigorous Daily News Analysis, question-anchored editorials, evaluated Mains answer writing, and offline classroom mentorship.' }}
                </p>

                <!-- Dual Medium Social Channels -->
                <div class="pt-2">
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Connect Across Social Channels</h5>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <a href="https://www.facebook.com/civilservicegurukul1" target="_blank" rel="noopener" class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#1877F2]"></span> Facebook
                        </a>
                        <a href="https://t.me/civilservicegurukul" target="_blank" rel="noopener" class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span> Telegram
                        </a>
                        <a href="https://youtube.com/@civilservicegurukul" target="_blank" rel="noopener" class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> YouTube
                        </a>
                        <a href="https://x.com/civilservicegurukul" target="_blank" rel="noopener" class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> X (Twitter)
                        </a>
                        <a href="https://instagram.com/civilservicegurukul" target="_blank" rel="noopener" class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:border-slate-700 transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span> Instagram
                        </a>
                        <a href="https://wa.me/919911020626" target="_blank" rel="noopener" class="px-2.5 py-1 rounded bg-slate-900 border border-slate-800 text-emerald-400 hover:text-emerald-300 hover:border-emerald-700/50 transition flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Spotlight Card: Official National Headquarters -->
            <div class="lg:col-span-7 bg-gradient-to-br from-slate-900/90 via-slate-900/60 to-slate-950 p-6 sm:p-7 rounded-2xl border border-amber-500/20 shadow-xl shadow-black/40 relative overflow-hidden backdrop-blur-sm">
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <h4 class="text-xs font-bold text-amber-400 uppercase tracking-widest">
                            National Headquarters & Corporate Office
                        </h4>
                    </div>
                    <span class="text-[10px] font-semibold text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700">
                        {{ $hqCity }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Office Address -->
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Registered Campus Address</span>
                        <p class="text-white font-medium text-xs leading-relaxed">
                            {{ $hqAddress }}
                        </p>
                        <p class="text-slate-400 text-[11px]">Landmark: Near Karol Bagh / Rajinder Place Metro</p>
                    </div>

                    <!-- Direct Connect Lines -->
                    <div class="space-y-2">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Direct Phone & WhatsApp</span>
                            <a href="tel:{{ preg_replace('/\s+/', '', $hqPhone) }}" class="text-amber-400 font-extrabold text-sm hover:underline flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z"/></svg>
                                <span>{{ $hqPhone }}</span>
                            </a>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Academic Admissions Desk</span>
                            <a href="mailto:{{ $hqEmail }}" class="text-white hover:text-sky-300 font-medium transition-colors flex items-center gap-1.5 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ $hqEmail }}</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Action CTAs -->
                <div class="mt-5 pt-4 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center space-x-2 text-[11px] text-slate-400">
                        <span>Mon – Sat: 9:00 AM – 8:00 PM</span>
                        <span>•</span>
                        <span>Sunday: 10:00 AM – 4:00 PM</span>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a href="{{ $hqMapsUrl }}" target="_blank" rel="noopener" class="px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            Directions
                        </a>
                        <a href="{{ route('counselling.create') }}" class="px-3.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow transition">
                            Book Campus Visit
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Middle Section: Physical Centres Network -->
        <div class="py-8 border-t border-b border-slate-800/80">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-6">
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center">
                        <svg class="w-4 h-4 text-amber-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $locale === 'hi' ? "{$centresCount} राष्ट्रीय अध्ययन केंद्र" : "Our {$centresCount} National Classroom Centres" }}
                    </h4>
                    <p class="text-slate-400 text-xs mt-0.5">Air-conditioned study libraries, offline doubt counters, and UPSC mock test halls.</p>
                </div>
                <a href="{{ route('centres.index') }}" class="text-xs font-bold text-amber-400 hover:underline">
                    View All Branches & Schedules &rarr;
                </a>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 text-xs">
                @forelse($centres as $centre)
                    @php $isHq = $centre->isHeadquarters(); @endphp
                    <div class="{{ $isHq ? 'bg-slate-900/80 border-amber-500/30 hover:border-amber-400' : 'bg-slate-900/50 border-slate-800 hover:border-slate-700' }} p-3.5 rounded-xl border transition">
                        @if($isHq)
                            <span class="text-[9px] font-bold text-amber-400 uppercase tracking-widest block mb-0.5">Headquarters</span>
                        @endif
                        <h5 class="font-bold text-white mb-1 text-xs">
                            <a href="{{ route('centres.show', $centre->slug) }}" class="hover:text-amber-300 transition">{{ $centre->name }}</a>
                        </h5>
                        <p class="text-slate-400 text-[11px] mb-2 line-clamp-2">{{ $centre->fullAddress() }}</p>
                        <div class="flex items-center justify-between text-[11px] pt-1 border-t border-slate-800">
                            <span class="{{ $isHq ? 'text-amber-400 font-bold' : 'text-slate-300' }}">{{ $centre->phone }}</span>
                            <a href="{{ $centre->mapsUrl() }}" target="_blank" rel="noopener" class="text-slate-400 hover:text-white underline">
                                Map
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-slate-400 text-xs">
                        Visit our <a href="{{ route('centres.index') }}" class="text-amber-400 underline">Centres Directory</a> for branch locations.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Bottom Copyright & Policies -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Civil Service Gurukul. All rights reserved.</p>
            <div class="flex items-center space-x-4">
                <a href="{{ route('centres.index') }}" class="hover:text-slate-400 transition">Centres</a>
                <span>•</span>
                <a href="{{ route('counselling.create') }}" class="hover:text-slate-400 transition">Counselling</a>
                <span>•</span>
                <a href="{{ route('interview.create') }}" class="hover:text-slate-400 transition">Interview Guidance</a>
                <span>•</span>
                <a href="{{ route('recruitment.teacher') }}" class="hover:text-slate-400 transition">Careers</a>
            </div>
        </div>

    </div>
</footer>
