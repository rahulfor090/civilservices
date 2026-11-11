@extends('layouts.app')

@section('title', 'Book 1-on-1 UPSC Counselling | CivilServiceGurukul')
@section('meta_description', 'Schedule a free 1-on-1 academic counselling session with senior IAS mentors online or at any of our 11 national classroom centres.')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <span class="text-xs font-bold text-primary-700 uppercase tracking-widest bg-primary-100 px-3 py-1 rounded-full">Academic Guidance</span>
            <h1 class="text-3xl font-extrabold text-gray-900 mt-3 sm:text-4xl">Book 1-on-1 UPSC Mentorship Session</h1>
            <p class="text-gray-600 mt-2 text-sm max-w-xl mx-auto">
                Discuss your preparation roadmap, optional subject selection, booklists, and classroom/online batch schedules with our senior faculty.
            </p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-start space-x-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <div>
                <strong class="font-bold">Booking Confirmed!</strong>
                <p class="mt-1">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <form action="{{ route('counselling.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Candidate Full Name *</label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. Rahul Sharma">
                        @error('full_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mobile Number (WhatsApp) *</label>
                        <input type="tel" name="mobile" required value="{{ old('mobile') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. 9876543210">
                        @error('mobile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="rahul@example.com">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Preferred Medium *</label>
                        <select name="medium" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="en" {{ old('medium') == 'en' ? 'selected' : '' }}>English Medium</option>
                            <option value="hi" {{ old('medium') == 'hi' ? 'selected' : '' }}>हिन्दी माध्यम</option>
                        </select>
                        @error('medium') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Preferred Centre</label>
                        <select name="centre_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Online / Phone Consultation</option>
                            @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ old('centre_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->city }})
                            </option>
                            @endforeach
                        </select>
                        @error('centre_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Target Exam Year *</label>
                        <select name="target_year" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="2026" {{ old('target_year') == '2026' ? 'selected' : '' }}>UPSC CSE 2026 (Recommended)</option>
                            <option value="2025" {{ old('target_year') == '2025' ? 'selected' : '' }}>UPSC CSE 2025</option>
                            <option value="2027" {{ old('target_year') == '2027' ? 'selected' : '' }}>UPSC CSE 2027 (Undergraduates)</option>
                        </select>
                        @error('target_year') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Best Call Window *</label>
                        <select name="call_time_window" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="morning" {{ old('call_time_window') == 'morning' ? 'selected' : '' }}>Morning (10:00 AM – 1:00 PM)</option>
                            <option value="afternoon" {{ old('call_time_window') == 'afternoon' ? 'selected' : '' }}>Afternoon (2:00 PM – 5:00 PM)</option>
                            <option value="evening" {{ old('call_time_window') == 'evening' ? 'selected' : '' }}>Evening (5:00 PM – 8:00 PM)</option>
                        </select>
                        @error('call_time_window') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Primary Area of Interest</label>
                        <select name="interest_category" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="UPSC Foundation (Pre-cum-Mains)">UPSC Foundation (Pre-cum-Mains)</option>
                            <option value="Daily Mains Answer Writing Programme">Daily Mains Answer Writing Programme</option>
                            <option value="All-India Prelims Test Series">All-India Prelims Test Series</option>
                            <option value="Optional Subject (PSIR / History / Geography / Sociology)">Optional Subject Guidance</option>
                            <option value="Interview Guidance Programme (IGP)">Interview Guidance Programme</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Specific Questions / Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Tell us about your educational background or attempts so far...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">100% Free & confidential session with zero sales pressure.</p>
                    <button type="submit" class="px-6 py-3 bg-primary-700 hover:bg-primary-800 text-white font-bold rounded-lg text-sm shadow transition-colors">
                        Confirm My Session
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
