<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfDictionary, PdfName, PdfObject, PdfString};

/**
 * Hyperlink annotation (`/Subtype /Link`).
 *
 * A link annotation activates an action or navigates to a destination when
 * clicked.  The clickable area is the annotation's `/Rect`.
 *
 * Example:
 *
 *   $link = new LinkAnnotation(72, 700, 300, 720);
 *   $link->setURI('https://example.com');
 *
 *   // Or navigate to page 3:
 *   $link->setDestination(new PdfArray(...));
 */
final class LinkAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Link'; }

    /**
     * Set a named or explicit destination (`/Dest`).
     *
     * @param PdfObject $dest  A destination array or name.
     */
    public function setDestination(PdfObject $dest): static
    {
        $this->dict->set('Dest', $dest);
        return $this;
    }

    /**
     * Set the highlight mode (`/H`) for visual feedback on click.
     *
     * @param string $mode  `N` none, `I` invert (default), `O` outline, `P` push.
     */
    public function setHighlightMode(string $mode): static
    {
        $this->dict->set('H', new PdfName($mode));
        return $this;
    }

    /**
     * Convenience method: set a URI action (`/A` with `/S /URI`).
     *
     * @param string $uri  The URL to open when the annotation is clicked.
     */
    public function setURI(string $uri): static
    {
        $action = new PdfDictionary();
        $action->set('Type', new PdfName('Action'));
        $action->set('S', new PdfName('URI'));
        $action->set('URI', new PdfString($uri));
        $this->dict->set('A', $action);
        return $this;
    }
}
