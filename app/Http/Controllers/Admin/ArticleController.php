<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\AuditLog;
use App\Models\ContentType;
use App\Models\Language;
use App\Models\Media;
use App\Models\Quiz;
use App\Models\Redirect;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::with(['contentType', 'language', 'section', 'author', 'quiz'])
            ->latest('id');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($langId = $request->query('language_id')) {
            $query->where('language_id', $langId);
        }

        if ($typeId = $request->query('content_type_id')) {
            $query->where('content_type_id', $typeId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $articles = $query->paginate(15)->withQueryString();
        $languages = Language::where('is_active', true)->get();
        $contentTypes = ContentType::where('is_active', true)->get();

        return view('admin.articles.index', compact('articles', 'languages', 'contentTypes'));
    }

    public function create(): View
    {
        $languages = Language::where('is_active', true)->get();
        $contentTypes = ContentType::where('is_active', true)->get();
        $sections = Section::with('translations')->where('is_active', true)->get();
        $quizzes = Quiz::with('language')->orderByDesc('id')->get();

        return view('admin.articles.create', compact('languages', 'contentTypes', 'sections', 'quizzes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language_id' => 'required|exists:languages,id',
            'content_type_id' => 'required|exists:content_types,id',
            'section_id' => 'nullable|exists:sections,id',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:191',
            'excerpt' => 'nullable|string',
            'body_html' => 'required|string',
            'status' => 'required|in:draft,in_review,scheduled,published',
            'reading_minutes' => 'nullable|integer|min:1',
            'source_name' => 'nullable|string|max:100',
            'source_url' => 'nullable|url|max:255',
            'featured_image' => 'nullable|string|max:500',
            'hero_media_id' => 'nullable|exists:media,id',
            'featured_image_file' => 'nullable|image|max:15360',
            'pdf_path' => 'nullable|string|max:500',
            'pdf_media_id' => 'nullable|exists:media,id',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
        ]);

        $heroMediaId = $validated['hero_media_id'] ?? null;
        $featuredImage = $validated['featured_image'] ?? null;
        $pdfMediaId = $validated['pdf_media_id'] ?? null;
        $pdfPath = $validated['pdf_path'] ?? null;

        if ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $storedName = Str::random(24) . '.' . $ext;
            $path = $file->storeAs('articles/pdfs/' . date('Y/m'), $storedName, 'public');

            $media = Media::create([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'extension' => $ext,
                'mime_type' => 'application/pdf',
                'size_bytes' => $file->getSize(),
                'alt_text' => $validated['title'] . ' PDF',
                'uploaded_by' => auth()->id(),
            ]);

            $pdfMediaId = $media->id;
            $pdfPath = $media->url;
        } elseif ($pdfMediaId && empty($pdfPath)) {
            $m = Media::find($pdfMediaId);
            if ($m) {
                $pdfPath = $m->url;
            }
        }

        if ($request->hasFile('featured_image_file')) {
            $file = $request->file('featured_image_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $storedName = Str::random(24) . '.' . $ext;
            $path = $file->storeAs('media/' . date('Y/m'), $storedName, 'public');

            $width = null;
            $height = null;
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }

            $media = Media::create([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'extension' => $ext,
                'mime_type' => $file->getMimeType() ?: 'image/jpeg',
                'size_bytes' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'alt_text' => $validated['title'],
                'uploaded_by' => auth()->id(),
            ]);

            $heroMediaId = $media->id;
            $featuredImage = $media->url;
        } elseif ($heroMediaId && empty($featuredImage)) {
            $m = Media::find($heroMediaId);
            if ($m) {
                $featuredImage = $m->url;
            }
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        $article = Article::create([
            'content_type_id' => $validated['content_type_id'],
            'section_id' => $validated['section_id'] ?? null,
            'quiz_id' => $validated['quiz_id'] ?? null,
            'author_id' => auth()->id(),
            'translation_group_id' => (string) Str::uuid(),
            'language_id' => $validated['language_id'],
            'slug' => $slug,
            'title' => $validated['title'],
            'featured_image' => $featuredImage,
            'hero_media_id' => $heroMediaId,
            'og_media_id' => $heroMediaId,
            'pdf_path' => $pdfPath,
            'pdf_media_id' => $pdfMediaId,
            'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body_html']), 160),
            'body_html' => $validated['body_html'],
            'reading_minutes' => $validated['reading_minutes'] ?? 5,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'created_by' => auth()->id(),
        ]);

        // Audit Log
        AuditLog::record(auth()->user(), 'content', 'create_article', $article, null, $article->toArray());

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    public function edit(Article $article): View
    {
        $article->load(['sections', 'language', 'contentType', 'quiz', 'heroMedia']);
        $languages = Language::where('is_active', true)->get();
        $contentTypes = ContentType::where('is_active', true)->get();
        $sections = Section::with('translations')->where('is_active', true)->get();
        $quizzes = Quiz::with('language')->orderByDesc('id')->get();

        return view('admin.articles.edit', compact('article', 'languages', 'contentTypes', 'sections', 'quizzes'));
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $before = $article->toArray();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:191',
            'section_id' => 'nullable|exists:sections,id',
            'quiz_id' => 'nullable|exists:quizzes,id',
            'excerpt' => 'nullable|string',
            'body_html' => 'required|string',
            'status' => 'required|in:draft,in_review,scheduled,published,unpublished,archived',
            'reading_minutes' => 'nullable|integer|min:1',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'featured_image' => 'nullable|string|max:500',
            'hero_media_id' => 'nullable|exists:media,id',
            'featured_image_file' => 'nullable|image|max:15360',
            'remove_featured_image' => 'nullable|boolean',
            'pdf_path' => 'nullable|string|max:500',
            'pdf_media_id' => 'nullable|exists:media,id',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'remove_uploaded_pdf' => 'nullable|boolean',
        ]);

        if ($request->boolean('remove_uploaded_pdf')) {
            $validated['pdf_path'] = null;
            $validated['pdf_media_id'] = null;
        } elseif ($request->hasFile('pdf_file')) {
            $file = $request->file('pdf_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $storedName = Str::random(24) . '.' . $ext;
            $path = $file->storeAs('articles/pdfs/' . date('Y/m'), $storedName, 'public');

            $media = Media::create([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'extension' => $ext,
                'mime_type' => 'application/pdf',
                'size_bytes' => $file->getSize(),
                'alt_text' => $validated['title'] . ' PDF',
                'uploaded_by' => auth()->id(),
            ]);

            $validated['pdf_media_id'] = $media->id;
            $validated['pdf_path'] = $media->url;
        } elseif (!empty($validated['pdf_media_id'])) {
            $m = Media::find($validated['pdf_media_id']);
            if ($m) {
                $validated['pdf_path'] = $m->url;
            }
        }

        if ($request->boolean('remove_featured_image')) {
            $validated['featured_image'] = null;
            $validated['hero_media_id'] = null;
            $validated['og_media_id'] = null;
        } elseif ($request->hasFile('featured_image_file')) {
            $file = $request->file('featured_image_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $storedName = Str::random(24) . '.' . $ext;
            $path = $file->storeAs('media/' . date('Y/m'), $storedName, 'public');

            $width = null;
            $height = null;
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }

            $media = Media::create([
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'extension' => $ext,
                'mime_type' => $file->getMimeType() ?: 'image/jpeg',
                'size_bytes' => $file->getSize(),
                'width' => $width,
                'height' => $height,
                'alt_text' => $validated['title'],
                'uploaded_by' => auth()->id(),
            ]);

            $validated['hero_media_id'] = $media->id;
            $validated['og_media_id'] = $media->id;
            $validated['featured_image'] = $media->url;
        } elseif (!empty($validated['hero_media_id'])) {
            $m = Media::find($validated['hero_media_id']);
            if ($m) {
                $validated['featured_image'] = $m->url;
                $validated['og_media_id'] = $m->id;
            }
        } elseif (!empty($validated['featured_image'])) {
            $validated['og_media_id'] = $validated['hero_media_id'] ?? null;
        }

        $oldSlug = $article->slug;
        $newSlug = Str::slug($validated['slug']);

        // Check if slug changed on a published article -> auto-generate permanent 301 redirect
        if ($oldSlug !== $newSlug && $article->status === 'published') {
            $prefix = $article->language->code === 'hi' ? '/hindi/' : '/';
            $typePrefix = $article->contentType ? $article->contentType->url_prefix : 'daily-updates/daily-news-analysis';
            $oldPath = $prefix . trim($typePrefix, '/') . '/' . $oldSlug;
            $newPath = $prefix . trim($typePrefix, '/') . '/' . $newSlug;

            Redirect::updateOrCreate(
                ['from_path' => $oldPath],
                [
                    'to_path' => $newPath,
                    'status_code' => 301,
                    'created_by' => auth()->id(),
                ]
            );
        }

        $validated['slug'] = $newSlug;
        if ($validated['status'] === 'published' && !$article->published_at) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        // Audit Log
        AuditLog::record(auth()->user(), 'content', 'update_article', $article, $before, $article->fresh()->toArray());

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        $before = $article->toArray();
        $article->delete();

        AuditLog::record(auth()->user(), 'content', 'delete_article', $article, $before, null);

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $action = $request->input('action');
        $ids = $request->input('selected_ids', []);

        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No articles selected.');
        }

        $articles = Article::whereIn('id', $ids)->get();

        if ($action === 'publish') {
            Article::whereIn('id', $ids)->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
            AuditLog::record(auth()->user(), 'content', 'bulk_publish', null, null, ['count' => count($ids), 'ids' => $ids]);
            return back()->with('success', count($ids) . ' articles published.');
        } elseif ($action === 'unpublish') {
            Article::whereIn('id', $ids)->update(['status' => 'draft']);
            AuditLog::record(auth()->user(), 'content', 'bulk_unpublish', null, null, ['count' => count($ids), 'ids' => $ids]);
            return back()->with('success', count($ids) . ' articles moved to draft.');
        } elseif ($action === 'delete') {
            Article::whereIn('id', $ids)->delete();
            AuditLog::record(auth()->user(), 'content', 'bulk_delete', null, null, ['count' => count($ids), 'ids' => $ids]);
            return back()->with('success', count($ids) . ' articles deleted.');
        }

        return back()->with('error', 'Invalid bulk action.');
    }
}
