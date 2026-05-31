<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailSubject }}</title>
<style>
  * { box-sizing:border-box; }
  body { margin:0; padding:0; background:#F0F0EE; font-family:Arial,'Helvetica Neue',Helvetica,sans-serif; color:#3D3D3D; }
  .shell { max-width:680px; margin:32px auto; }
  .card  { background:#ffffff; border:1px solid #E2E2E0; border-top:4px solid #2D6CDF; }
  .top-bar { padding:18px 36px; border-bottom:1px solid #E2E2E0; }
  .top-bar table { width:100%; border-collapse:collapse; }
  .wordmark { font-size:15px; font-weight:700; color:#0C0C0C; letter-spacing:-.3px; }
  .wordmark span { color:#2D6CDF; }
  .top-date { font-size:11px; color:#A8A8A8; text-align:right; }
  .status-header { background:#F7F7F5; border-bottom:1px solid #E2E2E0; padding:24px 36px; }
  .status-label { display:inline-block; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:4px 12px; margin-bottom:10px; }
  .label-shortlisted { background:#EDFAF2; color:#1E8E3E; border:1px solid #A3D9B4; }
  .label-hired       { background:#EBF2FF; color:#1B57C4; border:1px solid #BFCFEE; }
  .label-interviewed { background:#FFF8EC; color:#E37400; border:1px solid #F0C77A; }
  .label-rejected    { background:#fef2f2; color:#2D6CDF; border:1px solid #FCA5A5; }
  .status-header h2 { margin:0; font-size:19px; font-weight:700; color:#0C0C0C; line-height:1.3; }
  .status-header p  { margin:6px 0 0; font-size:13px; color:#737373; }
  .body { padding:28px 36px; }
  hr.rule { border:none; border-top:1px solid #E2E2E0; margin:22px 0 16px; }
  .section-label { font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#A8A8A8; margin:0 0 12px; }
  .prose { font-size:14px; line-height:1.75; color:#3D3D3D; margin:0 0 16px; white-space:pre-line; }
  table.data { width:100%; border-collapse:collapse; font-size:13px; margin-bottom:4px; }
  table.data td { padding:9px 12px; border:1px solid #E2E2E0; vertical-align:top; line-height:1.5; }
  table.data td.lbl { background:#F7F7F5; font-weight:600; color:#0C0C0C; width:170px; white-space:nowrap; }
  table.data td.val { color:#3D3D3D; }
  .score-pill { display:inline-block; background:#EBF2FF; color:#1B57C4; border:1px solid #BFCFEE; padding:2px 10px; font-size:12px; font-weight:700; }
  .skills-wrap { margin:0; padding:0; }
  .skill { display:inline-block; background:#F0F0EE; color:#3D3D3D; border:1px solid #C8C8C5; font-size:12px; padding:3px 10px; margin:2px 4px 2px 0; }
  .text-block { background:#F7F7F5; border:1px solid #E2E2E0; padding:14px 16px; font-size:13px; color:#3D3D3D; line-height:1.75; white-space:pre-line; }
  .rejection-block { background:#fef2f2; border:1px solid #FCA5A5; padding:14px 16px; font-size:13px; color:#2D6CDF; line-height:1.6; }
  .links-row { font-size:13px; }
  .links-row a { color:#2D6CDF; text-decoration:none; margin-right:16px; }
  .btn-primary { display:inline-block; background:#2D6CDF; color:#ffffff; text-decoration:none; padding:11px 26px; font-size:13px; font-weight:700; letter-spacing:.02em; }
  .cta-row { text-align:center; padding:20px 0 4px; }
  .footer { background:#F7F7F5; border-top:1px solid #E2E2E0; padding:16px 36px; }
  .footer p { margin:0; font-size:11px; color:#A8A8A8; line-height:1.7; text-align:center; }
  .footer strong { color:#737373; }
</style>
</head>
<body>
<div class="shell">
<div class="card">

  <div class="top-bar">
    <table>
      <tr>
        <td><span class="wordmark">Stud<span>AI</span> Hire</span></td>
        <td class="top-date">Internal HR Notification &bull; {{ now()->format('d M Y, H:i') }}</td>
      </tr>
    </table>
  </div>

  <div class="status-header">
    <span class="status-label label-{{ $eventType }}">
      @if($eventType === 'hired') Hired
      @elseif($eventType === 'rejected') Not Selected
      @elseif($eventType === 'interviewed') Interview Stage
      @else Shortlisted
      @endif
    </span>
    <h2>
      @if($eventType === 'hired') Candidate Marked as Hired
      @elseif($eventType === 'rejected') Candidate Not Selected &mdash; Notification Sent
      @elseif($eventType === 'interviewed') Candidate Advancing to Interview Stage
      @else New Shortlisted Candidate
      @endif
    </h2>
    <p>{{ $candidateName }} &bull; {{ $jobTitle }} &bull; {{ $companyName }}</p>
  </div>

  <div class="body">

    <p class="section-label">AI Assessment Summary</p>
    <p class="prose">{{ $body }}</p>

    <hr class="rule">
    <p class="section-label">Application Details</p>
    <table class="data">
      <tr><td class="lbl">Application No.</td><td class="val">{{ $applicationNumber ?: '&mdash;' }}</td></tr>
      <tr><td class="lbl">Date Applied</td><td class="val">{{ $appliedAt ?: '&mdash;' }}</td></tr>
      <tr><td class="lbl">Position</td><td class="val">{{ $jobTitle }}</td></tr>
      <tr><td class="lbl">Company</td><td class="val">{{ $companyName }}</td></tr>
      <tr><td class="lbl">Current Status</td><td class="val">{{ ucfirst($eventType) }}</td></tr>
      @if($matchScore > 0)
      <tr><td class="lbl">AI Match Score</td><td class="val"><span class="score-pill">{{ number_format($matchScore, 1) }}%</span></td></tr>
      @endif
      @if($eventType === 'rejected' && $rejectionReason)
      <tr><td class="lbl">Rejection Reason</td><td class="val" style="color:#2D6CDF;">{{ $rejectionReason }}</td></tr>
      @endif
    </table>

    <hr class="rule">
    <p class="section-label">Candidate Profile</p>
    <table class="data">
      <tr><td class="lbl">Full Name</td><td class="val">{{ $candidateName }}</td></tr>
      <tr><td class="lbl">Email Address</td><td class="val">{{ $candidateEmail }}</td></tr>
      @if(!empty($profile['headline']))
      <tr><td class="lbl">Headline</td><td class="val">{{ $profile['headline'] }}</td></tr>
      @endif
      @if(!empty($profile['location']))
      <tr><td class="lbl">Location</td><td class="val">{{ $profile['location'] }}</td></tr>
      @endif
      @if(!empty($profile['work_preference']))
      <tr><td class="lbl">Work Preference</td><td class="val">{{ ucfirst($profile['work_preference']) }}</td></tr>
      @endif
      @if(!empty($profile['notice_period']))
      <tr><td class="lbl">Notice Period</td><td class="val">{{ $profile['notice_period'] }}</td></tr>
      @endif
      @if(!empty($profile['expected_salary']))
      <tr><td class="lbl">Expected Salary</td><td class="val">{{ $profile['expected_salary'] }}</td></tr>
      @endif
      @if(!empty($profile['profile_completeness']))
      <tr><td class="lbl">Profile Completeness</td><td class="val">{{ $profile['profile_completeness'] }}%</td></tr>
      @endif
    </table>

    @if(!empty($profile['skills']))
    <p class="section-label" style="margin-top:20px;">Key Skills</p>
    <div class="skills-wrap">
      @foreach(explode(', ', $profile['skills']) as $skill)
        @if(trim($skill))<span class="skill">{{ trim($skill) }}</span>@endif
      @endforeach
    </div>
    @endif

    @if(!empty($profile['experience']))
    <p class="section-label" style="margin-top:20px;">Work Experience</p>
    <div class="text-block">{{ trim($profile['experience']) }}</div>
    @endif

    @if(!empty($profile['education']))
    <p class="section-label" style="margin-top:20px;">Education</p>
    <div class="text-block">{{ trim($profile['education']) }}</div>
    @endif

    @if(!empty($profile['summary']))
    <p class="section-label" style="margin-top:20px;">Professional Summary</p>
    <div class="text-block">{{ $profile['summary'] }}</div>
    @endif

    @if(!empty($coverLetter))
    <p class="section-label" style="margin-top:20px;">Cover Letter</p>
    <div class="text-block">{{ $coverLetter }}</div>
    @endif

    @if($eventType === 'rejected' && !empty($rejectionReason))
    <p class="section-label" style="margin-top:20px;">Rejection Rationale</p>
    <div class="rejection-block">{{ $rejectionReason }}</div>
    @endif

    @if($resumeUrl || $linkedinUrl || $githubUrl || $portfolioUrl)
    <hr class="rule">
    <p class="section-label">Attachments &amp; Links</p>
    <div class="links-row">
      @if($resumeUrl)    <a href="{{ $resumeUrl }}" target="_blank">Resume</a>@endif
      @if($linkedinUrl)  <a href="{{ $linkedinUrl }}" target="_blank">LinkedIn</a>@endif
      @if($githubUrl)    <a href="{{ $githubUrl }}" target="_blank">GitHub</a>@endif
      @if($portfolioUrl) <a href="{{ $portfolioUrl }}" target="_blank">Portfolio</a>@endif
    </div>
    @endif

    <div class="cta-row">
      <a href="{{ url('/employer/applicants') }}" class="btn-primary">Open in Applicant Tracker</a>
    </div>

  </div>

  <div class="footer">
    <p>This is an automated internal notification from <strong>StudAI Hire Platform</strong>. Do not reply to this email.</p>
    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
