<?php

declare(strict_types=1);

namespace Papier\Destination;

use Papier\Objects\{PdfArray, PdfName, PdfNull, PdfObject, PdfReal};

final class XYZDestination
{
    public static function create(PdfObject $page, ?float $left, ?float $top, ?float $zoom): PdfArray
    {
        $arr = new PdfArray();
        $arr->add($page);
        $arr->add(new PdfName('XYZ'));
        $arr->add($left  !== null ? new PdfReal($left)  : new PdfNull());
        $arr->add($top   !== null ? new PdfReal($top)   : new PdfNull());
        $arr->add($zoom  !== null ? new PdfReal($zoom)  : new PdfNull());
        return $arr;
    }
}
