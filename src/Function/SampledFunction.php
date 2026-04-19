<?php

declare(strict_types=1);

namespace Papier\Function;

use Papier\Objects\{PdfArray, PdfInteger, PdfObject, PdfReal, PdfStream};

final class SampledFunction extends PdfFunction
{
    public function __construct(
        array  $domain,
        array  $range,
        private array  $size,          // n integers — samples per input
        private string $samples,       // raw sample bytes
        private int    $bitsPerSample  = 8,
        private int    $order          = 1,
        private ?array $encode         = null,
        private ?array $decode         = null,
    ) {
        parent::__construct($domain, $range);
    }

    public function getFunctionType(): int { return 0; }

    public function toPdfObject(): PdfObject
    {
        $stream = new PdfStream($this->buildBaseDict());
        $dict   = $stream->getDictionary();

        $size = new PdfArray();
        foreach ($this->size as $v) { $size->add(new PdfInteger($v)); }
        $dict->set('Size', $size);
        $dict->set('BitsPerSample', new PdfInteger($this->bitsPerSample));
        if ($this->order !== 1) { $dict->set('Order', new PdfInteger($this->order)); }

        if ($this->encode !== null) {
            $enc = new PdfArray();
            foreach ($this->encode as $v) { $enc->add(new PdfReal($v)); }
            $dict->set('Encode', $enc);
        }
        if ($this->decode !== null) {
            $dec = new PdfArray();
            foreach ($this->decode as $v) { $dec->add(new PdfReal($v)); }
            $dict->set('Decode', $dec);
        }

        $stream->setData($this->samples);
        $stream->compress();
        return $stream;
    }
}
