<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfName, PdfObject};

final class Separation extends ColorSpace
{
    /**
     * @param string      $colorantName  Name of the colorant (e.g., 'Cyan', 'PANTONE 485 C').
     * @param ColorSpace  $alternateSpace  Fallback colour space.
     * @param PdfObject   $tintTransform  Function converting tint value to alternate.
     */
    public function __construct(
        private readonly string      $colorantName,
        private readonly ColorSpace  $alternateSpace,
        private readonly PdfObject   $tintTransform,
    ) {}

    public function getName(): string { return 'Separation'; }
    public function getComponentCount(): int { return 1; }

    public function toPdfObject(): PdfObject
    {
        $arr = new PdfArray();
        $arr->add(new PdfName('Separation'));
        $arr->add(new PdfName($this->colorantName));
        $arr->add($this->alternateSpace->toPdfObject());
        $arr->add($this->tintTransform);
        return $arr;
    }
}
