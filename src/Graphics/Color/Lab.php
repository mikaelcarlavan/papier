<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfReal};

final class Lab extends ColorSpace
{
    public function __construct(
        private readonly array  $whitePoint,
        private readonly ?array $blackPoint = null,
        private readonly ?array $range      = null,  // [amin,amax,bmin,bmax] default [-100,100,-100,100]
    ) {}

    public function getName(): string { return 'Lab'; }
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
        if ($this->range !== null) {
            $r = new PdfArray();
            foreach ($this->range as $v) { $r->add(new PdfReal($v)); }
            $dict->set('Range', $r);
        }
        $arr = new PdfArray();
        $arr->add(new PdfName('Lab'));
        $arr->add($dict);
        return $arr;
    }
}
