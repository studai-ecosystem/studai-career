<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
@php
    use Illuminate\Support\Str;
    $tpl      = $template ?? $resume->template;
    $colors   = $tpl->color_scheme ?? ['primary'=>'#0C0C0C'];
    $primary  = $colors['primary'] ?? '#0C0C0C';
    $slug     = $tpl->slug ?? 'professional-classic';
    $isTwoCol = in_array($slug, ['modern-tech','creative-portfolio','healthcare-professional']);
    $isMinimal = $slug === 'minimalist';

    if (!function_exists('pdfHexTint')) {
        function pdfHexTint(string $hex, float $opacity): string {
            $hex = ltrim($hex, '#');
            if (strlen($hex) === 3) { $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2]; }
            $r = (int) round(hexdec(substr($hex,0,2)) * $opacity + 255*(1-$opacity));
            $g = (int) round(hexdec(substr($hex,2,2)) * $opacity + 255*(1-$opacity));
            $b = (int) round(hexdec(substr($hex,4,2)) * $opacity + 255*(1-$opacity));
            return "rgb($r,$g,$b)";
        }
    }
    $primaryLight = pdfHexTint($primary, 0.10);
    $badgeBg      = pdfHexTint($primary, 0.12);
    $borderLight  = pdfHexTint($primary, 0.28);
    $nameParts    = explode(' ', trim($resume->full_name ?? 'U'));
    $initials     = strtoupper(substr($nameParts[0],0,1)).(isset($nameParts[1]) ? strtoupper(substr($nameParts[1],0,1)) : '');
@endphp
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size:9.5pt; color:#0C0C0C; line-height:1.45; background:#fff; }
.st { color:{{ $primary }}; border-bottom:1.5pt solid {{ $primary }}; padding-bottom:2pt; margin-bottom:7pt; font-size:9pt; font-weight:bold; letter-spacing:1pt; text-transform:uppercase; }
.badge { display:inline-block; padding:1.5pt 6pt; border-radius:10pt; font-size:7.5pt; background:{{ $badgeBg }}; color:{{ $primary }}; border:0.5pt solid {{ $borderLight }}; margin:1.5pt 2pt 1.5pt 0; }
table { border-collapse:collapse; }
</style>
</head>
<body>

@if($isTwoCol)
{{-- TWO-COLUMN LAYOUT --}}
<table width="100%"><tr>

{{-- SIDEBAR --}}
<td width="30%" style="background:{{ $primary }};padding:18pt 12pt;vertical-align:top;">
<div style="width:52pt;height:52pt;border-radius:26pt;background:rgba(255,255,255,0.2);text-align:center;line-height:52pt;font-size:18pt;font-weight:bold;color:#fff;margin:0 auto 8pt auto;">{{ $initials }}</div>
<p style="text-align:center;font-size:13pt;font-weight:bold;color:#fff;margin-bottom:3pt;">{{ $resume->full_name }}</p>
@if($resume->professional_summary)
<p style="text-align:center;font-size:7.5pt;color:rgba(255,255,255,0.8);margin-bottom:10pt;line-height:1.4;">{{ Str::limit($resume->professional_summary, 180) }}</p>
@endif
<p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-bottom:5pt;">Contact</p>
@if($resume->email)
<p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">{{ $resume->email }}</p>
@endif
@if($resume->phone)
<p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">{{ $resume->phone }}</p>
@endif
@if($resume->location)
<p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">{{ $resume->location }}</p>
@endif
@if($resume->linkedin_url)
<p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">LinkedIn</p>
@endif
@if($resume->github_url)
<p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">GitHub</p>
@endif
@if($resume->portfolio_url)
<p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:6pt;">Portfolio</p>
@endif
@if(!empty($resume->skills) && count($resume->skills) > 0)
<p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-top:8pt;margin-bottom:5pt;">Skills</p>
@foreach($resume->skills as $skill)
<span style="display:inline-block;background:rgba(255,255,255,0.18);color:#fff;font-size:7pt;padding:1.5pt 5pt;border-radius:3pt;margin:1.5pt 2pt 1.5pt 0;">{{ $skill }}</span>
@endforeach
@endif
@if(!empty($resume->languages) && count($resume->languages) > 0)
<p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-top:10pt;margin-bottom:5pt;">Languages</p>
@foreach($resume->languages as $lang)
<table width="100%" style="margin-bottom:2pt;"><tr>
<td style="font-size:7.5pt;color:#fff;">{{ $lang['name'] ?? '' }}</td>
<td style="font-size:7.5pt;color:rgba(255,255,255,0.7);text-align:right;">{{ $lang['proficiency'] ?? '' }}</td>
</tr></table>
@endforeach
@endif
@if(!empty($resume->certifications) && count($resume->certifications) > 0)
<p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-top:10pt;margin-bottom:5pt;">Certifications</p>
@foreach($resume->certifications as $cert)
<div style="margin-bottom:5pt;">
<p style="font-size:7.5pt;font-weight:bold;color:#fff;line-height:1.3;">{{ $cert['name'] ?? '' }}</p>
<p style="font-size:7pt;color:rgba(255,255,255,0.7);">{{ $cert['issuer'] ?? '' }}
@if(!empty($cert['issue_date']))
 &bull; {{ $cert['issue_date'] }}
