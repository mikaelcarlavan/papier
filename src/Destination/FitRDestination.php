<?php

declare(strict_types=1);

namespace Papier\Destination;

use Papier\Objects\{PdfArray, PdfName, PdfObject, PdfReal};

final class FitRDestination
{
    public static function create(PdfObject $page, float $left, float $bottom, float $right, float $top): PdfArray
    {
        $arr = new PdfArray();
        $arr->add($page);
        $arr->add(new PdfName('FitR'));
        $arr->add(new PdfReal($left));
        $arr->add(new PdfReal($bottom));
        $arr->add(new PdfReal($right));
        $arr->add(new PdfReal($top));
        return $arr;
    }
}
