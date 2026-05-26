<?php
// Pure PHP PDF template — no Blade directives (formatter-safe)
// Variables available: $resume, $template, $primary, $primaryLight, $badgeBg, $borderLight, $initials, $isTwoCol, $isMinimal
use Illuminate\Support\Str;

function e2(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function val(array $arr, string $key): string { return e2((string)($arr[$key] ?? '')); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size:9.5pt; color:#1a1a1a; line-height:1.45; background:#fff; }
.st { color:<?php echo $primary; ?>; border-bottom:1.5pt solid <?php echo $primary; ?>; padding-bottom:2pt; margin-bottom:7pt; font-size:9pt; font-weight:bold; letter-spacing:1pt; text-transform:uppercase; }
.badge { display:inline-block; padding:1.5pt 6pt; border-radius:10pt; font-size:7.5pt; background:<?php echo $badgeBg; ?>; color:<?php echo $primary; ?>; border:0.5pt solid <?php echo $borderLight; ?>; margin:1.5pt 2pt 1.5pt 0; }
table { border-collapse:collapse; }
</style>
</head>
<body>

<?php if ($isTwoCol): ?>
<table width="100%"><tr>
<td width="30%" style="background:<?php echo $primary; ?>;padding:18pt 12pt;vertical-align:top;">
  <div style="width:52pt;height:52pt;border-radius:26pt;background:rgba(255,255,255,0.2);text-align:center;line-height:52pt;font-size:18pt;font-weight:bold;color:#fff;margin:0 auto 8pt auto;"><?php echo e2($initials); ?></div>
  <p style="text-align:center;font-size:13pt;font-weight:bold;color:#fff;margin-bottom:3pt;"><?php echo e2($resume->full_name ?? ''); ?></p>
  <?php if ($resume->professional_summary): ?>
  <p style="text-align:center;font-size:7.5pt;color:rgba(255,255,255,0.8);margin-bottom:10pt;line-height:1.4;"><?php echo e2(Str::limit($resume->professional_summary, 180)); ?></p>
  <?php endif; ?>
  <p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-bottom:5pt;">Contact</p>
  <?php if ($resume->email): ?><p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;"><?php echo e2($resume->email); ?></p><?php endif; ?>
  <?php if ($resume->phone): ?><p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;"><?php echo e2($resume->phone); ?></p><?php endif; ?>
  <?php if ($resume->location): ?><p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;"><?php echo e2($resume->location); ?></p><?php endif; ?>
  <?php if ($resume->linkedin_url): ?><p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">LinkedIn</p><?php endif; ?>
  <?php if ($resume->github_url): ?><p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:2pt;">GitHub</p><?php endif; ?>
  <?php if ($resume->portfolio_url): ?><p style="font-size:7.5pt;color:rgba(255,255,255,0.9);margin-bottom:6pt;">Portfolio</p><?php endif; ?>

  <?php if (!empty($resume->skills) && count($resume->skills) > 0): ?>
  <p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-top:8pt;margin-bottom:5pt;">Skills</p>
  <?php foreach ($resume->skills as $skill): ?>
  <span style="display:inline-block;background:rgba(255,255,255,0.18);color:#fff;font-size:7pt;padding:1.5pt 5pt;border-radius:3pt;margin:1.5pt 2pt 1.5pt 0;"><?php echo e2((string)$skill); ?></span>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($resume->languages) && count($resume->languages) > 0): ?>
  <p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-top:10pt;margin-bottom:5pt;">Languages</p>
  <?php foreach ($resume->languages as $lang): ?>
  <table width="100%" style="margin-bottom:2pt;"><tr>
    <td style="font-size:7.5pt;color:#fff;"><?php echo val($lang,'name'); ?></td>
    <td style="font-size:7.5pt;color:rgba(255,255,255,0.7);text-align:right;"><?php echo val($lang,'proficiency'); ?></td>
  </tr></table>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($resume->certifications) && count($resume->certifications) > 0): ?>
  <p style="font-size:7pt;font-weight:bold;text-transform:uppercase;letter-spacing:1pt;color:rgba(255,255,255,0.65);margin-top:10pt;margin-bottom:5pt;">Certifications</p>
  <?php foreach ($resume->certifications as $cert): ?>
  <div style="margin-bottom:5pt;">
    <p style="font-size:7.5pt;font-weight:bold;color:#fff;line-height:1.3;"><?php echo val($cert,'name'); ?></p>
    <p style="font-size:7pt;color:rgba(255,255,255,0.7);"><?php echo val($cert,'issuer'); ?><?php if (!empty($cert['issue_date'])): ?> &bull; <?php echo e2($cert['issue_date']); ?><?php endif; ?></p>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</td>

