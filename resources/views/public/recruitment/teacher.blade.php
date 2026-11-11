@extends('layouts.app')

@section('title', 'Faculty Recruitment | CivilServiceGurukul')
@section('meta_description', 'Join our national faculty panel for UPSC CSE General Studies and Optionals at Delhi Old Rajinder Nagar, Mukherjee Nagar and online.')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-widest bg-amber-100 px-3 py-1 rounded-full">Academic Careers</span>
            <h1 class="text-3xl font-extrabold text-gray-900 mt-3 sm:text-4xl">Join as UPSC Faculty / Educator</h1>
            <p class="text-gray-600 mt-2 text-sm max-w-xl mx-auto">
                Teach the nation's brightest civil services aspirants. We invite experienced educators for GS Papers 1–4, CSAT, Essay, and optional subjects.
            </p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-sm flex items-start space-x-3">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <div>
                <strong class="font-bold">Application Received!</strong>
                <p class="mt-1">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <form action="{{ route('recruitment.teacher.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name *</label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Dr. / Prof. / Mr.">
                        @error('full_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mobile Number *</label>
                        <input type="tel" name="mobile" required value="{{ old('mobile') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="e.g. 9876543210">
                        @error('mobile') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address *</label>
                        <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="faculty@example.com">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Years of Teaching Experience *</label>
                        <input type="number" name="experience_years" required min="0" max="40" value="{{ old('experience_years', 2) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        @error('experience_years') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Subject Specialization *</label>
                    <select name="subject_specialization" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="Indian Polity & Governance (GS 2)">Indian Polity & Governance (GS 2)</option>
                        <option value="Modern Indian History & Culture (GS 1)">Modern Indian History & Culture (GS 1)</option>
                        <option value="Geography & Disaster Management (GS 1/3)">Geography & Disaster Management (GS 1/3)</option>
                        <option value="Indian Economy & Development (GS 3)">Indian Economy & Development (GS 3)</option>
                        <option value="Environment & Ecology (GS 3)">Environment & Ecology (GS 3)</option>
                        <option value="Ethics, Integrity & Aptitude (GS 4)">Ethics, Integrity & Aptitude (GS 4)</option>
                        <option value="International Relations (GS 2)">International Relations (GS 2)</option>
                        <option value="CSAT Quantitative & Reasoning">CSAT Quantitative & Reasoning</option>
                        <option value="PSIR Optional">PSIR Optional</option>
                        <option value="History Optional">History Optional</option>
                        <option value="Sociology Optional">Sociology Optional</option>
                        <option value="Geography Optional">Geography Optional</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Upload Resume / CV (PDF, Max 5MB)</label>
                    <input type="file" name="resume_file" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Demo Lecture Link / Academic Summary</label>
                    <textarea name="notes" rows="3" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Provide YouTube/Drive links to your lectures or publications...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">Competitive compensation benchmarked with top national institutions.</p>
                    <button type="submit" class="px-6 py-3 bg-primary-700 hover:bg-primary-800 text-white font-bold rounded-lg text-sm shadow transition-colors">
                        Submit Faculty Application
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
