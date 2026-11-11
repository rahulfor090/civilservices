@extends('layouts.app')

@section('title', $mainsTest->title . ' — Mains Test Paper | CivilServiceGurukul')

@section('content')
<div class="bg-gray-50 py-10 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex text-xs font-semibold text-gray-500 mb-6 space-x-2">
            <a href="{{ route('student.dashboard') }}" class="hover:text-gray-700">Dashboard</a>
            <span>/</span>
            <a href="{{ route('student.mains.index') }}" class="hover:text-gray-700">Mains Evaluation</a>
            <span>/</span>
            <span class="text-gray-900">{{ $mainsTest->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left 2 Cols: Question Paper Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
                    <div class="flex items-center space-x-3 mb-2">
                        <span class="px-2.5 py-1 text-xs font-bold uppercase rounded bg-navy-100 text-navy-800">
                            {{ strtoupper($mainsTest->gs_paper) }}
                        </span>
                        <span class="text-xs font-semibold text-gray-500">
                            Total Marks: {{ $mainsTest->total_marks }} | Time: {{ $mainsTest->duration_minutes }} Minutes
                        </span>
                    </div>

                    <h1 class="text-2xl font-extrabold text-gray-900 mt-2">{{ $mainsTest->title }}</h1>
                    
                    @if($mainsTest->instructions)
                    <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-900 leading-relaxed">
                        <strong class="font-bold block mb-1">UPSC Mains Instructions to Candidates:</strong>
                        {{ $mainsTest->instructions }}
                    </div>
                    @endif

                    <!-- Questions List -->
                    <div class="mt-8">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Question Paper Blueprint</h3>
                        <div class="space-y-6">
                            @foreach($mainsTest->questions as $q)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-primary-700">Question {{ $q->question_number }}</span>
                                    <span class="font-semibold text-gray-600">{{ $q->max_marks }} Marks • {{ $q->word_limit }} Words</span>
                                </div>
                                <p class="text-sm text-gray-800 leading-relaxed">
                                    {{ $q->stem }}
                                </p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Col: Upload Answer Copy Box -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-base font-bold text-gray-900 mb-2">Submit Answer Copy</h3>
                    <p class="text-xs text-gray-600 mb-4">
                        Write answers on standard A4 or UPSC margin answer booklet, scan as a single legible PDF, and upload below.
                    </p>

                    <form action="{{ route('student.mains.submit', $mainsTest) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Your UPSC Roll No (Optional)</label>
                            <input type="text" name="candidate_roll_no" value="{{ old('candidate_roll_no') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-primary-500 focus:outline-none" placeholder="Leave blank to auto-generate">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Answer Copy PDF (Max 20MB) *</label>
                            <input type="file" name="answer_copy_pdf" required accept=".pdf" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            @error('answer_copy_pdf') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-2.5 bg-primary-700 hover:bg-primary-800 text-white font-bold rounded-lg text-xs shadow transition-colors">
                                Upload Copy for Evaluation
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                        <p>✓ Evaluated by experienced UPSC Mains evaluators</p>
                        <p>✓ 48-Hour evaluation turnaround SLA</p>
                        <p>✓ Line-by-line margin comments and synopsis</p>
                    </div>
                </div>

                @if($existingSubmission)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-xs text-blue-900 space-y-2">
                    <strong class="font-bold block">Previous Submission Found:</strong>
                    <p>You submitted copy #{{ $existingSubmission->candidate_roll_no }} on {{ $existingSubmission->submitted_at->format('d M, Y h:i A') }}. Current Status: <strong>{{ ucfirst($existingSubmission->status) }}</strong>.</p>
                    @if($existingSubmission->status === 'evaluated')
                    <a href="{{ route('student.mains.evaluated', $existingSubmission) }}" class="inline-block mt-2 font-bold text-primary-700 underline">
                        View Evaluated Report Card →
                    </a>
                    @endif
                </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
