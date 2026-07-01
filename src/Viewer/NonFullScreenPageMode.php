<?php

declare(strict_types=1);

namespace Papier\Viewer;

/**
 * How the document displays when it exits full-screen mode (§12.2 Table 150,
 * /NonFullScreenPageMode).  Used with
 * {@see ViewerPreferences::nonFullScreenPageMode()}.
 */
enum NonFullScreenPageMode: string
{
    /** Neither the outline nor thumbnails are shown. */
    case UseNone     = 'UseNone';
    /** Show the document outline (bookmarks) panel. */
    case UseOutlines = 'UseOutlines';
    /** Show the page-thumbnail panel. */
    case UseThumbs   = 'UseThumbs';
    /** Show the optional-content (layers) panel. */
    case UseOC       = 'UseOC';
}
