<?php

namespace Papier\Widget;

use Papier\Factory\Factory;
use Papier\Graphics\LineCapStyle;
use Papier\Papier;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

class RectangleWidget extends BaseWidget
{
    use Color;
    use LineWidth;
    use Position;

    /**
     * The width of the widget
     *
     * @var float
     */
    protected float $width = 0;

    /**
     * The height of the widget
     *
     * @var float
     */
    protected float $height = 0;

    /**
     * Set widget's width.
     *
     * @param  float  $width
     * @return RectangleWidget
     */
    public function setWidth(float $width): RectangleWidget
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Set widget's height.
     *
     * @param  float  $height
     * @return RectangleWidget
     */
    public function setHeight(float $height): RectangleWidget
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get widget's width.
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Get widget's height.
     *
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    function format(): RectangleWidget
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