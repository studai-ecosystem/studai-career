<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailSubject }}</title>
<style>
  body { margin: 0; padding: 0; background: #F0F0EE; font-family: 'Google Sans', Arial, sans-serif; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: none; }
  .header-hired       { background: #2D6CDF; padding: 36px 40px; text-align: center; }
  .header-shortlisted { background: #1E8E3E; padding: 36px 40px; text-align: center; }  .header-interviewed { background: #F9AB00; padding: 36px 40px; text-align: center; }  .header-rejected    { background: #737373; padding: 36px 40px; text-align: center; }
  .header-icon  { font-size: 48px; margin-bottom: 8px; }
  .header h1    { margin: 0; color: #ffffff; font-size: 22px; font-weight: 600; }
  .header p     { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 14px; }
  .body         { padding: 36px 40px; }
  .body p       { color: #3D3D3D; font-size: 15px; line-height: 1.7; margin: 0 0 14px; white-space: pre-line; }
  .cta          { text-align: center; margin: 28px 0; }
  .cta a        { display: inline-block; background: #2D6CDF; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; }
  .footer       { background: #F0F0EE; padding: 20px 40px; text-align: center; border-top: 1px solid #E2E2E0; }
  .footer p     { margin: 0; color: #737373; font-size: 12px; }
  .badge-hired       { display: inline-block; background: #FFF8EC; color: #F9AB00; border: 1px solid #F9AB00; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .badge-shortlisted { display: inline-block; background: #EDFAF2; color: #1E8E3E; border: 1px solid #1E8E3E; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .badge-interviewed { display: inline-block; background: #FFF8EC; color: #E37400; border: 1px solid #F9AB00; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .badge-rejected    { display: inline-block; background: #FEF2F2; color: #2D6CDF; border: 1px solid #2D6CDF; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .tip-box { background: #EBF2FF; border-left: 4px solid #2D6CDF; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-top: 20px; }
  .tip-box p { color: #2D6CDF; font-size: 13px; margin: 0; }
  .reason-box { background: #FEF2F2; border-left: 4px solid #2D6CDF; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-top: 16px; }
  .reason-box p { color: #2D6CDF; font-size: 13px; margin: 0; line-height: 1.6; }
  .score-row { background: #EDFAF2; border-radius: 8px; padding: 10px 14px; margin-top: 14px; font-size: 13px; color: #1E8E3E; font-weight: 600; }
</style>
</head>
<body>
<div class="wrapper">

  {{-- Header --}}
  <div class="header-{{ $eventType }}">
    <div class="header-icon">
      @if($eventType === 'hired') 🎉
      @elseif($eventType === 'shortlisted') ⭐
      @elseif($eventType === 'interviewed') 📅
      @else 📋
      @endif
    </div>
    <h1>
      @if($eventType === 'hired') Congratulations — You're Hired!
      @elseif($eventType === 'shortlisted') You've Been Shortlisted!
      @elseif($eventType === 'interviewed') You're Going to Interview!
      @else Application Update
      @endif
    </h1>
    <p>{{ $companyName }} &bull; {{ $jobTitle }}</p>
  </div>

  <div class="body">
    <div class="badge-{{ $eventType }}">
      @if($eventType === 'hired') 🎉 Hired
      @elseif($eventType === 'shortlisted') ⭐ Shortlisted
      @elseif($eventType === 'interviewed') 📅 Interview Stage
      @else ✕ Not Selected
      @endif
    </div>

    <p>{{ $body }}</p>

    @if($matchScore > 0 && $eventType !== 'rejected')
    <div class="score-row">
      Your AI Match Score: {{ number_format($matchScore, 1) }}% — Great fit for this role!
    </div>
    @endif

    @if($eventType === 'rejected' && !empty($rejectionReason))
    <div class="reason-box">
      <p><strong>Feedback from {{ $companyName }}:</strong><br>{{ $rejectionReason }}</p>
    </div>
    @endif

    {{-- AI-generated actionable tip --}}
    @if(!empty($studentTip ?? ''))
    <div class="tip-box">
      <p>💡 <strong>Tip for you:</strong> {{ $studentTip }}</p>
    </div>
    @elseif($eventType === 'rejected')
    <div class="tip-box">
      <p>💡 <strong>Keep going!</strong> Your profile is saved. Apply for other roles on StudAI Hire that match your skills — new jobs are posted daily.</p>
    </div>
    @endif

    <div class="cta">
      @if($eventType === 'rejected')
        <a href="{{ url('/jobs') }}">Browse Matched Jobs</a>
      @elseif($eventType === 'interviewed')
        <a href="{{ url('/applications') }}">View My Applications</a>
      @else
        <a href="{{ url('/applications') }}">View My Application</a>
      @endif
    </div>
  </div>

  <div class="footer">
    <p>Sent by <strong>StudAI Hire Platform</strong> &bull; AI-Powered Hiring</p>
    <p style="margin-top:4px">If you did not apply for this position, please disregard this email.</p>
  </div>
</div>
</body>
</html>
