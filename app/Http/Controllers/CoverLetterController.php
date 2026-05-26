<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CoverLetter;
use App\Models\Resume;
use App\Traits\InteractsWithAI;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoverLetterController extends Controller
{
    use InteractsWithAI;

    private array $tones = [
        'professional' => 'Professional & Formal',
        'confident'    => 'Confident & Direct',
        'creative'     => 'Creative & Engaging',
        'concise'      => 'Concise & Impactful',
        'enthusiastic' => 'Enthusiastic & Passionate',
    ];

    /** Show the cover letter for a resume (or the generate form if none exists). */
    public function show(Resume $resume)
    {
        $this->authorize('view', $resume);

        $coverLetter = CoverLetter::where('resume_id', $resume->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('resume.cover-letter', [
            'resume'      => $resume,
            'coverLetter' => $coverLetter,
            'tones'       => $this->tones,
        ]);
    }

    /** Generate (or re-generate) a cover letter via AI. */
    public function generate(Request $request, Resume $resume)
    {
        $this->authorize('view', $resume);

        $request->validate([
            'tone'           => 'required|in:professional,confident,creative,concise,enthusiastic',
            'target_role'    => 'nullable|string|max:200',
            'target_company' => 'nullable|string|max:200',
        ]);

        $tone           = $request->input('tone', 'professional');
        $targetRole     = $request->input('target_role', $resume->title);
        $targetCompany  = $request->input('target_company', '');

        $flatSkills = implode(', ', $resume->flat_skills);

        $experience = collect($resume->experience ?? [])->map(function ($exp) {
            $pos     = $exp['position'] ?? '';
            $company = $exp['company']  ?? '';
            $dates   = ($exp['start_date'] ?? '') . ' – ' . ($exp['end_date'] ?? 'Present');
            $desc    = $exp['description'] ?? '';
            return "{$pos} at {$company} ({$dates}): {$desc}";
        })->implode("\n");

        $education = collect($resume->education ?? [])->map(function ($edu) {
            return ($edu['degree'] ?? '') . ' — ' . ($edu['institution'] ?? '');
        })->implode(', ');

        $toneInstructions = [
            'professional' => 'Use formal, polished business language. Structured and traditional.',
            'confident'    => 'Use assertive, direct language. Highlight accomplishments boldly.',
            'creative'     => 'Use vivid, engaging language. Show personality and originality.',
            'concise'      => 'Keep it tight — under 250 words. Every sentence must earn its place.',
            'enthusiastic' => 'Show genuine excitement for the role and company. Energetic tone.',
        ];

        $companyLine = $targetCompany ? "Company: {$targetCompany}" : '';
        $toneHint    = $toneInstructions[$tone] ?? $toneInstructions['professional'];

        $prompt = <<<PROMPT
Write a professional cover letter for a job application.

Applicant: {$resume->full_name}
Target Role: {$targetRole}
{$companyLine}

Tone: {$toneHint}

Professional Summary:
{$resume->professional_summary}

Experience:
{$experience}

Education: {$education}
Skills: {$flatSkills}

Instructions:
- Write 3–4 paragraphs: opening hook, relevant experience, value proposition, call to action.
- Address the hiring manager professionally.
- Do NOT include a date or address block — just start from the salutation.
- Use first person. Do not use bullet points.
- End with a professional sign-off using the applicant's name.
- Total length: 300–400 words.
PROMPT;

        try {
            $content = trim($this->ai(
                $prompt,
                'You are an expert career coach and cover letter writer. Write compelling, tailored cover letters that get interviews.',
                ['temperature' => 0.65]
            ));

            // Build formatted HTML
            $paragraphs = array_filter(array_map('trim', explode("\n\n", $content)));
            $htmlParts  = array_map(fn($p) => '<p>' . nl2br(htmlspecialchars($p)) . '</p>', $paragraphs);
            $contentHtml = implode("\n", $htmlParts);

        } catch (\Throwable $e) {
            Log::error('Cover letter generation failed', ['resume' => $resume->id, 'error' => $e->getMessage()]);

            return back()->with('error', 'AI generation failed. Please try again.');
        }

        $coverLetter = CoverLetter::updateOrCreate(
            ['resume_id' => $resume->id, 'user_id' => auth()->id()],
            [
                'tone'           => $tone,
                'target_role'    => $targetRole,
                'target_company' => $targetCompany,
                'content'        => $content,
                'content_html'   => $contentHtml,
            ]
        );

        auth()->user()->deductAICredits(1, 'cover_letter_builder', 'AI Cover Letter generated via Resume Builder');

        return redirect()->route('resume.cover-letter.show', $resume)
            ->with('success', 'Cover letter generated successfully!');
    }

    /** Download as PDF using DomPDF. */
    public function downloadPdf(Resume $resume)
    {
        $this->authorize('view', $resume);

        $coverLetter = CoverLetter::where('resume_id', $resume->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->firstOrFail();

        $html = view('resume.cover-letter-pdf', compact('resume', 'coverLetter'))->render();

        $pdf = Pdf::loadHTML($html)->setPaper('a4');

        $filename = 'cover-letter-' . str_replace(' ', '-', strtolower($resume->full_name)) . '.pdf';

        return $pdf->download($filename);
    }

    /** Download as plain-text DOCX (simple HTML-wrapped Word document). */
    public function downloadDocx(Resume $resume)
    {
        $this->authorize('view', $resume);

        $coverLetter = CoverLetter::where('resume_id', $resume->id)
            ->where('user_id', auth()->id())
            ->latest()
            ->firstOrFail();

        $content = $coverLetter->content;
        $name    = $resume->full_name;
        $role    = $coverLetter->target_role ?? $resume->title;

        // Word-compatible HTML
        $html = <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head><meta charset="UTF-8">
<style>
body { font-family: Calibri, sans-serif; font-size: 12pt; line-height: 1.6; margin: 2cm; color: #1a1a1a; }
p { margin: 0 0 12pt; }
</style></head>
<body>
HTML;
        foreach (explode("\n\n", $content) as $para) {
            $para = trim($para);
            if ($para) {
                $html .= '<p>' . nl2br(htmlspecialchars($para)) . '</p>' . "\n";
            }
        }
        $html .= '</body></html>';

        $filename = 'cover-letter-' . str_replace(' ', '-', strtolower($name)) . '.doc';

        return response($html, 200, [
            'Content-Type'        => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
