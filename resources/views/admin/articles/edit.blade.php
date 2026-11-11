@extends('layouts.admin')

@section('title', 'Edit Article: ' . $article->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.articles.index') }}" class="text-xs text-brand-orange hover:underline font-semibold">&larr; Back to Articles</a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Edit Article</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $article->url }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 text-xs font-semibold inline-flex items-center gap-1.5 shadow-sm">
                <span>View Live</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
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

    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3 bg-blue-50/70 border border-blue-100 rounded-xl flex items-center justify-between text-xs text-blue-900">
                    <div class="flex items-center gap-2">
                        <span class="font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-200/70 text-blue-900">{{ $article->language->code }}</span>
                        <span>Content Type: <strong>{{ $article->contentType ? $article->contentType->name : 'General' }}</strong></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">GS Section / Paper</label>
                    <select name="section_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                        <option value="">None / General</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}" {{ old('section_id', $article->section_id) == $sec->id ? 'selected' : '' }}>
                                {{ $sec->code }} - {{ $sec->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Associated Quiz for Students -->
            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
                        Associated Practice Quiz / Prelims Test for Students
                    </label>
                    <div class="flex items-center gap-2">
                        @if($article->quiz)
                            <a href="{{ route('admin.quizzes.edit', $article->quiz) }}" target="_blank" class="text-xs font-semibold text-amber-700 hover:text-amber-800 underline">
                                Edit Quiz #{{ $article->quiz->id }} &rarr;
                            </a>
                            <span class="text-amber-300">|</span>
                        @endif
                        <a href="{{ route('admin.quizzes.create') }}" target="_blank" class="text-xs font-semibold text-amber-700 hover:text-amber-800 underline">
                            + Create New Quiz
                        </a>
                    </div>
                </div>
                <select name="quiz_id" class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-amber-300 text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">-- No Associated Quiz --</option>
                    @foreach($quizzes as $q)
                        <option value="{{ $q->id }}" {{ old('quiz_id', $article->quiz_id) == $q->id ? 'selected' : '' }}>
                            #{{ $q->id }} - {{ $q->title }} ({{ strtoupper($q->language?->code ?? 'EN') }}) - {{ $q->questions()->count() }} Questions
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-amber-700">
                    Associating a quiz enables students to attempt an interactive practice test directly linked to this editorial/article.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Article Title *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title', $article->title) }}" 
                       required 
                       class="w-full px-4 py-2.5 text-sm rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">URL Slug *</label>
                <input type="text" 
                       name="slug" 
                       value="{{ old('slug', $article->slug) }}" 
                       required
                       class="w-full px-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Excerpt / Brief Summary</label>
                <textarea name="excerpt" 
                          rows="2" 
                          class="w-full px-4 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy leading-relaxed">{{ old('excerpt', $article->excerpt) }}</textarea>
            </div>

            <!-- Featured Image / Thumbnail / WhatsApp Preview -->
            <div>
                @include('admin.articles._featured_image_field', [
                    'initialUrl' => old('featured_image', $article->feature_image_url),
                    'initialMediaId' => old('hero_media_id', $article->hero_media_id),
                    'articleTitle' => old('title', $article->title)
                ])
            </div>

            <!-- Article PDF Attachment / Dynamic Generation -->
            <div>
                @include('admin.articles._pdf_upload_field', [
                    'initialPdfUrl' => old('pdf_path', $article->uploaded_pdf_url),
                    'initialPdfMediaId' => old('pdf_media_id', $article->pdf_media_id)
                ])
            </div>

            <div>
                @include('admin.articles._editor', ['initialContent' => old('body_html', $article->body_html)])
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Publication Status *</label>
                    <select name="status" required class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-semibold">
                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="in_review" {{ old('status', $article->status) == 'in_review' ? 'selected' : '' }}>In Review</option>
                        <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published (Live)</option>
                        <option value="unpublished" {{ old('status', $article->status) == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
                        <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Reading Time (Minutes)</label>
                    <input type="number" 
                           name="reading_minutes" 
                           value="{{ old('reading_minutes', $article->reading_minutes) }}" 
                           min="1" 
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Custom Meta Title (SEO)</label>
                    <input type="text" 
                           name="meta_title" 
                           value="{{ old('meta_title', $article->meta_title) }}" 
                           placeholder="Defaults to article title"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Custom Meta Description (SEO)</label>
                    <input type="text" 
                           name="meta_description" 
                           value="{{ old('meta_description', $article->meta_description) }}" 
                           placeholder="Defaults to excerpt"
                           class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div>
                <button type="button" 
                        onclick="if(confirm('Are you sure you want to permanently delete this article?')) document.getElementById('deleteForm').submit();"
                        class="text-xs text-red-600 hover:text-red-700 font-semibold">
                    Delete Article
                </button>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.articles.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-semibold">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-md transition-all">
                    Update Article & Record Audit
                </button>
            </div>
        </div>
    </form>

    <form id="deleteForm" action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection
