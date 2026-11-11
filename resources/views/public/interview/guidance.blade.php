@extends('layouts.app')

@section('title', 'UPSC Interview Guidance Programme (IGP) & Mock Interview | CivilServiceGurukul')
@section('meta_description', 'Detailed Application Form (DAF) analysis, one-on-one session with retired UPSC board members and mock interviews in Delhi Old Rajinder Nagar & online.')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <span class="text-xs font-bold text-navy-900 uppercase tracking-widest bg-amber-200 px-3 py-1 rounded-full">Phase 3 · Personality Test</span>
            <h1 class="text-3xl font-extrabold text-gray-900 mt-3 sm:text-4xl">UPSC Personality Test Guidance & Mock Interviews</h1>
            <p class="text-gray-600 mt-2 text-sm max-w-2xl mx-auto">
                Led by former UPSC board members, retired Secretaries to Govt. of India, and senior psychologists. Submit your DAF for customized questionnaire generation.
            </p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-start space-x-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <div>
                <strong class="font-bold">IGP Registration Submitted!</strong>
                <p class="mt-1">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <form action="{{ route('interview.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2">1. Candidate Credentials</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Candidate Full Name *</label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="As in UPSC DAF">
                        @error('full_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mobile (WhatsApp) *</label>
                        <input type="tel" name="mobile" required value="{{ old('mobile') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. 9876543210">
                        @error('mobile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address *</label>
                        <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="candidate@example.com">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">UPSC Roll Number *</label>
                        <input type="text" name="upsc_roll_number" required value="{{ old('upsc_roll_number') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. 0854321">
                        @error('upsc_roll_number') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Optional Subject *</label>
                        <input type="text" name="optional_subject" required value="{{ old('optional_subject') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. PSIR / History">
                        @error('optional_subject') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Interview Medium *</label>
                        <select name="medium" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="en" {{ old('medium') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="hi" {{ old('medium') == 'hi' ? 'selected' : '' }}>हिन्दी</option>
                        </select>
                    </div>
                </div>

                <h3 class="text-base font-bold text-gray-900 border-b border-gray-100 pb-2 pt-2">2. DAF Upload & Slot Preference</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Upload DAF PDF (Max 5MB)</label>
                        <input type="file" name="daf_file" accept=".pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        <p class="text-xs text-gray-400 mt-1">Both DAF-I and DAF-II accepted in PDF format.</p>
                        @error('daf_file') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Preferred Centre / Mode</label>
                        <select name="centre_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">Online Board Panel (Video Mock)</option>
                            @foreach($centres as $c)
                            <option value="{{ $c->id }}" {{ old('centre_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->city }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tentative Available Date</label>
                    <input type="date" name="interview_slot_date" value="{{ old('interview_slot_date') }}" min="{{ date('Y-m-d') }}" class="w-full sm:w-1/2 px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">DAF files are held under strict non-disclosure protocol.</p>
                    <button type="submit" class="px-6 py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold rounded-lg text-sm shadow transition-colors">
                        Submit DAF & Book Panel
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
