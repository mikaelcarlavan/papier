<?php

namespace Papier\Text;

class RenderingMode
{
    /**
     * Fill text
     *
     * @var int
     */
    const FILL = 0;
  
    /**
     * Stroke text
     *
     * @var int
     */
    const STROKE = 1;

    /**
     * Fill, then stroke text
     *
     * @var int
     */
    const FILL_THEN_STROKE = 2;

    /**
     * Neither fill nor stroke (invisible)
     *
     * @var int
     */
    const NEITHER_FILL_NOR_STROKE = 3;

    /**
     * Fill text and add to path for clipping
     *
     * @var int
     */
    const FILL_AND_ADD_TO_PATH = 4;

    /**
     * Stroke text and add to path for clipping
     *
     * @var int
     */
    const STROKE_AND_ADD_TO_PATH = 5;

    /**
     * Fill, then stroke text and add to path for clipping
     *
     * @var int
     */
    const FILL_THEN_STROKE_AND_ADD_TO_PATH = 6;

    /**
     * Add to path for clipping
     *
     * @var int
     */
    const ADD_TO_PATH = 7;
}