#!/usr/bin/env python3
"""Script to write formal professional email templates for StudAI Career."""
import os

BASE = r"c:\Users\user\Downloads\studai-career\resources\views\emails"

SHARED_STYLES = """
  * { box-sizing:border-box; }
  body { margin:0; padding:0; background:#f3f4f6; font-family:Arial,'Helvetica Neue',Helvetica,sans-serif; color:#374151; }
  .shell { max-width:640px; margin:32px auto; }
  .card  { background:#ffffff; border:1px solid #e5e7eb; border-top:4px solid #1A73E8; }
  .top-bar { padding:18px 36px; border-bottom:1px solid #e5e7eb; }
  .top-bar table { width:100%; border-collapse:collapse; }
  .wordmark { font-size:15px; font-weight:700; color:#111827; letter-spacing:-.3px; }
  .wordmark span { color:#1A73E8; }
  .top-date { font-size:11px; color:#9ca3af; text-align:right; }
  .status-header { background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:24px 36px; }
  .status-label { display:inline-block; font-size:11px; font-weight:700; letter-spacing:.08em; text-transform:uppercase; padding:4px 12px; margin-bottom:10px; }
  .label-shortlisted { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .label-hired       { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
  .label-interviewed { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
  .label-rejected    { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
  .status-header h2 { margin:0; font-size:19px; font-weight:700; color:#111827; line-height:1.3; }
  .status-header p  { margin:6px 0 0; font-size:13px; color:#6b7280; }
  .body { padding:28px 36px; }
  .salutation { font-size:15px; color:#374151; margin:0 0 20px; }
  .prose { font-size:14px; line-height:1.75; color:#374151; margin:0 0 16px; white-space:pre-line; }
  hr.rule { border:none; border-top:1px solid #e5e7eb; margin:22px 0 16px; }
  .section-label { font-size:10px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#9ca3af; margin:0 0 12px; }
  table.data { width:100%; border-collapse:collapse; font-size:13px; margin-bottom:4px; }
  table.data td { padding:9px 12px; border:1px solid #e5e7eb; vertical-align:top; line-height:1.5; }
  table.data td.lbl { background:#f9fafb; font-weight:600; color:#111827; width:170px; white-space:nowrap; }
  table.data td.val { color:#374151; }
  .score-pill { display:inline-block; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:2px 10px; font-size:12px; font-weight:700; }
  .notice-box { background:#f9fafb; border:1px solid #e5e7eb; border-left:3px solid #1A73E8; padding:14px 16px; font-size:13px; color:#374151; line-height:1.7; }
  .rejection-box { background:#fef2f2; border:1px solid #fecaca; border-left:3px solid #991b1b; padding:14px 16px; font-size:13px; color:#7f1d1d; line-height:1.7; }
  .skills-wrap { margin:0; padding:0; }
  .skill { display:inline-block; background:#f3f4f6; color:#374151; border:1px solid #d1d5db; font-size:12px; padding:3px 10px; margin:2px 4px 2px 0; }
  .text-block { background:#f9fafb; border:1px solid #e5e7eb; padding:14px 16px; font-size:13px; color:#374151; line-height:1.75; white-space:pre-line; }
  .links-row a { color:#1A73E8; text-decoration:none; margin-right:16px; font-size:13px; }
  .btn-primary { display:inline-block; background:#1A73E8; color:#ffffff; text-decoration:none; padding:11px 26px; font-size:13px; font-weight:700; letter-spacing:.02em; }
  .btn-secondary { display:inline-block; background:#ffffff; color:#1A73E8; text-decoration:none; padding:10px 22px; font-size:13px; font-weight:600; border:1.5px solid #1A73E8; }
  .cta-row { text-align:center; padding:20px 0 4px; }
  .footer { background:#f9fafb; border-top:1px solid #e5e7eb; padding:16px 36px; }
  .footer p { margin:0; font-size:11px; color:#9ca3af; line-height:1.7; text-align:center; }
  .footer strong { color:#6b7280; }
"""

