<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Annotation\Annotation;

/**
 * Optional interface for Elements that produce annotation objects.
 *
 * Implement this alongside {@see Element} when the element's behaviour
 * requires an entry in the page's `/Annots` array — for example a media
 * placeholder that activates a SoundAnnotation or ScreenAnnotation when
 * clicked.
 *
 * {@see \Papier\Structure\PdfPage::add()} checks for this interface after
 * calling `render()` and automatically registers the returned annotations
 * with the page, so callers never need to call `addAnnotation()` separately.
 *
 * Implementation contract:
 *   - The list returned by `getAnnotations()` must be stable across calls.
 *   - It is safe to call `getAnnotations()` before `render()`.
 */
interface AnnotationProvider
{
    /**
     * Return the annotations produced by this element.
     *
     * @return Annotation[]
     */
    public function getAnnotations(): array;
}
