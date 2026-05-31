<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>No Test Available — StudAI Hire</title>
<style>
  body{background:#EBF2FF;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
  .card{background:#fff;border-radius:20px;box-shadow: none;padding:48px 40px;max-width:480px;width:100%;text-align:center;}
  h1{font-size:22px;font-weight:800;color:#0C0C0C;margin-bottom:8px;}
  p{font-size:14px;color:#737373;margin-bottom:24px;}
  .back-btn{display:inline-block;padding:12px 28px;background:#2D6CDF;color:#fff;border-radius:12px;text-decoration:none;font-weight:700;font-size:14px;}
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