# ── 1. candidate-hiring.blade.php ─────────────────────────────────────────────
CANDIDATE_HIRING = """\
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailSubject }}</title>
<style>
""" + SHARED_STYLES + """\
</style>
</head>
<body>
<div class="shell">
<div class="card">

  <div class="top-bar">
    <table>
      <tr>
        <td><span class="wordmark">Stud<span>AI</span> Hire</span></td>
        <td class="top-date">Application Update &bull; {{ now()->format('d M Y') }}</td>
      </tr>
    </table>
  </div>

  <div class="status-header">
    <span class="status-label label-{{ $eventType }}">
      @if($eventType === 'hired') Offer Extended
      @elseif($eventType === 'rejected') Application Unsuccessful
      @elseif($eventType === 'interviewed') Interview Invitation
      @else Shortlisted
      @endif
    </span>
    <h2>
      @if($eventType === 'hired') Congratulations &mdash; You Have Received a Job Offer
      @elseif($eventType === 'rejected') Update on Your Application
      @elseif($eventType === 'interviewed') You Have Been Invited to an Interview
      @else Your Application Has Been Shortlisted
      @endif
    </h2>
    <p>{{ $jobTitle }} &bull; {{ $companyName }}</p>
  </div>

  <div class="body">
    <p class="salutation">Dear {{ $candidateName }},</p>
    <p class="prose">{{ $body }}</p>

    <hr class="rule">
    <p class="section-label">Application Reference</p>
    <table class="data">
      <tr><td class="lbl">Position</td><td class="val">{{ $jobTitle }}</td></tr>
      <tr><td class="lbl">Company</td><td class="val">{{ $companyName }}</td></tr>
      <tr><td class="lbl">Status</td><td class="val">
        @if($eventType === 'hired') Offer Extended
        @elseif($eventType === 'rejected') Application Unsuccessful
        @elseif($eventType === 'interviewed') Interview Invitation Sent
        @else Shortlisted for Next Stage
        @endif
      </td></tr>
      @if($matchScore > 0)
      <tr><td class="lbl">AI Match Score</td><td class="val">{{ number_format($matchScore, 1) }}%</td></tr>
      @endif
    </table>

    @if($eventType === 'rejected' && !empty($rejectionReason))
    <p class="section-label" style="margin-top:20px;">Feedback</p>
    <div class="rejection-box">{{ $rejectionReason }}</div>
    @endif

    @if(!empty($studentTip ?? ''))
    <p class="section-label" style="margin-top:20px;">Recommendation</p>
    <div class="notice-box">{{ $studentTip }}</div>
    @elseif($eventType === 'rejected')
    <div class="notice-box" style="margin-top:20px;">Your profile remains active on StudAI Hire. We encourage you to explore other opportunities that match your skills and experience. New positions are posted regularly.</div>
    @endif

    <div class="cta-row">
      @if($eventType === 'rejected')
        <a href="{{ url('/jobs') }}" class="btn-primary">Browse Available Positions</a>
      @else
        <a href="{{ url('/applications') }}" class="btn-primary">View My Application</a>
      @endif
    </div>
  </div>

  <div class="footer">
    <p>This notification was sent by <strong>StudAI Hire Platform</strong> on behalf of {{ $companyName }}.</p>
    <p>If you did not apply for this position, please disregard this email.</p>
    <p>&copy; {{ date('Y') }} StudAI Career. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
"""

