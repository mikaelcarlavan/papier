<?php

declare(strict_types=1);

namespace Papier\OptionalContent;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName};

/**
 * OCG membership dictionary (ISO 32000-1 §8.11.2.2).
 *
 * Controls which content is visible based on the state of a set of OCGs.
 */
final class OCMembership
{
    private PdfDictionary $dict;

    /**
     * @param PdfObject[] $ocgs    OCG references.
     * @param string      $policy  OCMD visibility policy:
     *                             AllOn|AnyOn|AnyOff|AllOff.
     */
    public function __construct(array $ocgs, string $policy = 'AnyOn')
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('OCMD'));

        $arr = new PdfArray();
        foreach ($ocgs as $ocg) { $arr->add($ocg); }
        $this->dict->set('OCGs', $arr);
        $this->dict->set('P', new PdfName($policy));
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
