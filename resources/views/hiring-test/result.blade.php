<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Test Result — StudAI Hire</title>
<style>
  body{background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:48px 40px;max-width:520px;width:100%;text-align:center;}
  .icon{font-size:64px;margin-bottom:16px;}
  .result-title{font-size:28px;font-weight:800;margin-bottom:8px;}
  .result-title.pass{color:#059669;}
  .result-title.fail{color:#dc2626;}
  .score-ring{width:120px;height:120px;border-radius:50%;margin:24px auto;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;}
  .score-ring.pass{background:#dcfce7;color:#059669;border:4px solid #6ee7b7;}
  .score-ring.fail{background:#fee2e2;color:#dc2626;border:4px solid #fca5a5;}
  .info{font-size:14px;color:#6b7280;margin-bottom:6px;}
  .stage-badge{display:inline-block;padding:6px 16px;border-radius:50px;font-size:13px;font-weight:600;background:#ede9fe;color:#6d28d9;margin:16px 0;}
  .message{font-size:15px;color:#374151;line-height:1.6;margin:20px 0;}
  .message.pass{background:#f0fdf4;border-radius:12px;padding:16px;border:1px solid #bbf7d0;}
  .message.fail{background:#fff1f2;border-radius:12px;padding:16px;border:1px solid #fecdd3;}
  .back-btn{display:inline-block;margin-top:24px;padding:12px 28px;background:linear-gradient(135deg,#1A73E8,#6366f1);color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;}
</style>
</head>
<body>
<div class="card">
  @if($attempt->passed)
    <div class="icon">🎉</div>
    <div class="result-title pass">You Passed!</div>
  @else
    <div class="icon">😔</div>
    <div class="result-title fail">Not Passed</div>
  @endif

  <div class="stage-badge">{{ \App\Models\HiringTest::STAGE_LABELS[$stage] ?? $stage }}</div>

  <div class="score-ring {{ $attempt->passed ? 'pass' : 'fail' }}">{{ $attempt->score }}%</div>

  <div class="info">Submitted: {{ $attempt->submitted_at?->format('d M Y, h:i A') }}</div>
  <div class="info">Position: <strong>{{ $application->job->title }}</strong> at <strong>{{ $application->job->company->name ?? 'Company' }}</strong></div>

  @if($attempt->passed)
  <div class="message pass">
    ✅ Congratulations! You've successfully completed this stage. The company will review your result and contact you about the next step.
  </div>
  @else
  <div class="message fail">
    Thank you for attempting the test. Unfortunately your score of <strong>{{ $attempt->score }}%</strong> did not meet the required passing score. The company will be in touch regarding next steps.
  </div>
  @endif

  <a href="{{ url('/dashboard') }}" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
