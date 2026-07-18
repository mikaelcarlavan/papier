<?php

/**
 * Documentation site generator.
 *
 * Reads the content fragments in docs/_content/*.html, highlights the code
 * blocks with PHP's native tokenizer, and writes standalone static pages to
 * docs/. The output has no build-time dependency of its own: plain HTML, CSS
 * and JS, deployable as-is to GitHub Pages or papier.io.
 *
 *     php tools/build-docs.php
 */

declare(strict_types=1);

const ROOT    = __DIR__ . '/..';
const CONTENT = ROOT . '/docs/_content';
const OUTPUT  = ROOT . '/docs';

/**
 * Site navigation. Each entry is [slug, label]; slug maps to
 * docs/_content/<slug>.html and docs/<slug>.html.
 */
const NAV = [
    'Getting started' => [
        ['index', 'Introduction'],
        ['installation', 'Installation'],
        ['quickstart', 'Quick start'],
    ],
    'Creating PDFs' => [
        ['documents', 'Documents & pages'],
        ['text', 'Text & fonts'],
        ['graphics', 'Graphics'],
        ['images', 'Images'],
        ['tables', 'Tables'],
    ],
    'Interactivity' => [
        ['forms', 'Forms'],
        ['annotations', 'Annotations'],
        ['navigation', 'Bookmarks & navigation'],
        ['multimedia', 'Layers & multimedia'],
    ],
    'Advanced' => [
        ['security', 'Encryption & signatures'],
        ['accessibility', 'Tagged PDF & PDF/A'],
        ['file-structure', 'File structure'],
        ['reading', 'Reading PDFs'],
    ],
    'Reference' => [
        ['examples', 'Examples'],
        ['changelog', 'Changelog'],
    ],
];

// ─────────────────────────────────────────────────────────────────────────────
// Syntax highlighting
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Highlight a PHP snippet using token_get_all(). Snippets in the docs are
 * written without an opening tag, so one is added and then stripped.
 */
function highlight_php(string $code): string
{
    $tokens = @token_get_all("<?php\n" . $code);
    array_shift($tokens);           // the injected <?php token
    $out    = '';
    $first  = true;

    foreach ($tokens as $token) {
        if (is_string($token)) {
            $out .= '<span class="t-op">' . e($token) . '</span>';
            continue;
        }

        [$id, $text] = $token;

        if ($first) {               // strip the newline that followed <?php
            $text  = preg_replace('/^\n/', '', $text);
            $first = false;
        }

        $out .= match (true) {
            in_array($id, [T_COMMENT, T_DOC_COMMENT], true)      => wrap('t-comment', $text),
            in_array($id, [T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE], true),
            $id === T_START_HEREDOC || $id === T_END_HEREDOC     => wrap('t-string', $text),
            in_array($id, [T_LNUMBER, T_DNUMBER], true)          => wrap('t-number', $text),
            $id === T_VARIABLE                                   => wrap('t-var', $text),
            $id === T_STRING                                     => wrap(is_known_type($text) ? 't-type' : 't-fn', $text),
            $id === T_INLINE_HTML                                => e($text),
            $id === T_WHITESPACE                                 => e($text),
            default                                              => wrap('t-kw', $text),
        };
    }

    return $out;
}

/** Class-ish identifiers (PascalCase) get the type colour, others read as calls. */
function is_known_type(string $name): bool
{
    return (bool) preg_match('/^[A-Z]/', $name);
}

function highlight_bash(string $code): string
{
    $lines = [];
    foreach (explode("\n", $code) as $line) {
        if (preg_match('/^\s*#/', $line)) {
            $lines[] = wrap('t-comment', $line);
            continue;
        }
        $html = e($line);
        $html = preg_replace('/^(\s*)(composer|php|git|npx|curl|qpdf|gs)\b/', '$1<span class="t-kw">$2</span>', $html);
        $html = preg_replace('/(\s)(--?[\w-]+)/', '$1<span class="t-op">$2</span>', $html);
        $lines[] = $html;
    }
    return implode("\n", $lines);
}

