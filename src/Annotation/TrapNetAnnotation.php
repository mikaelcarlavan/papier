<?php

declare(strict_types=1);

namespace Papier\Annotation;

/**
 * Trapping-network annotation (`/Subtype /TrapNet`).
 *
 * Describes the trapping characteristics of the page.  Created and consumed
 * by trapping software; not normally authored directly.
 */
final class TrapNetAnnotation extends Annotation
{
    public function getSubtype(): string { return 'TrapNet'; }
}
