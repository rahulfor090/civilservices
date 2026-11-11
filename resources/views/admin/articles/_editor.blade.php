@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    /* Quill Custom Admin Styling */
    .ql-toolbar.ql-snow {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        border-color: #cbd5e1;
        background: #f8fafc;
        padding: 0.6rem 0.75rem;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        border-color: #cbd5e1;
        font-family: 'Inter', system-ui, sans-serif;
        font-size: 0.925rem;
        background: #ffffff;
    }
    .ql-editor {
        min-height: 400px;
        max-height: 720px;
        overflow-y: auto;
        line-height: 1.7;
        color: #1e293b;
    }
    .ql-editor h1 { font-size: 1.75rem; font-weight: 700; color: #0b1b3d; margin-top: 1.25rem; margin-bottom: 0.5rem; }
    .ql-editor h2 { font-size: 1.4rem; font-weight: 700; color: #0b1b3d; margin-top: 1.1rem; margin-bottom: 0.5rem; }
    .ql-editor h3 { font-size: 1.2rem; font-weight: 600; color: #1e293b; margin-top: 0.9rem; margin-bottom: 0.4rem; }
    .ql-editor h4 { font-size: 1.05rem; font-weight: 600; color: #334155; margin-top: 0.75rem; margin-bottom: 0.3rem; }
    .ql-editor p { margin-bottom: 0.75rem; }
    .ql-editor ul, .ql-editor ol { padding-left: 1.5rem; margin-bottom: 0.75rem; }
    .ql-editor blockquote {
        border-left: 4px solid #e65100;
        padding-left: 1rem;
        font-style: italic;
        color: #475569;
        margin: 1rem 0;
        background: #fff7ed;
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
    }
    .ql-editor pre.ql-syntax {
        background: #0f172a;
        color: #38bdf8;
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.825rem;
    }
    /* Toolbar Button Hover & Active Enhancement */
    .ql-snow .ql-stroke { stroke: #475569; }
    .ql-snow .ql-fill { fill: #475569; }
    .ql-snow .ql-picker { color: #475569; font-weight: 500; }
    .ql-snow.ql-toolbar button:hover .ql-stroke,
    .ql-snow .ql-toolbar button:hover .ql-stroke,
    .ql-snow.ql-toolbar button.ql-active .ql-stroke,
    .ql-snow .ql-toolbar button.ql-active .ql-stroke {
        stroke: #e65100 !important;
    }
    .ql-snow.ql-toolbar button:hover .ql-fill,
    .ql-snow .ql-toolbar button:hover .ql-fill,
    .ql-snow.ql-toolbar button.ql-active .ql-fill,
    .ql-snow .ql-toolbar button.ql-active .ql-fill {
        fill: #e65100 !important;
    }
    .ql-snow .ql-picker:hover,
    .ql-snow .ql-picker.ql-expanded .ql-picker-label {
        color: #e65100 !important;
    }
    .ql-snow .ql-picker-options {
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border: 1px solid #e2e8f0 !important;
    }
</style>
@endpush

@php
    $inputName = $inputName ?? 'body_html';
    $inputLabel = $inputLabel ?? 'Article Body Content (HTML / Structured Sections)';
    $editorRequired = $editorRequired ?? true;
@endphp

<div class="space-y-2">
    <!-- Top Header: Title, Mode Indicator & Actions -->
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                {{ $inputLabel }} @if($editorRequired)<span class="text-red-500">*</span>@endif
            </label>
            <span id="editor-mode-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Visual WYSIWYG
            </span>
        </div>

        <div class="flex items-center gap-2">
            <!-- Toggle HTML Source Code Button -->
            <button type="button" 
                    id="btn-toggle-html-source" 
                    onclick="toggleHtmlSourceMode()" 
                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 flex items-center gap-1.5 transition-colors"
                    title="Toggle between Visual formatting and Raw HTML Source">
                <svg class="w-3.5 h-3.5 text-brand-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                </svg>
                <span id="btn-toggle-text">Raw HTML Source</span>
            </button>
        </div>
    </div>

    <!-- Quick UPSC / Editorial Section Insert Presets -->
    <div class="flex flex-wrap items-center gap-1.5 p-2 bg-slate-100/90 rounded-xl border border-slate-200 text-xs">
        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mr-1 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 text-brand-orange" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/></svg>
            Quick Insert:
        </span>
        <button type="button" onclick="insertSectionTemplate('why_in_news')" class="px-2 py-0.5 rounded-md bg-white hover:bg-amber-50 hover:border-amber-300 border border-slate-200 text-slate-700 text-[11px] font-semibold transition-all shadow-2xs">
            📌 Why in News?
        </button>
        <button type="button" onclick="insertSectionTemplate('key_points')" class="px-2 py-0.5 rounded-md bg-white hover:bg-amber-50 hover:border-amber-300 border border-slate-200 text-slate-700 text-[11px] font-semibold transition-all shadow-2xs">
            💡 Key Points
        </button>
        <button type="button" onclick="insertSectionTemplate('constitutional_provisions')" class="px-2 py-0.5 rounded-md bg-white hover:bg-amber-50 hover:border-amber-300 border border-slate-200 text-slate-700 text-[11px] font-semibold transition-all shadow-2xs">
            ⚖️ Constitutional Provisions
        </button>
        <button type="button" onclick="insertSectionTemplate('way_forward')" class="px-2 py-0.5 rounded-md bg-white hover:bg-amber-50 hover:border-amber-300 border border-slate-200 text-slate-700 text-[11px] font-semibold transition-all shadow-2xs">
            🚀 Way Forward
        </button>
        <button type="button" onclick="insertSectionTemplate('mains_question')" class="px-2 py-0.5 rounded-md bg-white hover:bg-amber-50 hover:border-amber-300 border border-slate-200 text-slate-700 text-[11px] font-semibold transition-all shadow-2xs">
            📝 Mains Practice Question Box
        </button>
        <button type="button" onclick="insertSectionTemplate('callout_box')" class="px-2 py-0.5 rounded-md bg-white hover:bg-amber-50 hover:border-amber-300 border border-slate-200 text-slate-700 text-[11px] font-semibold transition-all shadow-2xs">
            ℹ️ Highlight Note Box
        </button>
    </div>

    <!-- Editor Wrapper -->
    <div class="relative shadow-2xs rounded-xl">
        <!-- Visual Quill Toolbar with Full Rich HTML Controls -->
        <div id="quill-toolbar" class="border border-slate-300 rounded-t-xl bg-slate-50">
            <!-- Typography Headings -->
            <span class="ql-formats">
                <select class="ql-header" title="Heading Level">
                    <option value="1">Heading 1 (H1)</option>
                    <option value="2" selected>Heading 2 (H2)</option>
                    <option value="3">Heading 3 (H3)</option>
                    <option value="4">Heading 4 (H4)</option>
                    <option value="">Normal Text</option>
                </select>
            </span>

            <!-- Inline Styles: Bold, Italic, Underline, Strike -->
            <span class="ql-formats">
                <button class="ql-bold" title="Bold (Ctrl+B)"></button>
                <button class="ql-italic" title="Italic (Ctrl+I)"></button>
                <button class="ql-underline" title="Underline (Ctrl+U)"></button>
                <button class="ql-strike" title="Strikethrough"></button>
            </span>

            <!-- Colors: Text Color & Background Highlight -->
            <span class="ql-formats">
                <!-- Text Color Palette -->
                <select class="ql-color" title="Text Color">
                    <option value="#000000" selected>Black</option>
                    <option value="#0b1b3d">Brand Navy (#0b1b3d)</option>
                    <option value="#e65100">Brand Orange (#e65100)</option>
                    <option value="#1e40af">Royal Blue</option>
                    <option value="#0284c7">Sky Blue</option>
                    <option value="#059669">Emerald Green</option>
                    <option value="#16a34a">Forest Green</option>
                    <option value="#dc2626">Crimson Red</option>
                    <option value="#7c3aed">Deep Purple</option>
                    <option value="#d97706">Amber Gold</option>
                    <option value="#475569">Slate Grey</option>
                    <option value="#ffffff">White</option>
                </select>

                <!-- Background / Highlight Color Palette -->
                <select class="ql-background" title="Highlight Background Color">
                    <option value="#ffffff" selected>No Highlight</option>
                    <option value="#fef08a">Yellow Highlight</option>
                    <option value="#fde68a">Amber Highlight</option>
                    <option value="#fed7aa">Orange Highlight</option>
                    <option value="#bbf7d0">Light Green Highlight</option>
                    <option value="#bae6fd">Light Blue Highlight</option>
                    <option value="#e9d5ff">Lavender Highlight</option>
                    <option value="#fecdd3">Rose Highlight</option>
                    <option value="#f1f5f9">Slate Highlight</option>
                </select>
            </span>

            <!-- Lists & Indentation -->
            <span class="ql-formats">
                <button class="ql-list" value="ordered" title="Numbered List"></button>
                <button class="ql-list" value="bullet" title="Bulleted List"></button>
                <button class="ql-indent" value="-1" title="Decrease Indent"></button>
                <button class="ql-indent" value="+1" title="Increase Indent"></button>
            </span>

            <!-- Alignment -->
            <span class="ql-formats">
                <select class="ql-align" title="Text Alignment">
                    <option selected title="Align Left"></option>
                    <option value="center" title="Align Center"></option>
                    <option value="right" title="Align Right"></option>
                    <option value="justify" title="Justify Full"></option>
                </select>
            </span>

            <!-- Rich Inserts: Link, Image, Quote, Code -->
            <span class="ql-formats">
                <button class="ql-link" title="Insert Link"></button>
                <button class="ql-image" title="Insert Image"></button>
                <button class="ql-blockquote" title="Quote Block"></button>
                <button class="ql-code-block" title="Code Snippet"></button>
            </span>

            <!-- Clean Formatting -->
            <span class="ql-formats">
                <button class="ql-clean" title="Clear Formatting"></button>
            </span>
        </div>

        <!-- Visual Quill Editor Container -->
        <div id="quill-editor" class="border border-t-0 border-slate-300 rounded-b-xl bg-white"></div>

        <!-- Raw HTML Source Editor Container (Hidden by default) -->
        <textarea id="raw-html-editor" 
                  class="hidden w-full h-[450px] p-4 text-xs font-mono bg-slate-950 text-emerald-400 border border-slate-800 rounded-b-xl focus:outline-none focus:ring-2 focus:ring-brand-orange leading-relaxed" 
                  spellcheck="false" 
                  placeholder="Paste or write raw HTML markup here..."></textarea>

        <!-- Hidden Sync Textarea (Submitted to Laravel Backend) -->
        <textarea name="{{ $inputName }}" id="body_html_real_input" class="hidden" {{ $editorRequired ? 'required' : '' }}>{{ $initialContent ?? '' }}</textarea>
    </div>

    <!-- Status Bar: Word & Character Counter + Live Feedback -->
    <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-500 pt-1 px-1">
        <div class="flex items-center gap-3">
            <span>Words: <strong id="editor-word-count" class="text-slate-700">0</strong></span>
            <span>&bull;</span>
            <span>Characters: <strong id="editor-char-count" class="text-slate-700">0</strong></span>
        </div>
        <div class="text-slate-400">
            Rich HTML Formatting Active &bull; Auto-synced to form submission
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toolbarOptions = '#quill-toolbar';
        const quillContainer = document.getElementById('quill-editor');
        const rawHtmlTextarea = document.getElementById('raw-html-editor');
        const realInput = document.getElementById('body_html_real_input');
        const badge = document.getElementById('editor-mode-badge');
        const toggleBtnText = document.getElementById('btn-toggle-text');
        const wordCountEl = document.getElementById('editor-word-count');
        const charCountEl = document.getElementById('editor-char-count');

        // Initialize Quill
        const quill = new Quill(quillContainer, {
            theme: 'snow',
            modules: {
                toolbar: toolbarOptions
            },
            placeholder: 'Write or paste your article body here... Use headings, bold, italic, custom text & highlight colors, structured sections, and lists.'
        });

        // Populate initial content
        const initialHtml = realInput.value || '';
        if (initialHtml.trim().length > 0) {
            quill.clipboard.dangerouslyPasteHTML(0, initialHtml);
        }

        // Update counters and hidden input
        function updateEditorStatsAndInput() {
            const html = quill.root.innerHTML;
            realInput.value = html;

            const text = quill.getText().trim();
            const words = text.length > 0 ? text.split(/\s+/).length : 0;
            const chars = text.length;

            if (wordCountEl) wordCountEl.textContent = words;
            if (charCountEl) charCountEl.textContent = chars;
        }

        quill.on('text-change', function () {
            updateEditorStatsAndInput();
        });

        // Run once on load
        updateEditorStatsAndInput();

        // Mode Switching: Visual WYSIWYG <-> Raw HTML Source
        let isRawHtmlMode = false;

        window.toggleHtmlSourceMode = function () {
            isRawHtmlMode = !isRawHtmlMode;

            if (isRawHtmlMode) {
                // Switch to Raw HTML Mode
                rawHtmlTextarea.value = quill.root.innerHTML;
                quillContainer.classList.add('hidden');
                document.getElementById('quill-toolbar').classList.add('hidden');
                rawHtmlTextarea.classList.remove('hidden');
                rawHtmlTextarea.focus();

                badge.textContent = 'Raw HTML Source';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200';
                toggleBtnText.textContent = 'Visual Editor';
            } else {
                // Switch back to Visual Mode
                const newHtml = rawHtmlTextarea.value;
                quill.root.innerHTML = newHtml;
                realInput.value = newHtml;

                rawHtmlTextarea.classList.add('hidden');
                document.getElementById('quill-toolbar').classList.remove('hidden');
                quillContainer.classList.remove('hidden');

                badge.textContent = 'Visual WYSIWYG';
                badge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
                toggleBtnText.textContent = 'Raw HTML Source';

                updateEditorStatsAndInput();
            }
        };

        // When typing in Raw HTML mode, sync with realInput
        rawHtmlTextarea.addEventListener('input', function () {
            realInput.value = rawHtmlTextarea.value;
            const text = rawHtmlTextarea.value.replace(/<[^>]*>/g, ' ').trim();
            const words = text.length > 0 ? text.split(/\s+/).length : 0;
            if (wordCountEl) wordCountEl.textContent = words;
            if (charCountEl) charCountEl.textContent = text.length;
        });

        // Quick Insert Templates
        window.insertSectionTemplate = function (type) {
            let templateHtml = '';

            if (type === 'why_in_news') {
                templateHtml = `<h2>Why in News?</h2><p>Provide contextual details regarding recent government announcements, reports, judicial rulings, or parliamentary debates...</p><p><br></p>`;
            } else if (type === 'key_points') {
                templateHtml = `<h2>Key Dimensions & Core Concepts</h2><ul><li><strong>Dimension 1:</strong> Detail explanation and empirical evidence.</li><li><strong>Dimension 2:</strong> Constitutional or socio-economic impact.</li><li><strong>Dimension 3:</strong> International benchmarks or best practices.</li></ul><p><br></p>`;
            } else if (type === 'constitutional_provisions') {
                templateHtml = `<h2>Constitutional & Legal Provisions</h2><ul><li><strong>Article X:</strong> Guarantees the fundamental right to...</li><li><strong>Statutory Framework:</strong> Enacted under the provisions of the Act...</li><li><strong>Judicial Benchmark:</strong> In <em>Landmark Case Name</em>, the Supreme Court held that...</li></ul><p><br></p>`;
            } else if (type === 'way_forward') {
                templateHtml = `<h2>Way Forward</h2><ol><li><strong>Institutional Reforms:</strong> Modernize governance mechanisms and transparency safeguards.</li><li><strong>Stakeholder Consultations:</strong> Broaden federal participation with state authorities and civil society.</li><li><strong>Capacity Building:</strong> Reinforce grassroot delivery mechanisms and technological monitoring.</li></ol><p><br></p>`;
            } else if (type === 'mains_question') {
                templateHtml = `<div style="background-color: #f8fafc; border-left: 4px solid #e65100; padding: 14px 18px; margin: 16px 0; border-radius: 6px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"><strong style="color: #0b1b3d; font-size: 14px;">Mains Practice Question:</strong><p style="margin-top: 8px; margin-bottom: 0; color: #334155; font-size: 13.5px; line-height: 1.6;"><em>"Critically examine the balance between national security priorities and individual civil liberties in light of recent constitutional jurisprudence." (250 Words, 15 Marks)</em></p></div><p><br></p>`;
            } else if (type === 'callout_box') {
                templateHtml = `<div style="background-color: #f0fdf4; border-left: 4px solid #16a34a; padding: 12px 16px; margin: 14px 0; border-radius: 6px; color: #166534;"><strong style="display: block; margin-bottom: 4px; color: #14532d;">Exam Key Takeaway:</strong>Keep in mind the critical distinction between Article 32 and Article 226 regarding the territorial jurisdiction of writ remedies.</div><p><br></p>`;
            }

            if (isRawHtmlMode) {
                // In Raw HTML mode, append or insert into textarea
                const pos = rawHtmlTextarea.selectionStart || rawHtmlTextarea.value.length;
                const before = rawHtmlTextarea.value.substring(0, pos);
                const after = rawHtmlTextarea.value.substring(pos);
                rawHtmlTextarea.value = before + templateHtml + after;
                realInput.value = rawHtmlTextarea.value;
            } else {
                // In Visual mode, insert into Quill
                const range = quill.getSelection(true);
                const index = range ? range.index : quill.getLength();
                quill.clipboard.dangerouslyPasteHTML(index, templateHtml);
                quill.setSelection(index + templateHtml.length);
                updateEditorStatsAndInput();
            }
        };

        // Form Submit Handler: Guarantee sync before submission
        const form = realInput.closest('form');
        if (form) {
            form.addEventListener('submit', function (e) {
                if (isRawHtmlMode) {
                    realInput.value = rawHtmlTextarea.value;
                } else {
                    realInput.value = quill.root.innerHTML;
                }

                // If empty or only empty paragraph tags, prevent submitting blank content
                const cleanText = realInput.value.replace(/<[^>]*>/g, '').trim();
                if (cleanText.length === 0 && !realInput.value.includes('<img')) {
                    alert('Please enter article body content before submitting.');
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endpush
