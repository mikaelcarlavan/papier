<?php

namespace Papier\Graphics;

class TilingType
{  
    /**
     * Constant spacing (pattern cells are spaced consistently)
     *
     * @var int
     */
    const CONSTANT_SPACING = 1;

    /**
     * No distortion (the pattern cell is be distorted)
     *
     * @var int
     */
    const NO_DISTORTION = 2;

    /**
     * Constant spacing and faster tiling (pattern cells are spaced consistently with additional distortion permitted to enable a more efficient implementation)
     *
     * @var int
     */
    const CONSTANT_SPACING_AND_FASTER_TILING = 3;
}