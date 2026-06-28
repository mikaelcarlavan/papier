<?php

/**
 * External verification harness.
 *
 * Renders every PDF in examples/output with Ghostscript and runs qpdf --check
 * to confirm the files are structurally sound and actually display. Skips
 * gracefully when a tool is unavailable.
 *
 *   php tools/verify.php
 *
 * Exit code is non-zero if any file fails.
 */

declare(strict_types=1);

$dir = $argv[1] ?? __DIR__ . '/../examples/output';
$tmp = sys_get_temp_dir() . '/papier_verify_' . getmypid();
@mkdir($tmp, 0777, true);

$have = static fn(string $bin): bool => trim((string) shell_exec('command -v ' . escapeshellarg($bin) . ' 2>/dev/null')) !== '';
$hasGs   = $have('gs');
$hasQpdf = $have('qpdf');
$hasInfo = $have('pdfinfo');

if (!$hasGs && !$hasQpdf) {
    fwrite(STDERR, "Neither Ghostscript nor qpdf found; cannot verify.\n");
    exit(2);
}

$files = glob(rtrim($dir, '/') . '/*.pdf') ?: [];
sort($files);
if (empty($files)) {
    fwrite(STDERR, "No PDFs in $dir (run the examples first).\n");
    exit(2);
}

printf("%-34s %-8s %-7s %-8s %s\n", 'file', 'render', 'pages', 'ink', 'qpdf');
printf("%s\n", str_repeat('-', 78));

$failures = 0;
foreach ($files as $file) {
    $name = basename($file);

    // Encrypted files cannot be rendered/checked without the password — skip.
    if ($hasQpdf) {
        exec('qpdf --is-encrypted ' . escapeshellarg($file) . ' 2>/dev/null', $eo, $erc);
        if ($erc === 0) {
            printf("%-34s %-8s %-7s %-8s %s\n", $name, 'enc', '-', 'enc', 'skip');
            continue;
        }
    }

    // 1. Ghostscript render.
    $render = 'skip'; $pages = '-'; $ink = '-';
    if ($hasGs) {
        $pat = "$tmp/" . preg_replace('/\W/', '_', $name) . '-%d.png';
        exec('gs -dNOPAUSE -dBATCH -dQUIET -sDEVICE=png16m -r60 -o '
            . escapeshellarg($pat) . ' ' . escapeshellarg($file) . ' 2>&1', $o, $rc);
        $pngs = glob(str_replace('%d', '*', $pat)) ?: [];
        $render = $rc === 0 ? 'ok' : 'FAIL';
        $pages  = (string) count($pngs);
        // Blank-page heuristic: at least one rendered page is non-trivially sized.
        $maxBytes = 0;
        foreach ($pngs as $p) { $maxBytes = max($maxBytes, filesize($p) ?: 0); array_map('unlink', [$p]); }
        $ink = $maxBytes > 700 ? 'yes' : 'BLANK';
        if ($rc !== 0) { $failures++; }
    }

    // 2. qpdf structural check.
    $qpdf = 'skip';
    if ($hasQpdf) {
        exec('qpdf --check ' . escapeshellarg($file) . ' 2>&1', $qo, $qrc);
        $qpdf = $qrc === 0 ? 'ok' : (str_contains(implode(' ', $qo), 'WARNING') ? 'warn' : 'FAIL');
        if ($qrc !== 0 && $qpdf !== 'warn') { $failures++; }
    }

    if ($render === 'FAIL' || $ink === 'BLANK' || $qpdf === 'FAIL') { $failures++; }
    printf("%-34s %-8s %-7s %-8s %s\n", $name, $render, $pages, $ink, $qpdf);
}

printf("%s\n", str_repeat('-', 78));
echo $failures === 0 ? "All files verified.\n" : "$failures problem(s) found.\n";
exit($failures === 0 ? 0 : 1);
