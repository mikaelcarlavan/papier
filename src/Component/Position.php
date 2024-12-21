<?php

namespace Papier\Component;

use Papier\Validator\NumberValidator;
use InvalidArgumentException;

trait Position
{
    /**
     * The horizontal position of the component
     *
     * @var float
     */
    protected float $x = 0;

    /**
     * The vertical position of the component
     *
     * @var float
     */
    protected float $y = 0;

    /**
     * Set component's horizontal position.
     *
     * @param  float  $x
     * @return static
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setX(float $x): static
    {
        if (!NumberValidator::isValid($x, 0.0)) {
            throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->x = $x;
        return $this;
    }

    /**
     * Set component's vertical position.
     *
     * @param  float  $y
     * @return static
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setY(float $y): static
    {
        if (!NumberValidator::isValid($y, 0.0)) {
            throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->y = $y;
        return $this;
    }


    /**
     * Set component's position.
     *
     * @param  float  $x
     * @param  float  $y
     * @return static
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int' and positive.
     */
    public function setXY(float $x, float $y): static
    {
        return $this->setX($x)->setY($y);
    }

    /**
     * Get component's horizontal position.
     *
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * Get component's vertical position.
     *
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }
}