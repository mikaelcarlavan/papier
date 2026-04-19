<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfReal};

final class CalGray extends ColorSpace
{
    public function __construct(
        private readonly array $whitePoint,  // [Xw, Yw, Zw]
        private readonly ?array $blackPoint = null,
        private readonly float  $gamma      = 1.0,
    ) {}

    public function getName(): string { return 'CalGray'; }
    public function getComponentCount(): int { return 1; }

    public function toPdfObject(): PdfObject
    {
        $dict = new PdfDictionary();
        $wp = new PdfArray();
        foreach ($this->whitePoint as $v) { $wp->add(new PdfReal($v)); }
        $dict->set('WhitePoint', $wp);
        if ($this->blackPoint !== null) {
            $bp = new PdfArray();
            foreach ($this->blackPoint as $v) { $bp->add(new PdfReal($v)); }
            $dict->set('BlackPoint', $bp);
        }
        if ($this->gamma !== 1.0) {
            $dict->set('Gamma', new PdfReal($this->gamma));
        }
        $arr = new PdfArray();
        $arr->add(new PdfName('CalGray'));
        $arr->add($dict);
        return $arr;
    }
}
