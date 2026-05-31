<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Received</title>
<style>
  body { margin:0; padding:0; background:#EBF2FF; font-family:'Segoe UI',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow: none; }
  .header { background:#2D6CDF; padding:36px 40px; text-align:center; }
  .header img { width:44px; height:44px; margin-bottom:12px; }
  .header h1 { color:#fff; font-size:22px; margin:0; font-weight:700; }
  .header p { color:rgba(255,255,255,.85); font-size:14px; margin:6px 0 0; }
  .body { padding:36px 40px; }
  .greeting { font-size:18px; font-weight:700; color:#0C0C0C; margin-bottom:6px; }
  .subtitle { color:#737373; font-size:14px; margin-bottom:24px; }
  .success-badge { display:inline-flex; align-items:center; gap:8px; background:#EDFAF2; color:#1E8E3E; border:1px solid #A3D9B4; border-radius:50px; padding:8px 18px; font-size:14px; font-weight:600; margin-bottom:28px; }
  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:28px; }
  .info-card { background:#EBF2FF; border-radius:12px; padding:16px 18px; border-left:4px solid #2D6CDF; }
  .info-card.orange { border-left-color:#E37400; }
  .info-card.green { border-left-color:#1E8E3E; }
  .info-card.purple { border-left-color:#2D6CDF; }
  .info-label { font-size:11px; color:#A8A8A8; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px; }
  .info-value { font-size:15px; font-weight:700; color:#0C0C0C; }
  .timeline { margin:24px 0; }
  .timeline-title { font-size:15px; font-weight:700; color:#0C0C0C; margin-bottom:16px; }
  .timeline-step { display:flex; align-items:flex-start; gap:14px; margin-bottom:16px; }
  .step-dot { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
  .step-dot.blue { background:#EBF2FF; }
  .step-dot.amber { background:#FFF8EC; }
  .step-dot.green { background:#EDFAF2; }
  .step-content { flex:1; padding-top:4px; }
  .step-title { font-size:14px; font-weight:600; color:#0C0C0C; }
  .step-desc { font-size:12px; color:#737373; margin-top:2px; }
  .tip-box { background:#EBF2FF; border-radius:12px; padding:18px 20px; margin:24px 0; }
  .tip-box p { font-size:13px; color:#0C2E72; margin:0; }
  .footer { background:#EBF2FF; padding:24px 40px; text-align:center; border-top:1px solid #E2E2E0; }
  .footer p { font-size:12px; color:#A8A8A8; margin:0; }
  .footer a { color:#2D6CDF; text-decoration:none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>🎯 Application Received!</h1>
    <p>StudAI Hire — Your Career, On Autopilot.</p>
  </div>
  <div class="body">
    <div class="greeting">Hi {{ $candidateName }},</div>
    <div class="subtitle">Great news — your application has been successfully submitted.</div>

    <div class="success-badge">
      ✅ &nbsp; Application #{{ $applicationNumber }} confirmed
    </div>

    <div class="info-grid">
      <div class="info-card">
        <div class="info-label">Position</div>
        <div class="info-value">{{ $jobTitle }}</div>
      </div>
      <div class="info-card green">
        <div class="info-label">Company</div>
        <div class="info-value">{{ $companyName }}</div>
      </div>
      <div class="info-card orange">
        <div class="info-label">📅 Application Date</div>
        <div class="info-value">{{ $applicationDate }}</div>
      </div>
      <div class="info-card orange">
        <div class="info-label">⏰ Closing Date</div>
        <div class="info-value">{{ $closingDate }}</div>
      </div>
      <div class="info-card purple" style="grid-column:1/-1;">
        <div class="info-label">🔍 Evaluation Date</div>
        <div class="info-value">{{ $evaluationDate }}</div>
      </div>
    </div>

    <div class="timeline">
      <div class="timeline-title">What happens next?</div>
      <div class="timeline-step">
        <div class="step-dot blue">📝</div>
        <div class="step-content">
          <div class="step-title">Application Review</div>
          <div class="step-desc">Our team will review your resume and cover letter before the closing date.</div>
        </div>
      </div>
      <div class="timeline-step">
        <div class="step-dot amber">📊</div>
        <div class="step-content">
          <div class="step-title">Evaluation on {{ $evaluationDate }}</div>
          <div class="step-desc">Shortlisted candidates will be notified and invited to proceed to the next stage.</div>
        </div>
      </div>
      <div class="timeline-step">
        <div class="step-dot green">🏆</div>
        <div class="step-content">
          <div class="step-title">Selection Process</div>
          <div class="step-desc">Company Knowledge Test → Aptitude Assessment → One-on-One Interview</div>
        </div>
      </div>
    </div>

    <div class="tip-box">
      <p>💡 <strong>Pro Tip:</strong> While you wait, keep your profile updated on StudAI Hire and practice with our Interview Lab to ace the upcoming stages!</p>
    </div>

    <p style="color:#737373;font-size:13px;margin-top:20px;">We'll email you at every important milestone. Good luck — we're rooting for you! 🚀</p>
  </div>
  <div class="footer">
    <p>You're receiving this because you applied via <a href="{{ url('/') }}">StudAI Hire</a>.</p>
    <p style="margin-top:4px;">© {{ date('Y') }} StudAI Hire. All rights reserved.</p>
  </div>
</div>
</body>
</html>
