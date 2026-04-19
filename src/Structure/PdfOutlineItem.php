<?php

declare(strict_types=1);

namespace Papier\Structure;

use Papier\Action\Action;
use Papier\Elements\Color;
use Papier\Objects\{PdfArray, PdfObject, PdfString};

/**
 * A single bookmark entry in the document outline (ISO 32000-1 §12.3.3 Table 153).
 *
 * An outline item has a visible title and may navigate to a named destination,
 * an explicit destination array, or execute an action when clicked.  Items
 * can be nested: call {@see self::addChild()} to build multi-level outlines.
 *
 * Visual properties (`setColor`, `setItalic`, `setBold`) affect how the
 * bookmark appears in the viewer's navigation panel.
 */
final class PdfOutlineItem
{
    /** @var PdfOutlineItem[] Child items (sub-sections). */
    private array       $children = [];
    private ?PdfObject  $dest     = null;
    private ?PdfObject  $action   = null;
    /** @var float[]|null  RGB colour [r, g, b] in [0, 1], or null for default. */
    private ?array      $color    = null;
    /** Bit 1 = italic, bit 2 = bold. */
    private int         $flags    = 0;

    /**
     * @param string $title  Bookmark label shown in the viewer panel (UTF-8).
     */
    public function __construct(private readonly string $title) {}

    /** Return the bookmark label. */
    public function getTitle(): string { return $this->title; }

    /**
     * Set the navigation destination (`/Dest`).
     *
     * Pass either:
     *   - A **string** — a named destination registered via
     *     {@see \Papier\PdfDocument::addNamedDestination()}.
     *   - A **PdfArray** — an explicit destination built with one of the
     *     `Destination\*` factory classes (e.g. `XYZDestination::create()`).
     *
     * Mutually exclusive with {@see self::setAction()}.
     *
     * Example:
     *
     *   $item->setDestination('chapter-2');                  // named
     *   $item->setDestination(FitDestination::create($page)); // explicit
     *
     * @param string|PdfObject $dest  Named destination string or destination array.
     */
    public function setDestination(string|PdfObject $dest): static
    {
        $this->dest = is_string($dest) ? new PdfString($dest) : $dest;
        return $this;
    }

    /** Return the destination, or null if not set. */
    public function getDestination(): ?PdfObject { return $this->dest; }

    /**
     * Set an action to execute when the bookmark is clicked (`/A`).
     *
     * Pass any action class from `Papier\Action\*`
     * (e.g. `new URIAction('https://…')`) or a pre-built dictionary.
     * Mutually exclusive with {@see self::setDestination()}.
     *
     * @param Action|PdfObject $action  Action object or pre-built dictionary.
     */
    public function setAction(Action|PdfObject $action): static
    {
        $this->action = $action instanceof Action ? $action->getDictionary() : $action;
        return $this;
    }

    /** Return the action, or null if not set. */
    public function getAction(): ?PdfObject { return $this->action; }

    /**
     * Set the bookmark text colour (`/C`).
     *
     * Accepts a {@see Color} object or three RGB floats.
     *
     * @param Color|float $colorOrR  Color object, or the red component [0, 1].
     * @param float|null  $g         Green component (only when passing raw floats).
     * @param float|null  $b         Blue  component (only when passing raw floats).
     */
    public function setColor(Color|float $colorOrR, ?float $g = null, ?float $b = null): static
    {
        $this->color = $colorOrR instanceof Color
            ? $colorOrR->toRgb()
            : [$colorOrR, $g ?? 0.0, $b ?? 0.0];
        return $this;
    }

    /**
     * Return the colour as `[r, g, b]`, or null if not set.
     *
     * @return float[]|null
     */
    public function getColor(): ?array { return $this->color; }

    /**
     * Toggle italic rendering of the bookmark label (bit 1 of `/F`).
     *
     * @param bool $italic  true to render in italic.
     */
    public function setItalic(bool $italic): static
    {
        $italic ? $this->flags |= 1 : $this->flags &= ~1;
        return $this;
    }

    /**
     * Toggle bold rendering of the bookmark label (bit 2 of `/F`).
     *
     * @param bool $bold  true to render in bold.
     */
    public function setBold(bool $bold): static
    {
        $bold ? $this->flags |= 2 : $this->flags &= ~2;
        return $this;
    }

    /**
     * Return the style flags bitfield (`/F`).
     *
     * Bit 1 = italic, bit 2 = bold.
     */
    public function getFlags(): int { return $this->flags; }

    /**
     * Add a child outline item (nested sub-section).
     *
     * @param PdfOutlineItem $child  The child bookmark.
     */
    public function addChild(PdfOutlineItem $child): static
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Return all direct children of this item.
     *
     * @return PdfOutlineItem[]
     */
    public function getChildren(): array { return $this->children; }
}
