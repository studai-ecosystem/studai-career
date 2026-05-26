<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Report — {{ $session['job_title'] }}</title>
    <style>
        /* ─── Base ───────────────────────────────────────── */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1f2937;
            background: #fff;
            padding: 32px 40px;
            max-width: 860px;
            margin: 0 auto;
        }
        a { color: inherit; text-decoration: none; }

        /* ─── Print button (screen only) ─────────────────── */
        .print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1a73e8;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .print-bar button {
            background: #fff;
            color: #1a73e8;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
        }
        .print-bar button:hover { background: #e8f0fe; }
        @media print { .print-bar { display: none !important; } }

        /* ─── Header ─────────────────────────────────────── */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 3px solid #1a73e8;
            margin-bottom: 24px;
        }
        .brand { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; }
        .report-title { font-size: 22px; font-weight: 800; color: #1a73e8; margin: 4px 0; }
        .report-meta { font-size: 12px; color: #6b7280; }
        .score-badge {
            text-align: right;
        }
        .score-badge .score-num {
            font-size: 48px;
            font-weight: 900;
            color: #1a73e8;
            line-height: 1;
        }
        .score-badge .score-label { font-size: 11px; color: #6b7280; }
        .grade-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 13px;
            background: #e8f0fe;
            color: #1a73e8;
            margin-top: 6px;
        }

        /* ─── Section titles ─────────────────────────────── */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            margin-bottom: 10px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ─── Stats row ──────────────────────────────────── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
            text-align: center;
        }
        .stat-card .num { font-size: 26px; font-weight: 800; color: #1a73e8; }
        .stat-card .lbl { font-size: 10px; color: #9ca3af; text-transform: uppercase; margin-top: 2px; }

        /* ─── Insights grid ──────────────────────────────── */
        .insights-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        .insight-box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
        }
        .insight-box .box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .insight-box.strength .box-title { color: #059669; }
        .insight-box.improve  .box-title { color: #d97706; }
        .insight-box.suggest  .box-title { color: #1a73e8; }

        .insight-box ul { list-style: none; padding: 0; }
        .insight-box ul li {
            font-size: 12px;
            color: #374151;
            padding: 4px 0;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            gap: 6px;
        }
        .insight-box ul li:last-child { border-bottom: none; }
        .dot { width: 6px; height: 6px; border-radius: 50%; margin-top: 5px; flex-shrink: 0; }
        .dot-green  { background: #10b981; }
        .dot-amber  { background: #f59e0b; }
        .dot-blue   { background: #1a73e8; }

        /* ─── Q&A section ────────────────────────────────── */
        .qa-section { margin-bottom: 24px; }
        .qa-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .qa-head {
            background: #f9fafb;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
        }
        .qa-head .q-num {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #9ca3af;
            white-space: nowrap;
        }
        .qa-head .q-text { font-weight: 600; font-size: 13px; color: #111827; flex: 1; }
        .qa-head .q-score {
            font-size: 16px;
            font-weight: 800;
            color: #1a73e8;
            white-space: nowrap;
        }
        .qa-body { padding: 12px 14px; }
        .qa-answer-label { font-size: 10px; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px; }
        .qa-answer { font-size: 12px; color: #374151; line-height: 1.6; margin-bottom: 8px; }
        .qa-feedback { font-size: 12px; color: #6b7280; font-style: italic; }

        /* ─── Suggestions full-width ─────────────────────── */
        .suggest-box {
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 14px;
            background: #eff6ff;
            margin-bottom: 24px;
        }
        .suggest-box .box-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1a73e8;
            margin-bottom: 8px;
        }
        .suggest-box ul { list-style: none; padding: 0; columns: 2; }
        .suggest-box ul li {
            font-size: 12px;
            color: #1e3a5f;
            padding: 3px 0;
            break-inside: avoid;
            display: flex;
            gap: 6px;
        }

        /* ─── Footer ─────────────────────────────────────── */
        .report-footer {
            border-top: 1px solid #e5e7eb;
            padding-top: 12px;
            font-size: 10px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }

        /* ─── Page break ─────────────────────────────────── */
        @media print {
            body { padding: 20px 28px; }
            .qa-item { page-break-inside: avoid; }
            .section-title { page-break-after: avoid; }
        }
    </style>
</head>
<body>

    {{-- Screen-only print bar --}}
    <div class="print-bar">
        <span>📄 &nbsp;<strong>StudAI Hire</strong> — Interview Practice Report</span>
        <button onclick="window.print()">⬇ Save as PDF / Print</button>
    </div>

    {{-- Report Header --}}
    <div class="report-header">
        <div>
            <div class="brand">StudAI Hire · Interview Report</div>
            <div class="report-title">{{ $session['job_title'] }}</div>
            <div class="report-meta">
                Level: {{ ucfirst($session['experience_level']) }}
                @if(!empty($session['company']))
                    &nbsp;·&nbsp; Company: {{ $session['company'] }}
                @endif
                &nbsp;·&nbsp; Generated: {{ $generatedAt }}
            </div>
        </div>
        <div class="score-badge">
            <div class="score-num">{{ $averageScore }}</div>
            <div class="score-label">/ 100 Overall Score</div>
            <div class="grade-pill">{{ $grade['grade'] }} &middot; {{ $grade['label'] }}</div>
        </div>
    </div>

    {{-- Stats row --}}
    <div class="stats-row">
        <div class="stat-card">
            <div class="num">{{ $averageScore }}</div>
            <div class="lbl">Avg. Score</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $totalAnswered }}</div>
            <div class="lbl">Answered</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $totalQuestions }}</div>
            <div class="lbl">Total Questions</div>
        </div>
        <div class="stat-card">
            <div class="num">{{ $grade['grade'] }}</div>
            <div class="lbl">Grade</div>
        </div>
    </div>

    {{-- Insights --}}
    @if($topStrengths->count() || $topImprovements->count())
    <div class="insights-grid">
        <div class="insight-box strength">
            <div class="box-title">✅ Top Strengths</div>
            <ul>
                @forelse($topStrengths as $s)
                    <li><span class="dot dot-green"></span>{{ $s }}</li>
                @empty
                    <li><span class="dot dot-green"></span>No strength data yet.</li>
                @endforelse
            </ul>
        </div>
        <div class="insight-box improve">
            <div class="box-title">⚡ Focus Areas</div>
            <ul>
                @forelse($topImprovements as $item)
                    <li><span class="dot dot-amber"></span>{{ $item }}</li>
                @empty
                    <li><span class="dot dot-amber"></span>No improvement data yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
    @endif

    @if($topSuggestions->count())
    <div class="suggest-box">
        <div class="box-title">💡 Actionable Suggestions</div>
        <ul>
            @foreach($topSuggestions as $s)
                <li><span class="dot dot-blue"></span>{{ $s }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Question-by-Question --}}
    <div class="qa-section">
        <div class="section-title">Question-by-Question Review</div>

        @forelse($answers as $index => $answer)
            @php
                $meta  = $questionLookup[(int) $index] ?? [];
                $score = $answer['evaluation']['score'] ?? null;
            @endphp
            <div class="qa-item">
                <div class="qa-head">
                    <div>
                        <div class="q-num">Q{{ (int)$index + 1 }} &middot; {{ $meta['type'] ?? 'General' }}</div>
                        <div class="q-text">{{ $answer['question'] }}</div>
                    </div>
                    <div class="q-score">{{ $score !== null ? $score : '—' }}<span style="font-size:11px;color:#9ca3af">/100</span></div>
                </div>
                <div class="qa-body">
                    <div class="qa-answer-label">Your Answer</div>
                    <div class="qa-answer">{{ $answer['answer'] }}</div>
                    @if(!empty($answer['evaluation']['overall_feedback']))
                        <div class="qa-feedback">💬 {{ $answer['evaluation']['overall_feedback'] }}</div>
                    @endif
                </div>
            </div>
        @empty
            <p style="color:#9ca3af;font-size:13px;padding:12px 0;">No answers were saved in this session.</p>
        @endforelse
    </div>

    {{-- Footer --}}
    <div class="report-footer">
        <span>StudAI Hire — studai.com</span>
        <span>{{ $session['job_title'] }} · {{ ucfirst($session['experience_level']) }} Level · {{ $generatedAt }}</span>
    </div>

    <script>
        // Auto-open print dialog when page loads (only if accessed directly for PDF)
        // Remove the line below if you prefer the user to click the button manually
        // window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    </script>
</body>
</html>
