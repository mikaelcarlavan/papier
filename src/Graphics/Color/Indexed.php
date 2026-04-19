<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfName, PdfObject};

final class Indexed extends ColorSpace
{
    /**
     * @param ColorSpace $base      The base colour space.
     * @param int        $hiVal     Highest valid index (0–255).
     * @param string     $lookupTable  Raw bytes: (hiVal+1) × n colour values.
     */
    public function __construct(
        private readonly ColorSpace $base,
        private readonly int        $hiVal,
        private readonly string     $lookupTable,
    ) {}

    public function getName(): string { return 'Indexed'; }
    public function getComponentCount(): int { return 1; }

    public function toPdfObject(): PdfObject
    {
        $arr = new PdfArray();
        $arr->add(new PdfName('Indexed'));
        $arr->add($this->base->toPdfObject());
        $arr->add(new \Papier\Objects\PdfInteger($this->hiVal));
        $arr->add(new \Papier\Objects\PdfString($this->lookupTable));
        return $arr;
    }
}