function wrap(string $class, string $text): string
{
    return '<span class="' . $class . '">' . e($text) . '</span>';
}

function e(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Replace every <pre><code class="language-*"> block with highlighted markup. */
function highlight_blocks(string $html): string
{
    return preg_replace_callback(
        '#<pre><code class="language-(\w+)">(.*?)</code></pre>#s',
        static function (array $m): string {
            $lang = $m[1];
            $code = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $code = rtrim($code, "\n");

            $body = match ($lang) {
                'php'          => highlight_php($code),
                'bash', 'sh'   => highlight_bash($code),
                default        => e($code),
            };

            return '<div class="code-block" data-lang="' . e($lang) . '">'
                 . '<button class="copy" type="button" aria-label="Copy code">Copy</button>'
                 . '<pre><code>' . $body . '</code></pre></div>';
        },
        $html
    );
}

// ─────────────────────────────────────────────────────────────────────────────
// Page assembly
// ─────────────────────────────────────────────────────────────────────────────

/** Pull `<!-- key: value -->` front matter out of a fragment. */
function read_meta(string $html): array
{
    $meta = [];
    if (preg_match_all('/^<!--\s*(\w+):\s*(.*?)\s*-->$/m', $html, $m, PREG_SET_ORDER)) {
        foreach ($m as $match) {
            $meta[$match[1]] = $match[2];
        }
    }
    return $meta;
}

/** Collect <h2 id> / <h3 id> headings for the right-hand "On this page" rail. */
function extract_toc(string $html): array
{
    preg_match_all('#<h([23]) id="([^"]+)">(.*?)</h[23]>#s', $html, $m, PREG_SET_ORDER);

    return array_map(static fn(array $h) => [
        'level' => (int) $h[1],
        'id'    => $h[2],
        'text'  => trim(strip_tags($h[3])),
    ], $m);
}

function render_nav(string $current): string
{
    $html = '';
    foreach (NAV as $section => $pages) {
        $html .= '<div class="nav-section"><h4>' . e($section) . '</h4><ul>';
        foreach ($pages as [$slug, $label]) {
            $active = $slug === $current ? ' class="active"' : '';
            $html  .= '<li><a href="' . $slug . '.html"' . $active . '>' . e($label) . '</a></li>';
        }
        $html .= '</ul></div>';
    }
    return $html;
}

function render_toc(array $toc): string
{
    if (count($toc) < 2) {
        return '';
    }

    $items = '';
    foreach ($toc as $h) {
        $items .= '<li class="lvl-' . $h['level'] . '"><a href="#' . e($h['id']) . '">' . e($h['text']) . '</a></li>';
    }

    return '<aside class="toc"><h4>On this page</h4><ul>' . $items . '</ul></aside>';
}

/** Previous/next links, following NAV order. */
function render_pager(string $current): string
{
    $flat = [];
    foreach (NAV as $pages) {
        foreach ($pages as $page) {
            $flat[] = $page;
        }
    }

    $i = array_search($current, array_column($flat, 0), true);
    if ($i === false) {
        return '';
    }

    $html = '<nav class="pager">';
    if ($i > 0) {
        $html .= '<a class="prev" href="' . $flat[$i - 1][0] . '.html"><span>Previous</span>' . e($flat[$i - 1][1]) . '</a>';
    }
    if ($i < count($flat) - 1) {
        $html .= '<a class="next" href="' . $flat[$i + 1][0] . '.html"><span>Next</span>' . e($flat[$i + 1][1]) . '</a>';
    }
    return $html . '</nav>';
}

/** The page layout, with %%…%% placeholders the caller substitutes. */
function render_page(string $slug, array $meta): string
{
    $title = $meta['title'] ?? 'Papier';
    $desc  = $meta['description'] ?? 'A comprehensive PHP library for generating and reading PDF documents.';
    $full  = $slug === 'index' ? 'Papier — PHP PDF library' : $title . ' — Papier';
    $home  = $slug === 'index' ? ' class="home"' : '';

    return <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$full}</title>
    <meta name="description" content="{$desc}">
    <meta property="og:title" content="{$full}">
    <meta property="og:description" content="{$desc}">
    <meta property="og:type" content="website">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
    <script>
    (function () {
      var t = localStorage.getItem('papier-theme');
      if (t) document.documentElement.setAttribute('data-theme', t);
    })();
    </script>
    </head>
    <body{$home}>

    <a class="skip" href="#main">Skip to content</a>

    <header class="topbar">
      <button class="menu-toggle" type="button" aria-label="Toggle navigation">
        <svg viewBox="0 0 20 20" width="20" height="20"><path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.6" fill="none" stroke-linecap="round"/></svg>
      </button>
      <a class="brand" href="index.html">
        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true"><path d="M6 2h8l5 5v15H6z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M14 2v5h5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        <span>Papier</span>
      </a>
      <div class="search">
        <input type="search" id="search-input" placeholder="Search documentation…" autocomplete="off" aria-label="Search documentation">
        <kbd>/</kbd>
        <div id="search-results" role="listbox"></div>
      </div>
      <div class="topbar-links">
        <a href="https://packagist.org/packages/papier/papier">Packagist</a>
        <a href="https://github.com/mikaelcarlavan/papier">GitHub</a>
        <button class="theme-toggle" type="button" aria-label="Toggle dark mode">
          <svg class="sun" viewBox="0 0 24 24" width="18" height="18"><circle cx="12" cy="12" r="4.5" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.5 1.5M17.6 17.6l1.5 1.5M19.1 4.9l-1.5 1.5M6.4 17.6l-1.5 1.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
          <svg class="moon" viewBox="0 0 24 24" width="18" height="18"><path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </header>

    <div class="layout">
      <nav class="sidebar" aria-label="Documentation">
        %%NAV%%
      </nav>

      <main id="main">
        <article class="content">
        %%CONTENT%%
        %%PAGER%%
        </article>
      </main>

      %%TOC%%
    </div>

    <footer class="footer">
      <p>Papier — PHP PDF library implementing ISO 32000-1:2008. Released under the MIT licence.</p>
    </footer>

    <script src="assets/docs.js"></script>
    </body>
    </html>
    HTML;
}

