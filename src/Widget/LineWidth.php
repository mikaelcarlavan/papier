<?php

namespace Papier\Widget;

trait LineWidth
{
    /**
     * Line width.
     *
     * @var float
     */
    protected float $lineWidth = 0;

    /**
     * Set line's width.
     *
     * @param  float  $lineWidth
     * @return RectangleWidget
     */
    public function setLineWidth(float $lineWidth): RectangleWidget
    {
        $this->lineWidth = $lineWidth;
        return $this;
    }

    /**
     * Get line's cap width.
     *
     * @return float
     */
    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }
}