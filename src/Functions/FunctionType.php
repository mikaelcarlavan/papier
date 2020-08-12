<?php

namespace Papier\Functions;

class FunctionType
{
    /**
     * Sampled function
     *
     * @var int
     */
    const SAMPLED = 0;
  
    /**
     * Exponential interpolation function
     *
     * @var int
     */
    const EXPONENTIAL_INTERPOLATION = 2;

    /**
     * Stitching function
     *
     * @var int
     */
    const STITCHING = 4;

    /**
     * PostScript calculator function
     *
     * @var int
     */
    const POSTSCRIPT_CALCULATOR = 4;
}