// ─────────────────────────────────────────────────────────────────────────────
// Build
// ─────────────────────────────────────────────────────────────────────────────

$searchIndex = [];
$built       = 0;

foreach (NAV as $pages) {
    foreach ($pages as [$slug, $label]) {
        $source = CONTENT . '/' . $slug . '.html';

        if (!is_file($source)) {
            fwrite(STDERR, "  missing: docs/_content/{$slug}.html\n");
            continue;
        }

        $raw     = file_get_contents($source);
        $meta    = read_meta($raw);
        $body    = preg_replace('/^<!--\s*\w+:.*?-->$\n?/m', '', $raw);
        $toc     = extract_toc($body);
        $body    = highlight_blocks($body);

        // The layout heredoc is indented for readability; strip that indent
        // BEFORE substituting, so the leading whitespace inside code blocks
        // survives intact.
        $template = preg_replace('/^    /m', '', render_page($slug, $meta));

        $html = strtr($template, [
            '%%CONTENT%%' => $body,
            '%%NAV%%'     => render_nav($slug),
            '%%TOC%%'     => render_toc($toc),
            '%%PAGER%%'   => render_pager($slug),
        ]);

        file_put_contents(OUTPUT . '/' . $slug . '.html', $html);
        $built++;

        // Search index: one entry per heading, plus the page itself.
        $searchIndex[] = ['t' => $label, 'u' => $slug . '.html', 's' => $meta['description'] ?? '', 'p' => $label];
        foreach ($toc as $h) {
            $searchIndex[] = ['t' => $h['text'], 'u' => $slug . '.html#' . $h['id'], 's' => '', 'p' => $label];
        }
    }
}

file_put_contents(
    OUTPUT . '/assets/search-index.json',
    json_encode($searchIndex, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
);

echo "Built {$built} pages, " . count($searchIndex) . " search entries → docs/\n";
