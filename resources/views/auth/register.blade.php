<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration - Civil Service Gurukul</title>

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
<body class="min-h-full flex items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-slate-950 via-brand-navy to-slate-900 text-slate-100">
    <div class="w-full max-w-lg space-y-6 my-8">
        <!-- Logo & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Civil Service Gurukul Logo" class="w-14 h-14 rounded-full object-contain p-0.5 bg-white/10 shadow-lg border border-white/20">
                <div class="text-left">
                    <span class="text-2xl font-bold font-display tracking-tight text-white block leading-none">
                        Civil Service <span class="text-brand-orange">Gurukul</span>
                    </span>
                    <span class="text-[11px] text-amber-400 font-semibold tracking-wider uppercase">
                        श्रद्धावान् लभते ज्ञानम्
                    </span>
                </div>
            </div>
            <h2 class="text-lg font-semibold text-slate-300">Create Your Free Student Account</h2>
            <p class="text-xs text-slate-400 max-w-sm mx-auto">Access interactive test series, enrolled course video lectures, and faculty mentorship</p>
        </div>

        <!-- Registration Box -->
        <div class="bg-slate-800/90 backdrop-blur-xl border border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-950/80 border border-red-700/50 text-red-200 text-xs space-y-1">
                    <p class="font-bold">Please check the following details:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 rounded-xl bg-amber-950/80 border border-amber-700/50 text-amber-200 text-xs flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                @if(!empty($redirect))
                    <input type="hidden" name="redirect" value="{{ $redirect }}">
                @endif

                <!-- Candidate Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Full Name *</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}" 
                           required 
                           autocomplete="name"
                           placeholder="e.g. Rahul Sharma"
                           class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                </div>

                <!-- Email & Mobile -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Email Address *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email"
                               placeholder="candidate@gmail.com"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="mobile" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Mobile Number *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-xs font-bold text-slate-400 select-none">
                                +91
                            </span>
                            <input type="tel" 
                                   name="mobile" 
                                   id="mobile" 
                                   value="{{ old('mobile') }}" 
                                   required 
                                   maxlength="10"
                                   pattern="[0-9]{10}"
                                   autocomplete="tel"
                                   placeholder="9876543210"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all font-mono">
                        </div>
                    </div>
                </div>

                <!-- Target Exam & Medium -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="target_exam" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Target Exam *</label>
                        <select name="target_exam" id="target_exam" required class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                            <option value="upsc_cse" {{ old('target_exam') == 'upsc_cse' ? 'selected' : '' }}>UPSC CSE (IAS / IPS)</option>
                            <option value="uppcs" {{ old('target_exam') == 'uppcs' ? 'selected' : '' }}>UPPCS (Uttar Pradesh)</option>
                            <option value="bpsc" {{ old('target_exam') == 'bpsc' ? 'selected' : '' }}>BPSC (Bihar)</option>
                            <option value="ras" {{ old('target_exam') == 'ras' ? 'selected' : '' }}>RAS / RPSC (Rajasthan)</option>
                        </select>
                    </div>

                    <div>
                        <label for="locale" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Study Medium *</label>
                        <select name="locale" id="locale" required class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                            <option value="en" {{ old('locale') == 'en' ? 'selected' : '' }}>English Medium</option>
                            <option value="hi" {{ old('locale') == 'hi' ? 'selected' : '' }}>हिन्दी माध्यम (Hindi)</option>
                        </select>
                    </div>
                </div>

                <!-- Password & Confirm -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password *</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               minlength="8"
                               autocomplete="new-password"
                               placeholder="At least 8 chars"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Confirm Password *</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               required 
                               minlength="8"
                               autocomplete="new-password"
                               placeholder="Re-enter password"
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/90 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-orange focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="pt-2 text-xs text-slate-400">
                    By registering, you agree to Civil Service Gurukul's Terms & Privacy Policy and authorize academic mentors to contact you for guidance.
                </div>

                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white font-bold text-sm shadow-lg shadow-brand-orange/30 transition-all duration-150 flex items-center justify-center gap-2">
                    <span>Complete Registration</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>

                <div class="text-center pt-2">
                    <p class="text-xs text-slate-300">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 hover:underline font-bold">
                            Sign In to Portal &rarr;
                        </a>
                    </p>
                </div>
            </form>
        </div>

        <div class="text-center">
            <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-white transition-colors inline-flex items-center gap-1">
                &larr; Back to Civil Service Gurukul Public Site
            </a>
        </div>
    </div>
</body>
</html>