# ── 2. new-application-hr.blade.php ───────────────────────────────────────────
NEW_APPLICATION_HR = """\
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Application Received</title>
<style>
""" + SHARED_STYLES + """\
</style>
</head>
<body>
<div class="shell">
<div class="card">

  <div class="top-bar">
    <table>
      <tr>
        <td><span class="wordmark">Stud<span>AI</span> Hire</span></td>
        <td class="top-date">HR Notification &bull; {{ now()->format('d M Y, H:i') }}</td>
      </tr>
    </table>
  </div>

  <div class="status-header">
    <span class="status-label label-shortlisted">New Application</span>
    <h2>New Application Received</h2>
    <p>{{ $application->user?->name ?? 'A candidate' }} has applied for {{ $application->job->title ?? 'your open position' }}</p>
  </div>

  <div class="body">

    <p class="section-label">Application Summary</p>
    <table class="data">
      <tr><td class="lbl">Application No.</td><td class="val">{{ $application->application_number ?? 'N/A' }}</td></tr>
      <tr><td class="lbl">Position</td><td class="val">{{ $application->job->title ?? 'N/A' }}</td></tr>
      <tr><td class="lbl">Date Applied</td><td class="val">{{ $application->submitted_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}</td></tr>
      @if($application->match_score)
      <tr><td class="lbl">AI Match Score</td><td class="val"><span class="score-pill">{{ $application->match_score }}%</span></td></tr>
      @endif
    </table>

    <hr class="rule">
    <p class="section-label">Candidate Details</p>
    @php $profile = $application->user?->profile; @endphp
    <table class="data">
      <tr><td class="lbl">Full Name</td><td class="val">{{ $application->user?->name ?? 'N/A' }}</td></tr>
      <tr><td class="lbl">Email Address</td><td class="val">{{ $application->user?->email ?? 'N/A' }}</td></tr>
      @if($profile?->headline)
      <tr><td class="lbl">Headline</td><td class="val">{{ $profile->headline }}</td></tr>
      @endif
      @if($profile?->current_location)
      <tr><td class="lbl">Location</td><td class="val">{{ $profile->current_location }}</td></tr>
      @endif
      @if($profile?->work_preference)
      <tr><td class="lbl">Work Preference</td><td class="val">{{ ucfirst($profile->work_preference) }}</td></tr>
      @endif
      @if($profile?->notice_period)
      <tr><td class="lbl">Notice Period</td><td class="val">{{ $profile->notice_period }}</td></tr>
      @endif
      @if($profile?->expected_salary)
      <tr><td class="lbl">Expected Salary</td><td class="val">{{ $profile->expected_salary }}</td></tr>
      @endif
    </table>

    @if(!empty($application->cover_letter))
    <hr class="rule">
    <p class="section-label">Cover Letter</p>
    <div class="text-block">{{ $application->cover_letter }}</div>
    @endif

    <div class="cta-row" style="margin-top:24px;">
      <a href="{{ url('/employer/applicants/' . $application->id) }}" class="btn-primary" style="margin-right:12px;">View Full Application</a>
      <a href="{{ url('/employer/applicants?status=pending') }}" class="btn-secondary">Open ATS Dashboard</a>
    </div>

  </div>

  <div class="footer">
    <p>This is an automated internal notification from <strong>StudAI Hire Platform</strong>. Do not reply to this email.</p>
    <p>&copy; {{ date('Y') }} StudAI Career. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
"""

# ── 3. application-confirmation.blade.php ─────────────────────────────────────
APPLICATION_CONFIRMATION = """\
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application Received</title>
<style>
""" + SHARED_STYLES + """\
</style>
</head>
<body>
<div class="shell">
<div class="card">

  <div class="top-bar">
    <table>
      <tr>
        <td><span class="wordmark">Stud<span>AI</span> Hire</span></td>
        <td class="top-date">Application Confirmation &bull; {{ now()->format('d M Y') }}</td>
      </tr>
    </table>
  </div>

  <div class="status-header">
    <span class="status-label label-shortlisted">Received</span>
    <h2>Application Successfully Submitted</h2>
    <p>{{ $jobTitle }} &bull; {{ $companyName }}</p>
  </div>

  <div class="body">
    <p class="salutation">Dear {{ $candidateName }},</p>
    <p class="prose">We confirm that your application has been received and is currently under review. You will be notified at each stage of the hiring process.</p>

    <hr class="rule">
    <p class="section-label">Application Details</p>
    <table class="data">
      <tr><td class="lbl">Application No.</td><td class="val">{{ $applicationNumber }}</td></tr>
      <tr><td class="lbl">Position Applied</td><td class="val">{{ $jobTitle }}</td></tr>
      <tr><td class="lbl">Company</td><td class="val">{{ $companyName }}</td></tr>
      <tr><td class="lbl">Date Submitted</td><td class="val">{{ $applicationDate }}</td></tr>
      <tr><td class="lbl">Application Deadline</td><td class="val">{{ $closingDate }}</td></tr>
      <tr><td class="lbl">Evaluation Date</td><td class="val">{{ $evaluationDate }}</td></tr>
    </table>

    <hr class="rule">
    <p class="section-label">What Happens Next</p>
    <table class="data">
      <tr>
        <td class="lbl" style="white-space:normal;">1. Application Review</td>
        <td class="val">Our team will review your resume and cover letter before the closing date.</td>
      </tr>
      <tr>
        <td class="lbl" style="white-space:normal;">2. Shortlisting</td>
        <td class="val">Shortlisted candidates will be notified and invited to proceed to the next stage by {{ $evaluationDate }}.</td>
      </tr>
      <tr>
        <td class="lbl" style="white-space:normal;">3. Selection Process</td>
        <td class="val">Company Knowledge Test &rarr; Aptitude Assessment &rarr; One-on-One Interview</td>
      </tr>
    </table>

    <div class="notice-box" style="margin-top:20px;">
      While awaiting your results, we recommend keeping your profile updated and practising with the StudAI Interview Lab to prepare for upcoming assessment stages.
    </div>

    <div class="cta-row">
      <a href="{{ url('/applications') }}" class="btn-primary">Track My Application</a>
    </div>
  </div>

  <div class="footer">
    <p>You are receiving this because you submitted an application via <strong>StudAI Hire</strong>.</p>
    <p>&copy; {{ date('Y') }} StudAI Career. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
"""

