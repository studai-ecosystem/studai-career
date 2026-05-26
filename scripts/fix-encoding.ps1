param()

$viewDir = "c:\Users\user\Downloads\studai-career\resources\views"
$bladeFiles = Get-ChildItem $viewDir -Filter "*.blade.php" -Recurse
Write-Host "Files found: $($bladeFiles.Count)"

$fixed = 0
foreach ($file in $bladeFiles) {
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $original = $content

    # Rupee sign
    $content = $content.Replace('â‚¹', '&#8377;')
    # Trademark
    $content = $content.Replace('â„¢', '&trade;')
    # Registered
    $content = $content.Replace('Â®', '&reg;')
    # Copyright
    $content = $content.Replace('Â©', '&copy;')
    # Middle dot
    $content = $content.Replace('Â·', '&middot;')
    # Multiplication sign
    $content = $content.Replace('Ã—', '&times;')
    # Em dash
    $content = $content.Replace('â€"', '&mdash;')
    # Right single quote / apostrophe
    $content = $content.Replace('â€™', "'")
    # Left single quote
    $content = $content.Replace('â€˜', "'")
    # Left double quote
    $content = $content.Replace('â€œ', '"')
    # Ellipsis
    $content = $content.Replace('â€¦', '...')
    # Non-breaking space
    $content = $content.Replace('Â ', ' ')
    # Box-drawing light horizontal (used in blade comments as ----)
    $content = $content.Replace('â"€', '-')
    # Accented letters
    $content = $content.Replace('Ã©', 'e')
    $content = $content.Replace('Ã¨', 'e')
    $content = $content.Replace('Ã ', 'a')
    $content = $content.Replace('Ã¢', 'a')
    $content = $content.Replace('Ã®', 'i')
    $content = $content.Replace('Ã´', 'o')
    $content = $content.Replace('Ã»', 'u')
    # Strip corrupted 4-byte emoji sequences: ðŸXX (rocket, clipboard, etc.)
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, 'ðŸ..', '')
    # Strip corrupted 3-byte emoji: âœX (sparkles, checkmarks)
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, 'âœ.', '')
    # Strip DŸ emoji variant
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, 'DŸ..', '')
    # Lone trailing Â before space/punctuation
    $content = [System.Text.RegularExpressions.Regex]::Replace($content, 'Â(?=\s|[^a-zA-Z0-9])', '')
    # Star emoji ★ corrupted
    $content = $content.Replace('â˜…', '&#9733;')
    $content = $content.Replace('â˜†', '&#9734;')

    if ($content -ne $original) {
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.Encoding]::UTF8)
        $fixed++
        Write-Host "Fixed: $($file.Name)"
    }
}
Write-Host "`nDone. Fixed $fixed / $($bladeFiles.Count) files."
