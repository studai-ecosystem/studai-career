<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailSubject }}</title>
<style>
  body { margin: 0; padding: 0; background: #f5f5f5; font-family: 'Google Sans', Arial, sans-serif; }
  .wrapper { max-width: 600px; margin: 32px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  .header-hired       { background: #1A73E8; padding: 36px 40px; text-align: center; }
  .header-shortlisted { background: #34A853; padding: 36px 40px; text-align: center; }  .header-interviewed { background: #F9AB00; padding: 36px 40px; text-align: center; }  .header-rejected    { background: #5f6368; padding: 36px 40px; text-align: center; }
  .header-icon  { font-size: 48px; margin-bottom: 8px; }
  .header h1    { margin: 0; color: #ffffff; font-size: 22px; font-weight: 600; }
  .header p     { margin: 6px 0 0; color: rgba(255,255,255,0.85); font-size: 14px; }
  .body         { padding: 36px 40px; }
  .body p       { color: #3c4043; font-size: 15px; line-height: 1.7; margin: 0 0 14px; white-space: pre-line; }
  .cta          { text-align: center; margin: 28px 0; }
  .cta a        { display: inline-block; background: #1A73E8; color: #ffffff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-size: 14px; font-weight: 600; }
  .footer       { background: #f8f9fa; padding: 20px 40px; text-align: center; border-top: 1px solid #e8eaed; }
  .footer p     { margin: 0; color: #80868b; font-size: 12px; }
  .badge-hired       { display: inline-block; background: #FFF8E1; color: #F9AB00; border: 1px solid #F9AB00; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .badge-shortlisted { display: inline-block; background: #E6F4EA; color: #137333; border: 1px solid #137333; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .badge-interviewed { display: inline-block; background: #FFF3CD; color: #856404; border: 1px solid #F9AB00; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .badge-rejected    { display: inline-block; background: #fce8e6; color: #c5221f; border: 1px solid #c5221f; border-radius: 100px; padding: 4px 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; }
  .tip-box { background: #e8f0fe; border-left: 4px solid #1A73E8; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-top: 20px; }
  .tip-box p { color: #1A73E8; font-size: 13px; margin: 0; }
  .reason-box { background: #fce8e6; border-left: 4px solid #EA4335; padding: 14px 16px; border-radius: 0 8px 8px 0; margin-top: 16px; }
  .reason-box p { color: #c5221f; font-size: 13px; margin: 0; line-height: 1.6; }
  .score-row { background: #E6F4EA; border-radius: 8px; padding: 10px 14px; margin-top: 14px; font-size: 13px; color: #137333; font-weight: 600; }
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
