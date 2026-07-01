<?php

declare(strict_types=1);

namespace Papier\Viewer;

use Papier\Objects\{PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfObject};

/**
 * Fluent builder for the document `/ViewerPreferences` dictionary (§12.2
 * Table 150) — controls how a conforming reader presents the document.
 *
 * Pass the result to {@see \Papier\PdfDocument::setViewerPreferences()}:
 *
 *   $doc->setViewerPreferences(
 *       ViewerPreferences::create()
 *           ->hideToolbar()
 *           ->displayDocTitle()
 *           ->printScaling(PrintScaling::None)
 *           ->nonFullScreenPageMode(NonFullScreenPageMode::UseOutlines)
 *   );
 *
 * Every boolean flag defaults its argument to `true`, so `->fitWindow()` reads
 * naturally.  For rarely-used keys not covered by a dedicated method, use the
 * {@see set()} escape hatch.
 */
final class ViewerPreferences
{
    private PdfDictionary $dict;

    public function __construct()
    {
        $this->dict = new PdfDictionary();
    }

    public static function create(): self
    {
        return new self();
    }

    // ── Window / UI flags ───────────────────────────────────────────────────────

    /** Hide the reader's toolbar while the document is open. */
    public function hideToolbar(bool $on = true): static { return $this->flag('HideToolbar', $on); }

    /** Hide the reader's menu bar while the document is open. */
    public function hideMenubar(bool $on = true): static { return $this->flag('HideMenubar', $on); }

    /** Hide all UI elements except the document window (kiosk-style). */
    public function hideWindowUI(bool $on = true): static { return $this->flag('HideWindowUI', $on); }

    /** Resize the document's window to fit the first displayed page. */
    public function fitWindow(bool $on = true): static { return $this->flag('FitWindow', $on); }

    /** Position the document's window in the centre of the screen. */
    public function centerWindow(bool $on = true): static { return $this->flag('CenterWindow', $on); }

    /** Show the document title (not the file name) in the window title bar. */
    public function displayDocTitle(bool $on = true): static { return $this->flag('DisplayDocTitle', $on); }

    // ── Enumerated preferences ──────────────────────────────────────────────────

    /** Panel shown after leaving full-screen mode. */
    public function nonFullScreenPageMode(NonFullScreenPageMode $mode): static
    {
        return $this->name('NonFullScreenPageMode', $mode->value);
    }

    /** Predominant reading order, affecting facing-page layout. */
    public function readingDirection(ReadingDirection $direction): static
    {
        return $this->name('Direction', $direction->value);
    }

    /** Page-scaling policy for the Print dialog. */
    public function printScaling(PrintScaling $scaling): static
    {
        return $this->name('PrintScaling', $scaling->value);
    }

    /** Simplex/duplex paper-handling default for the Print dialog. */
    public function duplex(Duplex $duplex): static
    {
        return $this->name('Duplex', $duplex->value);
    }

    /** Default number of copies in the Print dialog (>= 1). */
    public function numCopies(int $copies): static
    {
        $this->dict->set('NumCopies', new PdfInteger($copies));
        return $this;
    }

    // ── Escape hatch ────────────────────────────────────────────────────────────

    /**
     * Set an arbitrary preference key not covered by a dedicated method
     * (e.g. `/ViewArea`, `/PrintPageRange`).  See §12.2 Table 150.
     */
    public function set(string $key, PdfObject $value): static
    {
        $this->dict->set($key, $value);
        return $this;
    }

    /** Build the `/ViewerPreferences` dictionary. */
    public function toDictionary(): PdfDictionary
    {
        return $this->dict;
    }

    private function flag(string $key, bool $on): static
    {
        $this->dict->set($key, new PdfBoolean($on));
        return $this;
    }

    private function name(string $key, string $value): static
    {
        $this->dict->set($key, new PdfName($value));
        return $this;
    }
}
