@extends('layouts.app')

@section('title', 'National Offline Classroom Centres | CivilServiceGurukul')
@section('meta_description', 'Locate our 11 national classroom coaching centres including Delhi Old Rajinder Nagar (National Headquarters), Mukherjee Nagar, Prayagraj, Jaipur, Patna, Lucknow, Indore, Pune and more.')

@section('content')
<div class="py-8 space-y-10">
    <!-- Header Hero Banner -->
    <div class="relative bg-gradient-to-br from-slate-900 via-brand-navy to-slate-950 text-white rounded-3xl p-8 sm:p-10 shadow-xl overflow-hidden border border-slate-800">
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-brand-orange/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-orange/20 border border-brand-orange/30 text-brand-orange text-xs font-semibold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-brand-orange animate-ping"></span>
                11 Pan-India Learning Campuses
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Our National Classroom Centres</h1>
            <p class="text-slate-300 mt-3 text-sm sm:text-base leading-relaxed">
                Experience disciplined offline classroom pedagogy, air-conditioned 24x7 study libraries, personalized 1-on-1 faculty mentorship, and UPSC Prelims & Mains test hubs across India.
            </p>

            <!-- Quick Action Info -->
            <div class="mt-6 flex flex-wrap items-center gap-4 text-xs text-slate-300 pt-4 border-t border-slate-800">
                <div class="flex items-center gap-1.5">
                    <span class="text-brand-orange font-bold">HQ Office:</span>
                    <span>Old Rajinder Nagar, New Delhi</span>
                </div>
                <span class="text-slate-600 hidden sm:inline">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-brand-orange font-bold">Helpline:</span>
                    <a href="tel:+919911020626" class="text-white hover:text-brand-orange underline font-semibold">+91 9911020626</a>
                </div>
                <span class="text-slate-600 hidden sm:inline">•</span>
                <div class="flex items-center gap-1.5">
                    <span class="text-brand-orange font-bold">Email:</span>
                    <a href="mailto:info@civilservicegurukul.com" class="text-white hover:text-brand-orange underline">info@civilservicegurukul.com</a>
                </div>
            </div>
        </div>
    </div>

    <!-- City Filter Navigation -->
    @if(count($cities) > 1)
    <div class="flex items-center justify-between flex-wrap gap-3 pb-2 border-b border-slate-200">
        <div class="text-sm font-bold text-slate-800">Filter by City:</div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('centres.index') }}" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ !request('city') ? 'bg-brand-navy text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                All Campuses ({{ count($centres) }})
            </a>
            @foreach($cities as $c)
            <a href="{{ route('centres.index', ['city' => $c]) }}" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-all {{ request('city') == $c ? 'bg-brand-navy text-white shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                {{ $c }}
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Centre Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($centres as $centre)
        @php
            $isHq = str_contains(strtolower($centre->slug), 'rajinder') || str_contains(strtolower($centre->name), 'headquarters');
        @endphp
        <div class="bg-white rounded-2xl p-6 flex flex-col justify-between transition-all duration-300 {{ $isHq ? 'border-2 border-amber-400 shadow-lg shadow-amber-500/10 ring-1 ring-amber-400/20' : 'border border-slate-200/80 shadow-sm hover:shadow-md hover:border-slate-300' }}">
            <div>
                <!-- Badge and Title -->
                <div class="flex items-center justify-between gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 text-[11px] font-bold rounded-md {{ $isHq ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-700' }}">
                        {{ $centre->city }}, {{ $centre->state }}
                    </span>
                    @if($isHq)
                    <span class="inline-flex items-center gap-1 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-full bg-brand-orange text-white shadow-sm">
                        ★ National HQ
                    </span>
                    @endif
                </div>

                <h2 class="text-lg font-bold text-slate-900 leading-snug">
                    <a href="{{ route('centres.show', $centre->slug) }}" class="hover:text-brand-orange transition-colors">
                        {{ $centre->name }}
                    </a>
                </h2>

                <!-- Address -->
                <div class="mt-3 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-600 leading-relaxed space-y-1">
                    <div class="font-semibold text-slate-800 flex items-start gap-1.5">
                        <svg class="w-4 h-4 text-brand-orange flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>{{ $centre->address }}</span>
                    </div>
                    @if($centre->pincode)
                    <div class="text-[11px] text-slate-500 pl-5.5">Pincode: <span class="font-mono font-medium text-slate-700">{{ $centre->pincode }}</span></div>
                    @endif
                </div>

                <!-- Contact & Phone -->
                <div class="mt-4 space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-medium">Desk Helpline:</span>
                        <a href="tel:{{ $centre->phone }}" class="text-brand-navy font-bold hover:text-brand-orange transition-colors font-mono">
                            {{ $centre->phone }}
                        </a>
                    </div>
                    @if($centre->email)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-medium">Email:</span>
                        <a href="mailto:{{ $centre->email }}" class="text-slate-700 hover:text-brand-orange transition-colors truncate max-w-[180px]">
                            {{ $centre->email }}
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Facilities -->
                @if($centre->facilities && is_array($centre->facilities))
                <div class="mt-4 flex flex-wrap gap-1.5">
                    @foreach($centre->facilities as $facility)
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-[11px] rounded-md font-medium flex items-center gap-1">
                        <span class="text-emerald-600 font-bold">✓</span> {{ $facility }}
                    </span>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                @if($centre->google_maps_url)
                <a href="{{ $centre->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-slate-600 hover:text-brand-orange flex items-center gap-1">
                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    <span>Maps & Route</span>
                </a>
                @else
                <span></span>
                @endif

                <div class="flex items-center gap-2">
                    <a href="{{ route('centres.show', $centre->slug) }}" class="px-3 py-1.5 text-xs font-semibold text-slate-700 hover:text-slate-900 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        Details
                    </a>
                    <a href="{{ route('counselling.create') }}" class="px-3 py-1.5 bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                        Book Visit
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200">
            <p class="text-slate-500">No centres found matching the selected filter.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
