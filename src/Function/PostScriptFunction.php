<?php

declare(strict_types=1);

namespace Papier\Function;

use Papier\Objects\{PdfObject, PdfStream};

final class PostScriptFunction extends PdfFunction
{
    public function __construct(
        array  $domain,
        array  $range,
        private string $psCode,  // PostScript calculator program (between {…})
    ) {
        parent::__construct($domain, $range);
    }

    public function getFunctionType(): int { return 4; }

    public function toPdfObject(): PdfObject
    {
        $stream = new PdfStream($this->buildBaseDict());
        $stream->setData('{' . $this->psCode . '}');
        $stream->compress();
        return $stream;
    }
}