<td style="padding:18pt 16pt;vertical-align:top;background:#fff;">
  <?php if (!empty($resume->experience) && count($resume->experience) > 0): ?>
  <div style="margin-bottom:12pt;">
  <p class="st">Work Experience</p>
  <?php foreach ($resume->experience as $exp): ?>
  <div style="margin-bottom:9pt;">
    <table width="100%"><tr>
      <td style="vertical-align:top;">
        <p style="font-weight:bold;font-size:10pt;color:#111;"><?php echo val($exp,'position'); ?></p>
        <p style="font-size:8.5pt;font-weight:bold;color:<?php echo $primary; ?>;"><?php echo val($exp,'company'); ?><?php if (!empty($exp['location'])): ?> &mdash; <?php echo e2($exp['location']); ?><?php endif; ?></p>
      </td>
      <td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
        <?php echo val($exp,'start_date'); ?><?php if (!empty($exp['end_date'])): ?> &ndash; <?php echo e2($exp['end_date']); ?><?php endif; ?>
        <?php if (!empty($exp['employment_type'])): ?><br><em><?php echo e2($exp['employment_type']); ?></em><?php endif; ?>
      </td>
    </tr></table>
    <?php if (!empty($exp['description'])): ?><p style="font-size:8.5pt;color:#444;margin-top:3pt;line-height:1.4;"><?php echo e2($exp['description']); ?></p><?php endif; ?>
    <?php if (!empty($exp['achievements']) && is_array($exp['achievements'])): ?>
    <?php foreach ($exp['achievements'] as $expAch): ?>
    <?php if (!empty(trim((string)$expAch))): ?>
    <table style="margin-top:2pt;" width="100%"><tr>
      <td style="width:8pt;vertical-align:top;padding-top:3pt;"><div style="width:5pt;height:5pt;border-radius:3pt;background:<?php echo $primary; ?>;"></div></td>
      <td style="font-size:8.5pt;color:#333;line-height:1.4;"><?php echo e2((string)$expAch); ?></td>
    </tr></table>
    <?php endif; ?>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php if (!empty($exp['technologies'])): ?><p style="font-size:7.5pt;color:#888;margin-top:2pt;font-style:italic;">Technologies: <?php echo e2($exp['technologies']); ?></p><?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume->education) && count($resume->education) > 0): ?>
  <div style="margin-bottom:12pt;">
  <p class="st">Education</p>
  <?php foreach ($resume->education as $edu): ?>
  <div style="margin-bottom:7pt;">
    <table width="100%"><tr>
      <td style="vertical-align:top;">
        <p style="font-weight:bold;font-size:10pt;color:#111;"><?php echo val($edu,'degree'); ?></p>
        <p style="font-size:8.5pt;font-weight:bold;color:<?php echo $primary; ?>;"><?php echo val($edu,'institution'); ?><?php if (!empty($edu['location'])): ?>, <?php echo e2($edu['location']); ?><?php endif; ?></p>
        <?php if (!empty($edu['gpa'])): ?><p style="font-size:7.5pt;color:#666;">GPA: <?php echo e2($edu['gpa']); ?><?php if (!empty($edu['honors'])): ?> &bull; <?php echo e2($edu['honors']); ?><?php endif; ?></p><?php endif; ?>
      </td>
      <td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
        <?php echo val($edu,'start_year'); ?><?php if (!empty($edu['end_year'])): ?> &ndash; <?php echo e2($edu['end_year']); ?><?php endif; ?>
      </td>
    </tr></table>
  </div>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume->projects) && count($resume->projects) > 0): ?>
  <div style="margin-bottom:12pt;">
  <p class="st">Projects</p>
  <?php foreach ($resume->projects as $proj): ?>
  <div style="margin-bottom:7pt;">
    <table width="100%"><tr>
      <td><p style="font-weight:bold;font-size:9.5pt;color:#111;"><?php echo val($proj,'name'); ?></p></td>
      <td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;"><?php if (!empty($proj['start_date'])): ?><?php echo e2($proj['start_date']); ?><?php if (!empty($proj['end_date'])): ?> &ndash; <?php echo e2($proj['end_date']); ?><?php endif; ?><?php endif; ?></td>
    </tr></table>
    <?php if (!empty($proj['description'])): ?><p style="font-size:8.5pt;color:#444;margin-top:2pt;line-height:1.4;"><?php echo e2($proj['description']); ?></p><?php endif; ?>
    <?php if (!empty($proj['technologies'])): ?><p style="font-size:7.5pt;color:#888;font-style:italic;"><?php echo e2($proj['technologies']); ?></p><?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume->achievements) && count($resume->achievements) > 0): ?>
  <div style="margin-bottom:12pt;">
  <p class="st">Achievements &amp; Awards</p>
  <?php foreach ($resume->achievements as $ach): ?>
  <div style="margin-bottom:5pt;">
    <table width="100%"><tr>
      <td><p style="font-weight:bold;font-size:9pt;color:#111;"><?php echo val($ach,'title'); ?><?php if (!empty($ach['issuer'])): ?> <span style="font-weight:normal;color:#555;font-size:8.5pt;">&bull; <?php echo e2($ach['issuer']); ?></span><?php endif; ?></p></td>
      <td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;"><?php echo val($ach,'date'); ?></td>
    </tr></table>
    <?php if (!empty($ach['description'])): ?><p style="font-size:8.5pt;color:#444;"><?php echo e2($ach['description']); ?></p><?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <?php if (!empty($resume->volunteer_work) && count($resume->volunteer_work) > 0): ?>
  <div style="margin-bottom:12pt;">
  <p class="st">Volunteer &amp; Activities</p>
  <?php foreach ($resume->volunteer_work as $vol): ?>
  <div style="margin-bottom:6pt;">
    <p style="font-weight:bold;font-size:9pt;color:#111;"><?php echo val($vol,'role'); ?><?php if (!empty($vol['organization'])): ?> &mdash; <span style="color:<?php echo $primary; ?>;"><?php echo e2($vol['organization']); ?></span><?php endif; ?></p>
    <?php if (!empty($vol['description'])): ?><p style="font-size:8.5pt;color:#444;"><?php echo e2($vol['description']); ?></p><?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>
  <?php endif; ?>
