<div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4"
     x-data="{
         pdfUrl: '{{ $initialPdfUrl ?? '' }}',
         pdfMediaId: '{{ $initialPdfMediaId ?? '' }}',
         removePdf: false,
         modalOpen: false,
         loadingMedia: false,
         mediaSearch: '',
         mediaItems: [],
         activeTab: 'upload', // 'upload', 'url'

         handleLocalFile(e) {
             const file = e.target.files[0];
             if (file) {
                 this.pdfUrl = file.name;
                 this.removePdf = false;
                 this.pdfMediaId = '';
             }
         },

         clearPdf() {
             this.pdfUrl = '';
             this.pdfMediaId = '';
             this.removePdf = true;
             if ($refs.pdfFileInput) {
                 $refs.pdfFileInput.value = '';
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
             let url = '{{ route('admin.media.picker') }}';
             if (this.mediaSearch) {
                 url += '?search=' + encodeURIComponent(this.mediaSearch);
             }
             fetch(url)
                 .then(res => res.json())
                 .then(data => {
                     // Filter or display all files
                     this.mediaItems = (data.data || []).filter(item => item.extension === 'pdf' || item.mime_type === 'application/pdf');
                     this.loadingMedia = false;
                 })
                 .catch(() => {
                     this.loadingMedia = false;
                 });
         },

         selectMediaItem(item) {
             this.pdfUrl = item.url;
             this.pdfMediaId = item.id;
             this.removePdf = false;
             this.modalOpen = false;
             if ($refs.pdfFileInput) {
                 $refs.pdfFileInput.value = '';
             }
         }
     }">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-200/70 pb-3">
        <div>
            <div class="flex items-center gap-2">
                <label class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Article PDF Download & Print Configuration</span>
                </label>
                <template x-if="pdfUrl">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-900 flex items-center gap-1">
                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span>Official PDF Attached</span>
                    </span>
                </template>
                <template x-if="!pdfUrl">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-900">
                        Dynamic Watermark Generator Active
                    </span>
                </template>
            </div>
            <p class="text-[11px] text-slate-500 mt-0.5">
                If an official PDF is uploaded, students clicking <strong>"Print / Download PDF"</strong> will receive this document. If no PDF is uploaded, the platform will <strong>automatically generate a printable PDF with the Civil Services Gurukul watermark</strong> from the article text and practice questions.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button" 
                    @click="openLibraryModal()" 
                    class="px-3 py-1.5 rounded-xl bg-white border border-slate-300 hover:border-brand-navy text-slate-700 text-xs font-semibold shadow-2xs transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Browse PDFs in Media</span>
            </button>
        </div>
    </div>

    <!-- Hidden form values -->
    <input type="hidden" name="pdf_media_id" :value="pdfMediaId">
    <input type="hidden" name="remove_uploaded_pdf" :value="removePdf ? '1' : '0'">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <!-- Left: Upload / URL Controls (7 cols) -->
        <div class="lg:col-span-7 space-y-3">
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs">
                <button type="button" 
                        @click="activeTab = 'upload'"
                        :class="activeTab === 'upload' ? 'font-bold text-red-600 border-b-2 border-red-600 -mb-2 pb-1.5' : 'text-slate-500 hover:text-slate-800'">
                    Upload Official PDF File
                </button>
                <span class="text-slate-300">|</span>
                <button type="button" 
                        @click="activeTab = 'url'"
                        :class="activeTab === 'url' ? 'font-bold text-red-600 border-b-2 border-red-600 -mb-2 pb-1.5' : 'text-slate-500 hover:text-slate-800'">
                    Provide External PDF URL
                </button>
            </div>

            <!-- Tab 1: File Upload -->
            <div x-show="activeTab === 'upload'" class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase">
                    Choose PDF Document (PDF &bull; Max 50MB)
                </label>
                <input type="file" 
                       x-ref="pdfFileInput"
                       name="pdf_file" 
                       accept="application/pdf"
                       @change="handleLocalFile($event)"
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-red-700 file:text-white hover:file:bg-red-800 file:cursor-pointer cursor-pointer border border-slate-200 rounded-xl bg-white p-1">
                <p class="text-[10px] text-slate-400">
                    Uploaded PDF will be stored securely on the public disk and linked to this article's download button.
                </p>
            </div>

            <!-- Tab 2: Direct URL -->
            <div x-show="activeTab === 'url'" class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-700 uppercase">
                    PDF Public Web URL
                </label>
                <input type="url" 
                       name="pdf_path" 
                       x-model="pdfUrl"
                       @input="removePdf = false; pdfMediaId = '';"
                       placeholder="https://example.com/downloads/current-affairs-analysis.pdf"
                       class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy font-mono">
                <p class="text-[10px] text-slate-400">
                    Supports any full HTTPS link to an official compilation, Gazette, or PIB release PDF.
                </p>
            </div>
        </div>

        <!-- Right: Status / Preview Card (5 cols) -->
        <div class="lg:col-span-5 space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase text-slate-600 tracking-wider">
                    Current Document Status
                </span>
                <template x-if="pdfUrl">
                    <button type="button" 
                            @click="clearPdf()" 
                            class="text-[11px] font-bold text-red-600 hover:text-red-700 underline">
                        Remove Uploaded PDF
                    </button>
                </template>
            </div>

            <!-- Card Box -->
            <div class="p-4 rounded-xl border bg-white shadow-2xs space-y-2.5"
                 :class="pdfUrl ? 'border-emerald-300' : 'border-blue-200 bg-blue-50/40'">
                <template x-if="pdfUrl">
                    <div class="space-y-2">
                        <div class="flex items-start gap-2.5">
                            <div class="p-2 rounded-lg bg-red-100 text-red-700 shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] uppercase font-bold text-emerald-700 tracking-wider block">Official Document Attached</span>
                                <div class="text-xs font-semibold text-slate-900 truncate" x-text="pdfUrl"></div>
                                <p class="text-[10px] text-slate-500 mt-0.5">This file will be directly delivered to readers when clicking "Download PDF".</p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px]">
                            <template x-if="pdfUrl.startsWith('http') || pdfUrl.startsWith('/')">
                                <a :href="pdfUrl" target="_blank" class="text-brand-orange font-bold hover:underline flex items-center gap-1">
                                    <span>Preview Uploaded PDF</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </template>
                            <span class="text-slate-400 text-[10px]">Overrides auto-generator</span>
                        </div>
                    </div>
                </template>

                <template x-if="!pdfUrl">
                    <div class="space-y-1.5 text-xs text-slate-700">
                        <div class="flex items-center gap-2 text-blue-900 font-bold text-xs">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            <span>Auto-PDF Engine: Watermark Included</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            No manual PDF attached. When students click <strong>"Download PDF"</strong> or <strong>"Print"</strong>, the system will dynamically synthesize an A4 PDF document containing:
                        </p>
                        <ul class="text-[10px] text-slate-500 list-disc list-inside space-y-0.5">
                            <li>Diagonal <strong>"CIVIL SERVICES GURUKUL"</strong> watermark</li>
                            <li>Official header, date, section, and reading time</li>
                            <li>Clean article body and anchored questions</li>
                            <li>Associated Prelims MCQ practice questions and explanations</li>
                        </ul>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Media Library Picker Modal (for PDFs) -->
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
                        <h3 class="text-base font-bold text-slate-900">Select PDF from Media Manager</h3>
                        <p class="text-xs text-slate-500">Pick any PDF document previously uploaded to the platform.</p>
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
                           placeholder="Search PDF files by name..." 
                           class="flex-1 px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-navy">
                    <button type="button" @click="fetchMedia()" class="px-4 py-2 rounded-xl bg-brand-navy text-white text-xs font-semibold">
                        Refresh
                    </button>
                </div>

                <!-- PDF Items Grid -->
                <div class="max-h-96 overflow-y-auto">
                    <div x-show="loadingMedia" class="p-8 text-center text-xs text-slate-400">
                        Loading files...
                    </div>

                    <div x-show="!loadingMedia && mediaItems.length === 0" class="p-8 text-center text-xs text-slate-400">
                        No PDF documents found in media library. You can upload directly via the form.
                    </div>

                    <div x-show="!loadingMedia && mediaItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        <template x-for="item in mediaItems" :key="item.id">
                            <div class="group relative rounded-xl border border-slate-200 bg-slate-50 p-3 cursor-pointer hover:border-red-500 hover:shadow-md transition-all flex items-start gap-3"
                                 @click="selectMediaItem(item)">
                                <div class="p-2.5 rounded-lg bg-red-100 text-red-700 shrink-0">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs font-bold text-slate-800 truncate" x-text="item.original_name"></div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="item.size_human"></div>
                                    <span class="inline-block mt-1 text-[10px] font-bold text-red-700 group-hover:underline">Use this PDF &rarr;</span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                    <button type="button" @click="modalOpen = false" class="px-4 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold ml-auto">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
