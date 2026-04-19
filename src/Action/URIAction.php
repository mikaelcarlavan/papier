<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfBoolean, PdfString};

/**
 * Resolve a URI — typically opens a URL in the user's browser (`/S /URI`).
 *
 * Example:
 *
 *   $action = new URIAction('https://example.com');
 */
final class URIAction extends Action
{
    /**
     * @param string $uri    The URI to resolve (absolute URL, mailto:, etc.).
     * @param bool   $isMap  true if the annotation is an image map; the viewer
     *                       appends the click coordinates to the URI.
     */
    public function __construct(string $uri, bool $isMap = false)
    {
        parent::__construct();
        $this->dict->set('URI', new PdfString($uri));
        if ($isMap) {
            $this->dict->set('IsMap', new PdfBoolean(true));
        }
    }

    public function getSubtype(): string { return 'URI'; }
}
