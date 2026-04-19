<?php

declare(strict_types=1);

namespace Papier\Destination;

use Papier\Objects\{PdfArray, PdfName, PdfNull, PdfObject, PdfReal};

final class FitVDestination
{
    public static function create(PdfObject $page, ?float $left = null): PdfArray
    {
        $arr = new PdfArray();
        $arr->add($page);
        $arr->add(new PdfName('FitV'));
        $arr->add($left !== null ? new PdfReal($left) : new PdfNull());
        return $arr;
    }
}
