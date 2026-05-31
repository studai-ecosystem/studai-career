@extends('layouts.app')

@section('title', 'Edit Resume — ' . $resume->title)

@section('content')
@php
    $cats     = $analysis['categories'] ?? [];
    $score    = (int)($analysis['score'] ?? 0);
    $label    = $analysis['label'] ?? 'Fair';
    $color    = $score >= 80 ? '#1E8E3E' : ($score >= 60 ? '#fbbc04' : '#2D6CDF');
    $ringDash = round($score * 2.83);

    $cat_content  = $cats['content']  ?? [];
    $cat_skills   = $cats['skills']   ?? [];
    $cat_format   = $cats['format']   ?? [];
    $cat_sections = $cats['sections'] ?? [];
    $cat_style    = $cats['style']    ?? [];

    $mr = $cat_content['measurable_results'] ?? [];
    $sg = $cat_content['spelling_grammar']   ?? [];
    $hs = $cat_skills['hard_skills']         ?? [];
    $ss = $cat_skills['soft_skills']         ?? [];
    $vo = $cat_style['voice']                ?? [];
    $bz = $cat_style['buzzwords']            ?? [];

    $sugg = [
        'content'  => (int)(!($mr['pass'] ?? true)) + (int)(!($sg['pass'] ?? true)),
        'skills'   => (int)(!($hs['pass'] ?? true)) + (int)(!($ss['pass'] ?? true)),
        'format'   => (int)(!(($cats['format']['date_formatting']['pass'] ?? true))) + (int)(!(($cats['format']['resume_length']['pass'] ?? true))) + (int)(!(($cats['format']['bullet_points']['pass'] ?? true))),
        'sections' => count($cat_sections['missing'] ?? []),
        'style'    => (int)(!($vo['pass'] ?? true)) + (int)(!($bz['pass'] ?? true)),
    ];
    $totalSuggestions = array_sum($sugg);

    // Flagged sentences (for highlight in editor)
    $flaggedSentences = array_merge(
        $mr['suggestions'] ?? [],
        $vo['found'] ?? []
    );
    $buzzwordsList = $bz['found'] ?? [];
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* ---- Layout ---- */
.ae-wrap{display:flex;gap:0;min-height:calc(100vh - 60px);background:#EBF2FF;}
.ae-panel{width:300px;min-width:280px;background:#fff;border-right:1px solid #E2E2E0;padding:20px 16px;position:sticky;top:60px;height:calc(100vh - 60px);overflow-y:auto;flex-shrink:0;}
.ae-doc-wrap{flex:1;padding:32px 40px;display:flex;justify-content:center;}
.ae-doc{background:#fff;width:100%;max-width:820px;min-height:1000px;box-shadow: none;border-radius:4px;padding:56px 64px;font-family:'Georgia',serif;font-size:14px;color:#0C0C0C;line-height:1.7;}

/* ---- Topbar ---- */
.ae-topbar{background:#2D6CDF;padding:0 32px;display:flex;align-items:center;justify-content:space-between;gap:12px;position:sticky;top:0;z-index:50;box-shadow: none;}
.ae-tab{color:rgba(255,255,255,.7);font-size:13px;padding:18px 20px;text-decoration:none;border-bottom:3px solid transparent;display:inline-block;}
.ae-tab.active{color:#fff;font-weight:700;border-bottom-color:#fff;}

/* ---- Editable sections ---- */
.ae-section{margin-bottom:28px;}
.ae-section-label{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#aaa;margin-bottom:4px;display:flex;align-items:center;gap:6px;}
.ae-field{outline:none;border:2px solid transparent;border-radius:4px;padding:6px 8px;transition:border .2s,background .2s;cursor:text;min-height:24px;}
.ae-field:hover{border-color:#E2E2E0;background:#F0F0EE;}
.ae-field:focus{border-color:#2D6CDF;background:#EBF2FF;cursor:auto;}
.ae-field[data-dirty="true"]{border-color:#fbbc04;background:#FFF8EC;}

/* ---- Highlight flags ---- */
.ae-flag{background:#FFF8EC;border-bottom:2px solid #fbbc04;cursor:pointer;border-radius:2px;transition:background .2s;}
.ae-flag:hover{background:#FFF8EC;}
.ae-flag-bz{background:#FEF2F2;border-bottom:2px solid #2D6CDF;}
.ae-flag-bz:hover{background:#fcc;}

/* ---- Resume typography ---- */
.ae-name{font-size:26px;font-weight:700;letter-spacing:.3px;font-family:'Arial',sans-serif;color:#0C0C0C;line-height:1.2;}
.ae-jobtitle{font-size:15px;color:#2D6CDF;font-weight:600;font-family:'Arial',sans-serif;margin-top:2px;}
.ae-contact{font-size:12px;color:#555;margin-top:8px;font-family:'Arial',sans-serif;}
.ae-divider{border:none;border-top:2px solid #2D6CDF;margin:18px 0 14px;}
.ae-sec-title{font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#2D6CDF;font-family:'Arial',sans-serif;margin-bottom:10px;}
.ae-exp-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:4px;}
.ae-exp-company{font-weight:700;font-size:14px;font-family:'Arial',sans-serif;}
.ae-exp-date{font-size:12px;color:#888;font-family:'Arial',sans-serif;}
.ae-exp-pos{font-size:13px;color:#555;font-family:'Arial',sans-serif;font-style:italic;}
.ae-bullet-list{margin:6px 0 0 16px;padding:0;}
.ae-bullet-list li{margin-bottom:3px;font-size:13px;}
.ae-skill-chip{display:inline-block;background:#EBF2FF;color:#1B57C4;border-radius:4px;padding:2px 8px;margin:2px;font-size:12px;font-family:'Arial',sans-serif;}

/* ---- Save banner ---- */
#ae-save-banner{position:fixed;bottom:0;left:0;right:0;background:#2D6CDF;color:#fff;padding:12px 32px;display:flex;align-items:center;justify-content:space-between;transform:translateY(100%);transition:transform .3s;z-index:100;box-shadow: none;}
#ae-save-banner.visible{transform:translateY(0);}

/* ---- Suggestion hint card ---- */
.ae-hint{background:#FFF8EC;border:1px solid #fbbc04;border-radius:8px;padding:10px 12px;margin-bottom:10px;font-size:12px;color:#555;}
.ae-hint strong{color:#E37400;}

/* ---- Floating suggestion popup ---- */
#ae-suggestion-card{position:fixed;z-index:2000;background:#fff;border-radius:14px;box-shadow: none;padding:18px 18px 14px;width:320px;border:1px solid #E2E2E0;animation:ae-pop .15s ease;}
@keyframes ae-pop{from{opacity:0;transform:scale(.94) translateY(-4px)}to{opacity:1;transform:none}}
.ae-sug-option{padding:9px 12px;margin-bottom:4px;border-radius:8px;border:1px solid #E2E2E0;cursor:pointer;font-size:12px;color:#0C0C0C;transition:background .12s;display:block;width:100%;text-align:left;background:#fff;}
.ae-sug-option:hover{background:#EBF2FF;border-color:#2D6CDF;}
.ae-sug-option.yellow{border-color:#fbbc04;background:#FFF8EC;color:#E37400;}
.ae-sug-option.yellow:hover{background:#FFF8EC;}
.ae-sug-verb{display:inline-block;padding:5px 10px;margin:3px;border-radius:6px;border:1px solid #2D6CDF;color:#2D6CDF;background:#fff;cursor:pointer;font-size:12px;transition:background .12s;}
.ae-sug-verb:hover{background:#EBF2FF;}
.ae-flag,.ae-flag-bz{cursor:pointer;}

/* ---- Suggestion cards (left panel) ---- */
.ae-sc{background:#fff;border:1px solid #E2E2E0;border-radius:10px;margin-bottom:8px;overflow:hidden;}
.ae-sc-head{padding:10px 12px 8px;cursor:pointer;}
.ae-sc-tag{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#E37400;display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;}
.ae-sc-tag.red{color:#2D6CDF;}
.ae-sc-tag.blue{color:#1B57C4;}
.ae-sc-pts{background:#EBF2FF;color:#1B57C4;border-radius:10px;padding:1px 7px;font-size:10px;font-weight:700;}
.ae-sc-pts.green{background:#EDFAF2;color:#1E8E3E;}
.ae-sc-excerpt{font-size:11px;color:#555;font-style:italic;line-height:1.5;}
.ae-sc-actions{display:flex;gap:6px;padding:0 12px 10px;}
.ae-sc-btn-ai{flex:1;background:#2D6CDF;color:#fff;border:none;border-radius:6px;padding:6px 0;font-size:11px;font-weight:700;cursor:pointer;transition:background .15s;}
.ae-sc-btn-ai:hover{background:#1B57C4;}
.ae-sc-btn-dismiss{background:none;border:none;color:#888;font-size:11px;cursor:pointer;padding:6px 8px;}
.ae-sc-btn-dismiss:hover{color:#2D6CDF;}
.ae-sc-suggestions{padding:0 12px;display:none;}
.ae-sc-sug-item{border:1px solid #E2E2E0;border-radius:8px;padding:9px 10px;margin-bottom:6px;font-size:11px;color:#0C0C0C;line-height:1.5;}
.ae-sc-sug-btns{display:flex;gap:6px;margin-top:7px;}
.ae-sc-apply{background:#1E8E3E;color:#fff;border:none;border-radius:5px;padding:4px 12px;font-size:11px;font-weight:700;cursor:pointer;}
.ae-sc-apply:hover{background:#1E8E3E;}
.ae-sc-sdismiss{background:none;border:none;color:#888;font-size:11px;cursor:pointer;padding:4px 6px;}
.ae-sc-loading{padding:10px 0;text-align:center;color:#888;font-size:11px;}

/* ---- Score mini ring ---- */
.ae-mini-score{text-align:center;padding:12px 0 8px;}
.ae-cat-row{display:flex;align-items:center;justify-content:space-between;padding:7px 0;border-bottom:1px solid #F0F0EE;font-size:12px;}
.ae-cat-row:last-child{border-bottom:none;}
.ae-cat-bar-bg{height:4px;background:#eee;border-radius:2px;overflow:hidden;flex:1;margin:0 8px;}
.ae-cat-bar-fg{height:100%;border-radius:2px;}
</style>

{{-- TOPBAR --}}
<div class="ae-topbar">
    <div style="display:flex;align-items:center;">
        <a href="{{ route('resume.edit', $resume) }}" class="ae-tab" style="padding-left:0;padding-right:12px;">← Back</a>
        <span style="color:rgba(255,255,255,.4);font-size:13px;margin-right:12px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $resume->title }}</span>
        <a href="{{ route('resume.ats.show', $resume) }}" class="ae-tab">① Report</a>
        <span class="ae-tab active">② Resume</span>
        <a href="{{ route('resume.cover-letter.show', $resume) }}" class="ae-tab">③ Cover Letter</a>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <span id="ae-score-pill" style="background:rgba(255,255,255,.15);color:#fff;border-radius:20px;padding:5px 14px;font-size:13px;font-weight:700;">
            {{ $score }}/100 {{ $label }}
        </span>
        <button id="ae-save-btn" onclick="saveResume()" style="background:#fff;color:#2D6CDF;border:none;border-radius:8px;padding:8px 20px;font-size:13px;font-weight:700;cursor:pointer;display:none;">
            💾 Save Changes
        </button>
        <span id="ae-saving-text" style="color:rgba(255,255,255,.7);font-size:12px;display:none;">Saving…</span>
        <span id="ae-saved-text" style="color:#A3D9B4;font-size:12px;display:none;">✔ Saved</span>
        <button onclick="saveAndGoToDashboard()" style="background:#1E8E3E;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;">
            Finish &amp; Dashboard →
        </button>
    </div>
</div>

<div class="ae-wrap">

    {{-- LEFT PANEL: Suggestions --}}
    <div class="ae-panel">
        {{-- Mini score ring --}}
        <div class="ae-mini-score">
            <svg width="90" height="90" viewBox="0 0 90 90">
                <circle cx="45" cy="45" r="36" fill="none" stroke="#eee" stroke-width="8"/>
                <circle cx="45" cy="45" r="36" fill="none" stroke="{{ $color }}" stroke-width="8"
                    stroke-dasharray="{{ round($score * 2.26) }} 226" stroke-linecap="round"
                    transform="rotate(-90 45 45)"/>
                <text x="45" y="41" text-anchor="middle" font-size="20" font-weight="800" fill="#0C0C0C">{{ $score }}</text>
                <text x="45" y="53" text-anchor="middle" font-size="10" fill="#888">/100</text>
            </svg>
            <div style="font-size:14px;font-weight:800;color:{{ $color }};">{{ $label }}</div>
            <div style="font-size:11px;color:#aaa;margin-top:2px;">{{ $totalSuggestions }} things to fix</div>
        </div>

        <hr style="border:none;border-top:1px solid #F0F0EE;margin:10px 0;">

        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#aaa;margin-bottom:8px;">Fix these issues</div>

        {{-- Measurable Results — actionable suggestion cards --}}
        @if(!($mr['pass'] ?? true))
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:#E37400;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    📊 Add Metrics <span style="background:#FFF8EC;border-radius:10px;padding:1px 7px;font-size:10px;">{{ max(1, count($mr['suggestions'] ?? [])) }}</span>
                </div>
                @forelse($mr['suggestions'] ?? [] as $si => $s)
                    <div class="ae-sc" id="sc-metric-{{ $si }}">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag">Quantify impact <span class="ae-sc-pts">+2 pts</span></div>
                            <div class="ae-sc-excerpt">"{{ Str::limit($s, 75) }}"</div>
                        </div>
                        <div class="ae-sc-suggestions" id="sc-sug-metric-{{ $si }}"></div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-ai" onclick="fetchPanelSuggestions('metric', {{ $si }}, {{ json_encode($s) }}, null)">✨ Get AI Suggestions</button>
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-metric-{{ $si }}')">Dismiss</button>
                        </div>
                    </div>
                @empty
                    {{-- No specific sentences flagged — show a generic card --}}
                    <div class="ae-sc" id="sc-metric-0">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag">Quantify impact <span class="ae-sc-pts">+2 pts</span></div>
                            <div class="ae-sc-excerpt">Your resume needs more measurable achievements (numbers, %, $, time saved).</div>
                        </div>
                        <div class="ae-sc-suggestions" id="sc-sug-metric-0"></div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-ai" onclick="fetchPanelSuggestions('metric', 0, {{ json_encode($resume->professional_summary ?? 'Add measurable results to your experience') }}, null)">✨ Get AI Suggestions</button>
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-metric-0')">Dismiss</button>
                        </div>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Buzzwords --}}
        @if(!($bz['pass'] ?? true) && !empty($bz['found']))
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:#2D6CDF;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    🚫 Replace Buzzwords <span style="background:#FEF2F2;border-radius:10px;padding:1px 7px;font-size:10px;">{{ count($bz['found']) }}</span>
                </div>
                @foreach($bz['found'] as $wi => $w)
                    <div class="ae-sc" id="sc-bz-{{ $wi }}">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag red">Buzzword <span class="ae-sc-pts" style="background:#FEF2F2;color:#2D6CDF;">+1 pt</span></div>
                            <div class="ae-sc-excerpt">"{{ $w }}" — overused, lacks impact</div>
                        </div>
                        <div class="ae-sc-suggestions" id="sc-sug-bz-{{ $wi }}"></div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-ai" onclick="fetchPanelSuggestions('buzzword', {{ $wi }}, null, {{ json_encode($w) }})" style="background:#2D6CDF;">✨ Get Replacements</button>
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-bz-{{ $wi }}')">Dismiss</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Passive voice --}}
        @if(!($vo['pass'] ?? true) && !empty($vo['found']))
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:#E37400;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    ⚡ Use Action Verbs <span style="background:#FFF8EC;border-radius:10px;padding:1px 7px;font-size:10px;">{{ count($vo['found']) }}</span>
                </div>
                @foreach(array_slice($vo['found'], 0, 3) as $vi => $vt)
                    <div class="ae-sc" id="sc-vo-{{ $vi }}">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag">Passive voice <span class="ae-sc-pts">+1 pt</span></div>
                            <div class="ae-sc-excerpt">"{{ Str::limit($vt, 75) }}"</div>
                        </div>
                        <div class="ae-sc-suggestions" id="sc-sug-vo-{{ $vi }}"></div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-ai" onclick="fetchPanelSuggestions('passive', {{ $vi }}, {{ json_encode($vt) }}, null)">✨ Get Active Rewrites</button>
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-vo-{{ $vi }}')">Dismiss</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Missing sections --}}
        @if($sugg['sections'] > 0)
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:#1B57C4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    📋 Missing Sections <span style="background:#EBF2FF;border-radius:10px;padding:1px 7px;font-size:10px;">{{ $sugg['sections'] }}</span>
                </div>
                @foreach($cat_sections['missing'] ?? [] as $mi => $m)
                    <div class="ae-sc" id="sc-sec-{{ $mi }}">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag blue">Sections <span class="ae-sc-pts green">+2 pts</span></div>
                            <div style="font-size:12px;font-weight:600;color:#0C0C0C;margin-bottom:2px;">Add {{ $m }}</div>
                            <div class="ae-sc-excerpt">Include this section to complete your resume.</div>
                        </div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-ai" onclick="applySection({{ json_encode($m) }}, 'sc-sec-{{ $mi }}')" style="background:#2D6CDF;">Apply</button>
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-sec-{{ $mi }}')">Dismiss</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Format issues --}}
        @php
            $fmt_date = $cats['format']['date_formatting'] ?? [];
            $fmt_len  = $cats['format']['resume_length']   ?? [];
            $fmt_bul  = $cats['format']['bullet_points']   ?? [];
            $fmtCount = (int)(!($fmt_date['pass'] ?? true)) + (int)(!($fmt_len['pass'] ?? true)) + (int)(!($fmt_bul['pass'] ?? true));
        @endphp
        @if($fmtCount > 0)
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:#E37400;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    📐 Format <span style="background:#FFF8EC;border-radius:10px;padding:1px 7px;font-size:10px;">{{ $fmtCount }}</span>
                </div>
                @if(!($fmt_date['pass'] ?? true) && !empty($fmt_date['issue']))
                    <div class="ae-sc" id="sc-fmt-date">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag">Date format <span class="ae-sc-pts">+2 pts</span></div>
                            <div class="ae-sc-excerpt">{{ $fmt_date['issue'] }}</div>
                        </div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-ai" onclick="document.getElementById('section-experience')?.scrollIntoView({behavior:'smooth',block:'start'});dismissCard('sc-fmt-date');" style="background:#fbbc04;color:#333;">Fix It ↓</button>
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-fmt-date')">Dismiss</button>
                        </div>
                    </div>
                @endif
                @if(!($fmt_len['pass'] ?? true) && !empty($fmt_len['issue']))
                    <div class="ae-sc" id="sc-fmt-len">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag">Resume length <span class="ae-sc-pts">+2 pts</span></div>
                            <div class="ae-sc-excerpt">{{ $fmt_len['issue'] }}</div>
                        </div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-fmt-len')" style="flex:1;text-align:center;color:#737373;">Got it</button>
                        </div>
                    </div>
                @endif
                @if(!($fmt_bul['pass'] ?? true) && !empty($fmt_bul['suggestions']))
                    @foreach($fmt_bul['suggestions'] as $fbi => $fbs)
                        <div class="ae-sc" id="sc-fmt-bul-{{ $fbi }}">
                            <div class="ae-sc-head">
                                <div class="ae-sc-tag">Bullet points <span class="ae-sc-pts">+1 pt</span></div>
                                <div class="ae-sc-excerpt">"{{ $fbs }}"</div>
                            </div>
                            <div class="ae-sc-suggestions" id="sc-sug-fmt-{{ $fbi }}"></div>
                            <div class="ae-sc-actions">
                                <button class="ae-sc-btn-ai" onclick="fetchPanelSuggestions('format', {{ $fbi }}, {{ json_encode($fbs) }}, null)">✨ Convert to Bullets</button>
                                <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-fmt-bul-{{ $fbi }}')">Dismiss</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif

        {{-- Grammar issues --}}
        @if(!($sg['pass'] ?? true) && !empty($sg['issues']))
            <div style="margin-bottom:12px;">
                <div style="font-size:11px;font-weight:700;color:#737373;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                    ✏️ Grammar <span style="background:#F0F0EE;border-radius:10px;padding:1px 7px;font-size:10px;">{{ count($sg['issues']) }}</span>
                </div>
                @foreach($sg['issues'] as $gi => $gissue)
                    <div class="ae-sc" id="sc-gr-{{ $gi }}">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag" style="color:#737373;">Grammar issue <span class="ae-sc-pts">+1 pt</span></div>
                            <div class="ae-sc-excerpt">{{ $gissue }}</div>
                        </div>
                        <div class="ae-sc-actions">
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-gr-{{ $gi }}')" style="flex:1;text-align:center;color:#737373;">Got it</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Missing skills --}}
        @php $missingSkills = array_slice(array_merge($hs['missing'] ?? [], $ss['missing'] ?? []), 0, 5); @endphp
        @if(!($hs['pass'] ?? true) || !($ss['pass'] ?? true))
            @if(!empty($missingSkills))
                <div style="margin-bottom:12px;">
                    <div style="font-size:11px;font-weight:700;color:#1B57C4;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">
                        🛠 Add Skills <span style="background:#EBF2FF;border-radius:10px;padding:1px 7px;font-size:10px;">{{ count($missingSkills) }}</span>
                    </div>
                    <div class="ae-sc" id="sc-skills-0">
                        <div class="ae-sc-head">
                            <div class="ae-sc-tag blue">Missing skills <span class="ae-sc-pts green">+2 pts</span></div>
                            <div class="ae-sc-excerpt">Click a skill chip to add it to your resume instantly.</div>
                        </div>
                        <div style="padding:4px 12px 10px;display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($missingSkills as $sk)
                                <span onclick="appendSkill({{ json_encode($sk) }}, this)"
                                      style="background:#EBF2FF;color:#1B57C4;border:1px solid #BFCFEE;border-radius:6px;padding:4px 10px;font-size:11px;cursor:pointer;font-weight:600;">+ {{ $sk }}</span>
                            @endforeach
                        </div>
                        <div class="ae-sc-actions" style="padding-top:0;">
                            <button class="ae-sc-btn-dismiss" onclick="dismissCard('sc-skills-0')" style="color:#888;">Dismiss</button>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <hr style="border:none;border-top:1px solid #F0F0EE;margin:10px 0;">

        {{-- Category scores --}}
        @foreach([
            ['Content', 'content', $sugg['content'], $cat_content['score'] ?? 0, $cat_content['max'] ?? 25, $cat_content['label'] ?? 'Needs Work'],
            ['Skills',  'skills',  $sugg['skills'],  $cat_skills['score']  ?? 0, $cat_skills['max']  ?? 20, $cat_skills['label']  ?? 'Needs Work'],
            ['Format',  'format',  $sugg['format'],  $cats['format']['score'] ?? 0, $cats['format']['max'] ?? 20, $cats['format']['label'] ?? 'Needs Work'],
            ['Sections','sections',$sugg['sections'],$cat_sections['score'] ?? 0, $cat_sections['max'] ?? 20, $cat_sections['label'] ?? 'Needs Work'],
            ['Style',   'style',   $sugg['style'],   $cat_style['score']   ?? 0, $cat_style['max']   ?? 15, $cat_style['label']   ?? 'Needs Work'],
        ] as [$cn, $ck, $cSugg, $cScore, $cMax, $cLabel])
            @php $cPct = $cMax > 0 ? round($cScore / $cMax * 100) : 0; $cColor = match(true){$cLabel==='Excellent','Pass'=>'#1E8E3E',$cLabel==='Good'=>'#fbbc04',default=>'#2D6CDF'}; @endphp
            <div class="ae-cat-row">
                <span style="color:#0C0C0C;font-weight:600;">{{ $cn }}</span>
                <div class="ae-cat-bar-bg"><div id="cat-bar-{{ $ck }}" class="ae-cat-bar-fg" style="width:{{ $cPct }}%;background:{{ $cColor }};transition:width .5s ease,background .5s ease;"></div></div>
                <span id="cat-score-{{ $ck }}" style="font-weight:700;color:{{ $cColor }};min-width:32px;text-align:right;">{{ $cScore }}/{{ $cMax }}</span>
            </div>
        @endforeach

        <a href="{{ route('resume.ats.show', $resume) }}" style="display:block;margin-top:14px;background:#EBF2FF;color:#2D6CDF;text-align:center;padding:10px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">← View Full Report</a>
        <button onclick="saveAndGoToDashboard()" style="display:block;width:100%;margin-top:8px;background:#1E8E3E;color:#fff;border:none;border-radius:8px;padding:11px;font-size:13px;font-weight:700;cursor:pointer;">💾 Save &amp; Go to Dashboard →</button>
    </div>

    {{-- RIGHT PANEL: Editable resume document --}}
    <div class="ae-doc-wrap">
        <div class="ae-doc" id="ae-resume-doc">

            {{-- ===== HEADER ===== --}}
            <div id="section-header">
                <div class="ae-section-label">NAME</div>
                <div class="ae-name ae-field" contenteditable="true"
                     data-field="full_name"
                     id="field-full_name">{{ $resume->full_name }}</div>

                <div class="ae-section-label" style="margin-top:10px;">JOB TITLE</div>
                @php $firstJobTitle = !empty($resume->experience) ? ($resume->experience[0]['position'] ?? '') : ''; @endphp
                <div class="ae-jobtitle ae-field" contenteditable="true"
                     data-custom="job_title"
                     id="field-job_title">{{ $resume->title !== 'Untitled Resume' ? $resume->title : $firstJobTitle }}</div>

                <div class="ae-section-label" style="margin-top:10px;">CONTACT</div>
                <div class="ae-contact" style="display:flex;gap:16px;flex-wrap:wrap;">
                    @if($resume->email)
                        <span>📧 {{ $resume->email }}</span>
                    @endif
                    @if($resume->phone)
                        <span>📞 {{ $resume->phone }}</span>
                    @endif
                    @if($resume->location)
                        <span class="ae-field {{ in_array('Location', $cat_sections['missing'] ?? []) ? 'ae-flag' : '' }}"
                              contenteditable="true" data-field="location" id="field-location"
                              style="padding:2px 4px;">{{ $resume->location ?: '+ Add Location' }}</span>
                    @endif
                    @if($resume->linkedin_url)
                        <span class="ae-field {{ in_array('LinkedIn URL', $cat_sections['missing'] ?? []) ? 'ae-flag' : '' }}"
                              contenteditable="true" data-field="linkedin_url" id="field-linkedin_url"
                              style="padding:2px 4px;color:#2D6CDF;">{{ $resume->linkedin_url ?: '+ Add LinkedIn' }}</span>
                    @endif
                </div>
                @if(!$resume->location)
                    <div class="ae-field ae-flag" contenteditable="true" data-field="location" id="field-location"
                         style="padding:4px 8px;margin-top:6px;display:inline-block;color:#888;">📍 + Add your location</div>
                @endif
                @if(!$resume->linkedin_url)
                    <div class="ae-field ae-flag" contenteditable="true" data-field="linkedin_url" id="field-linkedin_url"
                         style="padding:4px 8px;margin-top:4px;display:inline-block;color:#888;">🔗 + Add LinkedIn URL</div>
                @endif
            </div>

            <hr class="ae-divider">

            {{-- ===== SUMMARY ===== --}}
            @if($resume->professional_summary || in_array('Summary', $cat_sections['missing'] ?? []))
                <div id="section-summary" class="ae-section">
                    <div class="ae-sec-title">Professional Summary</div>
                    <div class="ae-field {{ !($mr['pass'] ?? true) || !empty($sg['issues']) ? 'ae-flag' : '' }}"
                         contenteditable="true"
                         data-field="professional_summary"
                         id="field-professional_summary"
                         style="min-height:60px;font-family:'Arial',sans-serif;font-size:13px;line-height:1.7;color:#333;">{{ $resume->professional_summary ?: 'Click here to add your professional summary. Describe your key skills and career goals in 40–120 words.' }}</div>
                </div>
            @endif

            {{-- ===== EXPERIENCE ===== --}}
            @if(!empty($resume->experience))
                <div id="section-experience" class="ae-section">
                    <div class="ae-sec-title">Experience</div>
                    @foreach($resume->experience as $idx => $exp)
                        @php
                            $desc = $exp['description'] ?? '';
                            $bullets = array_filter(array_map('trim', preg_split('/\n|•|-\s+/', $desc)));
                            $hasBullets = count($bullets) > 1;
                            // Flag if this experience has passive or missing metrics
                            $expFlagged = false;
                            foreach ($flaggedSentences as $fs) {
                                if (str_contains($desc, $fs)) { $expFlagged = true; break; }
                            }
                        @endphp
                        <div class="ae-exp-entry" style="margin-bottom:18px;" id="exp-{{ $idx }}">
                            <div class="ae-exp-header">
                                <div>
                                    <div class="ae-exp-company">{{ $exp['company'] ?? '' }}</div>
                                    <div class="ae-exp-pos">{{ $exp['position'] ?? '' }}</div>
                                </div>
                                <div class="ae-exp-date">
                                    {{ $exp['start_date'] ?? '' }}{{ !empty($exp['end_date']) ? ' – ' . $exp['end_date'] : ' – Present' }}
                                </div>
                            </div>

                            {{-- Editable description --}}
                            @if($hasBullets)
                                <ul class="ae-bullet-list">
                                    @foreach($bullets as $bi => $bullet)
                                        @php
                                            $bulletFlagged = false;
                                            foreach ($flaggedSentences as $fs) {
                                                if (stripos($bullet, substr($fs, 0, 30)) !== false) { $bulletFlagged = true; break; }
                                            }
                                            $buzzFlagged = false;
                                            foreach ($buzzwordsList as $bw) {
                                                if (stripos($bullet, $bw) !== false) { $buzzFlagged = true; break; }
                                            }
                                        @endphp
                                        <li class="ae-field {{ $bulletFlagged ? 'ae-flag' : ($buzzFlagged ? 'ae-flag-bz' : '') }}"
                                            contenteditable="true"
                                            data-exp-idx="{{ $idx }}"
                                            data-bullet-idx="{{ $bi }}"
                                            data-type="exp-bullet"
                                            style="padding:2px 4px;">{{ $bullet }}</li>
                                    @endforeach
                                    <li style="list-style:none;margin-top:4px;">
                                        <span onclick="addBullet({{ $idx }})" style="font-size:12px;color:#2D6CDF;cursor:pointer;">+ Add bullet point</span>
                                    </li>
                                </ul>
                            @else
                                <div class="ae-field {{ $expFlagged ? 'ae-flag' : '' }}"
                                     contenteditable="true"
                                     data-exp-idx="{{ $idx }}"
                                     data-type="exp-desc"
                                     style="font-size:13px;min-height:40px;padding:4px 6px;line-height:1.7;">{{ $desc ?: 'Click to add description' }}</div>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <div style="border-top:1px dashed #eee;margin-bottom:16px;"></div>
                        @endif
                    @endforeach
                </div>
            @else
                <div id="section-experience" class="ae-section">
                    <div class="ae-sec-title">Experience</div>
                    <div style="color:#aaa;font-size:13px;font-style:italic;padding:12px;border:2px dashed #eee;border-radius:6px;text-align:center;">
                        No experience added yet. <a href="{{ route('resume.edit', $resume) }}" style="color:#2D6CDF;">Add in the editor →</a>
                    </div>
                </div>
            @endif

            {{-- ===== EDUCATION ===== --}}
            @if(!empty($resume->education))
                <div id="section-education" class="ae-section">
                    <div class="ae-sec-title">Education</div>
                    @foreach($resume->education as $edu)
                        <div style="margin-bottom:14px;">
                            <div style="font-weight:700;font-size:14px;font-family:'Arial',sans-serif;">{{ $edu['institution'] ?? $edu['school'] ?? '' }}</div>
                            <div style="font-size:13px;color:#555;font-family:'Arial',sans-serif;">
                                {{ $edu['degree'] ?? '' }}{{ !empty($edu['field_of_study'] ?? $edu['field'] ?? '') ? ' · ' . ($edu['field_of_study'] ?? $edu['field']) : '' }}
                            </div>
                            <div style="font-size:12px;color:#888;font-family:'Arial',sans-serif;">
                                {{ $edu['start_year'] ?? $edu['start_date'] ?? '' }}{{ !empty($edu['end_year'] ?? $edu['end_date'] ?? '') ? ' – ' . ($edu['end_year'] ?? $edu['end_date']) : '' }}
                                @if(!empty($edu['gpa']))<span> · GPA: {{ $edu['gpa'] }}</span>@endif
                            </div>
                        </div>
                    @endforeach
                    <div style="margin-top:4px;">
                        <a href="{{ route('resume.edit', $resume) }}#education" style="font-size:12px;color:#2D6CDF;">✏️ Edit in full editor</a>
                    </div>
                </div>
            @endif

            {{-- ===== SKILLS ===== --}}
            @php $flatSkills = $resume->flat_skills ?? []; @endphp
            <div id="section-skills" class="ae-section">
                <div class="ae-sec-title">Skills</div>
                <div id="skills-container" style="margin-bottom:8px;">
                    @foreach($flatSkills as $sk)
                        @php $isBz = in_array(strtolower($sk), array_map('strtolower', $buzzwordsList)); @endphp
                        <span class="ae-skill-chip {{ $isBz ? 'ae-flag-bz' : '' }}"
                              style="position:relative;cursor:default;">{{ $sk }}
                            <span onclick="removeSkill(this, {{ json_encode($sk) }})"
                                  title="Remove skill"
                                  style="color:#888;cursor:pointer;margin-left:4px;font-size:11px;">×</span>
                        </span>
                    @endforeach
                    @if(!($hs['pass'] ?? true))
                        @foreach(array_slice($hs['missing'] ?? [], 0, 3) as $ms)
                            <span onclick="appendSkill({{ json_encode($ms) }}, this)" class="ae-skill-chip"
                                  style="background:#EBF2FF;color:#aaa;cursor:pointer;border:1px dashed #ccc;">+ {{ $ms }}</span>
                        @endforeach
                    @endif
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <input id="new-skill-input" type="text" placeholder="Type a skill and press Enter"
                           style="border:1px solid #E2E2E0;border-radius:6px;padding:6px 12px;font-size:12px;flex:1;outline:none;"
                           onkeydown="if(event.key==='Enter'){appendSkillFromInput();event.preventDefault();}">
                    <button onclick="appendSkillFromInput()" style="background:#2D6CDF;color:#fff;border:none;border-radius:6px;padding:6px 14px;font-size:12px;cursor:pointer;">Add</button>
                </div>
            </div>

            {{-- ===== CERTIFICATIONS ===== --}}
            @if(!empty($resume->certifications))
                <div id="section-certifications" class="ae-section">
                    <div class="ae-sec-title">Certifications</div>
                    @foreach($resume->certifications as $cert)
                        <div style="font-size:13px;margin-bottom:6px;">
                            <strong>{{ $cert['name'] ?? $cert['title'] ?? '' }}</strong>
                            @if(!empty($cert['issuer']))<span style="color:#888;"> · {{ $cert['issuer'] }}</span>@endif
                            @if(!empty($cert['year']))<span style="color:#aaa;font-size:12px;"> ({{ $cert['year'] }})</span>@endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>{{-- end .ae-doc --}}
    </div>
</div>

{{-- ===== FLOATING SUGGESTION CARD ===== --}}
<div id="ae-suggestion-card" style="display:none;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <span id="ae-card-title" style="font-size:13px;font-weight:700;color:#0C0C0C;"></span>
        <button onclick="closeSuggestionCard()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#aaa;line-height:1;padding:0 2px;">✕</button>
    </div>
    <div id="ae-card-body"></div>
</div>

{{-- ===== JAVASCRIPT ===== --}}
<script>
const RESUME_ID  = {{ $resume->id }};
const SAVE_URL   = '{{ route('resume.ats.save', $resume) }}';
const SUGGEST_URL= '{{ route('resume.ats.suggest', $resume) }}';
const CSRF       = document.querySelector('meta[name="csrf-token"]').content;

// PHP data passed to JS
const buzzwordsInDoc = @json($buzzwordsList);
const passiveFound   = @json($vo['found'] ?? []);
const metricFlagged  = @json($mr['suggestions'] ?? []);

// Track current skills array in memory
let currentSkills = @json($flatSkills);
let saveTimeout   = null;

// ========== BUZZWORD REPLACEMENTS (offline fallback) ==========
const BUZZWORD_REPLACEMENTS = {
    'motivated':        ['Results-driven', 'Initiative-taking', 'Achievement-focused'],
    'passionate':       ['Committed to', 'Dedicated to delivering', 'Focused on'],
    'dynamic':          ['Adaptable', 'Versatile', 'Cross-functional'],
    'detail-oriented':  ['Meticulous', 'Systematic', 'Precise in execution'],
    'proactive':        ['Spearheaded', 'Initiated', 'Anticipated and resolved'],
    'team player':      ['Collaborated with cross-functional teams', 'Partnered across departments', 'Co-led with peers'],
    'hard worker':      ['Consistently met deadlines', 'Delivered results under pressure', 'Completed X% on time'],
    'innovative':       ['Developed [specific solution]', 'Introduced [new process]', 'Redesigned [specific thing]'],
    'results-oriented': ['Achieved [measurable outcome]', 'Delivered [specific metric]', 'Produced X% improvement'],
    'synergy':          ['Collaborated', 'Combined efforts to achieve', 'Aligned teams on'],
    'leverage':         ['Utilized', 'Applied', 'Built on existing'],
    'strategic':        ['Planned and executed', 'Designed approach to', 'Led initiative for'],
    'excellent':        ['Top-performing', 'High-impact', 'Demonstrated proficiency in'],
    'strong':           ['Proficient in', 'Demonstrated expertise in', 'Skilled in'],
    'dedicated':        ['Consistently delivered', 'Committed to [outcome]', 'Maintained focus on'],
};

// ========== ACTION VERBS ==========
const ACTION_VERBS = ['Led','Built','Delivered','Implemented','Achieved','Managed','Drove','Created','Optimized','Spearheaded','Launched','Reduced','Increased','Streamlined','Designed','Executed'];

// =============================================
// FLOATING POPUP (for clicks in the document)
// =============================================
let activeSuggestionEl = null;

function showSuggestionCard(anchorEl, type, context) {
    activeSuggestionEl = anchorEl;
    const card  = document.getElementById('ae-suggestion-card');
    const title = document.getElementById('ae-card-title');
    const body  = document.getElementById('ae-card-body');

    // Show loading state immediately
    const typeLabels = { metric: '\uD83D\uDCCA Improve Metric', passive: '\u26A1 Strengthen Verb', buzzword: '\uD83D\uDD04 Replace Buzzword' };
    title.innerHTML = typeLabels[type] || 'Improve';
    body.innerHTML = '<div class="ae-sc-loading" style="padding:16px 0;text-align:center;">'
        + '<svg style="animation:spin 1s linear infinite;width:18px;height:18px;display:inline-block;vertical-align:middle;margin-right:6px;" viewBox="0 0 24 24" fill="none" stroke="#2D6CDF" stroke-width="2"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>'
        + '<span style="color:#888;font-size:12px;">Getting AI suggestions\u2026</span></div>';

    // Position popup
    card.style.display = 'block';
    const rect = anchorEl.getBoundingClientRect();
    let top  = rect.bottom + 10 + window.scrollY;
    let left = rect.left + window.scrollX;
    card.style.top  = top + 'px';
    card.style.left = left + 'px';
    requestAnimationFrame(() => {
        const cr = card.getBoundingClientRect();
        if (cr.right  > window.innerWidth  - 12) card.style.left = (window.innerWidth  - cr.width  - 12) + 'px';
        if (cr.bottom > window.innerHeight - 12) card.style.top  = (rect.top - cr.height - 10 + window.scrollY) + 'px';
    });

    // Fetch AI suggestions
    const text = anchorEl.innerText.trim();
    const word = context.word || null;
    fetchAISuggestions(text, type, word)
        .then(suggestions => {
            if (!activeSuggestionEl) return; // closed before response
            renderPopupSuggestions(body, suggestions, type, word);
        })
        .catch(() => {
            // Use offline fallback
            const fallback = type === 'buzzword'
                ? (BUZZWORD_REPLACEMENTS[(word||'').toLowerCase()] || ['[Specific outcome]', '[Measurable result]', '[Concrete achievement]'])
                : ACTION_VERBS.slice(0, 3).map(v => v + ' ' + lcFirst(text));
            renderPopupSuggestions(body, fallback, type, word);
        });
}

function renderPopupSuggestions(body, suggestions, type, word) {
    let html = '<div style="font-size:11px;color:#888;margin-bottom:8px;">Click a suggestion to apply it:</div>';
    suggestions.forEach((s, i) => {
        html += '<div class="ae-sc-sug-item" style="margin-bottom:8px;">'
            + '<div style="font-size:12px;color:#0C0C0C;line-height:1.5;margin-bottom:6px;">' + escHtml(s) + '</div>'
            + '<div style="display:flex;gap:6px;">'
            + '<button class="ae-sc-apply" data-sug-idx="' + i + '" data-type="' + type + '">Apply</button>'
            + '<button class="ae-sc-sdismiss">Dismiss</button>'
            + '</div></div>';
    });
    html += '<div style="margin-top:6px;font-size:10px;color:#bbb;">Or click in the document to edit manually.</div>';
    body.innerHTML = html;

    // Attach events
    body.querySelectorAll('.ae-sc-apply').forEach((btn, i) => {
        btn.addEventListener('click', () => {
            applyPopupSuggestion(suggestions[i], type, word);
        });
    });
    body.querySelectorAll('.ae-sc-sdismiss').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.ae-sc-sug-item').remove());
    });
}

function applyPopupSuggestion(newText, type, word) {
    const el = activeSuggestionEl;
    if (!el) { closeSuggestionCard(); return; }

    if (type === 'buzzword' && word) {
        const regex = new RegExp('\\b' + word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'gi');
        el.innerText = el.innerText.replace(regex, newText);
    } else {
        el.innerText = newText;
    }
    markDirty(el);
    closeSuggestionCard();
    el.style.outline = '2px solid #1E8E3E';
    setTimeout(() => { el.style.outline = ''; el.classList.remove('ae-flag', 'ae-flag-bz'); }, 1800);
}

function closeSuggestionCard() {
    document.getElementById('ae-suggestion-card').style.display = 'none';
    activeSuggestionEl = null;
}

// =============================================
// LEFT PANEL SUGGESTION CARDS
// =============================================

async function fetchPanelSuggestions(type, idx, text, word) {
    const sugBoxId = type === 'buzzword' ? 'sc-sug-bz-' + idx
                   : type === 'passive'  ? 'sc-sug-vo-' + idx
                   : type === 'format'   ? 'sc-sug-fmt-' + idx
                   : 'sc-sug-metric-' + idx;
    const sugBox   = document.getElementById(sugBoxId);
    const actionEl = sugBox ? sugBox.nextElementSibling : null;

    if (!sugBox) return;

    // Show loading
    sugBox.style.display = 'block';
    sugBox.innerHTML = '<div class="ae-sc-loading">\u231B Getting AI suggestions\u2026</div>';
    if (actionEl) actionEl.style.display = 'none';

    const lookupText = text || (type === 'buzzword' ? findBuzzwordContext(word) : '');

    try {
        const suggestions = await fetchAISuggestions(lookupText, type, word);
        renderPanelSuggestions(sugBox, suggestions, type, word, lookupText, idx);
    } catch (e) {
        sugBox.innerHTML = '<div style="padding:8px;font-size:11px;color:#2D6CDF;">Could not get suggestions. Click a highlighted bullet in the document to try again.</div>';
        if (actionEl) actionEl.style.display = 'flex';
    }
}

function renderPanelSuggestions(container, suggestions, type, word, originalText, idx) {
    let html = '';
    suggestions.forEach((s, i) => {
        html += '<div class="ae-sc-sug-item">'
            + '<div style="font-size:11px;color:#0C0C0C;line-height:1.5;margin-bottom:6px;">' + escHtml(s) + '</div>'
            + '<div class="ae-sc-sug-btns">'
            + '<button class="ae-sc-apply" data-si="' + i + '">Apply</button>'
            + '<button class="ae-sc-sdismiss">Dismiss</button>'
            + '</div></div>';
    });
    container.innerHTML = html;

    container.querySelectorAll('.ae-sc-apply').forEach((btn, i) => {
        btn.addEventListener('click', () => {
            applyPanelSuggestion(suggestions[i], type, word, originalText, container);
        });
    });
    container.querySelectorAll('.ae-sc-sdismiss').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.ae-sc-sug-item').remove());
    });
}

function applyPanelSuggestion(newText, type, word, originalText, container) {
    // Find the element in the document that contains this text and apply the change
    let targetEl = findDocElementByText(originalText, type, word);

    if (targetEl) {
        if (type === 'buzzword' && word) {
            const regex = new RegExp('\\b' + word.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\b', 'gi');
            targetEl.innerText = targetEl.innerText.replace(regex, newText);
        } else {
            targetEl.innerText = newText;
        }
        markDirty(targetEl);
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        targetEl.style.outline = '2px solid #1E8E3E';
        setTimeout(() => { targetEl.style.outline = ''; targetEl.classList.remove('ae-flag', 'ae-flag-bz'); }, 2000);
    }

    // Remove the suggestion card from panel
    const parentCard = container.closest('.ae-sc');
    if (parentCard) {
        parentCard.style.transition = 'opacity .3s';
        parentCard.style.opacity = '0';
        setTimeout(() => parentCard.remove(), 300);
    }
}

// Find a contenteditable element in the document that matches the text
function findDocElementByText(text, type, word) {
    if (!text && !word) return null;
    const search = text ? text.substring(0, 35).toLowerCase() : word.toLowerCase();
    let found = null;
    document.querySelectorAll('[contenteditable="true"]').forEach(el => {
        if (!found && el.innerText.toLowerCase().includes(search)) {
            found = el;
        }
    });
    return found;
}

function findBuzzwordContext(word) {
    // Find the full sentence/bullet containing this buzzword
    let text = '';
    document.querySelectorAll('[contenteditable="true"]').forEach(el => {
        if (!text && el.innerText.toLowerCase().includes(word.toLowerCase())) {
            text = el.innerText.trim();
        }
    });
    return text || word;
}

// =============================================
// APPLY SECTION (for missing sections)
// =============================================
function applySection(sectionName, cardId) {
    // Map section name to action
    const name = sectionName.toLowerCase();
    if (name.includes('portfolio') || name.includes('website') || name.includes('link')) {
        // Add portfolio/website field to header
        const loc = document.getElementById('field-linkedin_url') || document.getElementById('field-location');
        if (loc) {
            loc.focus();
            loc.scrollIntoView({ behavior: 'smooth', block: 'center' });
            loc.style.outline = '2px solid #2D6CDF';
            setTimeout(() => loc.style.outline = '', 2000);
        }
        const url = prompt('Enter your portfolio or website URL:');
        if (url && url.trim()) {
            const field = document.getElementById('field-linkedin_url');
            if (field) { field.innerText = url.trim(); markDirty(field); }
        }
    } else if (name.includes('linkedin')) {
        const field = document.getElementById('field-linkedin_url');
        if (field) { field.focus(); field.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    } else if (name.includes('location')) {
        const field = document.getElementById('field-location');
        if (field) { field.focus(); field.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    } else if (name.includes('summary')) {
        const field = document.getElementById('field-professional_summary');
        if (field) { field.focus(); field.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
    }
    dismissCard(cardId);
}

function dismissCard(cardId) {
    const card = document.getElementById(cardId);
    if (card) {
        card.style.transition = 'opacity .25s';
        card.style.opacity = '0';
        setTimeout(() => card.remove(), 250);
    }
}

// =============================================
// AI FETCH HELPER
// =============================================
async function fetchAISuggestions(text, type, word) {
    const res = await fetch(SUGGEST_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: JSON.stringify({ text: text || word || '', type, word: word || '' }),
    });
    if (!res.ok) throw new Error('API error ' + res.status);
    const data = await res.json();
    if (!data.suggestions || !data.suggestions.length) throw new Error('Empty suggestions');
    return data.suggestions;
}

// =============================================
// BUZZWORD PANEL / DOC CLICK HANDLERS
// =============================================
function showBuzzwordFromPanel(word) {
    let targetEl = null;
    document.querySelectorAll('.ae-flag-bz[contenteditable]').forEach(el => {
        if (!targetEl && el.innerText.toLowerCase().includes(word.toLowerCase())) targetEl = el;
    });
    if (!targetEl) {
        const summaryEl = document.getElementById('field-professional_summary');
        if (summaryEl && summaryEl.innerText.toLowerCase().includes(word.toLowerCase())) targetEl = summaryEl;
    }
    if (targetEl) {
        targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => showSuggestionCard(targetEl, 'buzzword', { word }), 350);
    }
}

// =============================================
// MARK DIRTY / AUTO-SAVE
// =============================================
function markDirty(el) {
    el.setAttribute('data-dirty', 'true');
    document.getElementById('ae-save-btn').style.display = 'inline-block';
    document.getElementById('ae-saved-text').style.display = 'none';
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveResume, 3000);
}

function collectFields() {
    const data = {};
    document.querySelectorAll('[data-field][contenteditable]').forEach(el => {
        const field = el.getAttribute('data-field');
        const val = el.innerText.trim();
        if (field && val !== '' && val !== el.getAttribute('data-original')) {
            data[field] = val;
        }
    });

    const expEntries = document.querySelectorAll('.ae-exp-entry');
    if (expEntries.length > 0) {
        const experience = @json($resume->experience ?? []);
        expEntries.forEach((entry, idx) => {
            const bullets = entry.querySelectorAll('[data-type="exp-bullet"]');
            const desc    = entry.querySelector('[data-type="exp-desc"]');
            if (bullets.length > 0) {
                const lines = Array.from(bullets).map(b => '• ' + b.innerText.trim()).join('\n');
                if (experience[idx]) experience[idx].description = lines;
            } else if (desc) {
                if (experience[idx]) experience[idx].description = desc.innerText.trim();
            }
        });
        data.experience = experience;
    }

    data.skills = currentSkills;
    return data;
}

async function saveAndGoToDashboard() {
    await saveResume();
    window.location.href = '{{ route('dashboard') }}';
}

async function saveResume() {
    const btn    = document.getElementById('ae-save-btn');
    const saving = document.getElementById('ae-saving-text');
    const saved  = document.getElementById('ae-saved-text');

    btn.style.display    = 'none';
    saving.style.display = 'inline';
    saved.style.display  = 'none';

    try {
        const res = await fetch(SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(collectFields()),
        });
        const json = await res.json();
        if (json.success) {
            saving.style.display = 'none';
            saved.style.display  = 'inline';
            document.getElementById('ae-score-pill').textContent = json.score + '/100 ' + json.label;
            document.querySelectorAll('[data-dirty="true"]').forEach(el => el.removeAttribute('data-dirty'));

            // Refresh category bars live
            const cats = json.analysis?.categories ?? {};
            const catMeta = {
                content:  { max: 25 }, skills: { max: 20 }, format: { max: 20 },
                sections: { max: 20 }, style:  { max: 15 },
            };
            for (const [key, meta] of Object.entries(catMeta)) {
                const catData = cats[key] ?? {};
                const s = catData.score ?? 0;
                const m = catData.max ?? meta.max;
                const lbl = catData.label ?? '';
                const color = lbl === 'Excellent' || lbl === 'Pass' ? '#1E8E3E' : lbl === 'Good' ? '#fbbc04' : '#2D6CDF';
                const pct = m > 0 ? Math.round(s / m * 100) : 0;
                const bar = document.getElementById('cat-bar-' + key);
                const sc  = document.getElementById('cat-score-' + key);
                if (bar) { bar.style.width = pct + '%'; bar.style.background = color; }
                if (sc)  { sc.textContent = s + '/' + m; sc.style.color = color; }
            }

            // Refresh SVG score ring
            const ringCircle = document.querySelector('.ae-mini-score circle:nth-child(2)');
            const ringText   = document.querySelector('.ae-mini-score text:nth-child(1)');
            const newScore   = json.score;
            const newColor   = newScore >= 80 ? '#1E8E3E' : newScore >= 60 ? '#fbbc04' : '#2D6CDF';
            if (ringCircle) { ringCircle.setAttribute('stroke', newColor); ringCircle.setAttribute('stroke-dasharray', Math.round(newScore * 2.26) + ' 226'); }
            if (ringText)   { ringText.textContent = newScore; }

            setTimeout(() => { saved.style.display = 'none'; }, 3000);
        } else {
            saving.style.display = 'none';
            btn.style.display    = 'inline-block';
        }
    } catch (e) {
        saving.style.display = 'none';
        btn.style.display    = 'inline-block';
    }
}

function addBullet(expIdx) {
    const list = document.querySelector('#exp-' + expIdx + ' .ae-bullet-list');
    if (!list) return;
    const li = document.createElement('li');
    li.className = 'ae-field';
    li.contentEditable = 'true';
    li.setAttribute('data-exp-idx', expIdx);
    li.setAttribute('data-type', 'exp-bullet');
    li.style = 'padding:2px 4px;';
    li.textContent = 'Describe your achievement with numbers and impact\u2026';
    list.insertBefore(li, list.lastElementChild);
    li.focus();
    const range = document.createRange();
    range.selectNodeContents(li);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    li.addEventListener('input', () => markDirty(li));
    wireSuggestionClick(li);
}

function appendSkill(name, clickedEl) {
    if (!name || currentSkills.includes(name)) return;
    currentSkills.push(name);
    if (clickedEl) {
        const chip = clickedEl.closest ? clickedEl.closest('.ae-skill-chip') || clickedEl : clickedEl;
        chip.remove();
    }
    renderSkillChip(name);
    markDirty(document.getElementById('skills-container'));
}

function appendSkillFromInput() {
    const input = document.getElementById('new-skill-input');
    const name = input.value.trim();
    if (!name) return;
    appendSkill(name);
    input.value = '';
}

function removeSkill(el, name) {
    currentSkills = currentSkills.filter(s => s !== name);
    el.closest('.ae-skill-chip').remove();
    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveResume, 2000);
}

function renderSkillChip(name) {
    const container = document.getElementById('skills-container');
    const chip = document.createElement('span');
    chip.className = 'ae-skill-chip';
    chip.innerHTML = name + ' <span onclick="removeSkill(this, ' + JSON.stringify(name) + ')" title="Remove" style="color:#888;cursor:pointer;margin-left:4px;font-size:11px;">\xD7</span>';
    container.appendChild(chip);
}

function highlightText(text) {
    const doc = document.getElementById('ae-resume-doc');
    const walk = document.createTreeWalker(doc, NodeFilter.SHOW_TEXT);
    let node;
    while (node = walk.nextNode()) {
        const idx = node.textContent.indexOf(text.substring(0, 40));
        if (idx !== -1) {
            node.parentElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            node.parentElement.focus && node.parentElement.focus();
            node.parentElement.style.outline = '3px solid #fbbc04';
            setTimeout(() => node.parentElement.style.outline = '', 2000);
            break;
        }
    }
}

// =============================================
// WIRE SUGGESTION CLICKS ON DOC ELEMENTS
// =============================================
function wireSuggestionClick(el) {
    el.addEventListener('click', function (e) {
        e.stopPropagation();
        if (el.classList.contains('ae-flag-bz')) {
            const text = el.innerText.toLowerCase();
            const found = buzzwordsInDoc.find(bw => text.includes(bw.toLowerCase()));
            if (found) showSuggestionCard(el, 'buzzword', { word: found });
            return;
        }
        if (el.classList.contains('ae-flag')) {
            const text = el.innerText.toLowerCase();
            const isPassive = passiveFound.some(p => text.includes((p || '').toLowerCase().substring(0, 25)));
            showSuggestionCard(el, isPassive ? 'passive' : 'metric', {});
        }
    });
}

// =============================================
// UTILITIES
// =============================================
function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function lcFirst(str) { return str.charAt(0).toLowerCase() + str.slice(1); }

// Spinner keyframe
const styleEl = document.createElement('style');
styleEl.textContent = '@keyframes spin{to{transform:rotate(360deg)}}';
document.head.appendChild(styleEl);

// =============================================
// DOM READY
// =============================================
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[contenteditable="true"]').forEach(el => {
        el.setAttribute('data-original', el.innerText.trim());
        el.addEventListener('input', () => markDirty(el));
        el.addEventListener('keydown', e => {
            if (e.key === 'Enter' && !e.shiftKey) {
                const multiFields = ['professional_summary', 'exp-bullet', 'exp-desc'];
                const isMulti = multiFields.some(f => el.getAttribute('data-field') === f || el.getAttribute('data-type') === f);
                if (!isMulti) { e.preventDefault(); el.blur(); }
            }
        });
        wireSuggestionClick(el);
    });

    // Close popup on outside click
    document.addEventListener('click', function (e) {
        const card = document.getElementById('ae-suggestion-card');
        if (card && card.style.display !== 'none' && !card.contains(e.target)) {
            closeSuggestionCard();
        }
    });
});
</script>
@endsection
