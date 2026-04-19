<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfObject, PdfReal};

/**
 * Axial (linear gradient) shading (ISO 32000-1 §8.7.4.5.3 Type 2).
 *
 * Defines a colour transition along a line between two points.
 */
final class AxialShading extends Shading
{
    private ?PdfObject $function    = null;
    private bool       $extend0     = false;
    private bool       $extend1     = false;
    private ?array     $domain      = null;  // [t0, t1]

    public function __construct(
        string $colorSpace,
        private readonly float $x0,
        private readonly float $y0,
        private readonly float $x1,
        private readonly float $y1,
    ) {
        parent::__construct(2, $colorSpace);
    }

    public function setFunction(PdfObject $function): static
    {
        $this->function = $function;
        return $this;
    }

    public function setExtend(bool $extend0, bool $extend1): static
    {
        $this->extend0 = $extend0;
        $this->extend1 = $extend1;
        return $this;
    }

    public function setDomain(float $t0, float $t1): static
    {
        $this->domain = [$t0, $t1];
        return $this;
    }

    public function toDictionary(): PdfDictionary
    {
        $coords = new PdfArray();
        $coords->add(new PdfReal($this->x0));
        $coords->add(new PdfReal($this->y0));
        $coords->add(new PdfReal($this->x1));
        $coords->add(new PdfReal($this->y1));
        $this->dictionary->set('Coords', $coords);

        if ($this->domain !== null) {
            $dom = new PdfArray();
            $dom->add(new PdfReal($this->domain[0]));
            $dom->add(new PdfReal($this->domain[1]));
            $this->dictionary->set('Domain', $dom);
        }

        if ($this->function !== null) {
            $this->dictionary->set('Function', $this->function);
        }

        $extend = new PdfArray();
        $extend->add(new PdfBoolean($this->extend0));
        $extend->add(new PdfBoolean($this->extend1));
        $this->dictionary->set('Extend', $extend);

        return $this->dictionary;
    }
}
