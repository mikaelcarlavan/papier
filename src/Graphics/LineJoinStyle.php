<?php

namespace Papier\Graphics;

class LineJoinStyle
{
    /**
     * Miter join (outer edges of the strokes for the two segments are extended until they meet at an angle)
     *
     * @var int
     */
    const MITER_JOIN = 0;
  
    /**
     * Round join (an arc of circle with a diameter equal to the line width is drawn around the point where the two segments meet)
     *
     * @var int
     */
    const ROUND_JOIN = 1;

    /**
     * Bevel join (two segments are finished with butt caps)
     *
     * @var int
     */
    const BEVEL_JOIN = 1;
}