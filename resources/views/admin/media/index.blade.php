@extends('layouts.admin')

@section('title', 'Media Manager & Master Files')

@section('content')
<div class="space-y-6" x-data="{
    copyFeedback: {},
    copyUrl(url, id) {
        navigator.clipboard.writeText(url).then(() => {
            this.copyFeedback[id] = true;
            setTimeout(() => {
                this.copyFeedback[id] = false;
            }, 2200);
        }).catch(err => {
            // Fallback
            const el = document.createElement('textarea');
            el.value = url;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            this.copyFeedback[id] = true;
            setTimeout(() => {
                this.copyFeedback[id] = false;
            }, 2200);
        });
    }
}">
    <!-- Header with Stats -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 font-display">Media Manager & Master Files</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-brand-navy/10 text-brand-navy">
                    Master of Files
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Upload article feature images, thumbnails, infographics and handouts. Copy public URLs instantly or delete files.
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 shadow-xs text-xs">
                <span class="text-slate-400 font-medium">Total Files:</span>
                <span class="font-bold text-slate-800 ml-1 font-mono">{{ $stats['total_count'] }}</span>
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 shadow-xs text-xs">
                <span class="text-slate-400 font-medium">Images:</span>
                <span class="font-bold text-brand-orange ml-1 font-mono">{{ $stats['images_count'] }}</span>
            </div>
            <div class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 shadow-xs text-xs">
                <span class="text-slate-400 font-medium">Storage:</span>
                <span class="font-bold text-emerald-700 ml-1 font-mono">{{ $stats['total_size_mb'] }} MB</span>
            </div>
        </div>
    </div>

    <!-- Upload Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs space-y-4"
         x-data="{
             dragging: false,
             fileName: '',
             previewUrl: null,
             handleFileSelect(e) {
                 const file = e.target.files[0];
                 if (file) {
                     this.fileName = file.name;
                     if (file.type.startsWith('image/')) {
                         this.previewUrl = URL.createObjectURL(file);
                     } else {
                         this.previewUrl = null;
                     }
                 }
             }
         }">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Upload New File or Feature Image
            </h2>
            <span class="text-[11px] text-slate-400">Supported: JPG, PNG, WEBP, GIF, SVG, PDF, DOCX (Up to 50MB)</span>
        </div>

        <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                <!-- Dropzone Area -->
                <div class="lg:col-span-6">
                    <label class="relative flex flex-col items-center justify-center border-2 border-dashed rounded-2xl p-6 text-center cursor-pointer transition-all"
                           :class="dragging ? 'border-brand-orange bg-amber-50/50' : 'border-slate-300 hover:border-brand-navy hover:bg-slate-50/60'"
                           @dragover.prevent="dragging = true"
                           @dragleave.prevent="dragging = false"
                           @drop="dragging = false">
                        <input type="file" name="file" required class="sr-only" @change="handleFileSelect($event)">
                        
                        <template x-if="!previewUrl">
                            <div class="space-y-2">
                                <div class="w-12 h-12 rounded-full bg-brand-navy/10 text-brand-navy mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </div>
                                <div class="text-xs text-slate-600">
                                    <span class="font-bold text-brand-orange hover:underline">Click to browse file</span> or drag & drop here
                                </div>
                                <p class="text-[11px] text-slate-400 font-mono" x-text="fileName || 'No file selected yet'"></p>
                            </div>
                        </template>

                        <template x-if="previewUrl">
                            <div class="space-y-2 text-center">
                                <img :src="previewUrl" class="h-28 mx-auto rounded-xl object-contain shadow-xs border border-slate-200">
                                <p class="text-xs font-bold text-slate-700 font-mono truncate max-w-xs" x-text="fileName"></p>
                                <span class="text-[10px] text-brand-orange hover:underline font-semibold">Change File</span>
                            </div>
                        </template>
                    </label>
                </div>

                <!-- Meta Details & Submit -->
                <div class="lg:col-span-6 space-y-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Alt Text / Label (Recommended for SEO & Social Media)</label>
                        <input type="text" name="alt_text" placeholder="e.g. UPSC CSE 2026 Strategy Infographic" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-600 uppercase mb-1">Caption / Credit (Optional)</label>
                        <input type="text" name="caption" placeholder="e.g. Source: PIB / The Hindu" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="text-[11px] text-slate-400">
                            Files are saved in <code class="font-mono text-slate-600">public/storage/media/</code>
                        </div>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-brand-navy hover:bg-brand-navyLight text-white text-xs font-bold shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            <span>Upload & Generate URL</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Search & Filter Filter-bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('admin.media.index') }}" method="GET" class="flex items-center gap-2 flex-1 max-w-md">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search files by name, caption..." 
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('admin.media.index', ['type' => request('type')]) }}" class="text-xs text-slate-400 hover:text-slate-600 underline">
                    Clear
                </a>
            @endif
        </form>

        <div class="flex items-center gap-1.5 text-xs">
            <a href="{{ route('admin.media.index', ['search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg font-medium transition {{ !request('type') ? 'bg-brand-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All Files
            </a>
            <a href="{{ route('admin.media.index', ['type' => 'image', 'search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg font-medium transition {{ request('type') === 'image' ? 'bg-brand-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Images Only
            </a>
            <a href="{{ route('admin.media.index', ['type' => 'document', 'search' => request('search')]) }}" 
               class="px-3 py-1.5 rounded-lg font-medium transition {{ request('type') === 'document' ? 'bg-brand-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                PDFs & Documents
            </a>
        </div>
    </div>

    <!-- Media Grid -->
    @if($media->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($media as $item)
                @php
                    $isImage = str_starts_with($item->mime_type, 'image/');
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col group hover:shadow-md hover:border-slate-300 transition-all">
                    <!-- File Preview Box -->
                    <div class="relative bg-slate-100 aspect-video flex items-center justify-center overflow-hidden border-b border-slate-100">
                        @if($isImage)
                            <img src="{{ $item->url }}" 
                                 alt="{{ $item->alt_text }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                        @else
                            <div class="text-center p-4">
                                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 mx-auto flex items-center justify-center font-bold font-mono text-xs shadow-xs">
                                    {{ strtoupper($item->extension) }}
                                </div>
                                <span class="text-[11px] text-slate-500 font-mono mt-1 block">Document</span>
                            </div>
                        @endif

                        <!-- Floating Extension Tag -->
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-white text-[10px] font-mono uppercase font-bold">
                            {{ $item->extension }}
                        </span>

                        <!-- Resolution Tag if Image -->
                        @if($item->width && $item->height)
                            <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-xs text-white text-[10px] font-mono">
                                {{ $item->width }} &times; {{ $item->height }}
                            </span>
                        @endif
                    </div>

                    <!-- File Details -->
                    <div class="p-3.5 flex-1 flex flex-col justify-between space-y-3">
                        <div>
                            <h3 class="text-xs font-bold text-slate-800 truncate" title="{{ $item->original_name }}">
                                {{ $item->original_name }}
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-400 font-mono mt-1">
                                <span>{{ round(((float)($item->size_bytes ?? 0)) / 1024, 1) }} KB</span>
                                <span>{{ $item->created_at ? $item->created_at->format('d M Y') : '' }}</span>
                            </div>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="space-y-2 pt-2 border-t border-slate-100">
                            <!-- Direct Copy URL Button -->
                            <button type="button" 
                                    @click="copyUrl('{{ $item->url }}', {{ $item->id }})"
                                    class="w-full px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5"
                                    :class="copyFeedback[{{ $item->id }}] ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 hover:bg-slate-200 text-slate-700'">
                                <template x-if="!copyFeedback[{{ $item->id }}]">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                        <span>Copy File URL</span>
                                    </span>
                                </template>
                                <template x-if="copyFeedback[{{ $item->id }}]">
                                    <span class="flex items-center gap-1 font-bold text-emerald-700">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        <span>Copied to Clipboard!</span>
                                    </span>
                                </template>
                            </button>

                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ $item->url }}" 
                                   target="_blank" 
                                   class="text-[11px] font-semibold text-slate-600 hover:text-brand-navy flex items-center gap-1">
                                    <span>Preview</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>

                                <form action="{{ route('admin.media.destroy', $item) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Permanently delete {{ addslashes($item->original_name) }} from server?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-[11px] font-semibold text-red-500 hover:text-red-700 transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($media->hasPages())
            <div class="pt-4">
                {{ $media->links() }}
            </div>
        @endif
    @else
        <div class="p-12 bg-white rounded-2xl border border-slate-200 text-center space-y-3">
            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <h3 class="text-sm font-bold text-slate-800">No media files found</h3>
            <p class="text-xs text-slate-500">Upload your first feature image or document using the form above.</p>
        </div>
    @endif
</div>
@endsection
