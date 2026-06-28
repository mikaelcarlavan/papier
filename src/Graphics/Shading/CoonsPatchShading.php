<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfDictionary, PdfInteger};

/**
 * Coons patch mesh (ISO 32000-1 §8.7.4.5.7 Type 6) and the base for the
 * tensor-product patch mesh (Type 7).
 *
 * Each patch is bounded by four cubic Bézier curves.  This implementation emits
 * flag-0 patches: every patch provides all of its control points (12 for Coons,
 * 16 for tensor) and all four corner colours.
 */
class CoonsPatchShading extends MeshShading
{
    protected const POINTS_PER_PATCH = 12;

    /** @var array<int, array{points:array, colors:array}> */
    protected array $patches = [];

    public function __construct(string $colorSpace, ?int $components = null)
    {
        parent::__construct(6, $colorSpace, $components);
    }

    /**
     * Add a full patch.
     *
     * @param array $points  Control points as [[x,y], …]: 12 for Coons, 16 for tensor.
     * @param array $colors  Four corner colours, each an array of components in [0, 1].
     */
    public function addPatch(array $points, array $colors): static
    {
        if (count($points) !== static::POINTS_PER_PATCH) {
            throw new \InvalidArgumentException(
                static::POINTS_PER_PATCH . ' control points required, got ' . count($points) . '.'
            );
        }
        if (count($colors) !== 4) {
            throw new \InvalidArgumentException('Exactly 4 corner colours are required.');
        }
        $this->patches[] = ['points' => $points, 'colors' => $colors];
        foreach ($points as [$x, $y]) {
            $this->allX[] = $x;
            $this->allY[] = $y;
        }
        return $this;
    }

    protected function decorateDictionary(PdfDictionary $dict): void
    {
        $dict->set('BitsPerFlag', new PdfInteger(8));
    }

    protected function encodeData(): string
    {
        [$xmin, $xmax, $ymin, $ymax] = $this->bounds();
        $data = '';
        foreach ($this->patches as $patch) {
            $data .= chr(0); // flag 0: new patch with all control points + 4 colours
            foreach ($patch['points'] as [$x, $y]) {
                $data .= $this->packCoord($x, $xmin, $xmax);
                $data .= $this->packCoord($y, $ymin, $ymax);
            }
            foreach ($patch['colors'] as $color) {
                $data .= $this->packColor($color);
            }
        }
        return $data;
    }
}
