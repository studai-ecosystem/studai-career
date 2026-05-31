<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $test->title }} — StudAI Hire</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0}
  body{background:#EBF2FF;font-family:'Segoe UI',Arial,sans-serif;min-height:100vh;}
  .topbar{background:#2D6CDF;color:#fff;padding:16px 24px;display:flex;align-items:center;justify-content:space-between;}
  .topbar-title{font-size:16px;font-weight:700;}
  .topbar-timer{background:rgba(255,255,255,.2);border-radius:50px;padding:6px 16px;font-size:14px;font-weight:700;font-family:monospace;}
  .topbar-timer.warn{background:rgba(185, 28, 28,.8);}
  .container{max-width:820px;margin:32px auto;padding:0 16px;}
  .card{background:#fff;border-radius:16px;box-shadow: none;overflow:hidden;margin-bottom:24px;}
  .card-header{padding:20px 28px;border-bottom:1px solid #F0F0EE;}
  .card-body{padding:24px 28px;}
  .job-info{font-size:13px;color:#737373;margin-bottom:2px;}
  .test-title{font-size:20px;font-weight:800;color:#0C0C0C;}
  .instructions{background:#EBF2FF;border-left:4px solid #2D6CDF;border-radius:8px;padding:14px 16px;font-size:13px;color:#1B57C4;margin-bottom:24px;}
  .meta-row{display:flex;gap:24px;margin-bottom:24px;flex-wrap:wrap;}
  .meta-item{display:flex;align-items:center;gap:6px;font-size:13px;color:#737373;}
  .meta-item strong{color:#0C0C0C;}
  .q-card{border:2px solid #E2E2E0;border-radius:12px;padding:20px;margin-bottom:16px;transition:border-color .2s;}
  .q-card.answered{border-color:#2D6CDF;}
  .q-num{font-size:11px;font-weight:700;color:#A8A8A8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;}
  .q-text{font-size:15px;font-weight:600;color:#0C0C0C;margin-bottom:14px;line-height:1.5;}
  .option{display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid #E2E2E0;border-radius:10px;cursor:pointer;margin-bottom:8px;transition:all .15s;}
  .option:hover{background:#EBF2FF;border-color:#2D6CDF;}
  .option input[type=radio]{display:none;}
  .option input[type=radio]:checked + .opt-label{color:#1B57C4;font-weight:600;}
  .option:has(input:checked){background:#EBF2FF;border-color:#2D6CDF;}
  .opt-dot{width:18px;height:18px;border-radius:50%;border:2px solid #C8C8C5;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s;}
  .option:has(input:checked) .opt-dot{background:#2D6CDF;border-color:#2D6CDF;}
  .opt-label{font-size:14px;color:#3D3D3D;}
  .progress-bar{height:6px;background:#E2E2E0;border-radius:6px;overflow:hidden;margin-bottom:24px;}
  .progress-fill{height:100%;background:#2D6CDF;transition:width .3s;}
  .submit-btn{width:100%;padding:14px;background:#2D6CDF;color:#fff;font-size:16px;font-weight:700;border:none;border-radius:12px;cursor:pointer;transition:opacity .2s;}
  .submit-btn:hover{opacity:.9;}
  .submit-btn:disabled{opacity:.5;cursor:not-allowed;}
  .answered-count{text-align:center;font-size:13px;color:#737373;margin-bottom:16px;}
</style>
</head>
<body>

<div class="topbar">
  <div class="topbar-title">
    {{ $stage === 'company_info_test' ? '📋' : '🧠' }}
    {{ \App\Models\HiringTest::STAGE_LABELS[$stage] ?? $stage }} — {{ $application->job->company->name ?? 'Company' }}
  </div>
  <div class="topbar-timer" id="timer">{{ str_pad((int)($test->time_limit_minutes / 60), 2, '0', STR_PAD_LEFT) }}:{{ str_pad($test->time_limit_minutes % 60, 2, '0', STR_PAD_LEFT) }}:00</div>
</div>

<div class="container">
  <div class="card">
    <div class="card-header">
      <div class="job-info">{{ $application->job->title }} at {{ $application->job->company->name ?? 'Company' }}</div>
      <div class="test-title">{{ $test->title }}</div>
    </div>
    <div class="card-body">
      @if($test->instructions)
      <div class="instructions">📌 {{ $test->instructions }}</div>
      @endif
      <div class="meta-row">
        <div class="meta-item">⏱️ <strong>{{ $test->time_limit_minutes }} mins</strong></div>
        <div class="meta-item">📝 <strong>{{ count($test->questions) }} questions</strong></div>
        <div class="meta-item">✅ Pass: <strong>{{ $test->pass_score }}%</strong></div>
      </div>

      <div class="progress-bar"><div class="progress-fill" id="progress" style="width:0%"></div></div>
      <div class="answered-count" id="answered-count">0 / {{ count($test->questions) }} answered</div>

      <form method="POST" action="{{ route('hiring-test.submit', ['token'=>$token,'stage'=>$stage]) }}" id="test-form">
        @csrf

        @foreach($test->questions as $i => $q)
        <div class="q-card" id="qcard-{{ $i }}">
          <div class="q-num">Question {{ $i + 1 }} of {{ count($test->questions) }}</div>
          <div class="q-text">{{ $q['question'] }}</div>
          @foreach($q['options'] as $oi => $opt)
          <label class="option" onclick="markAnswered({{ $i }}, this)">
            <input type="radio" name="answers[{{ $i }}]" value="{{ $oi }}">
            <div class="opt-dot"></div>
            <span class="opt-label">{{ $opt }}</span>
          </label>
          @endforeach
        </div>
        @endforeach

        <p id="warn-unanswered" class="hidden" style="color:#2D6CDF;font-size:13px;text-align:center;margin-bottom:12px;">⚠️ Please answer all questions before submitting.</p>
        <button type="button" class="submit-btn" onclick="confirmSubmit()">Submit Test →</button>
      </form>
    </div>
  </div>
</div>

<script>
const totalQ = {{ count($test->questions) }};
const timeLimitSecs = {{ $test->time_limit_minutes * 60 }};
let answered = {};
let secsLeft = timeLimitSecs;

function markAnswered(i, label) {
  answered[i] = true;
  document.getElementById('qcard-' + i).classList.add('answered');
  updateProgress();
}

function updateProgress() {
  const count = Object.keys(answered).length;
  document.getElementById('progress').style.width = (count / totalQ * 100) + '%';
  document.getElementById('answered-count').textContent = count + ' / ' + totalQ + ' answered';
}

function confirmSubmit() {
  const count = Object.keys(answered).length;
  if (count < totalQ) {
    if (!confirm(`You've answered ${count} of ${totalQ} questions. Submit anyway?`)) return;
  }
  document.getElementById('test-form').submit();
}

// Timer
const timerEl = document.getElementById('timer');
const interval = setInterval(() => {
  secsLeft--;
  if (secsLeft <= 0) {
    clearInterval(interval);
    timerEl.textContent = '00:00:00';
    timerEl.classList.add('warn');
    document.getElementById('test-form').submit();
    return;
  }
  const h = Math.floor(secsLeft / 3600);
  const m = Math.floor((secsLeft % 3600) / 60);
  const s = secsLeft % 60;
  timerEl.textContent = String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
  if (secsLeft <= 300) timerEl.classList.add('warn'); // last 5 mins
}, 1000);
</script>
</body>
</html>
