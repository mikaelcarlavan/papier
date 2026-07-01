<?php

declare(strict_types=1);

namespace Papier;

/**
 * Selects which pages a running element (header, footer, watermark…) applies to.
 *
 * Used with {@see PdfDocument::onEachPage()}, {@see PdfDocument::header()} and
 * {@see PdfDocument::footer()}.  Those methods also accept an `int` (every Nth
 * page) or a `\Closure` (custom `fn(int $pageNumber, int $pageCount): bool`)
 * when a fixed rule isn't enough.
 *
 * Example — a footer on every page, a notice on the first only:
 *
 *   $doc->footer($render);                       // PageRule::All by default
 *   $doc->onEachPage($notice, PageRule::First);
 */
enum PageRule
{
    /** Every page. */
    case All;
    /** Odd page numbers: 1, 3, 5 … */
    case Odd;
    /** Even page numbers: 2, 4, 6 … */
    case Even;
    /** The first page only. */
    case First;
    /** The last page only. */
    case Last;
}
