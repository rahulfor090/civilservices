<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $article->title }} - Civil Service Gurukul</title>
    <style>
        @page {
            margin: 24mm 16mm 18mm 16mm;
        }

        body {
            font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;
            font-size: 9.8pt;
            line-height: 1.58;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Running Header on every page */
        header {
            position: fixed;
            top: -18mm;
            left: 0;
            right: 0;
            height: 13mm;
            border-bottom: 1.8pt solid #d97706;
            padding-bottom: 3px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-brand {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-tagline {
            font-size: 7.2pt;
            color: #64748b;
            letter-spacing: 0.2px;
        }

        .header-right {
            text-align: right;
            font-size: 8pt;
            font-weight: bold;
            color: #b45309;
        }

        .header-domain {
            font-size: 7.2pt;
            color: #64748b;
            font-weight: normal;
        }

        /* Running Footer on every page */
        footer {
            position: fixed;
            bottom: -13mm;
            left: 0;
            right: 0;
            height: 9mm;
            border-top: 1pt solid #cbd5e1;
            padding-top: 3px;
            font-size: 8pt;
            color: #64748b;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Page 1 Masthead & Badges */
        .category-pill {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            border: 1pt solid #fde68a;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        h1.article-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            line-height: 1.35;
            margin: 4px 0 10px 0;
        }

        .meta-card {
            background-color: #f8fafc;
            border: 1pt solid #e2e8f0;
            border-radius: 6px;
            padding: 6px 12px;
            margin-bottom: 12px;
            font-size: 8.5pt;
            color: #475569;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 0;
        }

        /* Excerpt Callout */
        .excerpt-box {
            background-color: #fffbeb;
            border-left: 3.5pt solid #d97706;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 14px;
            font-size: 9.5pt;
            font-style: italic;
            color: #78350f;
            line-height: 1.5;
        }

        /* Headings & Structure */
        h2 {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            border-left: 3.5pt solid #d97706;
            padding-left: 7px;
            margin-top: 16px;
            margin-bottom: 7px;
            page-break-after: avoid;
        }

        h3 {
            font-size: 10.5pt;
            font-weight: bold;
            color: #1e293b;
            margin-top: 12px;
            margin-bottom: 5px;
            page-break-after: avoid;
        }

        p {
            margin: 0 0 9px 0;
            text-align: justify;
        }

        ul, ol {
            margin: 0 0 10px 0;
            padding-left: 18px;
        }

        li {
            margin-bottom: 3px;
        }

        /* Force normal flow for any imported user HTML */
        .body-content * {
            position: static !important;
            float: none !important;
        }

        /* Callout / Note Boxes inside body */
        .callout-box, .highlight-box, blockquote {
            page-break-inside: avoid;
            background-color: #f8fafc;
            border: 1pt solid #e2e8f0;
            border-left: 3.5pt solid #3b82f6;
            border-radius: 4px;
            padding: 8px 12px;
            margin: 10px 0;
            font-size: 9pt;
            color: #1e293b;
        }

        /* Tables */
        table.content-table, .body-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 8.5pt;
            page-break-inside: auto;
        }

        table.content-table thead, .body-content table thead {
            display: table-header-group;
        }

        table.content-table tr, .body-content table tr {
            page-break-inside: avoid;
        }

        table.content-table th, .body-content table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            border: 1pt solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }

        table.content-table td, .body-content table td {
            border: 1pt solid #e2e8f0;
            padding: 5px 8px;
            vertical-align: top;
        }

        table.content-table tr:nth-child(even) td, .body-content table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Prelims MCQs / Practice Questions */
        .quiz-section {
            page-break-inside: avoid;
            background-color: #fafaf9;
            border: 1pt solid #e7e5e4;
            border-radius: 6px;
            padding: 10px 12px;
            margin-top: 16px;
            margin-bottom: 12px;
        }

        .quiz-header {
            font-size: 10.5pt;
            font-weight: bold;
            color: #92400e;
            text-transform: uppercase;
            border-bottom: 1pt solid #f59e0b;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        .question-block {
            page-break-inside: avoid;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 0.5pt solid #e7e5e4;
        }

        .question-stem {
            font-weight: bold;
            font-size: 9pt;
            color: #1c1917;
            margin-bottom: 4px;
        }

        .question-option {
            font-size: 8.5pt;
            color: #44403c;
            padding: 2px 0 2px 10px;
        }

        .question-answer-box {
            background-color: #f0fdf4;
            border-left: 2.5pt solid #16a34a;
            border-radius: 3px;
            padding: 4px 8px;
            margin-top: 5px;
            font-size: 8pt;
            color: #14532d;
        }

        /* Closing Institute Card */
        .closing-brand-card {
            page-break-inside: avoid;
            background-color: #0f172a;
            color: #ffffff;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 18px;
            margin-bottom: 8px;
            text-align: center;
        }

        .closing-title {
            font-size: 11pt;
            font-weight: bold;
            color: #f59e0b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .closing-subtitle {
            font-size: 8pt;
            color: #94a3b8;
            margin: 2px 0 6px 0;
        }

        .closing-contacts {
            font-size: 7.5pt;
            color: #cbd5e1;
        }
    </style>
</head>
<body>

    <!-- Running Header on Every Page -->
    <header>
        <table class="header-table">
            <tr>
                <td>
                    <div class="header-brand">Civil Service Gurukul</div>
                    <div class="header-tagline">Excellence in Civil Services & State PCS Preparation</div>
                </td>
                <td class="header-right">
                    <div>{{ $article->contentType?->name ?? 'Daily News Analysis' }}</div>
                    <div class="header-domain">www.civilservicegurukul.com</div>
                </td>
            </tr>
        </table>
    </header>

    <!-- Running Footer on Every Page -->
    <footer>
        <table class="footer-table">
            <tr>
                <td style="width: 50%;">
                    Civil Service Gurukul &bull; All Rights Reserved
                </td>
                <td style="width: 50%; text-align: right;">
                    {{ $article->published_at ? $article->published_at->format('d M Y') : now()->format('d M Y') }} &bull;
                </td>
            </tr>
        </table>
    </footer>

    <!-- Main Content Area -->
    <main>
        <!-- Category Pill -->
        <div class="category-pill">
            {{ $article->section ? ($article->section->translation()?->name ?? 'General Studies') : ($article->contentType?->name ?? 'Current Affairs') }}
        </div>

        <!-- Article Title -->
        <h1 class="article-title">{{ $article->title }}</h1>

        <!-- Metadata Strip -->
        <div class="meta-card">
            <table class="meta-table">
                <tr>
                    <td>
                        <strong>Faculty/Author:</strong> {{ $article->author?->name ?? 'Faculty Editorial Board' }}
                    </td>
                    <td style="text-align: right;">
                        <strong>Date:</strong> {{ $article->published_at ? $article->published_at->format('d F Y') : now()->format('d F Y') }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Platform:</strong> Civil Service Gurukul Research Wing
                    </td>
                    <td style="text-align: right;">
                        <strong>Reading Time:</strong> {{ $article->reading_minutes ?? 5 }} Mins
                    </td>
                </tr>
            </table>
        </div>

        <!-- Excerpt Callout -->
        @if($article->excerpt)
            <div class="excerpt-box">
                {{ $article->excerpt }}
            </div>
        @endif

        <!-- Main Body / Structured Sections -->
        <div class="body-content">
            @if($article->sections->count() > 0)
                @foreach($article->sections as $sec)
                    <h2>{{ $sec->heading }}</h2>
                    <div>{!! $sec->body_html !!}</div>
                @endforeach
            @else
                <div>{!! $article->body_html !!}</div>
            @endif
        </div>

        <!-- Practice Questions / Quiz (if attached) -->
        @if($article->quiz && $article->quiz->questions->count() > 0)
            <div class="quiz-section">
                <div class="quiz-header">
                    Target UPSC Prelims — Practice Questions
                </div>
                <div style="font-size: 8pt; color: #78716c; margin-bottom: 6px;">
                    Test Title: {{ $article->quiz->title }} &bull; Total Marks: {{ $article->quiz->total_marks }}
                </div>

                @foreach($article->quiz->questions as $idx => $q)
                    <div class="question-block">
                        <div class="question-stem">
                            Q{{ $idx + 1 }}. {!! strip_tags($q->stem, '<b><strong><i><em><br>') !!}
                        </div>
                        @foreach($q->options as $optIdx => $opt)
                            <div class="question-option">
                                ({{ chr(65 + $optIdx) }}) {{ $opt->option_text }}
                            </div>
                        @endforeach
                        <div class="question-answer-box">
                            @php
                                $correctOpt = $q->options->where('is_correct', true)->first();
                                $correctLetter = $correctOpt ? chr(65 + $q->options->search($correctOpt)) : 'N/A';
                            @endphp
                            <strong>Correct Answer: ({{ $correctLetter }})</strong>
                            @if($q->explanation)
                                <div style="margin-top: 2px;">
                                    <strong>Explanation:</strong> {!! strip_tags($q->explanation, '<b><strong><i><em><br>') !!}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Closing Institute Card -->
        <div class="closing-brand-card">
            <div class="closing-title">Civil Service Gurukul</div>
            <div class="closing-subtitle">UPSC Preparation &bull; Current Affairs &bull; Mentorship &bull; Test Series</div>
            <div class="closing-contacts">
                Website: www.civilservicegurukul.com &bull; Email: info@civilservicegurukul.com &bull; Mobile: +91 9911020626
            </div>
        </div>
    </main>

    <!-- DomPDF Canvas: Watermark on EVERY page & Page Numbering -->
    <script type="text/php">
        if (isset($pdf)) {
            $boldFont = $fontMetrics->getFont("DejaVu Sans", "bold");
            $normalFont = $fontMetrics->getFont("DejaVu Sans", "normal");

            $w = $pdf->get_width();
            $h = $pdf->get_height();

            // 1. Watermark: Crisp diagonal brand watermark across center of every page
            $wmColor = array(0.88, 0.88, 0.90);
            $wmText = "CIVIL SERVICE GURUKUL";
            $wmSub = "www.civilservicegurukul.com";

            $pdf->page_text($w / 2 - 200, $h / 2 + 10, $wmText, $boldFont, 32, $wmColor, 0, 0, -38);
            $pdf->page_text($w / 2 - 150, $h / 2 + 55, $wmSub, $normalFont, 15, $wmColor, 0, 0, -38);

            // 2. Page Number at Bottom Right of footer on every page
            $footerColor = array(0.4, 0.45, 0.5);
            $pageText = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $pageWidth = $fontMetrics->getTextWidth($pageText, $normalFont, 8);
            $x = $w - 16 * 2.83465 - $pageWidth;
            $y = $h - 9 * 2.83465;
            $pdf->page_text($x, $y, $pageText, $normalFont, 8, $footerColor);
        }
    </script>
</body>
</html>
