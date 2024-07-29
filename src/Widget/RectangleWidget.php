<?php

namespace Papier\Widget;

use Papier\Factory\Factory;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

class RectangleWidget extends BaseWidget
{
    use ColorWidget;

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
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setWidth(float $width): RectangleWidget
    {
        if (!NumberValidator::isValid($width, 0.0)) {
            throw new InvalidArgumentException("Width is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->width = $width;
        return $this;
    }

    /**
     * Set widget's height.
     *
     * @param  float  $height
     * @return RectangleWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setHeight(float $height): RectangleWidget
    {
        if (!NumberValidator::isValid($height, 0.0)) {
            throw new InvalidArgumentException("Height is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

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

    function format(): BaseWidget
    {
        $contents = $this->getContents();
        $contents->save();

        $contents->setLineWidth(1);

        $strokingColors = $this->getStrokingColor();
        $nonStrokingColors = $this->getNonStrokingColor();

        if ($strokingColors) {
            $contents->setStrokingSpace($this->getStrokingColorSpace());
            $contents->setStrokingColor(...$strokingColors);
        }
        if ($nonStrokingColors) {
            $contents->setNonStrokingSpace($this->getNonStrokingColorSpace());
            $contents->setNonStrokingColor(...$nonStrokingColors);
        }
        $contents->beginPath($this->getX(), $this->getY());
        $contents->appendRectangle($this->getX(), $this->getY(), $this->getWidth(), $this->getHeight());
        $contents->closeFillAndStroke();

        $contents->restore();
        
        return $this;
    }
}