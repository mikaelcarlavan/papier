<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfName, PdfObject};

final class PatternColorSpace extends ColorSpace
{
    public function __construct(private readonly ?ColorSpace $underlyingSpace = null) {}

    public function getName(): string { return 'Pattern'; }
    public function getComponentCount(): int { return 0; }

    public function toPdfObject(): PdfObject
    {
        if ($this->underlyingSpace === null) {
            return new PdfName('Pattern');
        }
        $arr = new PdfArray();
        $arr->add(new PdfName('Pattern'));
        $arr->add($this->underlyingSpace->toPdfObject());
        return $arr;
    }
}
