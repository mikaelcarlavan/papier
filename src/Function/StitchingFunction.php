<?php

declare(strict_types=1);

namespace Papier\Function;

use Papier\Objects\{PdfArray, PdfObject, PdfReal};

final class StitchingFunction extends PdfFunction
{
    /**
     * @param PdfFunction[] $functions  Sub-functions to stitch together.
     * @param float[]       $bounds     k−1 breakpoints between k sub-functions.
     * @param float[]       $encode     Input range for each sub-function [2k values].
     */
    public function __construct(
        array $domain,
        array $range,
        private array $functions,
        private array $bounds,
        private array $encode,
    ) {
        parent::__construct($domain, $range);
    }

    public function getFunctionType(): int { return 3; }

    public function toPdfObject(): PdfObject
    {
        $dict = $this->buildBaseDict();

        $funcs = new PdfArray();
        foreach ($this->functions as $f) { $funcs->add($f->toPdfObject()); }
        $dict->set('Functions', $funcs);

        $bounds = new PdfArray();
        foreach ($this->bounds as $v) { $bounds->add(new PdfReal($v)); }
        $dict->set('Bounds', $bounds);

        $enc = new PdfArray();
        foreach ($this->encode as $v) { $enc->add(new PdfReal($v)); }
        $dict->set('Encode', $enc);

        return $dict;
    }
}