# ── 4. pipeline-stage-advanced.blade.php ──────────────────────────────────────
PIPELINE_STAGE_ADVANCED = """\
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Next Stage Invitation</title>
<style>
""" + SHARED_STYLES + """\
  .stage-block { background:#f9fafb; border:1px solid #e5e7eb; padding:20px 24px; margin-bottom:20px; }
  .stage-block table { width:100%; border-collapse:collapse; }
  .stage-block td { vertical-align:middle; }
  .stage-name { font-size:16px; font-weight:700; color:#111827; }
  .stage-date-label { font-size:11px; text-transform:uppercase; letter-spacing:.07em; color:#9ca3af; }
  .stage-date-value { font-size:17px; font-weight:700; color:#1A73E8; }
  .prep-list { margin:0; padding:0 0 0 18px; }
  .prep-list li { font-size:13px; color:#374151; line-height:1.75; }
  .notes-block { background:#fffbeb; border:1px solid #fde68a; border-left:3px solid #d97706; padding:14px 16px; font-size:13px; color:#78350f; line-height:1.7; }
</style>
</head>
<body>
<div class="shell">
<div class="card">

  <div class="top-bar">
    <table>
      <tr>
        <td><span class="wordmark">Stud<span>AI</span> Hire</span></td>
        <td class="top-date">Hiring Pipeline &bull; {{ now()->format('d M Y') }}</td>
      </tr>
    </table>
  </div>

  <div class="status-header">
    <span class="status-label label-interviewed">Next Stage Invitation</span>
    <h2>
      @if($stage === 'company_info_test') Company Information Test
      @elseif($stage === 'aptitude') Aptitude Assessment
      @elseif($stage === 'tech_test') Technical Assessment
      @else Non-Technical Assessment
      @endif
    </h2>
    <p>{{ $jobTitle }} &bull; {{ $companyName }}</p>
  </div>

  <div class="body">
    <p class="salutation">Dear {{ $candidateName }},</p>
    <p class="prose">We are pleased to inform you that you have been selected to proceed to the next stage of the hiring process. Please review the details below and ensure you are prepared by the scheduled date.</p>

    <hr class="rule">
    <p class="section-label">Assessment Details</p>
    <div class="stage-block">
      <table>
        <tr>
          <td>
            <div class="stage-name">
              @if($stage === 'company_info_test') Company Information Test
              @elseif($stage === 'aptitude') Aptitude Assessment
              @elseif($stage === 'tech_test') Technical Assessment
              @else Non-Technical Assessment
              @endif
            </div>
          </td>
          <td style="text-align:right;">
            <div class="stage-date-label">Scheduled Date</div>
            <div class="stage-date-value">{{ $scheduledDate }}</div>
          </td>
        </tr>
      </table>
    </div>

    <table class="data">
      <tr><td class="lbl">Position</td><td class="val">{{ $jobTitle }}</td></tr>
      <tr><td class="lbl">Company</td><td class="val">{{ $companyName }}</td></tr>
      <tr><td class="lbl">Assessment Type</td><td class="val">{{ $stageLabel }}</td></tr>
    </table>

    @if($notes)
    <p class="section-label" style="margin-top:20px;">Note from the Company</p>
    <div class="notes-block">{{ $notes }}</div>
    @endif

    <hr class="rule">
    <p class="section-label">Preparation Guidelines</p>
    @if($stage === 'company_info_test')
    <ul class="prep-list">
      <li>Research {{ $companyName }}'s mission, values, products, and recent news.</li>
      <li>Familiarise yourself with the industry they operate in and key competitors.</li>
      <li>Understand their company culture and work environment.</li>
    </ul>
    @elseif($stage === 'aptitude')
    <ul class="prep-list">
      <li>Practise numerical reasoning, logical reasoning, and verbal ability questions.</li>
      <li>Work on speed and accuracy &mdash; most aptitude tests are strictly time-limited.</li>
      <li>Review basic mathematics, data interpretation, and abstract reasoning.</li>
    </ul>
    @elseif($stage === 'tech_test')
    <ul class="prep-list">
      <li>Review core concepts relevant to the role (e.g. data structures, system design, coding).</li>
      <li>Practise problem-solving on platforms such as LeetCode or HackerRank.</li>
      <li>The assessment is timed &mdash; read all questions carefully before beginning.</li>
    </ul>
    @else
    <ul class="prep-list">
      <li>Prepare for situational and behavioural questions relevant to your field.</li>
      <li>Review industry terminology and general business knowledge.</li>
      <li>The assessment is timed &mdash; remain calm and manage your time effectively.</li>
    </ul>
    @endif

    @if(!empty($testLink))
    <div class="cta-row" style="margin-top:24px;">
      <a href="{{ $testLink }}" class="btn-primary">Access Assessment</a>
    </div>
    @else
    <div class="cta-row" style="margin-top:24px;">
      <a href="{{ url('/applications') }}" class="btn-primary">View Application Dashboard</a>
    </div>
    @endif

    <p class="prose" style="margin-top:24px;font-size:13px;color:#6b7280;">If you have any questions regarding this assessment, please contact the hiring team. We wish you the best of luck.</p>
  </div>

  <div class="footer">
    <p>This notification was sent by <strong>StudAI Hire Platform</strong> on behalf of {{ $companyName }}.</p>
    <p>&copy; {{ date('Y') }} StudAI Career. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
"""

