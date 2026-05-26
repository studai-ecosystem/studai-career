<?php

namespace App\Services;

use App\Models\Resume;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ResumeExportService
{
    /**
     * Export resume to PDF
     */
    public function exportToPDF(Resume $resume): string
    {
        $resume->loadMissing('template');
        $html = $this->renderPdfTemplate($resume);


        $pdf = PDF::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        $filename = 'resumes/' . Str::slug($resume->title) . '-' . now()->timestamp . '.pdf';
        
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Render PDF HTML using plain PHP template (formatter-safe, no Blade).
     */
    private function renderPdfTemplate(Resume $resume): string
    {
        $tpl     = $resume->template;
        $colors  = $tpl?->color_scheme ?? ['primary' => '#1a202c'];
        $primary = $colors['primary'] ?? '#1a202c';
        $slug    = $tpl?->slug ?? 'professional-classic';

        $isTwoCol  = in_array($slug, ['modern-tech', 'creative-portfolio', 'healthcare-professional']);
        $isMinimal = $slug === 'minimalist';

        $primaryLight = $this->hexTint($primary, 0.10);
        $badgeBg      = $this->hexTint($primary, 0.12);
        $borderLight  = $this->hexTint($primary, 0.28);

        $nameParts = explode(' ', trim($resume->full_name ?? 'U'));
        $initials  = strtoupper(substr($nameParts[0], 0, 1))
                   . (isset($nameParts[1]) ? strtoupper(substr($nameParts[1], 0, 1)) : '');

        $template = $tpl; // available inside included file

        ob_start();
        include resource_path('views/resume/templates/pdf_gen.php');
        return (string) ob_get_clean();
    }

    /**
     * Blend a hex color with white at given opacity (replaces CSS color-mix for dompdf).
     */
    private function hexTint(string $hex, float $opacity): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        $r = (int) round(hexdec(substr($hex, 0, 2)) * $opacity + 255 * (1 - $opacity));
        $g = (int) round(hexdec(substr($hex, 2, 2)) * $opacity + 255 * (1 - $opacity));
        $b = (int) round(hexdec(substr($hex, 4, 2)) * $opacity + 255 * (1 - $opacity));
        return "rgb($r,$g,$b)";
    }

    /**
     * Export resume to DOCX
     */
    public function exportToDOCX(Resume $resume): string
    {
        $phpWord = new PhpWord();

        // Set document properties
        $properties = $phpWord->getDocInfo();
        $properties->setCreator($resume->user->name);
        $properties->setCompany('StudAI Hire Platform');
        $properties->setTitle($resume->title);
        $properties->setDescription('Resume for ' . $resume->full_name);

        // Add section
        $section = $phpWord->addSection([
            'marginTop' => 720,
            'marginBottom' => 720,
            'marginLeft' => 720,
            'marginRight' => 720,
        ]);

        // Define styles
        $phpWord->addFontStyle('nameStyle', [
            'bold' => true,
            'size' => 20,
            'color' => '1a202c',
        ]);

        $phpWord->addFontStyle('headingStyle', [
            'bold' => true,
            'size' => 14,
            'color' => '2d3748',
        ]);

        $phpWord->addFontStyle('subheadingStyle', [
            'bold' => true,
            'size' => 11,
        ]);

        $phpWord->addFontStyle('normalStyle', [
            'size' => 10,
        ]);

        // Header - Name and Contact
        $section->addText($resume->full_name, 'nameStyle', ['alignment' => 'center']);
        
        $contactInfo = [];
        if ($resume->email) $contactInfo[] = $resume->email;
        if ($resume->phone) $contactInfo[] = $resume->phone;
        if ($resume->location) $contactInfo[] = $resume->location;
        
        if (!empty($contactInfo)) {
            $section->addText(implode(' | ', $contactInfo), 'normalStyle', ['alignment' => 'center']);
        }

        $linkInfo = [];
        if ($resume->linkedin_url) $linkInfo[] = $resume->linkedin_url;
        if ($resume->github_url) $linkInfo[] = $resume->github_url;
        if ($resume->portfolio_url) $linkInfo[] = $resume->portfolio_url;
        
        if (!empty($linkInfo)) {
            $section->addText(implode(' | ', $linkInfo), 'normalStyle', ['alignment' => 'center']);
        }

        $section->addTextBreak(1);

        // Professional Summary
        if ($resume->professional_summary) {
            $section->addText('PROFESSIONAL SUMMARY', 'headingStyle');
            $section->addText($resume->professional_summary, 'normalStyle');
            $section->addTextBreak(1);
        }

        // Experience
        if (!empty($resume->experience)) {
            $section->addText('EXPERIENCE', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->experience as $exp) {
                $section->addText($exp['title'] ?? '', 'subheadingStyle');
                
                $companyLine = ($exp['company'] ?? '') . ' | ' . ($exp['location'] ?? '');
                $section->addText($companyLine, 'normalStyle');
                
                $dates = ($exp['start_date'] ?? '') . ' - ' . ($exp['end_date'] ?? 'Present');
                $section->addText($dates, 'normalStyle', ['italic' => true]);
                
                if (!empty($exp['description'])) {
                    $section->addText($exp['description'], 'normalStyle');
                }

                if (!empty($exp['achievements'])) {
                    foreach ($exp['achievements'] as $achievement) {
                        $section->addListItem($achievement, 0, 'normalStyle');
                    }
                }

                $section->addTextBreak(0.5);
            }

            $section->addTextBreak(0.5);
        }

        // Education
        if (!empty($resume->education)) {
            $section->addText('EDUCATION', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->education as $edu) {
                $degreeLine = ($edu['degree'] ?? '') . ' in ' . ($edu['field'] ?? '');
                $section->addText($degreeLine, 'subheadingStyle');
                
                $section->addText($edu['institution'] ?? '', 'normalStyle');
                
                $dates = ($edu['start_year'] ?? '') . ' - ' . ($edu['end_year'] ?? 'Present');
                $section->addText($dates, 'normalStyle', ['italic' => true]);
                
                if (!empty($edu['gpa'])) {
                    $section->addText('GPA: ' . $edu['gpa'], 'normalStyle');
                }

                $section->addTextBreak(0.5);
            }

            $section->addTextBreak(0.5);
        }

        // Skills
        if (!empty($resume->skills)) {
            $section->addText('SKILLS', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->skills as $category => $skillList) {
                if (!empty($skillList)) {
                    $categoryName = ucwords(str_replace('_', ' ', $category));
                    $skillsText = implode(', ', $skillList);
                    $section->addText($categoryName . ': ' . $skillsText, 'normalStyle');
                }
            }

            $section->addTextBreak(0.5);
        }

        // Projects
        if (!empty($resume->projects)) {
            $section->addText('PROJECTS', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->projects as $project) {
                $section->addText($project['name'] ?? '', 'subheadingStyle');
                
                if (!empty($project['description'])) {
                    $section->addText($project['description'], 'normalStyle');
                }

                if (!empty($project['technologies'])) {
                    $section->addText('Technologies: ' . implode(', ', $project['technologies']), 'normalStyle');
                }

                if (!empty($project['url'])) {
                    $section->addText('URL: ' . $project['url'], 'normalStyle');
                }

                $section->addTextBreak(0.5);
            }

            $section->addTextBreak(0.5);
        }

        // Certifications
        if (!empty($resume->certifications)) {
            $section->addText('CERTIFICATIONS', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->certifications as $cert) {
                $certLine = ($cert['name'] ?? '') . ' - ' . ($cert['issuer'] ?? '');
                if (!empty($cert['date'])) {
                    $certLine .= ' (' . $cert['date'] . ')';
                }
                $section->addListItem($certLine, 0, 'normalStyle');
            }

            $section->addTextBreak(0.5);
        }

        // Achievements
        if (!empty($resume->achievements)) {
            $section->addText('ACHIEVEMENTS', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->achievements as $achievement) {
                $section->addListItem($achievement['description'] ?? $achievement, 0, 'normalStyle');
            }

            $section->addTextBreak(0.5);
        }

        // Languages
        if (!empty($resume->languages)) {
            $section->addText('LANGUAGES', 'headingStyle');
            $section->addTextBreak(0.5);

            foreach ($resume->languages as $lang) {
                $langLine = ($lang['language'] ?? '') . ' - ' . ($lang['proficiency'] ?? '');
                $section->addListItem($langLine, 0, 'normalStyle');
            }
        }

        // Save to file
        $filename = 'resumes/' . Str::slug($resume->title) . '-' . now()->timestamp . '.docx';
        $filepath = storage_path('app/public/' . $filename);

        // Ensure directory exists
        Storage::disk('public')->makeDirectory('resumes');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filepath);

        return $filename;
    }

    /**
     * Generate HTML for preview
     */
    public function generateHTML(Resume $resume): string
    {
        return view('resume.templates.html', compact('resume'))->render();
    }

    /**
     * Export resume with specific template
     */
    public function exportWithTemplate(Resume $resume, int $templateId, string $format = 'pdf'): string
    {
        // Load template
        $template = \App\Models\ResumeTemplate::findOrFail($templateId);
        
        // Temporarily change resume template
        $originalTemplateId = $resume->template_id;
        $resume->template_id = $templateId;

        // Export
        $filename = $format === 'pdf' ? $this->exportToPDF($resume) : $this->exportToDOCX($resume);

        // Restore original template
        $resume->template_id = $originalTemplateId;

        return $filename;
    }
}
