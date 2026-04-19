<?php

declare(strict_types=1);

namespace Papier\Destination;

use Papier\Objects\{PdfArray, PdfName, PdfNull, PdfObject, PdfReal};

final class FitBHDestination
{
    public static function create(PdfObject $page, ?float $top = null): PdfArray
    {
        $arr = new PdfArray(); $arr->add($page); $arr->add(new PdfName('FitBH'));
        $arr->add($top !== null ? new PdfReal($top) : new PdfNull());
        return $arr;
    }
}
