<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New sign-in detected</title>
<style>
  body { margin:0; padding:0; background:#f5f5f5; font-family:'Google Sans',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.08); }
  .header { background:linear-gradient(135deg,#1A73E8 0%,#1557b0 100%); padding:40px 40px 32px; text-align:center; }
  .header .logo { font-size:26px; font-weight:700; color:#fff; letter-spacing:-0.5px; }
  .header .logo span { color:#a8c7fa; }
  .header h1 { margin:16px 0 6px; color:#fff; font-size:22px; font-weight:600; }
  .header p { margin:0; color:rgba(255,255,255,.85); font-size:14px; }
  .body { padding:36px 40px; }
  .greeting { font-size:16px; color:#3c4043; font-weight:500; margin-bottom:6px; }
  .intro { font-size:14px; color:#5f6368; line-height:1.7; margin-bottom:24px; }
  .detail-row { display:table; width:100%; border-collapse:collapse; margin-bottom:8px; }
  .detail-label { display:table-cell; width:130px; padding:10px 12px; font-size:12px; font-weight:600; color:#80868b; background:#f8f9fa; border-radius:8px 0 0 8px; }
  .detail-value { display:table-cell; padding:10px 12px; font-size:13px; color:#202124; background:#f8f9fa; border-radius:0 8px 8px 0; }
  .tip-box { background:#FCE8E6; border-left:4px solid #D93025; padding:14px 16px; border-radius:0 8px 8px 0; margin:24px 0 0; }
  .tip-box p { margin:0; font-size:13px; color:#C5221F; line-height:1.6; }
  .footer { background:#f8f9fa; padding:20px 40px; border-top:1px solid #e8eaed; text-align:center; }
  .footer p { margin:0; color:#80868b; font-size:11px; line-height:1.8; }
</style>
</head>
<body>
<div class="wrapper">

  <div class="header">
    <div class="logo">StudAI <span>Hire</span></div>
    <h1>New sign-in detected 🔐</h1>
    <p>We noticed a new login to your account</p>
  </div>

  <div class="body">
    <p class="greeting">Hi {{ $userName }},</p>
    <p class="intro">
      Your StudAI Hire account was just accessed. If this was you, no action is needed.
      If you don't recognise this activity, please reset your password right away.
    </p>

    <div class="detail-row">
      <div class="detail-label">Date &amp; Time</div>
      <div class="detail-value">{{ $loginTime }}</div>
    </div>
    <div class="detail-row">
      <div class="detail-label">IP Address</div>
      <div class="detail-value">{{ $ipAddress }}</div>
    </div>
    <div class="detail-row">
      <div class="detail-label">Device</div>
      <div class="detail-value">{{ $device }}</div>
    </div>

    <div class="tip-box">
      <p><strong>Wasn't you?</strong> Secure your account immediately by resetting your password from the login page.</p>
    </div>
  </div>

  <div class="footer">
    <p>StudAI Hire — Your Career. On Autopilot.</p>
    <p>This is an automated security notification.</p>
  </div>

</div>
</body>
</html>
