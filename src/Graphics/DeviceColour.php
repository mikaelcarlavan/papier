<?php

namespace Papier\Graphics;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\DeviceColourSpaceValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait DeviceColour
{
    /**
     * Set colour space for stroking operations.
     *  
     * @param   string   $space
     * @return mixed
     */
    public function setStrokingSpace($space)
    {
        if (!DeviceColourSpaceValidator::isValid($space)) {
            throw new InvalidArgumentException("Space is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s CS', Factory::create('Name', $string)->format());
        return $this->addToContent($state);
    }

    /**
     * Set colour space for nonstroking operations.
     *  
     * @param   string   $space
     * @return mixed
     */
    public function setNonStrokingSpace($space)
    {
        if (!DeviceColourSpaceValidator::isValid($space)) {
            throw new InvalidArgumentException("Space is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s cs', Factory::create('Name', $string)->format());
        return $this->addToContent($state);
    }

    /**
     * Set colour for stroking operations.
     *  
     * @param   mixed   $values
     * @return mixed
     */
    public function setStrokingColor(...$values)
    {
        foreach ($values as $value) {
            if (!NumberValidator::isValid($value)) {
                throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        $state = sprintf('%s SC', implode(' ', $values));
        return $this->addToContent($state);
    }

    /**
     * Set colour for nonstroking operations.
     *  
     * @param   mixed   $values
     * @return mixed
     */
    public function setNonStrokingColor(...$values)
    {
        foreach ($values as $value) {
            if (!NumberValidator::isValid($value)) {
                throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        $state = sprintf('%s sc', implode(' ', $values));
        return $this->addToContent($state);
    }

    /**
     * Set gray colour for stroking operations.
     *  
     * @param   mixed   $colour
     * @return mixed
     */
    public function setStrokingGrayColour($colour)
    {
        if (!NumberValidator::isValid($colour)) {
            throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s G', Factory::create('Number', $colour)->format());
        return $this->addToContent($state);
    }

    /**
     * Set gray colour for stroking operations.
     *  
     * @param   mixed   $colour
     * @return mixed
     */
    public function setNonStrokingGrayColour($colour)
    {
        if (!NumberValidator::isValid($colour)) {
            throw new InvalidArgumentException("Colour is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s g', Factory::create('Number', $colour)->format());
        return $this->addToContent($state);
    }

    /**
     * Set RGB colour for stroking operations.
     *  
     * @param   mixed   $r
     * @param   mixed   $g
     * @param   mixed   $b
     * @return mixed
     */
    public function setStrokingRGBColour($r, $g, $b)
    {
        if (!NumberValidator::isValid($r)) {
            throw new InvalidArgumentException("Red is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($g)) {
            throw new InvalidArgumentException("Green is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($b)) {
            throw new InvalidArgumentException("Blue is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s %s RG', 
            Factory::create('Number', $r)->format(), 
            Factory::create('Number', $g)->format(),
            Factory::create('Number', $b)->format(),
        );        
        
        return $this->addToContent($state);
    }

    /**
     * Set RGB colour for nonstroking operations.
     *  
     * @param   mixed   $r
     * @param   mixed   $g
     * @param   mixed   $b
     * @return mixed
     */
    public function setNonStrokingRGBColour($r, $g, $b)
    {
        if (!NumberValidator::isValid($r)) {
            throw new InvalidArgumentException("Red is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($g)) {
            throw new InvalidArgumentException("Green is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($b)) {
            throw new InvalidArgumentException("Blue is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s %s rg', 
            Factory::create('Number', $r)->format(), 
            Factory::create('Number', $g)->format(),
            Factory::create('Number', $b)->format(),
        );

        return $this->addToContent($state);
    }

    /**
     * Set CMYK colour for stroking operations.
     *  
     * @param   mixed   $c
     * @param   mixed   $m
     * @param   mixed   $y
     * @param   mixed   $k
     * @return mixed
     */
    public function setCMYKColour($c, $m, $y, $k)
    {
        if (!NumberValidator::isValid($c)) {
            throw new InvalidArgumentException("Cyan is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($m)) {
            throw new InvalidArgumentException("Magenta is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y)) {
            throw new InvalidArgumentException("Yellow is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($k)) {
            throw new InvalidArgumentException("Black is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s %s %s K', 
            Factory::create('Number', $c)->format(), 
            Factory::create('Number', $m)->format(),
            Factory::create('Number', $y)->format(),
            Factory::create('Number', $k)->format(),
        );

        return $this->addToContent($state);
    }

    /**
     * Set CMYK colour for nonstroking operations.
     *  
     * @param   mixed   $c
     * @param   mixed   $m
     * @param   mixed   $y
     * @param   mixed   $k
     * @return mixed
     */
    public function setNonStrokingCMYKColour($c, $m, $y, $k)
    {
        if (!NumberValidator::isValid($c)) {
            throw new InvalidArgumentException("Cyan is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($m)) {
            throw new InvalidArgumentException("Magenta is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($y)) {
            throw new InvalidArgumentException("Yellow is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($k)) {
            throw new InvalidArgumentException("Black is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s %s %s k', 
            Factory::create('Number', $c)->format(), 
            Factory::create('Number', $m)->format(),
            Factory::create('Number', $y)->format(),
            Factory::create('Number', $k)->format(),
        );

        return $this->addToContent($state);
    }
}