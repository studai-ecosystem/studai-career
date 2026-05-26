<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to StudAI Hire</title>
<style>
  body { margin:0; padding:0; background:#f5f5f5; font-family:'Google Sans',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
  .header { background:linear-gradient(135deg,#1A73E8 0%,#0d47a1 100%); padding:40px 40px 32px; text-align:center; }
  .header .logo { font-size:26px; font-weight:700; color:#fff; letter-spacing:-0.5px; }
  .header .logo span { color:#8ab4f8; }
  .header h1 { margin:16px 0 6px; color:#fff; font-size:22px; font-weight:600; }
  .header p { margin:0; color:rgba(255,255,255,.8); font-size:14px; }
  .body { padding:36px 40px; }
  .greeting { font-size:16px; color:#3c4043; font-weight:500; margin-bottom:6px; }
  .intro { font-size:14px; color:#5f6368; line-height:1.7; margin-bottom:28px; }
  .section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#80868b; margin:24px 0 10px; }
  .card-grid { display:table; width:100%; border-collapse:separate; border-spacing:8px; margin-bottom:4px; }
  .card { background:#f8f9fa; border-radius:8px; padding:16px; display:table-cell; width:50%; vertical-align:top; }
  .card .icon { font-size:22px; margin-bottom:8px; }
  .card h4 { margin:0 0 4px; font-size:13px; font-weight:600; color:#202124; }
  .card p { margin:0; font-size:12px; color:#5f6368; line-height:1.5; }
  .info-table { width:100%; border-collapse:collapse; font-size:13px; margin-bottom:20px; }
  .info-table td { padding:8px 10px; border:1px solid #e8eaed; color:#3c4043; vertical-align:top; }
  .info-table td:first-child { background:#f8f9fa; font-weight:600; width:140px; }
  .cta-section { text-align:center; margin:28px 0 0; }
  .btn-primary { display:inline-block; background:#1A73E8; color:#fff; text-decoration:none; padding:13px 32px; border-radius:8px; font-size:14px; font-weight:600; }
  .btn-secondary { display:inline-block; background:#fff; color:#1A73E8; text-decoration:none; padding:10px 24px; border-radius:8px; font-size:13px; font-weight:600; border:1.5px solid #1A73E8; margin-top:10px; }
  .tip-box { background:#e8f0fe; border-left:4px solid #1A73E8; padding:14px 16px; border-radius:0 8px 8px 0; margin:24px 0 0; }
  .tip-box p { margin:0; font-size:13px; color:#1558d6; line-height:1.6; }
  .footer { background:#f8f9fa; padding:20px 40px; border-top:1px solid #e8eaed; text-align:center; }
  .footer p { margin:0; color:#80868b; font-size:11px; line-height:1.8; }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="logo">StudAI <span>Career</span></div>
    <h1>Welcome aboard, {{ $user->name }}! 🎉</h1>
    <p>Your company hiring dashboard is now active</p>
  </div>

  <div class="body">
    <p class="greeting">Hi {{ $user->name }},</p>
    <p class="intro">
      Thank you for registering <strong>{{ $company->name }}</strong> on StudAI Hire.
      Your employer account is live and you can start posting jobs, reviewing AI-matched candidates,
      and managing your entire hiring pipeline — all from one place.
    </p>

    <div class="section-title">Your Company Details</div>
    <table class="info-table">
      <tr><td>Company</td><td>{{ $company->name }}</td></tr>
      <tr><td>Your Email</td><td>{{ $user->email }}</td></tr>
      @if($company->hr_email)
      <tr><td>HR Email</td><td>{{ $company->hr_email }}</td></tr>
      @endif
      @if($company->industry)
      <tr><td>Industry</td><td>{{ $company->industry }}</td></tr>
      @endif
      @if($company->company_size)
      <tr><td>Company Size</td><td>{{ $company->company_size }}</td></tr>
      @endif
    </table>

    <div class="section-title">What You Can Do Now</div>
    <div class="card-grid">
      <div class="card">
        <div class="icon">📋</div>
        <h4>Post a Job</h4>
        <p>Create your first job listing and let our AI find the best-matched candidates.</p>
      </div>
      <div class="card">
        <div class="icon">🤖</div>
        <h4>AI Screening</h4>
        <p>Every applicant gets an AI match score so you focus only on the best fits.</p>
      </div>
    </div>
    <div class="card-grid" style="margin-top:0">
      <div class="card">
        <div class="icon">🧬</div>
        <h4>Corporate DNA</h4>
        <p>Define your company culture so the AI screens for cultural fit too.</p>
      </div>
      <div class="card">
        <div class="icon">📊</div>
        <h4>ATS Dashboard</h4>
        <p>Track, shortlist, schedule, and hire — all in one applicant pipeline view.</p>
      </div>
    </div>

    <div class="tip-box">
      <p>💡 <strong>Pro Tip:</strong> Complete your company profile and set up your Corporate DNA Profile to get higher-quality AI matches. Go to <em>Employer → Company Onboarding</em> to get started.</p>
    </div>

    <div class="cta-section">
      <a href="{{ url('/employer/dashboard') }}" class="btn-primary">Go to My Dashboard</a><br>
      <a href="{{ url('/employer/jobs/create') }}" class="btn-secondary">Post Your First Job</a>
    </div>
  </div>

  <div class="footer">
    <p>You're receiving this because you registered on <strong>StudAI Hire</strong>.</p>
    <p>StudAI Edutech Pvt. Ltd. &bull; AI-Powered Hiring Platform</p>
    <p style="margin-top:6px;color:#bdc1c6">onestudai@gmail.com</p>
  </div>

</div>
</body>
</html>
