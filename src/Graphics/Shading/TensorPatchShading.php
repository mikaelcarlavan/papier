<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

/**
 * Tensor-product patch mesh (ISO 32000-1 §8.7.4.5.8 Type 7).
 *
 * Like a Coons patch but with sixteen control points (the four interior points
 * are given explicitly), allowing finer control of the surface.
 */
final class TensorPatchShading extends CoonsPatchShading
{
    protected const POINTS_PER_PATCH = 16;

    public function __construct(string $colorSpace, ?int $components = null)
    {
        // Bypass CoonsPatchShading's fixed type-6 constructor.
        MeshShading::__construct(7, $colorSpace, $components);
    }
}