# ── 5. student-welcome.blade.php ──────────────────────────────────────────────
STUDENT_WELCOME = """\
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to StudAI Career</title>
<style>
""" + SHARED_STYLES + """\
  .welcome-header { background:#1A73E8; padding:32px 36px; border-bottom:1px solid #1557b0; }
  .welcome-header .wordmark-lg { font-size:18px; font-weight:700; color:#ffffff; letter-spacing:-.3px; }
  .welcome-header .wordmark-lg span { color:#a8c7fa; }
  .welcome-header h1 { margin:16px 0 6px; color:#ffffff; font-size:20px; font-weight:700; line-height:1.3; }
  .welcome-header p  { margin:0; color:rgba(255,255,255,.8); font-size:13px; }
  .feature-row { display:table; width:100%; border-collapse:collapse; margin-bottom:10px; }
  .feature-icon { display:table-cell; width:44px; vertical-align:middle; }
  .feature-icon-dot { width:36px; height:36px; background:#eff6ff; border:1px solid #bfdbfe; display:inline-block; line-height:36px; text-align:center; font-size:11px; font-weight:700; color:#1d4ed8; }
  .feature-text { display:table-cell; vertical-align:middle; padding:10px 12px 10px 4px; border-bottom:1px solid #f3f4f6; }
  .feature-text h4 { margin:0 0 2px; font-size:13px; font-weight:600; color:#111827; }
  .feature-text p  { margin:0; font-size:12px; color:#6b7280; line-height:1.5; }
  .steps-table { width:100%; border-collapse:collapse; }
  .steps-table td { padding:10px 12px; border:1px solid #e5e7eb; vertical-align:top; font-size:13px; line-height:1.6; }
  .steps-table td.step-num { background:#1A73E8; color:#ffffff; font-weight:700; font-size:14px; text-align:center; width:36px; white-space:nowrap; }
  .steps-table td.step-text { color:#374151; }
  .steps-table td.step-text strong { color:#111827; }
</style>
</head>
<body>
<div class="shell">
<div class="card">

  <div class="welcome-header">
    <div class="wordmark-lg">Stud<span>AI</span> Career</div>
    <h1>Welcome, {{ $user->name }}</h1>
    <p>Your AI-powered career platform is ready. Your Career, On Autopilot.</p>
  </div>

  <div class="body">
    <p class="salutation">Dear {{ $user->name }},</p>
    <p class="prose">Thank you for joining StudAI Career. Your account is now active and you have full access to our AI-powered career development tools. We have built this platform to help you find the right opportunity, prepare effectively, and advance your career with confidence.</p>

    <hr class="rule">
    <p class="section-label">Your Career Toolkit</p>

    <div class="feature-row">
      <div class="feature-icon"><div class="feature-icon-dot">AI</div></div>
      <div class="feature-text">
        <h4>AI Job Matching</h4>
        <p>Receive a personalised match score for every job based on your skills and experience.</p>
      </div>
    </div>
    <div class="feature-row">
      <div class="feature-icon"><div class="feature-icon-dot">CV</div></div>
      <div class="feature-text">
        <h4>Resume Analyser</h4>
        <p>Upload your resume and receive instant AI feedback on areas for improvement.</p>
      </div>
    </div>
    <div class="feature-row">
      <div class="feature-icon"><div class="feature-icon-dot">SAL</div></div>
      <div class="feature-text">
        <h4>Negotiation Strategist</h4>
        <p>Generate AI-crafted salary negotiation scripts tailored to your specific offer.</p>
      </div>
    </div>
    <div class="feature-row">
      <div class="feature-icon"><div class="feature-icon-dot">PATH</div></div>
      <div class="feature-text">
        <h4>Career Path Builder</h4>
        <p>Identify roles you can progress into and the skills required to get there.</p>
      </div>
    </div>
    <div class="feature-row">
      <div class="feature-icon"><div class="feature-icon-dot">MKT</div></div>
      <div class="feature-text">
        <h4>Market Intelligence</h4>
        <p>Access live salary benchmarks and hiring trends for your target roles.</p>
      </div>
    </div>

    <hr class="rule">
    <p class="section-label">Getting Started &mdash; Three Steps</p>
    <table class="steps-table">
      <tr>
        <td class="step-num">1</td>
        <td class="step-text"><strong>Complete your profile</strong> &mdash; Add your skills, experience, and education so the AI can match you accurately to open positions.</td>
      </tr>
      <tr>
        <td class="step-num">2</td>
        <td class="step-text"><strong>Upload your resume</strong> &mdash; Allow the AI to analyse it and provide improvement recommendations.</td>
      </tr>
      <tr>
        <td class="step-num">3</td>
        <td class="step-text"><strong>Apply to matched jobs</strong> &mdash; Apply with a single click using your full profile and track every application from your dashboard.</td>
      </tr>
    </table>

    <div class="notice-box" style="margin-top:20px;">
      Candidates with complete profiles receive significantly more interview invitations. We recommend spending a few minutes completing your profile to maximise your visibility to employers.
    </div>

    <div class="cta-row">
      <a href="{{ url('/dashboard') }}" class="btn-primary" style="margin-right:12px;">Go to Dashboard</a>
      <a href="{{ url('/profile') }}" class="btn-secondary">Complete My Profile</a>
    </div>
  </div>

  <div class="footer">
    <p>You are receiving this because you created an account on <strong>StudAI Career</strong>.</p>
    <p>If you did not register, please disregard this email.</p>
    <p>&copy; {{ date('Y') }} StudAI Career. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
"""

files = {
    'candidate-hiring.blade.php': CANDIDATE_HIRING,
    'new-application-hr.blade.php': NEW_APPLICATION_HR,
    'application-confirmation.blade.php': APPLICATION_CONFIRMATION,
    'pipeline-stage-advanced.blade.php': PIPELINE_STAGE_ADVANCED,
    'student-welcome.blade.php': STUDENT_WELCOME,
}

for name, content in files.items():
    path = os.path.join(BASE, name)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Written: {name}")

print("All done.")
