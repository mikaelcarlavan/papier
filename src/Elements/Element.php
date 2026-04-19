<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Structure\PdfResources;

/**
 * Interface for high-level page elements (text, images, shapes, media, …).
 *
 * An element encapsulates both the visual description (what to draw) and the
 * resource registration (fonts, XObjects, shadings, ExtGStates, …) needed by
 * the drawing commands.  This keeps all concerns for a single piece of
 * page content in one place.
 *
 * Add elements to a page via {@see \Papier\Structure\PdfPage::add()}:
 *
 *   $page->add(
 *       Text::write('Hello, World!')->at(72, 720)->font($f, 24),
 *       Rectangle::create(72, 60, 451, 2)->fill(Color::gray(0.8)),
 *       Image::fromFile('photo.jpg')->at(72, 500)->size(200, 150),
 *   );
 *
 * Each element receives a fresh {@see ContentStream} and the page's shared
 * {@see PdfResources} on every `render()` call.  Elements that also produce
 * annotation objects (e.g. media placeholders) should additionally implement
 * {@see AnnotationProvider}.
 */
interface Element
{
    /**
     * Write drawing operators into $cs and register any required resources.
     *
     * @param ContentStream $cs        The content stream to append operators to.
     * @param PdfResources  $resources The page resource dictionary; register
     *                                 fonts, XObjects, ExtGStates, etc. here.
     */
    public function render(ContentStream $cs, PdfResources $resources): void;
}
