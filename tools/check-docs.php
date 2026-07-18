<?php

/**
 * Documentation checks.
 *
 * Run locally before pushing, and in CI:
 *
 *     php tools/check-docs.php
 *
 * Verifies that:
 *   1. every PHP snippet in docs/_content/ and README.md parses;
 *   2. every internal link and heading anchor in docs/_content/ resolves;
 *   3. every <h2>/<h3> carries an id, so the table of contents is complete.
 *
 * Exits non-zero on the first category that fails, listing every problem.
 */

declare(strict_types=1);

const ROOT = __DIR__ . '/..';

$errors = [];

// ─────────────────────────────────────────────────────────────────────────────
// Helpers
// ─────────────────────────────────────────────────────────────────────────────

/** Extract the PHP code blocks from a documentation fragment or Markdown file. */
function php_blocks(string $path): array
{
    $source = file_get_contents($path);

    $pattern = str_ends_with($path, '.md')
        ? '/```php\n(.*?)```/s'
        : '#<pre><code class="language-php">(.*?)</code></pre>#s';

    preg_match_all($pattern, $source, $m);

    return array_map(
        static fn(string $code): string => str_ends_with($path, '.md')
            ? $code
            : html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        $m[1]
    );
}

/** Run `php -l` over a snippet, returning the parse error or null. */
function lint(string $code): ?string
{
    $tmp = tempnam(sys_get_temp_dir(), 'papier-doc-') . '.php';
    file_put_contents($tmp, "<?php\n" . $code);

    exec('php -l ' . escapeshellarg($tmp) . ' 2>&1', $output, $status);
    unlink($tmp);

    if ($status === 0) {
        return null;
    }

    // Strip the temp path so the message reads cleanly.
    return trim(preg_replace('/ in \S+ on line (\d+)/', ' on snippet line $1', $output[0] ?? 'parse error'));
}

// ─────────────────────────────────────────────────────────────────────────────
// 1. Every PHP snippet parses
// ─────────────────────────────────────────────────────────────────────────────

$files    = array_merge(glob(ROOT . '/docs/_content/*.html'), [ROOT . '/README.md']);
$snippets = 0;

foreach ($files as $file) {
    $name = basename($file);

    foreach (php_blocks($file) as $i => $code) {
        $snippets++;

        if ($error = lint($code)) {
            $errors[] = sprintf('%s: PHP block %d does not parse — %s', $name, $i + 1, $error);
        }
    }
}

echo "Checked {$snippets} PHP snippets across " . count($files) . " files.\n";

// ─────────────────────────────────────────────────────────────────────────────
// 2. Internal links and anchors resolve, 3. headings carry ids
// ─────────────────────────────────────────────────────────────────────────────

$fragments = glob(ROOT . '/docs/_content/*.html');
$anchors   = [];
$slugs     = [];

foreach ($fragments as $file) {
    $slug          = basename($file, '.html');
    $slugs[]       = $slug;
    $source        = file_get_contents($file);
    preg_match_all('/<h[23] id="([^"]+)"/', $source, $m);
    $anchors[$slug] = $m[1];

    // Headings without an id never reach the table of contents. The feature
    // cards on the home page are the deliberate exception.
    if ($slug !== 'index') {
        preg_match_all('#<h([23])(?![^>]*\bid=)[^>]*>(.*?)</h\1>#s', $source, $bad, PREG_SET_ORDER);

        foreach ($bad as $heading) {
            $errors[] = sprintf('%s.html: <h%s> without an id — "%s"',
                $slug, $heading[1], trim(strip_tags($heading[2])));
        }
    }
}

$links = 0;

foreach ($fragments as $file) {
    $slug = basename($file, '.html');

    preg_match_all('/href="([^"]+)"/', file_get_contents($file), $m);

    foreach ($m[1] as $href) {
        if (str_starts_with($href, 'http') || str_starts_with($href, 'mailto') || str_starts_with($href, '#')) {
            continue;
        }

        $links++;
        [$target, $fragment] = array_pad(explode('#', $href, 2), 2, '');
        $target = preg_replace('/\.html$/', '', $target);

        if ($target === '' || !in_array($target, $slugs, true)) {
            $errors[] = "{$slug}.html: link to a page that does not exist — {$href}";
            continue;
        }

        if ($fragment !== '' && !in_array($fragment, $anchors[$target], true)) {
            $errors[] = "{$slug}.html: link to an anchor that does not exist — {$href}";
        }
    }
}

echo "Checked {$links} internal links across " . count($fragments) . " pages.\n";

// ─────────────────────────────────────────────────────────────────────────────

if ($errors) {
    echo "\n" . count($errors) . " problem(s):\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
    exit(1);
}

echo "All documentation checks passed.\n";
