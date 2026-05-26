@extends('layouts.app')

@section('title', 'ATS Resume Checker — ' . $resume->title)

@section('content')
@php
    $cats      = $analysis['categories'] ?? [];
    $score     = (int)($analysis['score'] ?? 0);
    $label     = $analysis['label'] ?? 'Fair';
    $color     = $score >= 80 ? '#34a853' : ($score >= 60 ? '#fbbc04' : '#ea4335');
    $ringDash  = round($score * 2.83);

    $cat_content  = $cats['content']  ?? [];
    $cat_skills   = $cats['skills']   ?? [];
    $cat_format   = $cats['format']   ?? [];
    $cat_sections = $cats['sections'] ?? [];
    $cat_style    = $cats['style']    ?? [];

    $mr  = $cat_content['measurable_results'] ?? [];
    $sg  = $cat_content['spelling_grammar']   ?? [];
    $hs  = $cat_skills['hard_skills']         ?? [];
    $ss  = $cat_skills['soft_skills']         ?? [];
    $df  = $cat_format['date_formatting']     ?? [];
    $rl  = $cat_format['resume_length']       ?? [];
    $bp  = $cat_format['bullet_points']       ?? [];
    $vo  = $cat_style['voice']                ?? [];
    $bz  = $cat_style['buzzwords']            ?? [];

    $sugg = [
        'content'  => (int)(!($mr['pass'] ?? true)) + (int)(!($sg['pass'] ?? true)),
        'skills'   => (int)(!($hs['pass'] ?? true)) + (int)(!($ss['pass'] ?? true)),
        'format'   => (int)(!($df['pass'] ?? true)) + (int)(!($rl['pass'] ?? true)) + (int)(!($bp['pass'] ?? true)),
        'sections' => count($cat_sections['missing'] ?? []),
        'style'    => (int)(!($vo['pass'] ?? true)) + (int)(!($bz['pass'] ?? true)),
    ];
    $totalSuggestions = array_sum($sugg);

    function atsColor(string $lbl): string {
        return match($lbl) { 'Excellent', 'Pass' => '#34a853', 'Good' => '#fbbc04', default => '#ea4335' };
    }
@endphp

