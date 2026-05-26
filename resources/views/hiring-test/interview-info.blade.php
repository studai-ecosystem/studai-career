<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Interview Details — StudAI Hire</title>
<style>
  body{background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:48px 40px;max-width:520px;width:100%;text-align:center;}
  .icon{font-size:64px;margin-bottom:16px;}
  h1{font-size:26px;font-weight:800;color:#1a1a2e;margin-bottom:8px;}
  .subtitle{color:#6b7280;font-size:14px;margin-bottom:32px;}
  .info-box{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:20px 24px;margin-bottom:16px;text-align:left;}
  .info-box label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;}
  .info-box p{font-size:16px;font-weight:700;color:#065f46;margin-top:4px;}
  .notes-box{background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:20px 24px;margin-bottom:24px;text-align:left;}
  .notes-box label{font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.5px;}
  .notes-box p{font-size:14px;color:#92400e;margin-top:4px;line-height:1.6;}
  .tips{background:#ede9fe;border-radius:14px;padding:20px;text-align:left;margin-bottom:24px;}
  .tips h3{font-size:14px;font-weight:700;color:#4c1d95;margin-bottom:12px;}
  .tip{display:flex;align-items:flex-start;gap:8px;margin-bottom:8px;font-size:13px;color:#5b21b6;}
  .back-btn{display:inline-block;padding:12px 28px;background:linear-gradient(135deg,#1A73E8,#6366f1);color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;}
</style>
</head>
<body>
<div class="card">
  <div class="icon">🤝</div>
  <h1>One-on-One Interview</h1>
  <p class="subtitle">{{ $application->job->title }} at {{ $application->job->company->name ?? 'Company' }}</p>

  <div class="info-box">
    <label>Scheduled Date</label>
    <p>{{ $application->pipeline_stage_date ? \Carbon\Carbon::parse($application->pipeline_stage_date)->format('D, d M Y') : 'To be confirmed by the company' }}</p>
  </div>

  @if($application->pipeline_stage_notes)
  <div class="notes-box">
    <label>Notes from Company</label>
    <p>{{ $application->pipeline_stage_notes }}</p>
  </div>
  @endif

  <div class="tips">
    <h3>How to prepare:</h3>
    <div class="tip">🎯 Prepare STAR method answers (Situation, Task, Action, Result)</div>
    <div class="tip">📋 Review your resume — be ready to discuss every bullet point</div>
    <div class="tip">🏢 Research the company's products, mission, and culture</div>
    <div class="tip">❓ Prepare 3–5 thoughtful questions to ask the interviewer</div>
    <div class="tip">👔 Dress professionally and test your video setup in advance</div>
  </div>

  <a href="{{ url('/dashboard') }}" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
