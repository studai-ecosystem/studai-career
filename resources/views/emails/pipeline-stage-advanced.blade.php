<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Next Stage Invitation</title>
<style>
  body { margin:0; padding:0; background:#EBF2FF; font-family:'Segoe UI',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:40px auto; background:#fff; border-radius:16px; overflow:hidden; box-shadow: none; }
  .header { padding:36px 40px; text-align:center; }
  .header.company_info_test { background:#2D6CDF; }
  .header.aptitude { background:#2D6CDF; }
  .header.tech_test { background:#E37400; }
  .header.non_tech_test { background:#1B57C4; }
  .header h1 { color:#fff; font-size:22px; margin:0; font-weight:700; }
  .header p { color:rgba(255,255,255,.85); font-size:14px; margin:6px 0 0; }
  .body { padding:36px 40px; }
  .greeting { font-size:18px; font-weight:700; color:#0C0C0C; margin-bottom:6px; }
  .subtitle { color:#737373; font-size:14px; margin-bottom:24px; }
  .stage-card { border-radius:14px; padding:24px; margin-bottom:24px; text-align:center; }
  .stage-card.company_info_test { background:#EBF2FF; border:2px solid #BFCFEE; }
  .stage-card.aptitude { background:#EBF2FF; border:2px solid #2D6CDF; }
  .stage-card.tech_test { background:#FFF8EC; border:2px solid #E37400; }
  .stage-card.non_tech_test { background:#A3D9B4; border:2px solid #A3D9B4; }
  .stage-icon { font-size:40px; margin-bottom:10px; }
  .stage-title { font-size:18px; font-weight:700; color:#0C0C0C; }
  .stage-date { font-size:24px; font-weight:800; color:#2D6CDF; margin:8px 0; }
  .stage-desc { font-size:13px; color:#737373; }
  .info-row { display:flex; gap:12px; margin-bottom:24px; }
  .info-item { flex:1; background:#EBF2FF; border-radius:12px; padding:14px 16px; }
  .info-label { font-size:11px; color:#A8A8A8; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px; }
  .info-value { font-size:14px; font-weight:700; color:#0C0C0C; }
  .notes-box { background:#FFF8EC; border:1px solid #F0C77A; border-radius:12px; padding:16px 18px; margin-bottom:24px; }
  .notes-box p { font-size:13px; color:#E37400; margin:0; }
  .tips { margin:20px 0; }
  .tip { display:flex; align-items:flex-start; gap:10px; margin-bottom:12px; }
  .tip-icon { font-size:18px; }
  .tip-text { font-size:13px; color:#3D3D3D; }
  .footer { background:#EBF2FF; padding:24px 40px; text-align:center; border-top:1px solid #E2E2E0; }
  .footer p { font-size:12px; color:#A8A8A8; margin:0; }
  .footer a { color:#2D6CDF; text-decoration:none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header {{ $stage }}">
    @if($stage === 'company_info_test')
    <h1>📋 Company Info Test</h1>
    @elseif($stage === 'aptitude')
    <h1>🧠 Aptitude Assessment</h1>
    @elseif($stage === 'tech_test')
    <h1>💻 Technical Test</h1>
    @else
    <h1>📝 Non-Technical Test</h1>
    @endif
    <p>StudAI Hire — Hiring Pipeline</p>
  </div>
  <div class="body">
    <div class="greeting">Congratulations, {{ $candidateName }}! 🎉</div>
    <div class="subtitle">You've been selected to proceed to the next stage of the hiring process for <strong>{{ $jobTitle }}</strong> at <strong>{{ $companyName }}</strong>.</div>

    <div class="stage-card {{ $stage }}">
      @if($stage === 'company_info_test')
      <div class="stage-icon">📋</div>
      <div class="stage-title">Company Info Test</div>
      @elseif($stage === 'aptitude')
      <div class="stage-icon">🧠</div>
      <div class="stage-title">Aptitude Assessment</div>
      @elseif($stage === 'tech_test')
      <div class="stage-icon">💻</div>
      <div class="stage-title">Technical Test</div>
      @else
      <div class="stage-icon">📝</div>
      <div class="stage-title">Non-Technical Test</div>
      @endif
      <div class="stage-date">{{ $scheduledDate }}</div>
      <div class="stage-desc">Your {{ $stageLabel }} is scheduled for this date</div>
    </div>

    <div class="info-row">
      <div class="info-item">
        <div class="info-label">Position</div>
        <div class="info-value">{{ $jobTitle }}</div>
      </div>
      <div class="info-item">
        <div class="info-label">Company</div>
        <div class="info-value">{{ $companyName }}</div>
      </div>
    </div>

    @if($notes)
    <div class="notes-box">
      <p>📌 <strong>Note from the company:</strong><br>{{ $notes }}</p>
    </div>
    @endif

    <div class="tips">
      @if($stage === 'company_info_test')
      <p style="font-size:14px;font-weight:700;color:#0C0C0C;margin-bottom:12px;">How to prepare:</p>
      <div class="tip"><span class="tip-icon">🔍</span><span class="tip-text">Research {{ $companyName }}'s mission, values, products, and recent news.</span></div>
      <div class="tip"><span class="tip-icon">📰</span><span class="tip-text">Know the industry they operate in and their key competitors.</span></div>
      <div class="tip"><span class="tip-icon">💼</span><span class="tip-text">Understand their culture and work environment.</span></div>
      @elseif($stage === 'aptitude')
      <p style="font-size:14px;font-weight:700;color:#0C0C0C;margin-bottom:12px;">How to prepare:</p>
      <div class="tip"><span class="tip-icon">🧮</span><span class="tip-text">Practice numerical reasoning, logical reasoning, and verbal ability questions.</span></div>
      <div class="tip"><span class="tip-icon">⏱️</span><span class="tip-text">Work on speed — most aptitude tests are time-limited.</span></div>
      <div class="tip"><span class="tip-icon">📚</span><span class="tip-text">Review basic mathematics and data interpretation skills.</span></div>
      @elseif($stage === 'tech_test')
      <p style="font-size:14px;font-weight:700;color:#0C0C0C;margin-bottom:12px;">How to prepare:</p>
      <div class="tip"><span class="tip-icon">💻</span><span class="tip-text">Review core concepts relevant to the job role (e.g. data structures, system design, coding).</span></div>
      <div class="tip"><span class="tip-icon">⚙️</span><span class="tip-text">Practice problem-solving on platforms like LeetCode or HackerRank.</span></div>
      <div class="tip"><span class="tip-icon">⏱️</span><span class="tip-text">The test is timed — read all questions before you start.</span></div>
      @else
      <p style="font-size:14px;font-weight:700;color:#0C0C0C;margin-bottom:12px;">How to prepare:</p>
      <div class="tip"><span class="tip-icon">📝</span><span class="tip-text">Brush up on situational and behavioural questions relevant to your field.</span></div>
      <div class="tip"><span class="tip-icon">📊</span><span class="tip-text">Review industry terminology and general business knowledge.</span></div>
      <div class="tip"><span class="tip-icon">⏱️</span><span class="tip-text">The test is timed — stay calm and pace yourself.</span></div>
      @endif
    </div>

    <p style="color:#737373;font-size:13px;margin-top:20px;">If you have any questions, reply to this email. We look forward to speaking with you. Best of luck! 🚀</p>

    @if(!empty($testLink))
    <div style="text-align:center;margin-top:28px;">
      <a href="{{ $testLink }}" style="display:inline-block;padding:14px 36px;background:#2D6CDF;color:#fff;border-radius:12px;font-weight:700;font-size:15px;text-decoration:none;">
        @if($stage === 'company_info_test') 📋 Start Company Info Test
        @elseif($stage === 'aptitude') 🧠 Start Aptitude Test
        @elseif($stage === 'tech_test') 💻 Start Technical Test
        @else 📝 Start Non-Technical Test
        @endif
      </a>
      <p style="font-size:11px;color:#A8A8A8;margin-top:8px;">Button not working? Copy this link: {{ $testLink }}</p>
    </div>
    @endif
  </div>
  <div class="footer">
    <p>You're receiving this via <a href="{{ url('/') }}">StudAI Hire</a>.</p>
    <p style="margin-top:4px;">© {{ date('Y') }} StudAI Hire. All rights reserved.</p>
  </div>
</div>
</body>
</html>
