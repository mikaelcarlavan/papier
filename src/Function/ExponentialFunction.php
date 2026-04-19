<?php

declare(strict_types=1);

namespace Papier\Function;

use Papier\Objects\{PdfArray, PdfObject, PdfReal};

final class ExponentialFunction extends PdfFunction
{
    public function __construct(
        array $domain,
        array $range,
        private float  $n,      // exponent (1.0 = linear)
        private ?array $c0 = null,  // output value at 0 (default all zeros)
        private ?array $c1 = null,  // output value at 1 (default all ones)
    ) {
        parent::__construct($domain, $range);
    }

    public function getFunctionType(): int { return 2; }

    public function toPdfObject(): PdfObject
    {
        $dict = $this->buildBaseDict();
        $dict->set('N', new PdfReal($this->n));
        if ($this->c0 !== null) {
            $c0 = new PdfArray(); foreach ($this->c0 as $v) { $c0->add(new PdfReal($v)); }
            $dict->set('C0', $c0);
        }
        if ($this->c1 !== null) {
            $c1 = new PdfArray(); foreach ($this->c1 as $v) { $c1->add(new PdfReal($v)); }
            $dict->set('C1', $c1);
        }
        return $dict;
    }
}
