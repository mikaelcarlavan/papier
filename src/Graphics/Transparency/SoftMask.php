<?php

declare(strict_types=1);

namespace Papier\Graphics\Transparency;

use Papier\Objects\{PdfDictionary, PdfName, PdfObject};

/**
 * Soft-mask dictionary (ISO 32000-1 §11.6.5.2 Table 145).
 *
 * A soft mask specifies a mask to be applied to an object's alpha values.
 * Type: Luminosity (use luminosity of backdrop) or Alpha (use alpha of backdrop).
 */
final class SoftMask
{
    private PdfDictionary $dict;

    public function __construct(
        string     $type,       // 'Luminosity' or 'Alpha'
        PdfObject  $group,      // transparency group XObject reference
        ?PdfObject $backdropColor = null,
        ?PdfObject $transferFunction = null,
    ) {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Mask'));
        $this->dict->set('S', new PdfName($type));
        $this->dict->set('G', $group);
        if ($backdropColor !== null) {
            $this->dict->set('BC', $backdropColor);
        }
        if ($transferFunction !== null) {
            $this->dict->set('TR', $transferFunction);
        }
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
