<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Contact Form Submission</title>
<style>
  body { margin:0; padding:0; background:#F0F0EE; font-family:'Google Sans',Arial,sans-serif; }
  .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow: none; }
  .header { background:#2D6CDF; padding:28px 36px; }
  .header h1 { margin:0; color:#fff; font-size:18px; font-weight:600; }
  .header p  { margin:4px 0 0; color:rgba(255,255,255,.8); font-size:12px; }
  .body { padding:20px 36px 32px; }
  .section-title { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#737373; margin:20px 0 8px; }
  table.meta { width:100%; border-collapse:collapse; font-size:13px; }
  table.meta td { padding:7px 10px; border:1px solid #E2E2E0; color:#3D3D3D; vertical-align:top; }
  table.meta td:first-child { background:#F0F0EE; font-weight:600; width:120px; white-space:nowrap; }
  .message-box { margin:8px 0 0; background:#F0F0EE; border:1px solid #E2E2E0; border-radius:8px; padding:16px; font-size:14px; color:#3D3D3D; line-height:1.6; white-space:pre-wrap; }
  .footer { background:#F0F0EE; padding:14px 36px; border-top:1px solid #E2E2E0; text-align:center; }
  .footer p { margin:0; color:#737373; font-size:11px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>New Contact Form Submission</h1>
    <p>A visitor has reached out through the website contact form.</p>
  </div>
  <div class="body">
    <div class="section-title">Sender Details</div>
    <table class="meta">
      <tr><td>Name</td><td>{{ $senderName }}</td></tr>
      <tr><td>Email</td><td>{{ $senderEmail }}</td></tr>
      <tr><td>Subject</td><td>{{ $contactSubject }}</td></tr>
    </table>

    <div class="section-title">Message</div>
    <div class="message-box">{{ $contactMessage }}</div>
  </div>
  <div class="footer">
    <p>Reply directly to this email to respond to {{ $senderName }}.</p>
  </div>
</div>
</body>
</html>
