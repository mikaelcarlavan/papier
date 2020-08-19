<?php

namespace Papier\Graphics;

class RenderingIntent
{
    /**
     * Absolute colorimetric rendering intent (colours shall be represented solely with respect to the light source)
     *
     * @var string
     */
    const ABSOLUTE_COLORIMETRIC = 'AbsoluteColorimetric';
  
    /**
     * Relative colorimetric rendering intent (colours shall be represented with respect to the combination of the light source and the output medium’s white point)
     *
     * @var string
     */
    const RELATIVE_COLORIMETRIC = 'RelativeColorimetric';

    /**
     * Saturation rendering intent (colours shall be represented in a manner that preserves or emphasizes saturation)
     *
     * @var string
     */
    const SATURATION = 'Saturation';

    /**
     * Perceptual rendering intent (colours shall be represented in a manner that provides a pleasing perceptual appearance)
     *
     * @var string
     */
    const PERCEPTUAL = 'Perceptual';
}