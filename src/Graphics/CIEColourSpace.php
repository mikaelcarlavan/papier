<?php

namespace Papier\Graphics;

class CIEColourSpace
{
    /**
     * Gray colour space
     *
     * @var string
     */
    const GRAY = 'CalGray';
  
    /**
     * RGB colour space
     *
     * @var string
     */
    const RGB = 'CalRGB';

    /**
     * Lab colour space
     *
     * @var string
     */
    const LAB = 'Lab';

    /**
     * ICC-based colour space
     *
     * @var string
     */
    const ICC_BASED = 'ICCBased';
}