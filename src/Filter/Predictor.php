<?php


namespace Papier\Filter;


class Predictor
{
    /**
     * No prediction
     *
     * @var int
     */
    const NONE = 1;

    /**
     * TIFF Predictor 2
     *
     * @var int
     */
    const TIFF_2 = 2;

    /**
     * PNG prediction (on encoding, PNG None on all rows)
     *
     * @var int
     */
    const PNG_NONE = 10;

    /**
     * PNG prediction (on encoding, PNG Sub on all rows)
     *
     * @var int
     */
    const PNG_SUB = 11;

    /**
     * PNG prediction (on encoding, PNG Up on all rows)
     *
     * @var int
     */
    const PNG_UP = 12;

    /**
     * PNG prediction (on encoding, PNG Average on all rows)
     *
     * @var int
     */
    const PNG_AVERAGE = 13;

    /**
     * PNG prediction (on encoding, PNG Paeth on all rows)
     *
     * @var int
     */
    const PNG_PAETH = 14;

    /**
     * PNG prediction (on encoding, PNG optimum)
     *
     * @var int
     */
    const PNG_OPTIMUM = 15;
}