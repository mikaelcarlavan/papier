<?php

declare(strict_types=1);

namespace Papier\Viewer;

/**
 * Predominant reading order for the document, used to lay out facing pages
 * (§12.2 Table 150, /Direction).  Used with
 * {@see ViewerPreferences::readingDirection()}.
 */
enum ReadingDirection: string
{
    /** Left to right (default). */
    case LeftToRight = 'L2R';
    /** Right to left (e.g. Arabic, Hebrew, vertical CJK). */
    case RightToLeft = 'R2L';
}