@endif
</p>
</div>
@endforeach
@endif
</td>

{{-- MAIN CONTENT --}}
<td style="padding:18pt 16pt;vertical-align:top;background:#fff;">
@if(!empty($resume->experience) && count($resume->experience) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Work Experience</p>
@foreach($resume->experience as $exp)
<div style="margin-bottom:9pt;">
<table width="100%"><tr>
<td style="vertical-align:top;">
<p style="font-weight:bold;font-size:10pt;color:#111;">{{ $exp['position'] ?? '' }}</p>
<p style="font-size:8.5pt;font-weight:bold;color:{{ $primary }};">{{ $exp['company'] ?? '' }}
@if(!empty($exp['location']))
 &mdash; {{ $exp['location'] }}
@endif
</p>
</td>
<td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
{{ $exp['start_date'] ?? '' }}
@if(!empty($exp['end_date']))
 &ndash; {{ $exp['end_date'] }}
@endif
@if(!empty($exp['employment_type']))
<br><em>{{ $exp['employment_type'] }}</em>
@endif
</td>
</tr></table>
@if(!empty($exp['description']))
<p style="font-size:8.5pt;color:#444;margin-top:3pt;line-height:1.4;">{{ $exp['description'] }}</p>
@endif
@if(!empty($exp['achievements']) && is_array($exp['achievements']))
@foreach($exp['achievements'] as $expAch)
@if(!empty(trim($expAch)))
<table style="margin-top:2pt;" width="100%"><tr>
<td style="width:8pt;vertical-align:top;padding-top:3pt;"><div style="width:5pt;height:5pt;border-radius:3pt;background:{{ $primary }};"></div></td>
<td style="font-size:8.5pt;color:#333;line-height:1.4;">{{ $expAch }}</td>
</tr></table>
@endif
@endforeach
@endif
@if(!empty($exp['technologies']))
<p style="font-size:7.5pt;color:#888;margin-top:2pt;font-style:italic;">Technologies: {{ $exp['technologies'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
@if(!empty($resume->education) && count($resume->education) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Education</p>
@foreach($resume->education as $edu)
<div style="margin-bottom:7pt;">
<table width="100%"><tr>
<td style="vertical-align:top;">
<p style="font-weight:bold;font-size:10pt;color:#111;">{{ $edu['degree'] ?? '' }}</p>
<p style="font-size:8.5pt;font-weight:bold;color:{{ $primary }};">{{ $edu['institution'] ?? '' }}
@if(!empty($edu['location']))
, {{ $edu['location'] }}
@endif
</p>
@if(!empty($edu['gpa']))
<p style="font-size:7.5pt;color:#666;">GPA: {{ $edu['gpa'] }}
@if(!empty($edu['honors']))
 &bull; {{ $edu['honors'] }}
@endif
</p>
@endif
</td>
<td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
{{ $edu['start_year'] ?? '' }}
@if(!empty($edu['end_year']))
 &ndash; {{ $edu['end_year'] }}
@endif
</td>
</tr></table>
</div>
@endforeach
</div>
@endif
@if(!empty($resume->projects) && count($resume->projects) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Projects</p>
@foreach($resume->projects as $proj)
<div style="margin-bottom:7pt;">
<table width="100%"><tr>
<td><p style="font-weight:bold;font-size:9.5pt;color:#111;">{{ $proj['name'] ?? '' }}</p></td>
<td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;">
@if(!empty($proj['start_date']))
{{ $proj['start_date'] }}
@if(!empty($proj['end_date']))
 &ndash; {{ $proj['end_date'] }}
@endif
@endif
</td>
</tr></table>
@if(!empty($proj['description']))
<p style="font-size:8.5pt;color:#444;margin-top:2pt;line-height:1.4;">{{ $proj['description'] }}</p>
@endif
@if(!empty($proj['technologies']))
<p style="font-size:7.5pt;color:#888;font-style:italic;">{{ $proj['technologies'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
@if(!empty($resume->achievements) && count($resume->achievements) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Achievements &amp; Awards</p>
@foreach($resume->achievements as $ach2)
<div style="margin-bottom:5pt;">
<table width="100%"><tr>
<td>
<p style="font-weight:bold;font-size:9pt;color:#111;">{{ $ach2['title'] ?? '' }}
@if(!empty($ach2['issuer']))
<span style="font-weight:normal;color:#555;font-size:8.5pt;"> &bull; {{ $ach2['issuer'] }}</span>
@endif
</p>
</td>
<td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;">{{ $ach2['date'] ?? '' }}</td>
</tr></table>
@if(!empty($ach2['description']))
<p style="font-size:8.5pt;color:#444;">{{ $ach2['description'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
@if(!empty($resume->volunteer_work) && count($resume->volunteer_work) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Volunteer &amp; Activities</p>
@foreach($resume->volunteer_work as $vol)
<div style="margin-bottom:6pt;">
<p style="font-weight:bold;font-size:9pt;color:#111;">{{ $vol['role'] ?? '' }}
@if(!empty($vol['organization']))
 &mdash; <span style="color:{{ $primary }};">{{ $vol['organization'] }}</span>
@endif
</p>
@if(!empty($vol['description']))
<p style="font-size:8.5pt;color:#444;">{{ $vol['description'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
</td>
</tr></table>

@else
{{-- SINGLE-COLUMN LAYOUT --}}
<div>
@if($isMinimal)
<div style="padding:20pt 22pt 14pt;border-bottom:1pt solid #E2E2E0;">
<p style="font-size:22pt;font-weight:300;letter-spacing:3pt;text-transform:uppercase;color:#111;margin-bottom:4pt;">{{ $resume->full_name }}</p>
<p style="font-size:8.5pt;color:#666;">
@if($resume->email){{ $resume->email }}@endif
@if($resume->phone) &nbsp;|&nbsp; {{ $resume->phone }}@endif
@if($resume->location) &nbsp;|&nbsp; {{ $resume->location }}@endif
@if($resume->linkedin_url) &nbsp;|&nbsp; LinkedIn@endif
@if($resume->github_url) &nbsp;|&nbsp; GitHub@endif
</p>
</div>
@else
<div style="background:{{ $primaryLight }};border-bottom:3pt solid {{ $primary }};padding:18pt 22pt 14pt;">
<p style="font-size:22pt;font-weight:bold;color:{{ $primary }};margin-bottom:5pt;">{{ $resume->full_name }}</p>
<p style="font-size:8.5pt;color:#555;">
@if($resume->email){{ $resume->email }}@endif
@if($resume->phone) &nbsp;&bull;&nbsp; {{ $resume->phone }}@endif
@if($resume->location) &nbsp;&bull;&nbsp; {{ $resume->location }}@endif
@if($resume->linkedin_url) &nbsp;&bull;&nbsp; LinkedIn@endif
@if($resume->github_url) &nbsp;&bull;&nbsp; GitHub@endif
@if($resume->portfolio_url) &nbsp;&bull;&nbsp; Portfolio@endif
</p>
</div>
@endif
<div style="padding:14pt 22pt;">
@if($resume->professional_summary)
<div style="margin-bottom:12pt;">
<p class="st">Professional Summary</p>
<p style="font-size:9pt;color:#333;line-height:1.5;">{{ $resume->professional_summary }}</p>
</div>
@endif
@if(!empty($resume->skills) && count($resume->skills) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Skills</p>
@foreach($resume->skills as $skill)
<span class="badge">{{ $skill }}</span>
@endforeach
</div>
@endif
@if(!empty($resume->experience) && count($resume->experience) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Work Experience</p>
@foreach($resume->experience as $exp)
<div style="margin-bottom:9pt;">
<table width="100%"><tr>
<td style="vertical-align:top;">
<p style="font-weight:bold;font-size:10pt;color:#111;">{{ $exp['position'] ?? '' }}</p>
<p style="font-size:8.5pt;font-weight:bold;color:{{ $primary }};">{{ $exp['company'] ?? '' }}
@if(!empty($exp['location']))
 &middot; {{ $exp['location'] }}
@endif
</p>
</td>
<td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
{{ $exp['start_date'] ?? '' }}
@if(!empty($exp['end_date']))
 &ndash; {{ $exp['end_date'] }}
@endif
@if(!empty($exp['employment_type']))
<br><em>{{ $exp['employment_type'] }}</em>
@endif
</td>
</tr></table>
@if(!empty($exp['description']))
<p style="font-size:8.5pt;color:#444;margin-top:3pt;line-height:1.4;">{{ $exp['description'] }}</p>
@endif
@if(!empty($exp['achievements']) && is_array($exp['achievements']))
@foreach($exp['achievements'] as $expAch)
@if(!empty(trim($expAch)))
<table style="margin-top:2pt;" width="100%"><tr>
<td style="width:8pt;vertical-align:top;padding-top:4pt;"><div style="width:5pt;height:5pt;border-radius:3pt;background:{{ $primary }};"></div></td>
<td style="font-size:8.5pt;color:#333;line-height:1.4;">{{ $expAch }}</td>
</tr></table>
@endif
@endforeach
@endif
@if(!empty($exp['technologies']))
<p style="font-size:7.5pt;color:#888;margin-top:2pt;font-style:italic;">Technologies: {{ $exp['technologies'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
@if(!empty($resume->education) && count($resume->education) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Education</p>
@foreach($resume->education as $edu)
<div style="margin-bottom:7pt;">
<table width="100%"><tr>
<td style="vertical-align:top;">
<p style="font-weight:bold;font-size:10pt;color:#111;">{{ $edu['degree'] ?? '' }}</p>
<p style="font-size:8.5pt;font-weight:bold;color:{{ $primary }};">{{ $edu['institution'] ?? '' }}
@if(!empty($edu['location']))
, {{ $edu['location'] }}
@endif
</p>
@if(!empty($edu['field']))
<p style="font-size:8pt;color:#666;">{{ $edu['field'] }}</p>
@endif
@if(!empty($edu['gpa']))
<p style="font-size:7.5pt;color:#666;">GPA: {{ $edu['gpa'] }}
@if(!empty($edu['honors']))
 &bull; {{ $edu['honors'] }}
@endif
</p>
@endif
</td>
<td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
{{ $edu['start_year'] ?? '' }}
@if(!empty($edu['end_year']))
 &ndash; {{ $edu['end_year'] }}
@endif
</td>
</tr></table>
</div>
@endforeach
</div>
@endif
@if(!empty($resume->certifications) && count($resume->certifications) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Certifications</p>
@foreach($resume->certifications as $cert)
<div style="display:inline-block;width:48%;vertical-align:top;margin:2pt 1%;">
<div style="border:0.5pt solid {{ $borderLight }};border-radius:3pt;padding:5pt 7pt;">
<p style="font-weight:bold;font-size:8.5pt;color:#111;">{{ $cert['name'] ?? '' }}</p>
<p style="font-size:7.5pt;color:{{ $primary }};">{{ $cert['issuer'] ?? '' }}</p>
<p style="font-size:7pt;color:#888;">
@if(!empty($cert['issue_date']))
Issued: {{ $cert['issue_date'] }}
@endif
@if(!empty($cert['credential_id']))
 &bull; {{ $cert['credential_id'] }}
@endif
</p>
</div>
</div>
@endforeach
</div>
@endif
@if(!empty($resume->projects) && count($resume->projects) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Projects</p>
@foreach($resume->projects as $proj)
<div style="margin-bottom:7pt;">
<table width="100%"><tr>
<td>
<p style="font-weight:bold;font-size:9.5pt;color:#111;">{{ $proj['name'] ?? '' }}</p>
@if(!empty($proj['technologies']))
<p style="font-size:7.5pt;color:#888;font-style:italic;">{{ $proj['technologies'] }}</p>
@endif
</td>
<td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;">
@if(!empty($proj['start_date']))
{{ $proj['start_date'] }}
@if(!empty($proj['end_date']))
 &ndash; {{ $proj['end_date'] }}
@endif
@endif
</td>
</tr></table>
@if(!empty($proj['description']))
<p style="font-size:8.5pt;color:#444;line-height:1.4;">{{ $proj['description'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
@if(!empty($resume->achievements) && count($resume->achievements) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Achievements &amp; Awards</p>
@foreach($resume->achievements as $ach2)
<table width="100%" style="margin-bottom:5pt;"><tr>
<td style="width:10pt;vertical-align:top;padding-top:2pt;"><div style="width:7pt;height:7pt;background:{{ $primary }};border-radius:1pt;"></div></td>
<td style="vertical-align:top;">
<p style="font-weight:bold;font-size:9pt;color:#111;">{{ $ach2['title'] ?? '' }}
@if(!empty($ach2['issuer']))
<span style="font-weight:normal;color:#666;"> &bull; {{ $ach2['issuer'] }}</span>
@endif
@if(!empty($ach2['date']))
<span style="font-size:7.5pt;color:#888;"> ({{ $ach2['date'] }})</span>
@endif
</p>
@if(!empty($ach2['description']))
<p style="font-size:8.5pt;color:#444;">{{ $ach2['description'] }}</p>
@endif
</td>
</tr></table>
@endforeach
</div>
@endif
@if(!empty($resume->languages) && count($resume->languages) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Languages</p>
@foreach($resume->languages as $lang)
<span style="display:inline-block;margin-right:14pt;font-size:9pt;"><strong style="color:#111;">{{ $lang['name'] ?? '' }}</strong> <span class="badge">{{ $lang['proficiency'] ?? '' }}</span></span>
@endforeach
</div>
@endif
@if(!empty($resume->volunteer_work) && count($resume->volunteer_work) > 0)
<div style="margin-bottom:12pt;">
<p class="st">Volunteer &amp; Extra-Curricular</p>
@foreach($resume->volunteer_work as $vol)
<div style="margin-bottom:6pt;">
<table width="100%"><tr>
<td>
<p style="font-weight:bold;font-size:9pt;color:#111;">{{ $vol['role'] ?? '' }}</p>
<p style="font-size:8.5pt;color:{{ $primary }};">{{ $vol['organization'] ?? '' }}</p>
</td>
<td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;">
{{ $vol['start_date'] ?? '' }}
@if(!empty($vol['end_date']))
 &ndash; {{ $vol['end_date'] }}
@endif
</td>
</tr></table>
@if(!empty($vol['description']))
<p style="font-size:8.5pt;color:#444;">{{ $vol['description'] }}</p>
@endif
</div>
@endforeach
</div>
@endif
</div>
</div>
@endif

</body>
</html>
