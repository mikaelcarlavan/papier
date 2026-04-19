<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfReal};

final class CalRGB extends ColorSpace
{
    public function __construct(
        private readonly array  $whitePoint,
        private readonly ?array $blackPoint = null,
        private readonly ?array $gamma      = null,  // [gr, gg, gb]
        private readonly ?array $matrix     = null,  // 3×3 column-major
    ) {}

    public function getName(): string { return 'CalRGB'; }
    public function getComponentCount(): int { return 3; }

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
        if ($this->gamma !== null) {
            $g = new PdfArray();
            foreach ($this->gamma as $v) { $g->add(new PdfReal($v)); }
            $dict->set('Gamma', $g);
        }
        if ($this->matrix !== null) {
            $m = new PdfArray();
            foreach ($this->matrix as $v) { $m->add(new PdfReal($v)); }
            $dict->set('Matrix', $m);
        }
        $arr = new PdfArray();
        $arr->add(new PdfName('CalRGB'));
        $arr->add($dict);
        return $arr;
    }
}
