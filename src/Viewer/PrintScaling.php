<?php

declare(strict_types=1);

namespace Papier\Viewer;

/**
 * Page-scaling policy the viewer applies in its Print dialog (§12.2 Table 150,
 * /PrintScaling).  Used with {@see ViewerPreferences::printScaling()}.
 */
enum PrintScaling: string
{
    /** No scaling — print at physical page size. */
    case None       = 'None';
    /** The viewer's default page-scaling behaviour. */
    case AppDefault = 'AppDefault';
}
