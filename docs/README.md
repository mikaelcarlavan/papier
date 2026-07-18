# Papier documentation site

The static documentation site for Papier. Plain HTML, CSS and JavaScript — no
Node.js, no bundler, no runtime dependencies. Deployable as-is to GitHub Pages,
Netlify, or any static host.

## Layout

```
docs/
├── *.html              Generated pages — do not edit by hand
├── _content/*.html     Page content fragments — edit these
├── assets/
│   ├── style.css       Design system, light and dark themes
│   ├── docs.js         Theme toggle, search, copy buttons, scroll spy
│   ├── favicon.svg
│   └── search-index.json   Generated
└── .nojekyll           Serve underscore-prefixed paths on GitHub Pages
```

## Editing

Edit the fragment in `_content/`, then regenerate:

```bash
php tools/build-docs.php
```

The generator wraps each fragment in the shared layout, builds the sidebar, the
"On this page" rail and the previous/next pager, highlights the code blocks
using PHP's native tokenizer, and rebuilds the search index.

### Fragment format

A fragment is a body-only HTML snippet with two front-matter comments:

```html
<!-- title: Page title -->
<!-- description: One sentence for the <meta> description and search. -->
<h1>Page title</h1>
<p class="lead">Introduction.</p>

<h2 id="a-section">A section</h2>
<pre><code class="language-php">$doc = PdfDocument::create();</code></pre>
```

- Every `<h2>` and `<h3>` needs a kebab-case `id` — the table of contents and
  the search index are built from them.
- Code blocks must use `<pre><code class="language-php">` (or `language-bash`).
  PHP snippets are tokenised, so they must be valid PHP without a `<?php` tag.
- `<div class="note">` and `<div class="warn">` render as callouts.

## Adding a page

Add the slug to the `NAV` constant at the top of `tools/build-docs.php`, create
`_content/<slug>.html`, and rebuild. The sidebar, pager and search index pick it
up automatically.

## Previewing

```bash
php -S 127.0.0.1:8000 -t docs
```

Then open <http://127.0.0.1:8000/>.
