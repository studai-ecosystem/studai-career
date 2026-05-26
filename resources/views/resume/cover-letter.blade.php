@extends('layouts.app')

@section('title', 'Cover Letter — ' . $resume->title)

@section('content')
<div style="min-height:100vh;background:#f8fafc;">

    {{-- Top bar --}}
    <div style="background:linear-gradient(135deg,#1A73E8,#0d47a1);padding:20px 32px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <a href="{{ route('resume.edit', $resume) }}" style="color:rgba(255,255,255,.75);font-size:13px;text-decoration:none;display:flex;align-items:center;gap:6px;">
                <i class="fas fa-arrow-left"></i> Back to Resume
            </a>
            <div style="width:1px;height:20px;background:rgba(255,255,255,.3);"></div>
            <div>
                <h1 style="color:#fff;font-size:18px;font-weight:700;margin:0;">Cover Letter</h1>
                <p style="color:rgba(255,255,255,.7);font-size:12px;margin:2px 0 0;">{{ $resume->title }}</p>
            </div>
        </div>
        @if($coverLetter)
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('resume.cover-letter.pdf', $resume) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#fff;color:#dc2626;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <a href="{{ route('resume.cover-letter.docx', $resume) }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.4);border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                <i class="fas fa-file-word"></i> Download Word
            </a>
        </div>
        @endif
    </div>

    <div style="max-width:1100px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:1fr 320px;gap:24px;">

        {{-- LEFT: Letter preview --}}
        <div>
            @if(session('success'))
            <div style="background:#dcfce7;border:1px solid #86efac;border-radius:10px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:13px;color:#166534;">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#991b1b;">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @if($coverLetter)
            {{-- Cover letter document preview --}}
            <div style="background:#fff;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,.08);overflow:hidden;">
                {{-- Document header strip --}}
                <div style="background:linear-gradient(135deg,#1A73E8,#0d47a1);height:6px;"></div>
                <div style="padding:48px 56px;" id="cover-letter-content">
                    <p style="font-size:15px;font-weight:700;color:#1A73E8;margin:0 0 4px;">{{ $resume->full_name }}</p>
                    <p style="font-size:12px;color:#6b7280;margin:0 0 32px;">
                        {{ $resume->email }}
                        @if($resume->phone) · {{ $resume->phone }} @endif
                        @if($resume->location) · {{ $resume->location }} @endif
                    </p>

                    <div style="font-size:14px;color:#1f2937;line-height:1.8;">
                        @foreach(array_filter(array_map('trim', explode("\n\n", $coverLetter->content))) as $para)
                        <p style="margin:0 0 18px;">{{ $para }}</p>
                        @endforeach
                    </div>
                </div>
                {{-- Footer meta --}}
                <div style="background:#f8fafc;border-top:1px solid #f0f0f0;padding:12px 56px;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:11px;color:#9ca3af;">
                        Tone: <strong style="color:#374151;">{{ ucfirst($coverLetter->tone) }}</strong>
                        @if($coverLetter->target_company) · For: <strong style="color:#374151;">{{ $coverLetter->target_company }}</strong> @endif
                        · Generated {{ $coverLetter->updated_at->diffForHumans() }}
                    </span>
                    <span style="font-size:11px;color:#9ca3af;">{{ str_word_count($coverLetter->content) }} words</span>
                </div>
            </div>
            @else
            {{-- Empty state --}}
            <div style="background:#fff;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,.08);padding:64px 40px;text-align:center;">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fas fa-file-signature" style="font-size:28px;color:#1A73E8;"></i>
                </div>
                <h2 style="font-size:20px;font-weight:700;color:#1f2937;margin:0 0 8px;">No Cover Letter Yet</h2>
                <p style="font-size:14px;color:#6b7280;margin:0 0 24px;max-width:400px;margin-left:auto;margin-right:auto;">
                    Generate a professional AI-powered cover letter tailored to your resume in seconds.
                    Choose your tone and target role on the right.
                </p>
                <i class="fas fa-arrow-right" style="color:#9ca3af;font-size:20px;transform:rotate(90deg);display:inline-block;"></i>
            </div>
            @endif
        </div>

        {{-- RIGHT: Generator panel --}}
        <div style="display:flex;flex-direction:column;gap:16px;">

            {{-- Generator card --}}
            <div style="background:#fff;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,.08);overflow:hidden;">
                <div style="background:linear-gradient(135deg,#1e1b4b,#4c1d95);padding:16px 20px;">
                    <h3 style="color:#fff;font-size:14px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px;">
                        <i class="fas fa-magic" style="color:#a78bfa;"></i>
                        {{ $coverLetter ? 'Regenerate' : 'Generate' }} Cover Letter
                    </h3>
                    <p style="color:rgba(255,255,255,.6);font-size:11px;margin:3px 0 0;">AI-powered · takes ~10 seconds</p>
                </div>
                <form method="POST" action="{{ route('resume.cover-letter.generate', $resume) }}" id="generate-form" style="padding:20px;">
                    @csrf

                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;">Target Role</label>
                        <input type="text" name="target_role" value="{{ $coverLetter->target_role ?? $resume->title }}"
                               placeholder="e.g. Senior Software Engineer"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 10px;font-size:13px;color:#1f2937;box-sizing:border-box;">
                    </div>

                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;">Target Company <span style="font-weight:400;color:#9ca3af;">(optional)</span></label>
                        <input type="text" name="target_company" value="{{ $coverLetter->target_company ?? '' }}"
                               placeholder="e.g. Google, Acme Corp"
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:8px 10px;font-size:13px;color:#1f2937;box-sizing:border-box;">
                    </div>

                    <div style="margin-bottom:18px;">
                        <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px;">Tone / Style</label>
                        <div style="display:flex;flex-direction:column;gap:6px;">
                            @foreach($tones as $key => $label)
                            <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1.5px solid {{ ($coverLetter && $coverLetter->tone === $key) ? '#6366f1' : '#e5e7eb' }};border-radius:8px;cursor:pointer;font-size:12px;transition:border-color .15s;"
                                   x-data x-on:click="document.querySelectorAll('.tone-opt').forEach(el=>{el.style.borderColor='#e5e7eb'});$el.style.borderColor='#6366f1'">
                                <input type="radio" name="tone" value="{{ $key }}" class="tone-opt"
                                       {{ ($coverLetter && $coverLetter->tone === $key) || (!$coverLetter && $key === 'professional') ? 'checked' : '' }}
                                       style="accent-color:#6366f1;">
                                <span style="font-weight:600;color:#374151;">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" id="gen-btn"
                            style="width:100%;padding:12px;background:linear-gradient(135deg,#6366f1,#a855f7);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
                        <span id="gen-text">✨ {{ $coverLetter ? 'Regenerate' : 'Generate' }} Cover Letter</span>
                        <span id="gen-loading" style="display:none;"><i class="fas fa-spinner fa-spin"></i> Generating…</span>
                    </button>
                </form>
            </div>

            {{-- Tips --}}
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 18px;">
                <p style="font-size:12px;font-weight:700;color:#1d4ed8;margin:0 0 8px;"><i class="fas fa-lightbulb"></i> Tips</p>
                <ul style="font-size:11px;color:#1e40af;margin:0;padding-left:16px;line-height:1.7;">
                    <li>Add a specific company name for a more tailored letter</li>
                    <li>Try different tones to find the best fit for the role</li>
                    <li>Quantify achievements in your resume for better results</li>
                    <li>Download as PDF for email applications, Word for online portals</li>
                </ul>
            </div>

            @if($coverLetter)
            {{-- Quick download buttons --}}
            <div style="background:#fff;border-radius:12px;box-shadow:0 2px 16px rgba(0,0,0,.08);padding:16px 18px;">
                <p style="font-size:12px;font-weight:700;color:#374151;margin:0 0 10px;"><i class="fas fa-download" style="color:#6b7280;margin-right:4px;"></i> Download</p>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="{{ route('resume.cover-letter.pdf', $resume) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#fee2e2;color:#dc2626;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="fas fa-file-pdf"></i> Download as PDF
                    </a>
                    <a href="{{ route('resume.cover-letter.docx', $resume) }}"
                       style="display:flex;align-items:center;justify-content:center;gap:8px;padding:10px;background:#dbeafe;color:#1d4ed8;border-radius:8px;font-size:13px;font-weight:700;text-decoration:none;">
                        <i class="fas fa-file-word"></i> Download as Word (.doc)
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.getElementById('generate-form').addEventListener('submit', function() {
    document.getElementById('gen-btn').disabled = true;
    document.getElementById('gen-text').style.display = 'none';
    document.getElementById('gen-loading').style.display = 'inline';
});
</script>
@endsection
