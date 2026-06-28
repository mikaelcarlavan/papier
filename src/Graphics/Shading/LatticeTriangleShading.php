<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfDictionary, PdfInteger};

/**
 * Lattice-form Gouraud-shaded triangle mesh (ISO 32000-1 §8.7.4.5.6 Type 5).
 *
 * Vertices are given as a regular grid of `verticesPerRow` columns; the reader
 * builds triangles between consecutive rows.  There are no edge flags.
 */
final class LatticeTriangleShading extends MeshShading
{
    /** @var array<int, array{x:float, y:float, color:array}> */
    private array $vertices = [];
    private int   $verticesPerRow;

    public function __construct(string $colorSpace, int $verticesPerRow, ?int $components = null)
    {
        parent::__construct(5, $colorSpace, $components);
        $this->verticesPerRow = $verticesPerRow;
    }

    /**
     * Append a vertex in row-major order.
     *
     * @param array $color Colour components in [0, 1].
     */
    public function addVertex(float $x, float $y, array $color): static
    {
        $this->vertices[] = ['x' => $x, 'y' => $y, 'color' => $color];
        $this->allX[] = $x;
        $this->allY[] = $y;
        return $this;
    }

    protected function decorateDictionary(PdfDictionary $dict): void
    {
        $dict->set('VerticesPerRow', new PdfInteger($this->verticesPerRow));
    }

    protected function encodeData(): string
    {
        [$xmin, $xmax, $ymin, $ymax] = $this->bounds();
        $data = '';
        foreach ($this->vertices as $v) {
            $data .= $this->packCoord($v['x'], $xmin, $xmax);
            $data .= $this->packCoord($v['y'], $ymin, $ymax);
            $data .= $this->packColor($v['color']);
        }
        return $data;
    }
}
