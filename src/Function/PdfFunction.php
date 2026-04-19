<?php

declare(strict_types=1);

namespace Papier\Function;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfObject, PdfReal, PdfStream};

/**
 * PDF functions (ISO 32000-1 §7.10).
 *
 * Functions map n input values to m output values.
 * FunctionType values:
 *   0 = Sampled (lookup table)
 *   2 = Exponential interpolation
 *   3 = Stitching (piecewise)
 *   4 = PostScript calculator
 */
abstract class PdfFunction
{
    protected array $domain; // [d0min d0max …]
    protected array $range;  // [r0min r0max …]

    public function __construct(array $domain, array $range)
    {
        $this->domain = $domain;
        $this->range  = $range;
    }

    abstract public function getFunctionType(): int;
    abstract public function toPdfObject(): PdfObject;

    protected function buildBaseDict(): PdfDictionary
    {
        $dict = new PdfDictionary();
        $dict->set('FunctionType', new PdfInteger($this->getFunctionType()));

        $domain = new PdfArray();
        foreach ($this->domain as $v) { $domain->add(new PdfReal($v)); }
        $dict->set('Domain', $domain);

        $range = new PdfArray();
        foreach ($this->range as $v) { $range->add(new PdfReal($v)); }
        $dict->set('Range', $range);

        return $dict;
    }
}
