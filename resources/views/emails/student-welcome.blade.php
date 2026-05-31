<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to StudAI Hire</title>
<style>
  body { margin:0; padding:0; background:#F0F0EE; font-family:'Google Sans',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow: none; }
  .header { background:#1E8E3E; padding:40px 40px 32px; text-align:center; }
  .header .logo { font-size:26px; font-weight:700; color:#fff; letter-spacing:-0.5px; }
  .header .logo span { color:#A3D9B4; }
  .header h1 { margin:16px 0 6px; color:#fff; font-size:22px; font-weight:600; }
  .header p { margin:0; color:rgba(255,255,255,.8); font-size:14px; }
  .body { padding:36px 40px; }
  .greeting { font-size:16px; color:#3D3D3D; font-weight:500; margin-bottom:6px; }
  .intro { font-size:14px; color:#737373; line-height:1.7; margin-bottom:28px; }
  .section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#737373; margin:24px 0 10px; }
  .tool-row { display:table; width:100%; border-collapse:collapse; margin-bottom:8px; }
  .tool-icon { display:table-cell; width:44px; vertical-align:middle; font-size:22px; text-align:center; }
  .tool-text { display:table-cell; vertical-align:middle; padding:10px 12px; background:#F0F0EE; border-radius:8px; }
  .tool-text h4 { margin:0 0 2px; font-size:13px; font-weight:600; color:#0C0C0C; }
  .tool-text p { margin:0; font-size:12px; color:#737373; }
  .steps { counter-reset:step; margin:0; padding:0; list-style:none; }
  .steps li { counter-increment:step; position:relative; padding:10px 10px 10px 44px; border-left:2px solid #E2E2E0; margin-left:12px; font-size:13px; color:#3D3D3D; line-height:1.5; }
  .steps li::before { content:counter(step); position:absolute; left:-14px; top:8px; width:24px; height:24px; background:#1E8E3E; color:#fff; border-radius:50%; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; text-align:center; line-height:24px; }
  .cta-section { text-align:center; margin:28px 0 0; }
  .btn-primary { display:inline-block; background:#1E8E3E; color:#fff; text-decoration:none; padding:13px 32px; border-radius:8px; font-size:14px; font-weight:600; }
  .btn-secondary { display:inline-block; background:#fff; color:#1E8E3E; text-decoration:none; padding:10px 24px; border-radius:8px; font-size:13px; font-weight:600; border:1.5px solid #1E8E3E; margin-top:10px; }
  .tip-box { background:#EDFAF2; border-left:4px solid #1E8E3E; padding:14px 16px; border-radius:0 8px 8px 0; margin:24px 0 0; }
  .tip-box p { margin:0; font-size:13px; color:#1E8E3E; line-height:1.6; }
  .footer { background:#F0F0EE; padding:20px 40px; border-top:1px solid #E2E2E0; text-align:center; }
  .footer p { margin:0; color:#737373; font-size:11px; line-height:1.8; }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="logo">StudAI <span>Career</span></div>
    <h1>Welcome, {{ $user->name }}! 🚀</h1>
    <p>Your AI-powered career platform is ready</p>
  </div>

  <div class="body">
    <p class="greeting">Hi {{ $user->name }},</p>
    <p class="intro">
      You've just joined <strong>StudAI Hire</strong> — the platform where AI does the heavy lifting
      so you can focus on landing the right job. Your profile is live and you're ready to
      explore thousands of opportunities.
    </p>

    <div class="section-title">Your Career Toolkit</div>

    <div class="tool-row">
      <div class="tool-icon">🤖</div>
      <div class="tool-text">
        <h4>AI Job Matching</h4>
        <p>Get a personalised match score for every job based on your skills and experience.</p>
      </div>
    </div>
    <div class="tool-row">
      <div class="tool-icon">📄</div>
      <div class="tool-text">
        <h4>Resume Analyser</h4>
        <p>Upload your resume and get instant AI feedback on what to improve.</p>
      </div>
    </div>
    <div class="tool-row">
      <div class="tool-icon">💼</div>
      <div class="tool-text">
        <h4>Negotiation Strategist</h4>
        <p>Get AI-generated salary negotiation scripts tailored to your offer.</p>
      </div>
    </div>
    <div class="tool-row">
      <div class="tool-icon">🧭</div>
      <div class="tool-text">
        <h4>Career Path Builder</h4>
        <p>Discover which roles you can grow into and what skills to build next.</p>
      </div>
    </div>
    <div class="tool-row">
      <div class="tool-icon">📊</div>
      <div class="tool-text">
        <h4>Market Intelligence</h4>
        <p>Live salary benchmarks and hiring trends for your target roles.</p>
      </div>
    </div>

    <div class="section-title">Get Started in 3 Steps</div>
    <ol class="steps">
      <li><strong>Complete your profile</strong> — add your skills, experience, and education so the AI can match you accurately.</li>
      <li><strong>Upload your resume</strong> — let the AI analyse it and suggest improvements instantly.</li>
      <li><strong>Apply to matched jobs</strong> — one click applies with your full profile. Track every application from your dashboard.</li>
    </ol>

    <div class="tip-box">
      <p>💡 <strong>Did you know?</strong> Candidates with complete profiles get <strong>3x more interview calls</strong>. Spend 5 minutes completing your profile now.</p>
    </div>

    <div class="cta-section">
      <a href="{{ url('/profile') }}" class="btn-primary">Complete My Profile</a><br>
      <a href="{{ url('/jobs') }}" class="btn-secondary">Browse Jobs</a>
    </div>
  </div>

  <div class="footer">
    <p>You're receiving this because you registered on <strong>StudAI Hire</strong>.</p>
    <p>StudAI Edutech Pvt. Ltd. &bull; Your Career. On Autopilot.</p>
    <p style="margin-top:6px;color:#A8A8A8">onestudai@gmail.com</p>
  </div>

</div>
</body>
</html>