</td>
</tr></table>

<?php else: ?>
<div>
  <?php if ($isMinimal): ?>
  <div style="padding:20pt 22pt 14pt;border-bottom:1pt solid #e0e0e0;">
    <p style="font-size:22pt;font-weight:300;letter-spacing:3pt;text-transform:uppercase;color:#111;margin-bottom:4pt;"><?php echo e2($resume->full_name ?? ''); ?></p>
    <p style="font-size:8.5pt;color:#666;">
      <?php if ($resume->email): ?><?php echo e2($resume->email); ?><?php endif; ?>
      <?php if ($resume->phone): ?> &nbsp;|&nbsp; <?php echo e2($resume->phone); ?><?php endif; ?>
      <?php if ($resume->location): ?> &nbsp;|&nbsp; <?php echo e2($resume->location); ?><?php endif; ?>
      <?php if ($resume->linkedin_url): ?> &nbsp;|&nbsp; LinkedIn<?php endif; ?>
      <?php if ($resume->github_url): ?> &nbsp;|&nbsp; GitHub<?php endif; ?>
    </p>
  </div>
  <?php else: ?>
  <div style="background:<?php echo $primaryLight; ?>;border-bottom:3pt solid <?php echo $primary; ?>;padding:18pt 22pt 14pt;">
    <p style="font-size:22pt;font-weight:bold;color:<?php echo $primary; ?>;margin-bottom:5pt;"><?php echo e2($resume->full_name ?? ''); ?></p>
    <p style="font-size:8.5pt;color:#555;">
      <?php if ($resume->email): ?><?php echo e2($resume->email); ?><?php endif; ?>
      <?php if ($resume->phone): ?> &nbsp;&bull;&nbsp; <?php echo e2($resume->phone); ?><?php endif; ?>
      <?php if ($resume->location): ?> &nbsp;&bull;&nbsp; <?php echo e2($resume->location); ?><?php endif; ?>
      <?php if ($resume->linkedin_url): ?> &nbsp;&bull;&nbsp; LinkedIn<?php endif; ?>
      <?php if ($resume->github_url): ?> &nbsp;&bull;&nbsp; GitHub<?php endif; ?>
      <?php if ($resume->portfolio_url): ?> &nbsp;&bull;&nbsp; Portfolio<?php endif; ?>
    </p>
  </div>
  <?php endif; ?>

  <div style="padding:14pt 22pt;">
    <?php if ($resume->professional_summary): ?>
    <div style="margin-bottom:12pt;">
      <p class="st">Professional Summary</p>
      <p style="font-size:9pt;color:#333;line-height:1.5;"><?php echo e2($resume->professional_summary); ?></p>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->skills) && count($resume->skills) > 0): ?>
    <div style="margin-bottom:12pt;">
      <p class="st">Skills</p>
      <?php foreach ($resume->skills as $skill): ?>
      <span class="badge"><?php echo e2((string)$skill); ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->experience) && count($resume->experience) > 0): ?>
    <div style="margin-bottom:12pt;">
    <p class="st">Work Experience</p>
    <?php foreach ($resume->experience as $exp): ?>
    <div style="margin-bottom:9pt;">
      <table width="100%"><tr>
        <td style="vertical-align:top;">
          <p style="font-weight:bold;font-size:10pt;color:#111;"><?php echo val($exp,'position'); ?></p>
          <p style="font-size:8.5pt;font-weight:bold;color:<?php echo $primary; ?>;"><?php echo val($exp,'company'); ?><?php if (!empty($exp['location'])): ?> &middot; <?php echo e2($exp['location']); ?><?php endif; ?></p>
        </td>
        <td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
          <?php echo val($exp,'start_date'); ?><?php if (!empty($exp['end_date'])): ?> &ndash; <?php echo e2($exp['end_date']); ?><?php endif; ?>
          <?php if (!empty($exp['employment_type'])): ?><br><em><?php echo e2($exp['employment_type']); ?></em><?php endif; ?>
        </td>
      </tr></table>
      <?php if (!empty($exp['description'])): ?><p style="font-size:8.5pt;color:#444;margin-top:3pt;line-height:1.4;"><?php echo e2($exp['description']); ?></p><?php endif; ?>
      <?php if (!empty($exp['achievements']) && is_array($exp['achievements'])): ?>
      <?php foreach ($exp['achievements'] as $expAch): ?>
      <?php if (!empty(trim((string)$expAch))): ?>
      <table style="margin-top:2pt;" width="100%"><tr>
        <td style="width:8pt;vertical-align:top;padding-top:4pt;"><div style="width:5pt;height:5pt;border-radius:3pt;background:<?php echo $primary; ?>;"></div></td>
        <td style="font-size:8.5pt;color:#333;line-height:1.4;"><?php echo e2((string)$expAch); ?></td>
      </tr></table>
      <?php endif; ?>
      <?php endforeach; ?>
      <?php endif; ?>
      <?php if (!empty($exp['technologies'])): ?><p style="font-size:7.5pt;color:#888;margin-top:2pt;font-style:italic;">Technologies: <?php echo e2($exp['technologies']); ?></p><?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->education) && count($resume->education) > 0): ?>
    <div style="margin-bottom:12pt;">
    <p class="st">Education</p>
    <?php foreach ($resume->education as $edu): ?>
    <div style="margin-bottom:7pt;">
      <table width="100%"><tr>
        <td style="vertical-align:top;">
          <p style="font-weight:bold;font-size:10pt;color:#111;"><?php echo val($edu,'degree'); ?></p>
          <p style="font-size:8.5pt;font-weight:bold;color:<?php echo $primary; ?>;"><?php echo val($edu,'institution'); ?><?php if (!empty($edu['location'])): ?>, <?php echo e2($edu['location']); ?><?php endif; ?></p>
          <?php if (!empty($edu['field'])): ?><p style="font-size:8pt;color:#666;"><?php echo e2($edu['field']); ?></p><?php endif; ?>
          <?php if (!empty($edu['gpa'])): ?><p style="font-size:7.5pt;color:#666;">GPA: <?php echo e2($edu['gpa']); ?><?php if (!empty($edu['honors'])): ?> &bull; <?php echo e2($edu['honors']); ?><?php endif; ?></p><?php endif; ?>
        </td>
        <td style="text-align:right;vertical-align:top;white-space:nowrap;font-size:7.5pt;color:#888;">
          <?php echo val($edu,'start_year'); ?><?php if (!empty($edu['end_year'])): ?> &ndash; <?php echo e2($edu['end_year']); ?><?php endif; ?>
        </td>
      </tr></table>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->certifications) && count($resume->certifications) > 0): ?>
    <div style="margin-bottom:12pt;">
    <p class="st">Certifications</p>
    <?php foreach ($resume->certifications as $cert): ?>
    <div style="display:inline-block;width:48%;vertical-align:top;margin:2pt 1%;">
      <div style="border:0.5pt solid <?php echo $borderLight; ?>;border-radius:3pt;padding:5pt 7pt;">
        <p style="font-weight:bold;font-size:8.5pt;color:#111;"><?php echo val($cert,'name'); ?></p>
        <p style="font-size:7.5pt;color:<?php echo $primary; ?>;"><?php echo val($cert,'issuer'); ?></p>
        <p style="font-size:7pt;color:#888;"><?php if (!empty($cert['issue_date'])): ?>Issued: <?php echo e2($cert['issue_date']); ?><?php endif; ?><?php if (!empty($cert['credential_id'])): ?> &bull; <?php echo e2($cert['credential_id']); ?><?php endif; ?></p>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->projects) && count($resume->projects) > 0): ?>
    <div style="margin-bottom:12pt;">
    <p class="st">Projects</p>
    <?php foreach ($resume->projects as $proj): ?>
    <div style="margin-bottom:7pt;">
      <table width="100%"><tr>
        <td>
          <p style="font-weight:bold;font-size:9.5pt;color:#111;"><?php echo val($proj,'name'); ?></p>
          <?php if (!empty($proj['technologies'])): ?><p style="font-size:7.5pt;color:#888;font-style:italic;"><?php echo e2($proj['technologies']); ?></p><?php endif; ?>
        </td>
        <td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;"><?php if (!empty($proj['start_date'])): ?><?php echo e2($proj['start_date']); ?><?php if (!empty($proj['end_date'])): ?> &ndash; <?php echo e2($proj['end_date']); ?><?php endif; ?><?php endif; ?></td>
      </tr></table>
      <?php if (!empty($proj['description'])): ?><p style="font-size:8.5pt;color:#444;line-height:1.4;"><?php echo e2($proj['description']); ?></p><?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->achievements) && count($resume->achievements) > 0): ?>
    <div style="margin-bottom:12pt;">
    <p class="st">Achievements &amp; Awards</p>
    <?php foreach ($resume->achievements as $ach): ?>
    <table width="100%" style="margin-bottom:5pt;"><tr>
      <td style="width:10pt;vertical-align:top;padding-top:2pt;"><div style="width:7pt;height:7pt;background:<?php echo $primary; ?>;border-radius:1pt;"></div></td>
      <td style="vertical-align:top;">
        <p style="font-weight:bold;font-size:9pt;color:#111;"><?php echo val($ach,'title'); ?><?php if (!empty($ach['issuer'])): ?> <span style="font-weight:normal;color:#666;">&bull; <?php echo e2($ach['issuer']); ?></span><?php endif; ?><?php if (!empty($ach['date'])): ?> <span style="font-size:7.5pt;color:#888;">(<?php echo e2($ach['date']); ?>)</span><?php endif; ?></p>
        <?php if (!empty($ach['description'])): ?><p style="font-size:8.5pt;color:#444;"><?php echo e2($ach['description']); ?></p><?php endif; ?>
      </td>
    </tr></table>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->languages) && count($resume->languages) > 0): ?>
    <div style="margin-bottom:12pt;">
      <p class="st">Languages</p>
      <?php foreach ($resume->languages as $lang): ?>
      <span style="display:inline-block;margin-right:14pt;font-size:9pt;"><strong style="color:#111;"><?php echo val($lang,'name'); ?></strong> <span class="badge"><?php echo val($lang,'proficiency'); ?></span></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($resume->volunteer_work) && count($resume->volunteer_work) > 0): ?>
    <div style="margin-bottom:12pt;">
    <p class="st">Volunteer &amp; Extra-Curricular</p>
    <?php foreach ($resume->volunteer_work as $vol): ?>
    <div style="margin-bottom:6pt;">
      <table width="100%"><tr>
        <td>
          <p style="font-weight:bold;font-size:9pt;color:#111;"><?php echo val($vol,'role'); ?></p>
          <p style="font-size:8.5pt;color:<?php echo $primary; ?>;"><?php echo val($vol,'organization'); ?></p>
        </td>
        <td style="text-align:right;font-size:7.5pt;color:#888;white-space:nowrap;"><?php echo val($vol,'start_date'); ?><?php if (!empty($vol['end_date'])): ?> &ndash; <?php echo e2($vol['end_date']); ?><?php endif; ?></td>
      </tr></table>
      <?php if (!empty($vol['description'])): ?><p style="font-size:8.5pt;color:#444;"><?php echo e2($vol['description']); ?></p><?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

</body>
</html>
