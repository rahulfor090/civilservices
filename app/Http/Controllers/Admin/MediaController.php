<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaController extends Controller
{
    /**
     * Master Media Manager listing & browser
     */
    public function index(Request $request): View
    {
        $query = Media::latest('id');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%")
                  ->orWhere('caption', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            if ($type === 'image') {
                $query->where('mime_type', 'like', 'image/%');
            } elseif ($type === 'document') {
                $query->where('mime_type', 'not like', 'image/%');
            }
        }

        $media = $query->paginate(24)->withQueryString();

        $totalBytes = (float) (Media::sum('size_bytes') ?? 0);

        $stats = [
            'total_count' => Media::count(),
            'images_count' => Media::where('mime_type', 'like', 'image/%')->count(),
            'total_size_mb' => round($totalBytes / (1024 * 1024), 2),
        ];

        return view('admin.media.index', compact('media', 'stats'));
    }

    /**
     * Upload single or multiple files
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200', // max 50MB
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        $storedName = Str::random(24) . '.' . $ext;
        $folder = 'media/' . date('Y/m');

        // Store file on public disk
        $path = $file->storeAs($folder, $storedName, 'public');

        $mime = $file->getMimeType() ?: 'application/octet-stream';
        $width = null;
        $height = null;

        if (str_starts_with($mime, 'image/') && in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'])) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
            }
        }

        $media = Media::create([
            'disk' => 'public',
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'extension' => $ext,
            'mime_type' => $mime,
            'size_bytes' => $file->getSize(),
            'width' => $width,
            'height' => $height,
            'alt_text' => $request->input('alt_text') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'caption' => $request->input('caption'),
            'uploaded_by' => auth()->id(),
        ]);

        AuditLog::record(auth()->user(), 'media', 'upload_file', $media, null, $media->toArray());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'media' => [
                    'id' => $media->id,
                    'url' => $media->url,
                    'original_name' => $media->original_name,
                    'mime_type' => $media->mime_type,
                    'size_bytes' => $media->size_bytes,
                    'width' => $media->width,
                    'height' => $media->height,
                ],
                'message' => 'File uploaded successfully.'
            ]);
        }

        return redirect()->route('admin.media.index')->with('success', "File '{$media->original_name}' uploaded successfully. Public URL is ready to copy.");
    }

    /**
     * Delete file from disk & database
     */
    public function destroy(Media $media, Request $request): JsonResponse|RedirectResponse
    {
        $name = $media->original_name;
        $disk = $media->disk ?: 'public';

        if (Storage::disk($disk)->exists($media->path)) {
            Storage::disk($disk)->delete($media->path);
        }

        AuditLog::record(auth()->user(), 'media', 'delete_file', $media, $media->toArray(), null);
        $media->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "File '{$name}' deleted."
            ]);
        }

        return redirect()->route('admin.media.index')->with('success', "File '{$name}' permanently deleted.");
    }

    /**
     * JSON picker API for modal media selector inside editors
     */
    public function picker(Request $request): JsonResponse
    {
        $query = Media::latest('id');

        if ($search = $request->query('search')) {
            $query->where('original_name', 'like', "%{$search}%");
        }

        if ($request->boolean('images_only', true)) {
            $query->where('mime_type', 'like', 'image/%');
        }

        $items = $query->take(60)->get()->map(function ($m) {
            return [
                'id' => $m->id,
                'url' => $m->url,
                'original_name' => $m->original_name,
                'extension' => $m->extension,
                'mime_type' => $m->mime_type,
                'size_human' => round(((float) ($m->size_bytes ?? 0)) / 1024, 1) . ' KB',
                'width' => $m->width,
                'height' => $m->height,
                'created_at' => $m->created_at ? $m->created_at->format('d M Y') : '',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }
}