<style>
.ats-wrap{display:flex;gap:0;min-height:100vh;background:#f0f4f8;}
.ats-sidebar{width:300px;min-width:280px;background:#fff;border-right:1px solid #e8eaed;padding:24px 20px;position:sticky;top:60px;height:calc(100vh - 60px);overflow-y:auto;flex-shrink:0;}
.ats-main{flex:1;padding:28px 32px;overflow-y:auto;}
.ats-card{background:#fff;border-radius:14px;box-shadow:0 1px 8px rgba(0,0,0,.07);overflow:hidden;margin-bottom:20px;}
.ats-card-header{padding:16px 22px;border-bottom:1px solid #f0f0f0;display:flex;align-items:center;justify-content:space-between;}
.ats-card-header h3{margin:0;font-size:15px;font-weight:700;color:#1a1a2e;display:flex;align-items:center;gap:10px;}
.ats-card-body{padding:20px 22px;}
.ats-chip{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;margin:2px;white-space:nowrap;}
.chip-green{background:#e6f4ea;color:#137333;}
.chip-red{background:#fce8e6;color:#c5221f;}
.chip-blue{background:#e8f0fe;color:#1557b0;}
.ats-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f5f5f5;}
.ats-row:last-child{border-bottom:none;}
.cat-bar-wrap{display:flex;align-items:center;gap:8px;min-width:80px;}
.cat-bar-bg{flex:1;height:5px;background:#eee;border-radius:3px;overflow:hidden;}
.cat-bar-fg{height:100%;border-radius:3px;}
</style>

{{-- Top bar --}}
<div style="background:#1A73E8;padding:0 32px;display:flex;align-items:center;justify-content:space-between;gap:12px;position:sticky;top:0;z-index:50;box-shadow:0 2px 8px rgba(0,0,0,.15);">
    <div style="display:flex;align-items:center;gap:0;">
        <a href="{{ route('resume.edit', $resume) }}" style="color:rgba(255,255,255,.8);text-decoration:none;font-size:13px;padding:18px 16px 18px 0;display:flex;align-items:center;gap:6px;white-space:nowrap;">
            ← Back
        </a>
        <span style="color:rgba(255,255,255,.4);font-size:13px;margin-right:16px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $resume->title }}</span>
        <span style="color:#fff;font-size:13px;font-weight:700;padding:18px 20px;border-bottom:3px solid #fff;cursor:default;">① Report</span>
        <a href="{{ route('resume.ats.editor', $resume) }}" style="color:rgba(255,255,255,.7);font-size:13px;padding:18px 20px;text-decoration:none;border-bottom:3px solid transparent;">② Resume</a>
        <a href="{{ route('resume.cover-letter.show', $resume) }}" style="color:rgba(255,255,255,.7);font-size:13px;padding:18px 20px;text-decoration:none;border-bottom:3px solid transparent;">③ Cover Letter</a>
    </div>
    <form method="POST" action="{{ route('resume.ats.run', $resume) }}">
        @csrf
        <button type="submit" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:8px;padding:8px 18px;font-size:13px;font-weight:600;cursor:pointer;">
            ↻ Re-scan
        </button>
    </form>
</div>

<div class="ats-wrap">

    {{-- SIDEBAR --}}
    <div class="ats-sidebar">
        <div style="text-align:center;padding:16px 0 12px;">
            <svg width="120" height="120" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="50" fill="none" stroke="#eee" stroke-width="10"/>
                <circle cx="60" cy="60" r="50" fill="none"
                    stroke="{{ $color }}" stroke-width="10"
                    stroke-dasharray="{{ $ringDash }} 314"
                    stroke-linecap="round"
                    transform="rotate(-90 60 60)"/>
                <text x="60" y="55" text-anchor="middle" font-size="28" font-weight="800" fill="#1a1a2e">{{ $score }}</text>
                <text x="60" y="70" text-anchor="middle" font-size="12" fill="#888">/100</text>
            </svg>
            <div style="font-size:20px;font-weight:800;color:{{ $color }};margin-top:4px;">{{ $label }}</div>
            <div style="font-size:12px;color:#888;margin-top:2px;">{{ $totalSuggestions }} suggestion{{ $totalSuggestions !== 1 ? 's' : '' }}</div>
            @if($score >= 75)
                <div style="font-size:11px;color:#34a853;margin-top:8px;background:#e6f4ea;border-radius:8px;padding:8px 10px;line-height:1.5;">
                    Resumes with 75+ score get 3× more interviews
                </div>
            @else
                <div style="font-size:11px;color:#888;margin-top:8px;line-height:1.5;">Improve your score to land more interviews</div>
            @endif
        </div>

        <hr style="border:none;border-top:1px solid #f0f0f0;margin:12px 0;">

        @php
            $catAnchors = ['Content'=>'content','Skills'=>'skills','Format'=>'format','Sections'=>'sections','Style'=>'style'];
        @endphp
        @foreach([
            ['Content',  $sugg['content'],  $cat_content['score']  ?? 0, $cat_content['max']  ?? 25, $cat_content['label']  ?? 'Fair'],
            ['Skills',   $sugg['skills'],   $cat_skills['score']   ?? 0, $cat_skills['max']   ?? 20, $cat_skills['label']   ?? 'Fair'],
            ['Format',   $sugg['format'],   $cat_format['score']   ?? 0, $cat_format['max']   ?? 20, $cat_format['label']   ?? 'Fair'],
            ['Sections', $sugg['sections'], $cat_sections['score'] ?? 0, $cat_sections['max'] ?? 20, $cat_sections['label'] ?? 'Fair'],
            ['Style',    $sugg['style'],    $cat_style['score']    ?? 0, $cat_style['max']    ?? 15, $cat_style['label']    ?? 'Fair'],
        ] as [$cName, $cSugg, $cScore, $cMax, $cLabel])
            @php $cPct = $cMax > 0 ? round($cScore / $cMax * 100) : 0; $cColor = atsColor($cLabel); @endphp
            <div class="ats-row" onclick="document.getElementById('ats-card-{{ strtolower($cName) }}')?.scrollIntoView({behavior:'smooth',block:'start'})" style="cursor:pointer;" title="View {{ $cName }} details">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#1a1a2e;">{{ $cName }}</div>
                    @if($cSugg > 0)
                        <a href="{{ route('resume.ats.editor', $resume) }}" style="font-size:11px;color:#ea4335;text-decoration:none;font-weight:600;">{{ $cSugg }} suggestion{{ $cSugg !== 1 ? 's' : '' }} →</a>
                    @else
                        <div style="font-size:11px;color:#34a853;">Complete ✔</div>
                    @endif
                </div>
                <div class="cat-bar-wrap">
                    <div class="cat-bar-bg">
                        <div class="cat-bar-fg" style="width:{{ $cPct }}%;background:{{ $cColor }};"></div>
                    </div>
                    <span style="font-size:11px;font-weight:700;color:{{ $cColor }};min-width:36px;text-align:right;">{{ $cScore }}/{{ $cMax }}</span>
                </div>
            </div>
        @endforeach

        <hr style="border:none;border-top:1px solid #f0f0f0;margin:12px 0;">

        @if(!empty($analysis['summary']))
            <div style="margin-bottom:12px;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#aaa;margin-bottom:6px;">Overview</div>
                <div style="font-size:12px;color:#555;line-height:1.6;">{{ $analysis['summary'] }}</div>
            </div>
        @endif

        @if(!empty($analysis['highlights']))
            <div style="margin-bottom:12px;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#aaa;margin-bottom:6px;">Highlights</div>
                @foreach($analysis['highlights'] as $h)
                    <div style="font-size:12px;color:#137333;display:flex;align-items:flex-start;gap:6px;margin-bottom:4px;"><span>✔</span><span>{{ $h }}</span></div>
                @endforeach
            </div>
        @endif

        @if(!empty($analysis['recommendations']))
            <div style="margin-bottom:16px;">
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#aaa;margin-bottom:6px;">Quick Wins</div>
                @foreach(array_slice($analysis['recommendations'], 0, 3) as $r)
                    <div style="font-size:12px;color:#c5221f;display:flex;align-items:flex-start;gap:6px;margin-bottom:4px;"><span>→</span><span>{{ $r }}</span></div>
                @endforeach
            </div>
        @endif

        <a href="{{ route('resume.edit', $resume) }}" style="display:block;background:#1A73E8;color:#fff;text-align:center;padding:11px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;margin-bottom:8px;">✏️ Edit Resume</a>
        <a href="{{ route('resume.preview', $resume) }}" target="_blank" style="display:block;background:#f0f4f8;color:#1A73E8;text-align:center;padding:11px;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;border:1px solid #d0e0ff;">👁 Preview</a>
    </div>

    {{-- MAIN PANEL --}}
    <div class="ats-main">

        {{-- CONTENT CARD --}}
        <div class="ats-card" id="ats-card-content">
            <div class="ats-card-header">
                <h3>📄 Content
                    @if($sugg['content'] > 0)
                        <span style="background:#fce8e6;color:#c5221f;border-radius:20px;font-size:11px;padding:2px 10px;">{{ $sugg['content'] }} suggestion{{ $sugg['content'] !== 1 ? 's' : '' }}</span>
                    @else
                        <span style="background:#e6f4ea;color:#137333;border-radius:20px;font-size:11px;padding:2px 10px;">PASS</span>
                    @endif
                </h3>
                <span style="font-size:12px;color:#999;">Impact & phrasing</span>
            </div>
            <div class="ats-card-body">
                {{-- Measurable Results --}}
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="font-size:14px;font-weight:700;">Measurable Results</div>
                        @if($mr['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ Pass</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Improve</span>
                        @endif
                    </div>
                    <div style="font-size:12px;color:#666;margin-bottom:8px;">{{ $mr['count'] ?? 0 }} quantified bullet{{ ($mr['count'] ?? 0) !== 1 ? 's' : '' }} found. Add numbers (%, $, ×) to show impact.</div>
                    @foreach(array_slice($mr['suggestions'] ?? [], 0, 3) as $s)
                        <div style="background:#fff8f0;border-left:3px solid #fbbc04;padding:7px 12px;border-radius:0 6px 6px 0;margin-bottom:5px;font-size:12px;color:#555;font-style:italic;">"{{ $s }}"</div>
                    @endforeach
                    @if(!($mr['pass'] ?? true))
                        <div style="margin-top:8px;">
                            <a href="{{ route('resume.ats.editor', $resume) }}#section-experience" style="display:inline-flex;align-items:center;gap:5px;background:#1A73E8;color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">✏️ Fix in Editor →</a>
                        </div>
                    @endif
                </div>

                {{-- Spelling & Grammar --}}
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="font-size:14px;font-weight:700;">Spelling & Grammar</div>
                        @if($sg['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ Pass</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Improve</span>
                        @endif
                    </div>
                    @forelse($sg['issues'] ?? [] as $issue)
                        <div style="background:#fce8e6;border-left:3px solid #ea4335;padding:6px 12px;border-radius:0 6px 6px 0;font-size:12px;color:#c5221f;margin-bottom:4px;">{{ $issue }}</div>
                    @empty
                        <div style="font-size:12px;color:#137333;">No obvious issues detected.</div>
                    @endforelse
                    @if(!($sg['pass'] ?? true))
                        <div style="margin-top:8px;">
                            <a href="{{ route('resume.ats.editor', $resume) }}#section-summary" style="display:inline-flex;align-items:center;gap:5px;background:#1A73E8;color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">✏️ Fix in Editor →</a>
                        </div>
                    @endif
                </div>

                {{-- Summary preview --}}
                @php $sumText = $resume->professional_summary ?? null; @endphp
                @if($sumText)
                    <div style="padding:12px 0;">
                        <div style="font-size:14px;font-weight:700;margin-bottom:6px;">Professional Summary</div>
                        <div style="font-size:12px;color:#555;font-style:italic;border-left:3px solid #1A73E8;padding:8px 12px;background:#f0f4ff;border-radius:0 6px 6px 0;">"{{ Str::limit($sumText, 220) }}"</div>
                        <div style="margin-top:8px;">
                            <a href="{{ route('resume.ats.editor', $resume) }}#section-summary" style="display:inline-flex;align-items:center;gap:5px;background:#f0f4ff;color:#1A73E8;border:1px solid #1A73E8;font-size:12px;font-weight:700;padding:5px 12px;border-radius:6px;text-decoration:none;">✏️ Edit Summary →</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- SKILLS CARD --}}
        <div class="ats-card" id="ats-card-skills">
            <div class="ats-card-header">
                <h3>🛠 Skills
                    @if($sugg['skills'] > 0)
                        <span style="background:#fce8e6;color:#c5221f;border-radius:20px;font-size:11px;padding:2px 10px;">{{ $sugg['skills'] }} suggestion{{ $sugg['skills'] !== 1 ? 's' : '' }}</span>
                    @else
                        <span style="background:#e6f4ea;color:#137333;border-radius:20px;font-size:11px;padding:2px 10px;">PASS</span>
                    @endif
                </h3>
                <span style="font-size:12px;color:#999;">Hard & soft skills</span>
            </div>
            <div class="ats-card-body">
                {{-- Hard Skills --}}
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div style="font-size:14px;font-weight:700;">Hard Skills</div>
                        @if($hs['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ {{ $hs['count'] ?? 0 }} found</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Only {{ $hs['count'] ?? 0 }} found</span>
                        @endif
                    </div>
                    @if(!empty($hs['found']))
                        <div style="margin-bottom:8px;">
                            @foreach($hs['found'] as $sk)<span class="ats-chip chip-green">{{ $sk }}</span>@endforeach
                        </div>
                    @endif
                    @if(!empty($hs['missing']))
                        <div style="margin-top:6px;">
                            <div style="font-size:11px;color:#888;margin-bottom:4px;">Consider adding:</div>
                            @foreach(array_slice($hs['missing'], 0, 8) as $sk)<span class="ats-chip chip-red">+ {{ $sk }}</span>@endforeach
                        </div>
                    @endif
                    @if(!($hs['pass'] ?? true))
                        <div style="margin-top:8px;">
                            <a href="{{ route('resume.ats.editor', $resume) }}#section-skills" style="display:inline-flex;align-items:center;gap:5px;background:#1A73E8;color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">✏️ Add Skills →</a>
                        </div>
                    @endif
                </div>

                {{-- Soft Skills --}}
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div style="font-size:14px;font-weight:700;">Soft Skills</div>
                        @if($ss['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ {{ $ss['count'] ?? 0 }} found</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ {{ $ss['count'] ?? 0 }} found</span>
                        @endif
                    </div>
                    @if(!empty($ss['found']))
                        @foreach($ss['found'] as $sk)<span class="ats-chip chip-blue">{{ $sk }}</span>@endforeach
                    @endif
                </div>

                {{-- Listed Skills --}}
                @php $listedSkills = $resume->flat_skills ?? []; @endphp
                @if(!empty($listedSkills))
                    <div style="padding:12px 0;">
                        <div style="font-size:14px;font-weight:700;margin-bottom:8px;">Your Listed Skills</div>
                        @foreach($listedSkills as $sk)<span class="ats-chip" style="background:#f0f4f8;color:#555;">{{ $sk }}</span>@endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- FORMAT CARD --}}
        <div class="ats-card" id="ats-card-format">
            <div class="ats-card-header">
                <h3>📐 Format
                    @if($sugg['format'] > 0)
                        <span style="background:#fce8e6;color:#c5221f;border-radius:20px;font-size:11px;padding:2px 10px;">{{ $sugg['format'] }} suggestion{{ $sugg['format'] !== 1 ? 's' : '' }}</span>
                    @else
                        <span style="background:#e6f4ea;color:#137333;border-radius:20px;font-size:11px;padding:2px 10px;">PASS</span>
                    @endif
                </h3>
                <span style="font-size:12px;color:#999;">Dates, length & bullets</span>
            </div>
            <div class="ats-card-body">
                {{-- Date Formatting --}}
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="font-size:14px;font-weight:700;">Date Formatting</div>
                        @if($df['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ Consistent</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Inconsistent</span>
                        @endif
                    </div>
                    @if(!empty($df['issue']))
                        <div style="font-size:12px;color:#c5221f;">{{ $df['issue'] }}</div>
                    @else
                        <div style="font-size:12px;color:#137333;">Dates are consistently formatted.</div>
                    @endif
                </div>

                {{-- Resume Length --}}
                @php $wc = (int)($rl['word_count'] ?? $resume->getWordCount() ?? 0); $wcPct = min(100, round($wc / 900 * 100)); $wcOk = $wc >= 200 && $wc <= 900; @endphp
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
                        <div style="font-size:14px;font-weight:700;">Resume Length</div>
                        <span style="color:{{ $wcOk ? '#34a853' : '#ea4335' }};font-size:13px;font-weight:600;">{{ $wcOk ? '✔' : '✖' }} {{ $wc }} words</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:10px;color:#aaa;margin-bottom:4px;"><span>0</span><span>200 min</span><span>900 max</span></div>
                    <div style="height:8px;background:#eee;border-radius:4px;overflow:hidden;margin-bottom:6px;">
                        <div style="width:{{ $wcPct }}%;height:100%;background:{{ $wcOk ? '#34a853' : '#ea4335' }};border-radius:4px;"></div>
                    </div>
                    @if(!empty($rl['issue']))<div style="font-size:12px;color:#c5221f;">{{ $rl['issue'] }}</div>@endif
                </div>

                {{-- Bullet Points --}}
                <div style="padding:12px 0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="font-size:14px;font-weight:700;">Bullet Points</div>
                        @if($bp['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ Good</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Add bullets</span>
                        @endif
                    </div>
                    @foreach(array_slice($bp['suggestions'] ?? [], 0, 2) as $s)
                        <div style="font-size:12px;color:#555;background:#f8f8f8;padding:6px 10px;border-radius:6px;margin-bottom:4px;">{{ $s }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SECTIONS CARD --}}
        <div class="ats-card" id="ats-card-sections">
            <div class="ats-card-header">
                <h3>📋 Sections
                    @if($sugg['sections'] > 0)
                        <span style="background:#fce8e6;color:#c5221f;border-radius:20px;font-size:11px;padding:2px 10px;">{{ $sugg['sections'] }} missing</span>
                    @else
                        <span style="background:#e6f4ea;color:#137333;border-radius:20px;font-size:11px;padding:2px 10px;">PASS</span>
                    @endif
                </h3>
                <span style="font-size:12px;color:#999;">Required resume sections</span>
            </div>
            <div class="ats-card-body">
                @php
                    $sectionChecks = [
                        ['Name',        !empty($resume->full_name)],
                        ['Email',       !empty($resume->email)],
                        ['Phone',       !empty($resume->phone)],
                        ['Location',    !empty($resume->location)],
                        ['LinkedIn',    !empty($resume->linkedin_url)],
                        ['Summary',     !empty($resume->professional_summary)],
                        ['Experience',  !empty($resume->experience)],
                        ['Education',   !empty($resume->education)],
                        ['Skills',      !empty($resume->flat_skills)],
                    ];
                @endphp
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:16px;">
                    @foreach($sectionChecks as [$secName, $secPass])
                        <div style="display:flex;align-items:center;gap:8px;padding:10px 12px;border-radius:8px;background:{{ $secPass ? '#f0faf5' : '#fff5f5' }};border:1px solid {{ $secPass ? '#c3e6cb' : '#f5c6cb' }};">
                            <span style="font-size:16px;line-height:1;">{{ $secPass ? '✔' : '✖' }}</span>
                            <span style="font-size:13px;font-weight:600;color:{{ $secPass ? '#137333' : '#c5221f' }};">{{ $secName }}</span>
                        </div>
                    @endforeach
                </div>
                @if(!empty($cat_sections['missing']))
                    <div style="padding:12px 16px;background:#fff8f0;border-radius:8px;border-left:3px solid #fbbc04;margin-bottom:12px;">
                        <div style="font-size:12px;font-weight:700;color:#b06000;margin-bottom:6px;">Add these missing sections:</div>
                        @foreach($cat_sections['missing'] as $m)
                            <div style="font-size:12px;color:#555;padding:2px 0;">→ {{ $m }}</div>
                        @endforeach
                    </div>
                    <a href="{{ route('resume.ats.editor', $resume) }}#section-header" style="display:inline-flex;align-items:center;gap:5px;background:#1A73E8;color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">✏️ Fix Missing Sections →</a>
                @endif
            </div>
        </div>

        {{-- STYLE CARD --}}
        <div class="ats-card" id="ats-card-style">
            <div class="ats-card-header">
                <h3>✍️ Style
                    @if($sugg['style'] > 0)
                        <span style="background:#fce8e6;color:#c5221f;border-radius:20px;font-size:11px;padding:2px 10px;">{{ $sugg['style'] }} suggestion{{ $sugg['style'] !== 1 ? 's' : '' }}</span>
                    @else
                        <span style="background:#e6f4ea;color:#137333;border-radius:20px;font-size:11px;padding:2px 10px;">PASS</span>
                    @endif
                </h3>
                <span style="font-size:12px;color:#999;">Voice & word choice</span>
            </div>
            <div class="ats-card-body">
                {{-- Voice --}}
                <div style="padding:12px 0;border-bottom:1px solid #f5f5f5;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="font-size:14px;font-weight:700;">Active Voice</div>
                        @if($vo['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ Pass</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Use action verbs</span>
                        @endif
                    </div>
                    @if(!empty($vo['note']))<div style="font-size:12px;color:#666;margin-bottom:8px;">{{ $vo['note'] }}</div>@endif
                    @foreach(array_slice($vo['found'] ?? [], 0, 3) as $phrase)
                        <div style="background:#fce8e6;border-left:3px solid #ea4335;padding:6px 10px;border-radius:0 4px 4px 0;font-size:12px;color:#c5221f;margin-bottom:4px;font-style:italic;">"{{ $phrase }}"</div>
                    @endforeach
                    @if(!($vo['pass'] ?? true))
                        <div style="margin-top:8px;">
                            <a href="{{ route('resume.ats.editor', $resume) }}#section-experience" style="display:inline-flex;align-items:center;gap:5px;background:#1A73E8;color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">✏️ Fix in Editor →</a>
                        </div>
                    @endif
                </div>

                {{-- Buzzwords --}}
                <div style="padding:12px 0;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <div style="font-size:14px;font-weight:700;">Buzzwords</div>
                        @if($bz['pass'] ?? true)
                            <span style="color:#34a853;font-size:13px;font-weight:600;">✔ Pass</span>
                        @else
                            <span style="color:#ea4335;font-size:13px;font-weight:600;">✖ Replace vague words</span>
                        @endif
                    </div>
                    @if(!empty($bz['found']))
                        <div style="margin-bottom:6px;">
                            <div style="font-size:11px;color:#888;margin-bottom:4px;">Overused — replace with specific achievements:</div>
                            @foreach($bz['found'] as $w)<span class="ats-chip chip-red">{{ $w }}</span>@endforeach
                        </div>
                        <div style="margin-top:8px;">
                            <a href="{{ route('resume.ats.editor', $resume) }}#section-summary" style="display:inline-flex;align-items:center;gap:5px;background:#1A73E8;color:#fff;font-size:12px;font-weight:700;padding:6px 14px;border-radius:6px;text-decoration:none;">✏️ Fix in Editor →</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Recommendations --}}
        @if(!empty($analysis['recommendations']))
            <div class="ats-card">
                <div class="ats-card-header"><h3>💡 All Recommendations</h3></div>
                <div class="ats-card-body">
                    @foreach($analysis['recommendations'] as $i => $rec)
                        <div style="display:flex;gap:10px;padding:8px 0;border-bottom:1px solid #f5f5f5;">
                            <span style="color:#1A73E8;font-weight:700;">{{ $i + 1 }}.</span>
                            <span style="font-size:13px;color:#444;">{{ $rec }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div style="font-size:11px;color:#bbb;text-align:center;padding:20px 0 40px;">
            ATS analysis is rule-based and does not require an internet connection. Results may vary by employer's ATS system.
        </div>
    </div>
</div>
@endsection
