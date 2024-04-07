<?php

namespace Papier\Widget;


use Papier\Object\BaseObject;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;
use Papier\Papier;

abstract class BaseWidget
{
    /**
     * The parent of the widget
     *
     * @var Papier
     */
    protected Papier $pdf;

    /**
     * The horizontal position of the widget
     *
     * @var float
     */
    protected float $x = 0;

    /**
     * The vertical position of the widget
     *
     * @var float
     */
    protected float $y = 0;

    /**
     * Create a new BaseWidget instance.
     *
     * @return void
     */
    public function __construct(Papier $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * Set widget's horizontal position.
     *
     * @param  float  $x
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    protected function setX(float $x): BaseWidget
    {
        if (!NumberValidator::isValid($x, 0.0)) {
            throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->x = $x;
        return $this;
    }

    /**
     * Set widget's vertical position.
     *
     * @param  float  $y
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    protected function setY(float $y): BaseWidget
    {
        if (!NumberValidator::isValid($y, 0.0)) {
            throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->y = $y;
        return $this;
    }


    /**
     * Set widget's position.
     *
     * @param  float  $x
     * @param  float  $y
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int' and positive.
     */
    protected function setXY(float $x, float $y): BaseWidget
    {
        return $this->setX($x)->setY($y);
    }

}