<?php

namespace Papier\Graphics;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;

use InvalidArgumentException;

trait PathConstruction
{
    /**
     * Begin a new subpath.
     *  
     * @param   mixed   $x
     * @param   mixed   $y
     * @return mixed
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
            Factory::create('Number', $x)->format(),
            Factory::create('Number', $y)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Append a straight line segment.
     *  
     * @param   mixed   $x
     * @param   mixed   $y
     * @return mixed
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
            Factory::create('Number', $x)->format(),
            Factory::create('Number', $y)->format()
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
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return mixed
     */
    public function appendCubicBezier($x1, $y1, $x2, $y2, $x3, $y3)
    {
        if (!NumberValidator::isValid($x1)) {
            throw new InvalidArgumentException("X1 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y1)) {
            throw new InvalidArgumentException("Y1 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($x2)) {
            throw new InvalidArgumentException("X2 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y2)) {
            throw new InvalidArgumentException("Y2 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($x3)) {
            throw new InvalidArgumentException("X3 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y3)) {
            throw new InvalidArgumentException("Y3 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %s %s %s %s %s c', 
            Factory::create('Number', $x1)->format(), 
            Factory::create('Number', $y1)->format(),
            Factory::create('Number', $x2)->format(),
            Factory::create('Number', $y2)->format(),
            Factory::create('Number', $x3)->format(),
            Factory::create('Number', $y3)->format()
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
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return mixed
     */
    public function appendCubicBezier2a($x2, $y2, $x3, $y3)
    {
        if (!NumberValidator::isValid($x2)) {
            throw new InvalidArgumentException("X2 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y2)) {
            throw new InvalidArgumentException("Y2 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($x3)) {
            throw new InvalidArgumentException("X3 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y3)) {
            throw new InvalidArgumentException("Y3 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %s %s %s v', 
            Factory::create('Number', $x2)->format(),
            Factory::create('Number', $y2)->format(),
            Factory::create('Number', $x3)->format(),
            Factory::create('Number', $y3)->format()
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
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return mixed
     */
    public function appendCubicBezier2b($x1, $y1, $x3, $y3)
    {
        if (!NumberValidator::isValid($x1)) {
            throw new InvalidArgumentException("X1 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y1)) {
            throw new InvalidArgumentException("Y1 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($x3)) {
            throw new InvalidArgumentException("X3 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y3)) {
            throw new InvalidArgumentException("Y3 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %s %s %s y', 
            Factory::create('Number', $x1)->format(), 
            Factory::create('Number', $y1)->format(), 
            Factory::create('Number', $x3)->format(), 
            Factory::create('Number', $y3)->format(), 
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
            Factory::create('Number', $x)->format(), 
            Factory::create('Number', $y)->format(), 
            Factory::create('Number', $width)->format(), 
            Factory::create('Number', $height)->format(), 
        );

        return $this->addToContent($state);
    }
}