@extends('layouts.app')

@section('title', $course->title . ' - Civil Service Gurukul')
@section('meta_description', $course->excerpt ?: 'Enroll in ' . $course->title . ' by Civil Service Gurukul. Live classes, recorded lectures, mentorship, and test series.')

@section('content')
<div class="space-y-10">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('courses.index') }}" class="text-xs text-amber-600 hover:underline font-bold inline-flex items-center gap-1">
            &larr; Back to All Courses & Batches
        </a>
        <span class="text-xs font-mono text-slate-500 uppercase">
            {{ strtoupper(str_replace('_', ' ', $course->target_exam)) }} &bull; {{ strtoupper($course->level) }}
        </span>
    </div>

    <!-- Course Hero Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left 8 Cols: Overview, Syllabus & Batches -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-gradient-to-br from-slate-950 via-[#0B132B] to-slate-900 text-white rounded-3xl p-8 sm:p-10 border border-slate-800 shadow-2xl space-y-5 relative overflow-hidden">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ strtoupper(str_replace('_', ' ', $course->target_exam)) }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-200">
                        {{ ucfirst($course->level) }} Level
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-slate-200">
                        {{ $course->language->name ?? 'Bilingual' }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight">
                    {{ $course->title }}
                </h1>

                @if($course->excerpt)
                    <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                        {{ $course->excerpt }}
                    </p>
                @endif

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-800/80 text-xs text-slate-300">
                    <div>
                        <div class="text-slate-400">Total Lectures</div>
                        <div class="text-lg font-bold text-white font-mono">{{ $lessons->count() }} Classes</div>
                    </div>
                    <div>
                        <div class="text-slate-400">Course Validity</div>
                        <div class="text-lg font-bold text-white font-mono">{{ $course->validity_days }} Days</div>
                    </div>
                    <div>
                        <div class="text-slate-400">Delivery Mode</div>
                        <div class="text-lg font-bold text-amber-400 capitalize">{{ $batch->delivery_mode ?? 'Online Live' }}</div>
                    </div>
                    <div>
                        <div class="text-slate-400">Study Hall & Doubts</div>
                        <div class="text-lg font-bold text-emerald-400">24x7 Included</div>
                    </div>
                </div>
            </div>

            <!-- Active Batch Information -->
            @if($batch)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        Current Batch Details & Timings
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-slate-400 block">Batch Name & Code</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $batch->title }}</span>
                            <span class="block font-mono text-slate-500 text-[11px]">Code: {{ $batch->batch_code }}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-slate-400 block">Schedule & Class Timing</span>
                            <span class="font-bold text-slate-900 text-sm">{{ $batch->class_timing ?? '08:30 AM - 11:00 AM (Mon-Fri)' }}</span>
                            @if($batch->start_date)
                                <span class="block text-slate-500 text-[11px]">Commences: {{ $batch->start_date->format('d M Y') }}</span>
                            @endif
                        </div>

                        @if($batch->centre_name)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-400 block">Offline Centre / Studio</span>
                                <span class="font-bold text-slate-900 text-sm">{{ $batch->centre_name }}</span>
                            </div>
                        @endif

                        @if($batch->faculty_names)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-slate-400 block">Lead Mentors & Faculty</span>
                                <span class="font-bold text-slate-900 text-sm">{{ $batch->faculty_names }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Dynamic Day-by-Day Course Planner & Curriculum -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6"
                 x-data="{ showAlertModal: false }">
                
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-brand-orange animate-pulse"></span>
                            <h2 class="text-lg font-bold text-slate-900">
                                Dynamic Day-by-Day Course Planner ({{ $lessons->count() }} Sessions)
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Scheduled timeline: Pre-recorded lectures unlock at exact timings &rarr; followed by timed daily quizzes &rarr; Mains questions &rarr; dedicated phone discussion call windows.
                        </p>
                    </div>

                    <!-- Planner Action Buttons: Calendar Sync & Alerts -->
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <a href="{{ route('courses.calendar.ics', $course) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition-all">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Add to Calendar (.ICS)</span>
                        </a>

                        <button type="button" 
                                @click="showAlertModal = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 text-xs font-bold transition-all">
                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span>Get 30m Prior Alerts</span>
                        </button>
                    </div>
                </div>

                <!-- Alert Subscription Modal -->
                <div x-show="showAlertModal" 
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
                     x-transition>
                    <div @click.away="showAlertModal = false" class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                </div>
                                <h3 class="font-bold text-slate-900 text-sm">Course Timeline Reminder Alerts</h3>
                            </div>
                            <button type="button" @click="showAlertModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Receive notifications <strong>30 minutes before</strong> each class, quiz, or phone session (e.g. at 4:30 PM for a 5:00 PM session) so you never miss a study milestone.
                        </p>

                        <form action="{{ route('courses.alerts.subscribe', $course) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Email Address *</label>
                                <input type="email" name="email" required value="{{ auth()->user()->email ?? '' }}" placeholder="student@example.com" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Mobile / WhatsApp (Optional)</label>
                                <input type="tel" name="phone" placeholder="+91 98765 43210" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                            </div>

                            <div class="flex items-center gap-2 pt-1 text-[11px] text-slate-500">
                                <input type="checkbox" checked required class="rounded text-brand-orange">
                                <span>Notify me 30 mins before live classes, video releases & quiz unlocks</span>
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="w-full py-2.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white font-bold text-xs shadow-md transition-all">
                                    Subscribe to 30-min Class Reminders
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Grouped Day-by-Day Timeline Cards -->
                <div class="space-y-6">
                    @forelse($groupedPlanner as $dateKey => $dayLessons)
                        @php
                            $isDateFormatted = $dateKey !== 'Flexible Curriculum';
                            $parsedDate = $isDateFormatted ? \Carbon\Carbon::parse($dateKey) : null;
                        @endphp
                        <div class="border border-slate-200/90 rounded-2xl overflow-hidden bg-white shadow-2xs">
                            <!-- Day Header -->
                            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-brand-navy"></span>
                                    <h3 class="font-bold text-slate-900 text-xs sm:text-sm">
                                        @if($parsedDate)
                                            {{ $parsedDate->format('l, d F Y') }}
                                        @else
                                            Flexible / Self-Paced Modules
                                        @endif
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">
                                        {{ $dayLessons->count() }} activities scheduled
                                    </span>
                                </div>

                                @if($parsedDate)
                                    <span class="text-[11px] font-medium text-slate-500 flex items-center gap-1">
                                        <span>Starts {{ $dayLessons->first()->scheduled_time ?? '05:00 PM' }}</span>
                                        &bull;
                                        <span class="text-indigo-600 font-semibold">🔔 30m prior alert</span>
                                    </span>
                                @endif
                            </div>

                            <!-- Day Activities Flow -->
                            <div class="divide-y divide-slate-100 p-2 sm:p-3">
                                @foreach($dayLessons as $lesson)
                                    @php
                                        $effType = $lesson->effective_type;
                                    @endphp
                                    <div class="p-3 sm:p-4 rounded-xl hover:bg-slate-50/80 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-3">
                                        <div class="flex items-start gap-3">
                                            <!-- Activity Icon -->
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5
                                                {{ $effType === 'video' ? 'bg-red-50 text-red-600 border border-red-200' : '' }}
                                                {{ $effType === 'live' ? 'bg-purple-50 text-purple-600 border border-purple-200' : '' }}
                                                {{ $effType === 'quiz' ? 'bg-amber-50 text-amber-600 border border-amber-200' : '' }}
                                                {{ $effType === 'mains' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : '' }}
                                                {{ $effType === 'phone_session' ? 'bg-blue-50 text-blue-600 border border-blue-200' : '' }}
                                                {{ $effType === 'test_series' ? 'bg-orange-50 text-orange-600 border border-orange-200' : '' }}
                                                {{ $effType === 'pdf' ? 'bg-slate-100 text-slate-600 border border-slate-200' : '' }}">
                                                @if($effType === 'video')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @elseif($effType === 'live')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                @elseif($effType === 'quiz')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                                @elseif($effType === 'mains')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                @elseif($effType === 'phone_session')
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                @endif
                                            </div>

                                            <div class="space-y-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ $lesson->title }}</h4>
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                                        {{ $effType === 'video' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $effType === 'live' ? 'bg-purple-100 text-purple-800' : '' }}
                                                        {{ $effType === 'quiz' ? 'bg-amber-100 text-amber-800' : '' }}
                                                        {{ $effType === 'mains' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                        {{ $effType === 'phone_session' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $effType === 'test_series' ? 'bg-orange-100 text-orange-800' : '' }}
                                                        {{ $effType === 'pdf' ? 'bg-slate-200 text-slate-800' : '' }}">
                                                        {{ $lesson->type_badge_label }}
                                                    </span>
                                                </div>

                                                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-mono">
                                                    @if($lesson->scheduled_time)
                                                        <span class="font-bold text-slate-800 font-sans">
                                                            ⏰ Scheduled: {{ $lesson->scheduled_time }}
                                                        </span>
                                                        <span>&bull;</span>
                                                    @endif
                                                    <span>⏱ {{ $lesson->duration_minutes }} Mins</span>

                                                    @if($effType === 'phone_session')
                                                        <span>&bull;</span>
                                                        <span class="text-blue-700 font-semibold font-sans">
                                                            📞 Faculty: {{ $lesson->mentor_name ?? 'Senior Mentor' }} &bull; Window: {{ $lesson->call_window ?? 'Direct Call Hour' }}
                                                        </span>
                                                    @elseif($effType === 'quiz' && $lesson->quiz)
                                                        <span>&bull;</span>
                                                        <span class="text-amber-700 font-semibold font-sans">
                                                            📝 {{ $lesson->quiz->questions_count ?? $lesson->quiz->questions()->count() }} MCQs ({{ (int)$lesson->quiz->total_marks }} Marks)
                                                        </span>
                                                    @elseif($effType === 'mains')
                                                        <span>&bull;</span>
                                                        <span class="text-emerald-700 font-semibold font-sans">
                                                            ✍️ {{ $lesson->mains_marks ?? 15 }} Marks ({{ $lesson->mains_word_limit ?? 250 }} Words Model Answer)
                                                        </span>
                                                    @elseif($effType === 'live')
                                                        <span>&bull;</span>
                                                        <span class="text-purple-700 font-semibold font-sans">
                                                            🎥 1-on-1 Interactive Video Class ({{ $lesson->meeting_platform ?? 'Live' }})
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Status Badge & CTA -->
                                        <div class="self-end md:self-center shrink-0">
                                            @if($lesson->is_free_preview)
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider">
                                                    Free Demo
                                                </span>
                                            @elseif($isEnrolled)
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 uppercase tracking-wider">
                                                    Included in Batch
                                                </span>
                                            @else
                                                <span class="text-xs text-slate-400 font-medium flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    Enrolled Only
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                            Course planner and timetable will be released prior to batch commencement.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Full Description -->
            @if($course->description)
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-4">
                    <h2 class="text-lg font-bold text-slate-900">About this Programme</h2>
                    <div class="prose prose-slate max-w-none text-sm leading-relaxed text-slate-700">
                        {!! Str::contains($course->description, '<') ? $course->description : nl2br(e($course->description)) !!}
                    </div>
                </div>
            @endif
        </div>

        <!-- Right 4 Cols: Purchase / Enrollment Sticky Card -->
        <div class="lg:col-span-4 sticky top-24 space-y-6">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-amber-500/20 shadow-xl space-y-6 relative overflow-hidden">
                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Course Fee</span>
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl font-extrabold text-slate-900 font-mono">
                            @if($course->price > 0)
                                ₹{{ number_format($course->price, 0) }}
                            @else
                                <span class="text-emerald-600 font-sans">FREE</span>
                            @endif
                        </span>
                        @if($course->mrp > $course->price && $course->price > 0)
                            @php
                                $disc = round((($course->mrp - $course->price) / $course->mrp) * 100);
                            @endphp
                            <span class="text-sm text-slate-400 line-through">₹{{ number_format($course->mrp, 0) }}</span>
                            <span class="px-2 py-0.5 rounded text-xs font-extrabold bg-emerald-100 text-emerald-800">{{ $disc }}% OFF</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-400">Includes live classes, recorded HD player, test series & GST</p>
                </div>

                <!-- Action Button -->
                @if($isEnrolled)
                    <div class="space-y-2">
                        <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>You are actively enrolled in this course!</span>
                        </div>
                        <a href="{{ route('student.courses.show', $course->slug) }}" 
                           class="w-full py-3.5 px-4 rounded-2xl bg-brand-orange hover:bg-brand-orangeHover text-white font-bold text-sm text-center shadow-lg shadow-brand-orange/30 transition flex items-center justify-center gap-2">
                            <span>Open Course Player</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                @else
                    <form action="{{ route('courses.enroll', $course) }}" method="POST" class="space-y-3">
                        @csrf
                        <button type="submit" 
                                class="w-full py-3.5 px-4 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-sm text-center shadow-xl shadow-amber-600/30 transition flex items-center justify-center gap-2 group">
                            <span>Enroll Now &bull; Start Learning</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>

                        @guest
                            <p class="text-[11px] text-slate-500 text-center">
                                New student? You'll be prompted to create your free account in 30 seconds.
                            </p>
                        @endguest
                    </form>
                @endif

                <!-- Guarantee & Inclusions List -->
                <div class="pt-4 border-t border-slate-100 space-y-2.5 text-xs text-slate-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Unlimited playback access for {{ $course->validity_days }} days</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Downloadable lecture notes & mind maps</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Access to Prelims timed test series</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Delhi HQ & 11 National Centres support</span>
                    </div>
                </div>

                <!-- Mentorship Helpline -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 text-center space-y-1">
                    <div class="text-[11px] text-slate-500">Need help deciding on your batch?</div>
                    <a href="tel:+919911020626" class="text-xs font-bold text-amber-700 hover:underline inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 4V3z"/></svg>
                        <span>Speak with Academic Counsellor: +91 9911020626</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
