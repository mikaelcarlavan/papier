<?php

declare(strict_types=1);

namespace Papier\Graphics\Pattern;

use Papier\Graphics\Shading\Shading;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfReal, PdfStream};

/**
 * Shading pattern (ISO 32000-1 §8.7.4.3, PatternType 2).
 *
 * Wraps any shading (axial, radial, function-based, or a mesh) so it can be used
 * as a fill/stroke colour via the Pattern colour space — letting you paint
 * arbitrary shapes and text with a gradient, not just a clipped region.
 *
 * Usage:
 *
 *   $pat = new ShadingPattern($axial->toDictionary());   // or $mesh->toStream()
 *   $page->getResources()->addPattern('P1', $pat->getDictionary());
 *   $cs->setFillColorSpace('Pattern')->setFillColorN('P1')->drawRect(...)->fill();
 */
final class ShadingPattern
{
    private PdfDictionary $dict;

    /**
     * @param Shading|PdfObject $shading  A Shading instance, or its dictionary
     *                                    (axial/radial) or stream (mesh).
     * @param float[]|null      $matrix   Optional pattern matrix [a b c d e f].
     */
    public function __construct(Shading|PdfObject $shading, ?array $matrix = null)
    {
        $shadingObj = $shading instanceof Shading
            ? ($shading instanceof \Papier\Graphics\Shading\MeshShading ? $shading->toStream() : $shading->toDictionary())
            : $shading;

        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Pattern'));
        $this->dict->set('PatternType', new PdfInteger(2));
        $this->dict->set('Shading', $shadingObj);

        if ($matrix !== null) {
            $this->setMatrix(...$matrix);
        }
    }

    /** Set the pattern matrix mapping pattern space to the default page space. */
    public function setMatrix(float $a, float $b, float $c, float $d, float $e, float $f): static
    {
        $m = new PdfArray();
        foreach ([$a, $b, $c, $d, $e, $f] as $v) {
            $m->add(new PdfReal($v));
        }
        $this->dict->set('Matrix', $m);
        return $this;
    }

    public function getDictionary(): PdfDictionary
    {
        return $this->dict;
    }
}
