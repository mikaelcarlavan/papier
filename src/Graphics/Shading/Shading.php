<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfReal};

/**
 * Abstract shading dictionary (ISO 32000-1 §8.7.4).
 *
 * Shadings define how to smoothly transition between colours across a region.
 * ShadingType values:
 *   1 = Function-based
 *   2 = Axial (linear gradient)
 *   3 = Radial
 *   4 = Free-form Gouraud-shaded triangle mesh
 *   5 = Lattice-form Gouraud-shaded triangle mesh
 *   6 = Coons patch mesh
 *   7 = Tensor-product patch mesh
 */
abstract class Shading
{
    protected PdfDictionary $dictionary;

    public function __construct(int $shadingType, string $colorSpace)
    {
        $this->dictionary = new PdfDictionary();
        $this->dictionary->set('ShadingType', new PdfInteger($shadingType));
        $this->dictionary->set('ColorSpace', new PdfName($colorSpace));
    }

    public function setBackground(array $components): static
    {
        $arr = new PdfArray();
        foreach ($components as $c) {
            $arr->add(new PdfReal($c));
        }
        $this->dictionary->set('Background', $arr);
        return $this;
    }

    public function setBBox(float $x1, float $y1, float $x2, float $y2): static
    {
        $arr = new PdfArray();
        $arr->add(new PdfReal($x1));
        $arr->add(new PdfReal($y1));
        $arr->add(new PdfReal($x2));
        $arr->add(new PdfReal($y2));
        $this->dictionary->set('BBox', $arr);
        return $this;
    }

    public function setAntiAlias(bool $aa): static
    {
        $this->dictionary->set('AntiAlias', new PdfBoolean($aa));
        return $this;
    }

    abstract public function toDictionary(): PdfDictionary;
}
