<div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4"
     x-data="{
         imageUrl: '{{ $initialUrl ?? '' }}',
         mediaId: '{{ $initialMediaId ?? '' }}',
         removeImage: false,
         modalOpen: false,
         loadingMedia: false,
         mediaSearch: '',
         mediaItems: [],
         activeTab: 'upload', // 'upload', 'library', 'url'

         handleLocalFile(e) {
             const file = e.target.files[0];
             if (file) {
                 this.imageUrl = URL.createObjectURL(file);
                 this.removeImage = false;
                 this.mediaId = '';
             }
         },

         clearImage() {
             this.imageUrl = '';
             this.mediaId = '';
             this.removeImage = true;
             if ($refs.fileInput) {
                 $refs.fileInput.value = '';
             }
         },

         openLibraryModal() {
             this.modalOpen = true;
             if (this.mediaItems.length === 0) {
                 this.fetchMedia();
             }
         },

         fetchMedia() {
             this.loadingMedia = true;
             let url = '{{ route('admin.media.picker') }}?images_only=1';
             if (this.mediaSearch) {
                 url += '&search=' + encodeURIComponent(this.mediaSearch);
             }
             fetch(url)
                 .then(res => res.json())
                 .then(data => {
                     this.mediaItems = data.data || [];
                     this.loadingMedia = false;
                 })
                 .catch(() => {
                     this.loadingMedia = false;
                 });
         },

         selectMediaItem(item) {
             this.imageUrl = item.url;
             this.mediaId = item.id;
             this.removeImage = false;
             this.modalOpen = false;
             if ($refs.fileInput) {
                 $refs.fileInput.value = '';
             }
         }
     }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/70 pb-3">
        <div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Feature Image & Social Share Thumbnail (WhatsApp / OG Image)</span>
                </label>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">
                    Primary Thumbnail
                </span>
            </div>
            <p class="text-[11px] text-slate-500 mt-0.5">
                This image will appear <strong>first as the prominent preview</strong> when this link is shared on <strong>WhatsApp, Facebook, Telegram, and X (Twitter)</strong>.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" 
                    @click="openLibraryModal()" 
                    class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 hover:border-brand-navy text-slate-700 text-xs font-semibold shadow-2xs transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Browse Media Library</span>
            </button>
            <a href="{{ route('admin.media.index') }}" target="_blank" class="text-xs font-semibold text-brand-navy hover:underline flex items-center gap-0.5">
                <span>Manage Files</span>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </div>

    <!-- Hidden form values -->
    <input type="hidden" name="hero_media_id" :value="mediaId">
    <input type="hidden" name="remove_featured_image" :value="removeImage ? '1' : '0'">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left: Upload / URL Controls (7 cols) -->
        <div class="lg:col-span-7 space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs">
                <button type="button" 
                        @click="activeTab = 'upload'"
                        :class="activeTab === 'upload' ? 'font-bold text-brand-orange border-b-2 border-brand-orange -mb-2 pb-1.5' : 'text-slate-500 hover:text-slate-800'">
                    Upload New Image File
                </button>
                <span class="text-slate-300">|</span>
                <button type="button" 
                        @click="activeTab = 'url'"
                        :class="activeTab === 'url' ? 'font-bold text-brand-orange border-b-2 border-brand-orange -mb-2 pb-1.5' : 'text-slate-500 hover:text-slate-800'">
                    Paste Direct Image URL
                </button>
            </div>

            <!-- Tab 1: File Upload -->
            <div x-show="activeTab === 'upload'" class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase">
                    Choose Image File (JPG, PNG, WEBP &bull; Max 15MB)
                </label>
                <input type="file" 
                       x-ref="fileInput"
                       name="featured_image_file" 
                       accept="image/*"
                       @change="handleLocalFile($event)"
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-navy file:text-white hover:file:bg-brand-navyLight file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                <p class="text-[10px] text-slate-400">
                    Uploaded file will be automatically added to the Master Media Manager and linked to this article.
                </p>
            </div>

            <!-- Tab 2: Direct URL -->
            <div x-show="activeTab === 'url'" class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase">
                    Image Public Web URL
                </label>
                <input type="url" 
                       name="featured_image" 
                       x-model="imageUrl"
                       @input="removeImage = false; mediaId = '';"
                       placeholder="https://example.com/images/feature-thumb.jpg"
                       class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                <p class="text-[10px] text-slate-400">
                    Supports any full HTTPS image URL (CDN, Cloudinary, Imgur, or previously uploaded media URL).
                </p>
            </div>

            <!-- Quick Specs Tip -->
            <div class="p-3 bg-white rounded-xl border border-slate-200/80 text-[11px] text-slate-600 space-y-1">
                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Recommended Size for WhatsApp & Social Media:</span>
                </div>
                <p class="text-slate-500">
                    <strong>1200 &times; 630 pixels</strong> (or 16:9 aspect ratio). This guarantees sharp, edge-to-edge preview cards on WhatsApp chats, Facebook newsfeed, and X (Twitter) cards.
                </p>
            </div>
        </div>

        <!-- Right: Live WhatsApp & Social Preview Mockup (5 cols) -->
        <div class="lg:col-span-5 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase text-slate-600 tracking-wider">
                    WhatsApp & Social Card Preview
                </span>
                <template x-if="imageUrl">
                    <button type="button" 
                            @click="clearImage()" 
                            class="text-[11px] font-bold text-red-600 hover:text-red-700 underline">
                        Remove Image
                    </button>
                </template>
            </div>

            <!-- Social Card Mockup -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-md overflow-hidden max-w-sm mx-auto">
                <!-- Image Box -->
                <div class="relative bg-slate-900 aspect-video flex items-center justify-center overflow-hidden">
                    <template x-if="imageUrl">
                        <img :src="imageUrl" 
                             class="w-full h-full object-cover" 
                             alt="Feature preview">
                    </template>
                    <template x-if="!imageUrl">
                        <div class="text-center p-4 text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-[10px] uppercase font-bold tracking-wider">No Image Selected</span>
                            <p class="text-[9px] text-slate-500 mt-0.5">Upload a file or choose from Media Library</p>
                        </div>
                    </template>

                    <span class="absolute bottom-1.5 right-1.5 px-1.5 py-0.5 rounded bg-black/70 text-white text-[9px] font-mono">
                        og:image
                    </span>
                </div>

                <!-- WhatsApp Card Footer Preview -->
                <div class="p-3 bg-slate-50 border-t border-slate-100 space-y-1">
                    <div class="text-[10px] font-mono uppercase text-slate-400 font-bold">
                        civilservicegurukul.com
                    </div>
                    <div class="text-xs font-bold text-slate-900 line-clamp-1">
                        {{ $articleTitle ?? 'Article Title Preview for WhatsApp' }}
                    </div>
                    <div class="text-[10px] text-slate-500 line-clamp-2 leading-relaxed">
                        India’s premier civil services education portal for UPSC IAS, UPPCS, BPSC, and State PCS exam preparation.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media Library Picker Modal -->
    <div x-show="modalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="modalOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-4xl p-6 space-y-4"
                 @click.away="modalOpen = false">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Select Image from Master Media Library</h3>
                        <p class="text-xs text-slate-500">Choose any previously uploaded image to set as this article's feature thumbnail.</p>
                    </div>
                    <button type="button" @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Search inside modal -->
                <div class="flex items-center gap-2">
                    <input type="text" 
                           x-model="mediaSearch" 
                           @input.debounce.300ms="fetchMedia()"
                           placeholder="Search media files by filename..." 
                           class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <button type="button" @click="fetchMedia()" class="px-4 py-2 rounded-xl bg-brand-navy text-white text-xs font-semibold">
                        Refresh
                    </button>
                </div>

                <!-- Images Grid -->
                <div class="max-h-96 overflow-y-auto">
                    <div x-show="loadingMedia" class="p-8 text-center text-xs text-slate-400">
                        Loading media files...
                    </div>

                    <div x-show="!loadingMedia && mediaItems.length === 0" class="p-8 text-center text-xs text-slate-400">
                        No image files found in the media library.
                    </div>

                    <div x-show="!loadingMedia && mediaItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        <template x-for="item in mediaItems" :key="item.id">
                            <div class="group relative rounded-xl border border-slate-200 bg-slate-50 overflow-hidden cursor-pointer hover:border-brand-orange hover:shadow-md transition-all"
                                 @click="selectMediaItem(item)">
                                <div class="aspect-video bg-slate-200 overflow-hidden flex items-center justify-center">
                                    <img :src="item.url" :alt="item.original_name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                                </div>
                                <div class="p-2">
                                    <div class="text-[11px] font-bold text-slate-800 truncate" x-text="item.original_name"></div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 font-mono mt-0.5">
                                        <span x-text="item.size_human"></span>
                                        <span x-text="item.width ? item.width + 'x' + item.height : ''"></span>
                                    </div>
                                </div>
                                <div class="absolute inset-0 bg-brand-navy/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold">
                                    Use as Feature Image
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                    <a href="{{ route('admin.media.index') }}" target="_blank" class="text-brand-orange hover:underline font-semibold flex items-center gap-1">
                        <span>Go to Media Manager to upload new files</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <button type="button" @click="modalOpen = false" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
