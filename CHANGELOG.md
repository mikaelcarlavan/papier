# Changelog

All notable changes to this project are documented in this file.

## [3.0.0] - 2026-07-01

Type-safe public API: stringly-typed and loosely-typed parameters are replaced
with enums and a fluent builder. Invalid values are now caught at the type level
instead of silently producing a wrong PDF, and IDE autocompletion makes the
options discoverable.

### ⚠️ Breaking changes

Four public method signatures changed.

- **`PdfDocument::enablePdfA()`** — the conformance level is now a
  `Papier\Metadata\PdfAConformance` enum instead of a string.
  ```php
  // before
  $doc->enablePdfA(2, 'B');
  // after
  $doc->enablePdfA(2, PdfAConformance::Basic);   // ::Accessible, ::Unicode
  ```

- **`PdfDocument::onEachPage()` / `header()` / `footer()`** — the page rule is now
  a `Papier\PageRule` enum. `int` (every Nth page) and `Closure` predicates are
  still accepted. This also fixes a latent bug where a misspelled rule string
  silently matched every page.
  ```php
  // before
  $doc->header($render, 'odd');
  // after
  $doc->header($render, PageRule::Odd);          // ::All (default), ::Even, ::First, ::Last
  ```

- **`PdfDocument::setViewerPreferences()`** — now takes a
  `Papier\Viewer\ViewerPreferences` fluent builder instead of an untyped array.
  Uncommon keys remain available via `->set(string $key, PdfObject $value)`.
  ```php
  // before
  $doc->setViewerPreferences([
      'DisplayDocTitle'       => true,
      'PrintScaling'          => 'None',
      'NonFullScreenPageMode' => 'UseOutlines',
  ]);
  // after
  $doc->setViewerPreferences(
      ViewerPreferences::create()
          ->displayDocTitle()
          ->printScaling(PrintScaling::None)
          ->nonFullScreenPageMode(NonFullScreenPageMode::UseOutlines)
  );
  ```

- **`PdfDocument::encrypt()`** — the `$algorithm` argument is now a
  `Papier\Encryption\EncryptionAlgorithm` enum. The
  `StandardSecurityHandler::RC4_40 / RC4_128 / AES_128 / AES_256` constants have
  been **removed**; the `PERM_*` permission flags are unchanged.
  ```php
  // before
  $doc->encrypt('pw', '', $perms, StandardSecurityHandler::AES_256);
  // after
  $doc->encrypt('pw', '', $perms, EncryptionAlgorithm::Aes_256);
  ```

### Added

- `Papier\Metadata\PdfAConformance` enum (`Basic`, `Accessible`, `Unicode`).
- `Papier\PageRule` enum (`All`, `Odd`, `Even`, `First`, `Last`).
- `Papier\Encryption\EncryptionAlgorithm` enum (`Rc4_40`, `Rc4_128`, `Aes_128`, `Aes_256`).
- `Papier\Viewer\ViewerPreferences` fluent builder, with value enums
  `PrintScaling`, `Duplex`, `NonFullScreenPageMode`, and `ReadingDirection`.

### Notes

- No changes to output format — regenerating a document with the updated call
  sites produces the same bytes.
