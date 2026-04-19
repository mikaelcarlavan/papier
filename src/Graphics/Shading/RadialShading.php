<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfObject, PdfReal};

/**
 * Radial shading (ISO 32000-1 §8.7.4.5.4 Type 3).
 *
 * Defines a colour transition between two circles (one may have radius 0).
 */
final class RadialShading extends Shading
{
    private ?PdfObject $function = null;
    private bool       $extend0  = false;
    private bool       $extend1  = false;

    public function __construct(
        string $colorSpace,
        private readonly float $x0,
        private readonly float $y0,
        private readonly float $r0,
        private readonly float $x1,
        private readonly float $y1,
        private readonly float $r1,
    ) {
        parent::__construct(3, $colorSpace);
    }

    public function setFunction(PdfObject $function): static { $this->function = $function; return $this; }
    public function setExtend(bool $e0, bool $e1): static { $this->extend0 = $e0; $this->extend1 = $e1; return $this; }

    public function toDictionary(): PdfDictionary
    {
        $coords = new PdfArray();
        foreach ([$this->x0, $this->y0, $this->r0, $this->x1, $this->y1, $this->r1] as $v) {
            $coords->add(new PdfReal($v));
        }
        $this->dictionary->set('Coords', $coords);

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
