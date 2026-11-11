<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q3H74QSJWT"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-Q3H74QSJWT');
    </script>

    <title>@yield('title', 'Civil Service Gurukul | Best UPSC & State PCS Preparation Portal')</title>
    <meta name="description" content="@yield('meta_description', 'India’s premier civil services education portal for UPSC IAS, UPPCS, BPSC, and State PCS exam preparation. Daily News Analysis, Editorials, Mind Maps, and Test Series.')">
    @yield('meta_tags')

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#0B132B">

    <!-- Self-referencing Canonical URL -->
    <link rel="canonical" href="@yield('canonical_url', url()->current())" />

    <!-- Reciprocal Bilingual Hreflang Tags & x-default -->
    @hasSection('hreflang_tags')
        @yield('hreflang_tags')
    @else
        @php
            $isHindi = app()->getLocale() === 'hi';
            $currentUri = request()->getRequestUri();
            $enUri = $isHindi ? preg_replace('#^/hindi#', '', $currentUri) : $currentUri;
            $enUri = empty($enUri) ? '/' : $enUri;
            $hiUri = $isHindi ? $currentUri : ('/hindi' . ($currentUri === '/' ? '' : $currentUri));
        @endphp
        <link rel="alternate" hreflang="en-in" href="{{ url($enUri) }}" />
        <link rel="alternate" hreflang="hi-in" href="{{ url($hiUri) }}" />
        <link rel="alternate" hreflang="x-default" href="{{ url($enUri) }}" />
    @endif

    <!-- Typography: Inter & Noto Sans Devanagari -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN for zero-build preview / standalone local run) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Devanagari', 'sans-serif'],
                        heading: ['Outfit', 'Noto Sans Devanagari', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                            navy: '#0b1b3d',
                            navyLight: '#142c5f',
                            orange: '#e65100',
                            orangeHover: '#bf360c',
                        }
                    },
                    boxShadow: {
                        'card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'cardHover': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for Progressive Islands -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Inter', 'Noto Sans Devanagari', sans-serif;
            background-color: #fcfbf9; /* Warm neutral background */
            color: #1e293b;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', 'Noto Sans Devanagari', sans-serif;
        }
        /* Rich Text / Quill Content Formatting Support */
        .ql-align-center { text-align: center; }
        .ql-align-right { text-align: right; }
        .ql-align-justify { text-align: justify; }
        .ql-syntax {
            background-color: #0f172a;
            color: #38bdf8;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-family: monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }
    </style>

    <!-- Global Organization Structured Data (JSON-LD) -->
    <script type="application/ld+json">
        {!! \App\Services\SeoService::getOrganizationSchema() !!}
    </script>
    @yield('structured_data')
</head>
<body class="min-h-screen flex flex-col antialiased">
    <!-- G1: Top Utility Strip -->
    <x-utility-strip />

    <!-- G2: Schedulable Promo Strip -->
    <x-promo-strip :banner="$topBanner ?? null" />

    <!-- G3: Announcement Ticker -->
    <x-announcement-ticker :items="$tickerItems ?? collect()" />

    <!-- G4: Crawlable Mega-Menu Header -->
    <x-mega-menu />

    <!-- Flash Messages (Success / Alerts) -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded shadow-xs flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Body Surface -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- G6: Comprehensive Footer with 11 Centres -->
    <x-footer />

    @stack('scripts')
</body>
</html>
