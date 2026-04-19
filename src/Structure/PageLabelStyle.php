<?php

declare(strict_types=1);

namespace Papier\Structure;

/**
 * Page-label numbering styles (ISO 32000-1 §12.4.2 Table 159).
 *
 * Used with {@see \Papier\PdfDocument::addPageLabel()}.
 *
 * Example — front matter as lowercase roman, body as decimal:
 *
 *   $doc->addPageLabel(0, PageLabelStyle::RomanLower);           // i, ii, iii …
 *   $doc->addPageLabel(4, PageLabelStyle::Decimal);              // 1, 2, 3 …
 *   $doc->addPageLabel(4, PageLabelStyle::Decimal, prefix: 'p.'); // p.1, p.2 …
 */
enum PageLabelStyle: string
{
    /** Arabic decimal numerals: 1, 2, 3 … */
    case Decimal    = 'D';
    /** Lowercase roman numerals: i, ii, iii … */
    case RomanLower = 'r';
    /** Uppercase roman numerals: I, II, III … */
    case RomanUpper = 'R';
    /** Lowercase letters: a, b, c … aa, bb … */
    case AlphaLower = 'a';
    /** Uppercase letters: A, B, C … AA, BB … */
    case AlphaUpper = 'A';
    /** No automatic numbering — label is the prefix alone (or blank). */
    case None       = '';
}
