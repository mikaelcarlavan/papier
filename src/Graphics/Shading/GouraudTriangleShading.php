<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfDictionary, PdfInteger};

/**
 * Free-form Gouraud-shaded triangle mesh (ISO 32000-1 §8.7.4.5.5 Type 4).
 *
 * Each vertex carries an edge flag, a position, and a colour; triangles are
 * built from consecutive vertices according to the flags.  The convenience
 * {@see addTriangle()} emits three flag-0 vertices, i.e. independent triangles.
 */
final class GouraudTriangleShading extends MeshShading
{
    /** @var array<int, array{flag:int, x:float, y:float, color:array}> */
    private array $vertices = [];

    public function __construct(string $colorSpace, ?int $components = null)
    {
        parent::__construct(4, $colorSpace, $components);
    }

    /**
     * Add a single mesh vertex.
     *
     * @param int   $flag   0 = start a new triangle; 1/2 = reuse vertices of the previous triangle.
     * @param array $color  Colour components in [0, 1].
     */
    public function addVertex(int $flag, float $x, float $y, array $color): static
    {
        $this->vertices[] = ['flag' => $flag, 'x' => $x, 'y' => $y, 'color' => $color];
        $this->allX[] = $x;
        $this->allY[] = $y;
        return $this;
    }

    /**
     * Add an independent triangle from three (point, colour) pairs.
     *
     * @param array $p0 [x, y]   @param array $c0 colour
     * @param array $p1 [x, y]   @param array $c1 colour
     * @param array $p2 [x, y]   @param array $c2 colour
     */
    public function addTriangle(array $p0, array $c0, array $p1, array $c1, array $p2, array $c2): static
    {
        return $this->addVertex(0, $p0[0], $p0[1], $c0)
                    ->addVertex(0, $p1[0], $p1[1], $c1)
                    ->addVertex(0, $p2[0], $p2[1], $c2);
    }

    protected function decorateDictionary(PdfDictionary $dict): void
    {
        $dict->set('BitsPerFlag', new PdfInteger(8));
    }

    protected function encodeData(): string
    {
        [$xmin, $xmax, $ymin, $ymax] = $this->bounds();
        $data = '';
        foreach ($this->vertices as $v) {
            $data .= chr($v['flag'] & 0xFF);
            $data .= $this->packCoord($v['x'], $xmin, $xmax);
            $data .= $this->packCoord($v['y'], $ymin, $ymax);
            $data .= $this->packColor($v['color']);
        }
        return $data;
    }
}
