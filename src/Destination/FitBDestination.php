<?php

declare(strict_types=1);

namespace Papier\Destination;

use Papier\Objects\{PdfArray, PdfName, PdfObject};

final class FitBDestination
{
    public static function create(PdfObject $page): PdfArray
    {
        $arr = new PdfArray(); $arr->add($page); $arr->add(new PdfName('FitB')); return $arr;
    }
}
