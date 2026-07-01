<?php

declare(strict_types=1);

namespace Papier\Metadata;

/**
 * PDF/A conformance level (ISO 19005).
 *
 * The level records which additional requirements a PDF/A file meets on top of
 * its part.  Used with {@see \Papier\PdfDocument::enablePdfA()}.
 *
 * Example — an accessible PDF/A-2a document:
 *
 *   $doc->enablePdfA(2, PdfAConformance::Accessible);
 *
 * @see https://www.pdfa.org/resource/iso-19005-pdfa/
 */
enum PdfAConformance: string
{
    /** Level B ("basic") — reliable visual reproduction. Valid for all parts. */
    case Basic      = 'B';
    /** Level A ("accessible") — Level B plus tagging for accessibility. Valid for all parts. */
    case Accessible = 'A';
    /** Level U ("Unicode") — Level B plus Unicode text mapping. PDF/A-2 and PDF/A-3 only. */
    case Unicode    = 'U';
}
