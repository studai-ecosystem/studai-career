<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Application Received</title>
<style>
  body { margin:0; padding:0; background:#F0F0EE; font-family:'Google Sans',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow: none; }
  .header { background:#2D6CDF; padding:28px 36px; }
  .header h1 { margin:0; color:#fff; font-size:18px; font-weight:600; }
  .header p  { margin:4px 0 0; color:rgba(255,255,255,.8); font-size:12px; }
  .alert { margin:20px 28px 0; background:#EBF2FF; border-left:4px solid #2D6CDF; padding:12px 16px; border-radius:0 6px 6px 0; }
  .alert strong { font-size:14px; color:#1558d6; }
  .body { padding:20px 36px 32px; }
  .section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#737373; margin:20px 0 8px; }
  table.meta { width:100%; border-collapse:collapse; font-size:13px; }
  table.meta td { padding:7px 10px; border:1px solid #E2E2E0; color:#3D3D3D; vertical-align:top; }
  table.meta td:first-child { background:#F0F0EE; font-weight:600; width:150px; white-space:nowrap; }
  .score-badge { display:inline-block; background:#EBF2FF; color:#2D6CDF; border-radius:100px; padding:2px 10px; font-size:12px; font-weight:700; }
  .cta { text-align:center; margin:24px 0 0; }
  .cta a { display:inline-block; background:#2D6CDF; color:#fff; text-decoration:none; padding:10px 24px; border-radius:8px; font-size:13px; font-weight:600; margin:4px; }
  .cta a.secondary { background:#fff; color:#2D6CDF; border:1.5px solid #2D6CDF; }
  .footer { background:#F0F0EE; padding:14px 36px; border-top:1px solid #E2E2E0; text-align:center; }
  .footer p { margin:0; color:#737373; font-size:11px; }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <h1>📩 New Application Received</h1>
    <p>StudAI Hire — HR Notification &bull; {{ now()->format('M d, Y H:i') }}</p>
  </div>

  <div class="alert">
    <strong>{{ $application->user?->name ?? 'A candidate' }} has applied for {{ $application->job->title ?? 'your open position' }}</strong>
  </div>

  <div class="body">

    <div class="section-title">Application Summary</div>
    <table class="meta">
      <tr><td>Application #</td><td>{{ $application->application_number ?? 'N/A' }}</td></tr>
      <tr><td>Position</td><td>{{ $application->job->title ?? 'N/A' }}</td></tr>
      <tr><td>Applied On</td><td>{{ $application->submitted_at?->format('M d, Y H:i') ?? now()->format('M d, Y H:i') }}</td></tr>
      @if($application->match_score)
      <tr><td>AI Match Score</td><td><span class="score-badge">{{ $application->match_score }}%</span></td></tr>
      @endif
    </table>

    <div class="section-title">Candidate Details</div>
    <table class="meta">
      <tr><td>Name</td><td>{{ $application->user?->name ?? 'N/A' }}</td></tr>
      <tr><td>Email</td><td>{{ $application->user?->email ?? 'N/A' }}</td></tr>
      @php $profile = $application->user?->profile; @endphp
      @if($profile?->headline)
      <tr><td>Headline</td><td>{{ $profile->headline }}</td></tr>
      @endif
      @if($profile?->current_location)
      <tr><td>Location</td><td>{{ $profile->current_location }}</td></tr>
      @endif
      @if($profile?->work_preference)
      <tr><td>Work Preference</td><td>{{ ucfirst($profile->work_preference) }}</td></tr>
      @endif
      @if($profile?->notice_period)
      <tr><td>Notice Period</td><td>{{ $profile->notice_period }}</td></tr>
      @endif
    </table>

    @if(!empty($application->cover_letter))
    <div class="section-title">Cover Letter</div>
    <div style="background:#F0F0EE;border:1px solid #E2E2E0;border-radius:8px;padding:14px 16px;font-size:13px;color:#3D3D3D;line-height:1.6;white-space:pre-line;max-height:160px;overflow:auto;">{{ $application->cover_letter }}</div>
    @endif

    <div class="cta">
      <a href="{{ url('/employer/applicants/' . $application->id) }}">View Full Application</a>
      <a href="{{ url('/employer/applicants?status=pending') }}" class="secondary">Open ATS Dashboard</a>
    </div>

  </div>

  <div class="footer">
    <p>Hi {{ $hrName }}, this is an automated notification from <strong>StudAI Hire</strong>.</p>
    <p>To reply to the candidate, email <strong>{{ $application->user?->email }}</strong> directly.</p>
  </div>

</div>
</body>
</html>
