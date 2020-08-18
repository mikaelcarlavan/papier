<?php

namespace Papier\Graphics;

class LineCapStyle
{
    /**
     * Butt cap (stroke squared off at the endpoint of the path)
     *
     * @var int
     */
    const BUTT_CAP = 0;
  
    /**
     * Round cap (a semicircular arc with a diameter equal to the line width)
     *
     * @var int
     */
    const ROUND_CAP = 1;

    /**
     * Projecting square cap (stroke continue beyond the endpoint of the path for a distance equal to the half line width and is squared off)
     *
     * @var int
     */
    const PROJECTING_SQUARE_CAP = 1;
}