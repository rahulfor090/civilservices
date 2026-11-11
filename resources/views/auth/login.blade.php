<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In - Civil Service Gurukul Staff & Student Portal</title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q3H74QSJWT"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-Q3H74QSJWT');
    </script>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#0b1b3d">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            navy: '#0b1b3d',
                            navyLight: '#142c5f',
                            orange: '#e65100',
                            orangeHover: '#bf360c',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full flex items-center justify-center p-4 bg-gradient-to-br from-slate-950 via-brand-navy to-slate-900 text-slate-100">
    <div class="w-full max-w-md space-y-8">
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Civil Service Gurukul Logo" class="w-14 h-14 rounded-full object-contain p-0.5 bg-white/10 shadow-lg border border-white/20">
                <span class="text-2xl font-bold font-display tracking-tight text-white">
                    Civil Service <span class="text-brand-orange">Gurukul</span>
                </span>
            </div>
            <h2 class="text-lg font-semibold text-slate-300">Staff & Student Portal</h2>
            <p class="text-xs text-slate-400">Enter your credentials to access editorial CMS or your LMS account</p>
        </div>

        <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-8 shadow-2xl space-y-6">
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-950/80 border border-red-700/50 text-red-200 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 rounded-xl bg-emerald-950/80 border border-emerald-700/50 text-emerald-200 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email"
                           placeholder="name@example.com"
                           class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                    </div>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-brand-orange focus:ring-brand-orange">
                        <span>Remember my device</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white font-bold text-sm shadow-lg shadow-brand-orange/30 transition-all duration-150">
                    Sign In to Portal
                </button>

                <div class="text-center pt-2">
                    <p class="text-xs text-slate-300">
                        New UPSC / State PCS Aspirant? 
                        <a href="{{ route('register') }}" class="text-amber-400 hover:text-amber-300 hover:underline font-bold">
                            Register Student Account &rarr;
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-white transition-colors inline-flex items-center gap-1">
                &larr; Back to CivilServiceGurukul Public Site
            </a>
        </div>
    </div>
</body>
</html>
