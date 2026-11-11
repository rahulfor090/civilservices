@extends('layouts.admin')

@section('title', 'Create New Course')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to Courses</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Create New Course</h1>
            <p class="text-xs text-slate-500">Configure curriculum, target examinations, fee structures, and admissions.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs space-y-1">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Core Details Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                1. Core Course Information
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Language *</label>
                    <select name="language_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->id }}" {{ old('language_id') == $lang->id ? 'selected' : '' }}>
                                {{ $lang->name }} ({{ $lang->code }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Target Exam *</label>
                    <select name="target_exam" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="upsc_cse" {{ old('target_exam') == 'upsc_cse' ? 'selected' : '' }}>UPSC CSE (IAS / IPS)</option>
                        <option value="uppcs" {{ old('target_exam') == 'uppcs' ? 'selected' : '' }}>UPPSC (Uttar Pradesh)</option>
                        <option value="bpsc" {{ old('target_exam') == 'bpsc' ? 'selected' : '' }}>BPSC (Bihar PCS)</option>
                        <option value="ras" {{ old('target_exam') == 'ras' ? 'selected' : '' }}>RAS (Rajasthan)</option>
                        <option value="mppsc" {{ old('target_exam') == 'mppsc' ? 'selected' : '' }}>MPPSC (Madhya Pradesh)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Level *</label>
                    <select name="level" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="foundation" {{ old('level') == 'foundation' ? 'selected' : '' }}>Foundation / GS Integrated (Pre+Mains)</option>
                        <option value="prelims" {{ old('level') == 'prelims' ? 'selected' : '' }}>Prelims Specialized / Crash Course</option>
                        <option value="mains" {{ old('level') == 'mains' ? 'selected' : '' }}>Mains Mentorship & Answer Writing</option>
                        <option value="optional" {{ old('level') == 'optional' ? 'selected' : '' }}>Optional Subject Program</option>
                        <option value="interview" {{ old('level') == 'interview' ? 'selected' : '' }}>Interview Guidance / DAF</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Title *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required 
                       placeholder="e.g. UPSC CSE 2026 GS Foundation Course (Pre+Mains Bilingual)"
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug (Auto-generated if empty)</label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug') }}" 
                       placeholder="e.g. upsc-cse-2026-gs-foundation-bilingual"
                       class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Short Excerpt / Synopsis</label>
                <textarea name="excerpt" 
                          rows="2" 
                          placeholder="Brief 2-3 line summary shown in course directory cards and search listings..."
                          class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed">{{ old('excerpt') }}</textarea>
            </div>

            <!-- Rich HTML Course Description -->
            <div>
                @include('admin.articles._editor', [
                    'inputName' => 'description',
                    'inputLabel' => 'Course Overview & Curriculum Details (Rich HTML)',
                    'initialContent' => old('description', ''),
                    'editorRequired' => false
                ])
            </div>
        </div>

        <!-- Pricing & Commercials Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-5">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                2. Fee Structure & Validity
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Selling Fee (₹) *</label>
                    <input type="number" 
                           step="0.01" 
                           name="price" 
                           value="{{ old('price', '45000') }}" 
                           required 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono font-bold focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Original MRP (₹)</label>
                    <input type="number" 
                           step="0.01" 
                           name="mrp" 
                           value="{{ old('mrp', '65000') }}" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Access Validity (Days) *</label>
                    <input type="number" 
                           name="validity_days" 
                           value="{{ old('validity_days', '365') }}" 
                           required 
                           min="1" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Publication Status *</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Active)</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" 
                       id="is_featured" 
                       name="is_featured" 
                       value="1" 
                       {{ old('is_featured') ? 'checked' : '' }}
                       class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                <label for="is_featured" class="text-xs font-semibold text-slate-700">
                    Feature this course on public homepage banner cards
                </label>
            </div>
        </div>

        <!-- Initial Batch Setup Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                3. Initial Batch Setup (Optional)
            </h2>

            <div class="flex items-center gap-2">
                <input type="checkbox" 
                       id="create_initial_batch" 
                       name="create_initial_batch" 
                       value="1" 
                       checked
                       class="rounded border-slate-300 text-brand-orange focus:ring-brand-orange h-4 w-4">
                <label for="create_initial_batch" class="text-xs font-semibold text-slate-700">
                    Automatically create an initial batch for student enrollments
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batch Code</label>
                    <input type="text" 
                           name="batch_code" 
                           value="{{ old('batch_code', 'BATCH-CSE-' . date('y') . '-' . strtoupper(\Illuminate\Support\Str::random(3))) }}" 
                           placeholder="e.g. BATCH-CSE-26-01" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Batch Name</label>
                    <input type="text" 
                           name="batch_title" 
                           value="{{ old('batch_title', 'Regular Morning Batch (Live + Recorded)') }}" 
                           placeholder="e.g. Regular Morning Batch" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Delivery Mode</label>
                    <select name="delivery_mode" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="online">Online (Live / Recorded Video)</option>
                        <option value="offline">Offline (Classroom Centre)</option>
                        <option value="hybrid">Hybrid (Classroom + Online)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.courses.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-orange hover:bg-brand-orangeHover text-white text-xs font-bold shadow-md transition-all">
                Save & Continue to Batch Manager &rarr;
            </button>
        </div>
    </form>
</div>
@endsection
