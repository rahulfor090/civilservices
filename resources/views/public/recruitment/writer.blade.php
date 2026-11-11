@extends('layouts.app')

@section('title', 'Content Writer & Researcher Recruitment | CivilServiceGurukul')
@section('meta_description', 'Join our editorial and research desk for Daily News Analysis, Prelims Test Series question development, and UPSC Mains content.')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-8">
            <span class="text-xs font-bold text-navy-900 uppercase tracking-widest bg-blue-100 px-3 py-1 rounded-full">Editorial Careers</span>
            <h1 class="text-3xl font-extrabold text-gray-900 mt-3 sm:text-4xl">UPSC Content Developer & Researcher</h1>
            <p class="text-gray-600 mt-2 text-sm max-w-xl mx-auto">
                Author authoritative Daily News Analysis, Monthly Gists, and high-yield Prelims MCQs for India's premier civil services readership.
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
            <form action="{{ route('recruitment.writer.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name *</label>
                        <input type="text" name="full_name" required value="{{ old('full_name') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Your Name">
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
                        <input type="email" name="email" required value="{{ old('email') }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="writer@example.com">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">UPSC Preparation Background *</label>
                        <input type="number" name="experience_years" required min="0" max="20" value="{{ old('experience_years', 1) }}" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <span class="text-xs text-gray-400">Years appeared in UPSC Prelims/Mains</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Role / Content Track *</label>
                    <select name="subject_specialization" required class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        <option value="Daily News Analysis & Editorial Research">Daily News Analysis & Editorial Research</option>
                        <option value="Prelims MCQ Development & Test Bank Authoring">Prelims MCQ Development & Test Bank Authoring</option>
                        <option value="Mains Model Answer Synopsis Development">Mains Model Answer Synopsis Development</option>
                        <option value="Hindi Medium Editorial Translation & Adaptation">Hindi Medium Editorial Translation & Adaptation</option>
                        <option value="PIB, Sansad TV & Monthly Magazine Synthesis">PIB, Sansad TV & Monthly Magazine Synthesis</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Upload Resume & Writing Samples (PDF, Max 5MB)</label>
                    <input type="file" name="resume_file" accept=".pdf,.doc,.docx" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">UPSC Attempts / Mains Cleared / Portfolio Links</label>
                    <textarea name="notes" rows="3" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Detail your attempts (e.g. appeared in Mains 2023, 2024) and published articles...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">Flexible full-time and freelance research positions available.</p>
                    <button type="submit" class="px-6 py-3 bg-navy-900 hover:bg-navy-800 text-white font-bold rounded-lg text-sm shadow transition-colors">
                        Submit Editorial Application
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
