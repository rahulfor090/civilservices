@extends('layouts.app')

@section('title', $centre->name . ' — UPSC Coaching Centre | CivilServiceGurukul')
@section('meta_description', 'Full details, faculty, classroom batches and contact information for our ' . $centre->name . ' campus in ' . $centre->city . '.')

@section('content')
<div class="bg-gray-50 py-10 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex text-xs font-semibold text-gray-500 mb-6 space-x-2">
            <a href="{{ route('home') }}" class="hover:text-gray-700">Home</a>
            <span>/</span>
            <a href="{{ route('centres.index') }}" class="hover:text-gray-700">Centres</a>
            <span>/</span>
            <span class="text-gray-900">{{ $centre->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                    <span class="px-2.5 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-md">
                        {{ $centre->city }} Campus
                    </span>
                    <h1 class="text-3xl font-extrabold text-gray-900 mt-2">{{ $centre->name }}</h1>
                    <p class="text-gray-600 mt-2 text-sm leading-relaxed">
                        Official offline classroom centre and regional academic counselling headquarters in {{ $centre->city }}, {{ $centre->state }}.
                    </p>

                    <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Address</span>
                            <p class="text-gray-800 font-medium mt-1">{{ $centre->address }}</p>
                            @if($centre->pincode)
                            <p class="text-gray-500 text-xs mt-0.5">PIN Code: {{ $centre->pincode }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Contact & Inquiry</span>
                            <p class="text-gray-800 font-semibold mt-1">
                                <a href="tel:{{ $centre->phone }}" class="text-primary-700 hover:underline">{{ $centre->phone }}</a>
                            </p>
                            @if($centre->email)
                            <p class="text-gray-500 text-xs mt-0.5">{{ $centre->email }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Facilities list -->
                    @if($centre->facilities)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider text-xs">Campus Facilities & Infrastructure</h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($centre->facilities as $fac)
                            <div class="flex items-center space-x-2 text-xs text-gray-700 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                <span class="font-medium">{{ $fac }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($centre->google_maps_url)
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <a href="{{ $centre->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            Open in Google Maps Navigation
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Upcoming Classroom Batches -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Upcoming Classroom Batches</h2>
                    @if(count($batches) > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($batches as $batch)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-xs font-bold text-primary-700 uppercase">{{ $batch->course?->title ?? 'UPSC Foundation' }}</span>
                                <h4 class="text-base font-bold text-gray-900">{{ $batch->title }}</h4>
                                <div class="text-xs text-gray-500 mt-1 flex items-center space-x-3">
                                    <span>Starts: {{ $batch->start_date ? \Carbon\Carbon::parse($batch->start_date)->format('d M Y') : 'Announced Soon' }}</span>
                                    <span>•</span>
                                    <span>Faculty: {{ $batch->faculty_names ?? 'Senior Panel' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('counselling.create') }}" class="px-3 py-1.5 bg-navy-900 hover:bg-navy-800 text-white rounded text-xs font-semibold self-start sm:self-center">
                                Reserve Seat
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-500">Upcoming offline classroom batches for this session will be updated shortly. Call the centre desk to inquire.</p>
                    @endif
                </div>

            </div>

            <!-- Sidebar Counselling Booking CTA -->
            <div class="space-y-6">
                <div class="bg-gradient-to-br from-navy-900 to-primary-900 text-white rounded-xl shadow-md p-6">
                    <span class="px-2 py-0.5 text-xs font-bold bg-amber-400 text-navy-950 rounded uppercase">Offline Counselling</span>
                    <h3 class="text-xl font-extrabold mt-2">Visit {{ $centre->city }} Campus</h3>
                    <p class="text-gray-200 text-xs mt-2 leading-relaxed">
                        Book a complimentary 1-on-1 session with our senior UPSC faculty in {{ $centre->city }}. Get custom syllabus planning and offline class demo passes.
                    </p>

                    <div class="mt-6">
                        <a href="{{ route('counselling.create') }}" class="block text-center w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-navy-950 font-bold rounded-lg text-sm shadow transition-all">
                            Book Free Appointment
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h4 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider text-xs">National Admissions Desk</h4>
                    <p class="text-xs text-gray-600 mb-2">Central Admissions & Support Helpline:</p>
                    <a href="tel:+919911020626" class="text-base font-extrabold text-brand-navy hover:text-brand-orange block mb-1">+91 9911020626</a>
                    <a href="mailto:info@civilservicegurukul.com" class="text-xs text-slate-600 hover:text-brand-orange hover:underline block mb-3">info@civilservicegurukul.com</a>
                    <p class="text-[11px] text-gray-500">Desk active Monday – Saturday: 9:00 AM to 8:00 PM IST</p>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
