<?php

declare(strict_types=1);

namespace Papier\Viewer;

/**
 * Paper-handling (duplex) mode the viewer selects when printing (§12.2
 * Table 150, /Duplex).  Used with {@see ViewerPreferences::duplex()}.
 */
enum Duplex: string
{
    /** Single-sided printing. */
    case Simplex            = 'Simplex';
    /** Double-sided, flipped along the short edge. */
    case FlipShortEdge      = 'DuplexFlipShortEdge';
    /** Double-sided, flipped along the long edge. */
    case FlipLongEdge       = 'DuplexFlipLongEdge';
}
