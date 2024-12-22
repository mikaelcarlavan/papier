<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Papier;

class RectangleComponent extends BaseComponent
{
    use Color;
    use LineWidth;
    use Position;

    /**
     * The width of the component
     *
     * @var float
     */
    protected float $width = 0;

    /**
     * The height of the component
     *
     * @var float
     */
    protected float $height = 0;

    /**
     * Set component's width.
     *
     * @param  float  $width
     * @return RectangleComponent
     */
    public function setWidth(float $width): RectangleComponent
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set component's height.
     *
     * @param  float  $height
     * @return RectangleComponent
     */
    public function setHeight(float $height): RectangleComponent
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get component's width.
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Get component's height.
     *
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    function format(): RectangleComponent
    {
        $contents = $this->getContents();
        $contents->save();

        $this->applyColors($contents);

        $x = $this->getX();
        $y = $this->getY();
        $width = $this->getWidth();
        $height = $this->getHeight();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        $lineWidth = $this->getLineWidth();

        $contents->setLineWidth($mmToUserUnit * $lineWidth);
        
        $contents->appendRectangle($mmToUserUnit * $x, $mmToUserUnit * $y, $mmToUserUnit * $width, $mmToUserUnit * $height);

        $strokingColors = $this->getStrokingColor();
        $nonStrokingColors = $this->getNonStrokingColor();

        if ($strokingColors && $nonStrokingColors) {
            $contents->fillAndStroke();
        } else if ($strokingColors) {
            $contents->stroke();
        } else {
            $contents->fill();
        }

        $contents->restore();
        
        return $this;
    }
}