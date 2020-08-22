<?php

namespace Papier\Graphics;

class ShadingType
{  
    /**
     * Function-based shading
     *
     * @var int
     */
    const FUNCTION_BASED = 1;

    /**
     * Axial shading
     *
     * @var int
     */
    const AXIAL = 2;

    /**
     * Radial shading
     *
     * @var int
     */
    const RADIAL = 3;

    /**
     * Free-form Gouraud-shaded triangle mesh
     *
     * @var int
     */
    const FREE_FORM_TRIANGLE_MESH = 4;

    /**
     * Lattice-form Gouraud-shaded triangle mesh
     *
     * @var int
     */
    const LATTICE_FORM_TRIANGLE_MESH = 5;

    /**
     * Coons patch mesh
     *
     * @var int
     */
    const COONS_PATCH_MESH = 6;

    /**
     * Tensor-product patch mesh
     *
     * @var int
     */
    const TENSOR_PRODUCT_PATCH_MESH = 7;
}