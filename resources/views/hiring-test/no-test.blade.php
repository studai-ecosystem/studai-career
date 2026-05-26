<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>No Test Available — StudAI Hire</title>
<style>
  body{background:#f4f6fb;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,.1);padding:48px 40px;max-width:480px;width:100%;text-align:center;}
  h1{font-size:22px;font-weight:800;color:#1a1a2e;margin-bottom:8px;}
  p{font-size:14px;color:#6b7280;margin-bottom:24px;}
  .back-btn{display:inline-block;padding:12px 28px;background:linear-gradient(135deg,#1A73E8,#6366f1);color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;}
</style>
</head>
<body>
<div class="card">
  <div style="font-size:56px;margin-bottom:16px;">⏳</div>
  <h1>Test Not Ready Yet</h1>
  <p>The company hasn't uploaded the test questions for this stage yet. Please check back later or wait for an updated email from the company.</p>
  <p><strong>Position:</strong> {{ $application->job->title }}</p>
  <a href="{{ url('/dashboard') }}" class="back-btn">← Back to Dashboard</a>
</div>
</body>
</html>
