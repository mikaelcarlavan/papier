<?php

namespace Papier\Graphics;

use Papier\Validator\NumberValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait PathConstruction
{
    /**
     * Begin a new subpath.
     *  
     * @param   mixed   $x
     * @param   mixed   $y
     * @return mixed
     * @throws InvalidArgumentException if one of the provided argument is not of type 'float' or 'int'.
     */
    public function beginPath($x, $y)
    {
        if (!NumberValidator::isValid($x)) {
            throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y)) {
            throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s m', 
            Factory::create('Papier\Type\NumberType', $x)->format(),
            Factory::create('Papier\Type\NumberType', $y)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Append a straight line segment.
     *  
     * @param   mixed   $x
     * @param   mixed   $y
     * @return mixed
     * @throws InvalidArgumentException if one of the provided argument is not of type 'float' or 'int'.
     */
    public function appendSegment($x, $y)
    {
        if (!NumberValidator::isValid($x)) {
            throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y)) {
            throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s l', 
            Factory::create('Papier\Type\NumberType', $x)->format(),
            Factory::create('Papier\Type\NumberType', $y)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Append a cubic Bézier curve.
     *  
     * @param   mixed   $x1
     * @param   mixed   $y1
     * @param   mixed   $x2
     * @param   mixed   $y2
     * @param   mixed   $x3
     * @param   mixed   $y3
     * @return mixed
     * @throws InvalidArgumentException if one of the provided argument is not of type 'float' or 'int'.
     */
    public function appendCubicBezier($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $components = [
            'X1' => $x1,
            'Y1' => $y1,
            'X2' => $x2,
            'Y2' => $y2,
            'X3' => $x3,
            'Y3' => $y3,
        ];

        $this->checkBezierComponents($components);

        $state = sprintf('%s %s %s %s %s %s c', 
            Factory::create('Papier\Type\NumberType', $x1)->format(), 
            Factory::create('Papier\Type\NumberType', $y1)->format(),
            Factory::create('Papier\Type\NumberType', $x2)->format(),
            Factory::create('Papier\Type\NumberType', $y2)->format(),
            Factory::create('Papier\Type\NumberType', $x3)->format(),
            Factory::create('Papier\Type\NumberType', $y3)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Append a cubic Bézier curve.
     *  
     * @param   mixed   $x2
     * @param   mixed   $y2
     * @param   mixed   $x3
     * @param   mixed   $y3
     * @return mixed
     * @throws InvalidArgumentException if one of the provided argument is not of type 'float' or 'int'.
     */
    public function appendCubicBezier2a($x2, $y2, $x3, $y3)
    {
        $components = [
            'X2' => $x2,
            'Y2' => $y2,
            'X3' => $x3,
            'Y3' => $y3,
        ];

        $this->checkBezierComponents($components);
        
        $state = sprintf('%s %s %s %s v', 
            Factory::create('Papier\Type\NumberType', $x2)->format(),
            Factory::create('Papier\Type\NumberType', $y2)->format(),
            Factory::create('Papier\Type\NumberType', $x3)->format(),
            Factory::create('Papier\Type\NumberType', $y3)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Append a cubic Bézier curve.
     *  
     * @param   mixed   $x1
     * @param   mixed   $y1
     * @param   mixed   $x3
     * @param   mixed   $y3
     * @return mixed
     * @throws InvalidArgumentException if one of the provided argument is not of type 'float' or 'int'.
     */
    public function appendCubicBezier2b($x1, $y1, $x3, $y3)
    {
        $components = [
            'X1' => $x1,
            'Y1' => $y1,
            'X3' => $x3,
            'Y3' => $y3,
        ];

        $this->checkBezierComponents($components);
        
        $state = sprintf('%s %s %s %s y', 
            Factory::create('Papier\Type\NumberType', $x1)->format(), 
            Factory::create('Papier\Type\NumberType', $y1)->format(), 
            Factory::create('Papier\Type\NumberType', $x3)->format(), 
            Factory::create('Papier\Type\NumberType', $y3)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Close current subpath.
     *  
     * @return mixed
     */
    public function closePath()
    {
        $state = 'h';
        return $this->addToContent($state);
    }

    /**
     * Append a rectangle.
     *  
     * @param   mixed   $x
     * @param   mixed   $y
     * @param   mixed   $width
     * @param   mixed   $height
     * @return mixed
     * @throws InvalidArgumentException if one of the provided argument is not of type 'float' or 'int'.
     */
    public function appendRectangle($x, $y, $width, $height)
    {
        if (!NumberValidator::isValid($x)) {
            throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y)) {
            throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($width)) {
            throw new InvalidArgumentException("Width is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($height)) {
            throw new InvalidArgumentException("Height is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s %s %s re', 
            Factory::create('Papier\Type\NumberType', $x)->format(), 
            Factory::create('Papier\Type\NumberType', $y)->format(), 
            Factory::create('Papier\Type\NumberType', $width)->format(), 
            Factory::create('Papier\Type\NumberType', $height)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Check Bézier components.
     *
     * @param array $components
     * @return bool
     * @throws InvalidArgumentException if one of the provided argument is not 'float' or 'int'.
     */
    private function checkBezierComponents(array $components): bool
    {
        if (count($components) > 0) {
            foreach ($components as $key => $component) {
                if (!NumberValidator::isValid($component)) {
                    throw new InvalidArgumentException("$key is incorrect. See ".__CLASS__." class's documentation for possible values.");
                }
            }
        }

        return true;
    }
}