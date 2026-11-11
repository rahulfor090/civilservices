@extends('layouts.admin')

@section('title', 'Write New Article')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to Articles</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Create New Article</h1>
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

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5">
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
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Content Type *</label>
                    <select name="content_type_id" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        @foreach($contentTypes as $type)
                            <option value="{{ $type->id }}" {{ old('content_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">GS Section / Paper</label>
                    <select name="section_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        <option value="">None / General</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ old('section_id') == $sec->id ? 'selected' : '' }}>
                                {{ $sec->code }} - {{ $sec->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Associated Quiz for Students -->
            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                        Associated Practice Quiz / Prelims Test for Students
                    </label>
                    <a href="{{ route('admin.quizzes.create') }}" target="_blank" class="text-xs font-semibold text-amber-700 hover:text-amber-800 underline">
                        + Create New Quiz
                    </a>
                </div>
                <select name="quiz_id" class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-amber-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">-- No Associated Quiz --</option>
                    @foreach($quizzes as $q)
                        <option value="{{ $q->id }}" {{ old('quiz_id') == $q->id ? 'selected' : '' }}>
                            #{{ $q->id }} - {{ $q->title }} ({{ strtoupper($q->language?->code ?? 'EN') }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-amber-700 mt-1.5">
                    Associating a quiz embeds an interactive test module on the article/editorial page, letting students test themselves immediately.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Article Title *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required 
                       placeholder="e.g. Electoral Bonds Scheme: Constitutional Benchmark and Transparency"
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug (Auto-generated if blank)</label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug') }}" 
                       placeholder="electoral-bonds-scheme-constitutional-benchmark"
                       class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Excerpt / Brief Summary</label>
                <textarea name="excerpt" 
                          rows="2" 
                          placeholder="Brief 2-3 line summary shown in archive cards and search snippets..."
                          class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed">{{ old('excerpt') }}</textarea>
            </div>

            <!-- Featured Image / Thumbnail / WhatsApp Preview -->
            <div>
                @include('admin.articles._featured_image_field', [
                    'initialUrl' => old('featured_image', ''),
                    'initialMediaId' => old('hero_media_id', ''),
                    'articleTitle' => old('title', 'Article Title Preview for WhatsApp')
                ])
            </div>

            <!-- Article PDF Attachment / Dynamic Generation -->
            <div>
                @include('admin.articles._pdf_upload_field', [
                    'initialPdfUrl' => old('pdf_path', ''),
                    'initialPdfMediaId' => old('pdf_media_id', '')
                ])
            </div>

            <div>
                @include('admin.articles._editor', ['initialContent' => old('body_html', '')])
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Publication Status *</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="in_review" {{ old('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (Live immediately)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Reading Time (Minutes)</label>
                    <input type="number" 
                           name="reading_minutes" 
                           value="{{ old('reading_minutes', 5) }}" 
                           min="1" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Source Name</label>
                    <input type="text" 
                           name="source_name" 
                           value="{{ old('source_name', 'The Hindu') }}" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.articles.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-md transition-all">
                Save & Create Article
            </button>
        </div>
    </form>
</div>
@endsection
