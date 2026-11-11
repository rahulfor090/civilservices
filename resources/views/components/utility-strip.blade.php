@php
    $locale = app()->getLocale();
    $centres = \App\Models\Centre::activeOrdered();
    $hqCentre = \App\Models\Centre::headquarters();
    $hqPhone = $hqCentre?->phone ?? '+91 9911020626';
    $hqEmail = $hqCentre?->email ?? 'info@civilservicegurukul.com';
    $hqAddress = $hqCentre?->fullAddress() ?: '57, 16, Block 47, Old Rajinder Nagar, Delhi 110060';
    $centresCount = $centres->count() ?: 11;
@endphp
<div class="bg-gradient-to-r from-slate-950 via-[#0B132B] to-slate-950 text-slate-300 text-xs py-2 border-b border-slate-800/80 sticky top-0 z-50 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-wrap items-center justify-between gap-y-2 gap-x-4">
        
        <!-- Contact & Location Highlights -->
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] sm:text-xs">
            <!-- Mobile Call & WhatsApp -->
            <a href="tel:{{ preg_replace('/\s+/', '', $hqPhone) }}" class="inline-flex items-center text-amber-400 hover:text-amber-300 font-bold transition-colors group">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                <svg class="w-3.5 h-3.5 mr-1 text-emerald-400 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                <span>{{ $hqPhone }}</span>
            </a>

            <span class="text-slate-700 hidden sm:inline">•</span>

            <!-- Email -->
            <a href="mailto:{{ $hqEmail }}" class="hidden sm:inline-flex items-center text-slate-300 hover:text-white transition-colors">
                <svg class="w-3.5 h-3.5 mr-1 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <span>{{ $hqEmail }}</span>
            </a>

            <span class="text-slate-700 hidden md:inline">•</span>

            <!-- Official Delhi Headquarters Address Tag -->
            <span class="hidden md:inline-flex items-center text-slate-400">
                <svg class="w-3.5 h-3.5 mr-1 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                <span class="truncate max-w-[280px] lg:max-w-none">{{ $hqAddress }}</span>
            </span>
        </div>

        <!-- Right: Actions, Language Switch & Student Portal -->
        <div class="flex items-center space-x-3 text-[11px] sm:text-xs">
            <a href="{{ route('centres.index') }}" class="hidden lg:inline-block text-slate-400 hover:text-white transition">
                {{ $centresCount }} National Centres
            </a>

            <span class="text-slate-700 hidden lg:inline">|</span>

            <!-- Language Switcher Pill -->
            @if($locale === 'hi')
                <a href="{{ url('/') }}" class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-300 hover:bg-amber-400/20 font-semibold transition">
                    English
                </a>
            @else
                <a href="{{ url('/hindi') }}" class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-400/10 border border-amber-400/20 text-amber-300 hover:bg-amber-400/20 font-semibold transition">
                    हिन्दी माध्यम
                </a>
            @endif

            <span class="text-slate-700">|</span>

            @auth
                <div class="flex items-center space-x-2">
                    <a href="{{ route('student.dashboard') }}" class="text-amber-400 font-bold hover:underline">
                        My LMS
                    </a>
                    <span class="text-slate-700">/</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-400 transition">Logout</button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="text-slate-300 hover:text-white font-medium transition">
                    Login
                </a>
                <span class="text-slate-600">/</span>
                <a href="{{ route('register') }}" class="px-2.5 py-0.5 rounded bg-amber-600 hover:bg-amber-500 text-white font-bold shadow-xs transition">
                    Register Free
                </a>
            @endauth
        </div>

    </div>
</div>
