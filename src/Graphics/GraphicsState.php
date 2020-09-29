<?php

namespace Papier\Graphics;

use Papier\Validator\NumberValidator;
use Papier\Validator\LineCapStyleValidator;
use Papier\Validator\LineJoinStyleValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\RenderingIntentValidator;
use Papier\Validator\IntegersArrayValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait GraphicsState
{
    /**
     * Save stack.
     *  
     * @return mixed
     */
    public function save()
    {
        $state = 'q';
        return $this->addToContent($state);
    }

    /**
     * Restore stack.
     *  
     * @return mixed
     */
    public function restore()
    {
        $state = 'Q';
        return $this->addToContent($state);
    }

    /**
     * Set current transformation matrix.
     *  
     * @param   mixed   $a
     * @param   mixed   $b
     * @param   mixed   $c
     * @param   mixed   $d
     * @param   mixed   $e
     * @param   mixed   $f
     * @return mixed
     * @throws InvalidArgumentException if one of the provided arguments are not of type 'float' or 'int'.
     */
    public function setCTM($a, $b , $c, $d, $e, $f)
    {
        if (!NumberValidator::isValid($a)) {
            throw new InvalidArgumentException("A is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($b)) {
            throw new InvalidArgumentException("B is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($c)) {
            throw new InvalidArgumentException("C is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($d)) {
            throw new InvalidArgumentException("D is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($e)) {
            throw new InvalidArgumentException("E is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($f)) {
            throw new InvalidArgumentException("F is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %s %s %s %s %s cm', 
            Factory::create('Number', $a)->format(), 
            Factory::create('Number', $b)->format(), 
            Factory::create('Number', $c)->format(), 
            Factory::create('Number', $d)->format(), 
            Factory::create('Number', $e)->format(),  
            Factory::create('Number', $f)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Set line width.
     *  
     * @param   mixed   $lw
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setLineWidth($lw)
    {
        if (!NumberValidator::isValid($lw)) {
            throw new InvalidArgumentException("LW is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s w', Factory::create('Number', $lw)->format());
        return $this->addToContent($state);
    }

    /**
     * Set line cap style.
     *  
     * @param  mixed  $lc
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not a valid line cap style.
     */
    public function setLineCapStyle($lc)
    {
        if (!LineCapStyleValidator::isValid($lc)) {
            throw new InvalidArgumentException("LC is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%d J', $lc);
        return $this->addToContent($state);
    }


    /**
     * Set line join style.
     *  
     * @param  mixed  $lj
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not a valid line join style.
     */
    public function setLineJoinStyle($lj)
    {
        if (!LineJoinStyleValidator::isValid($lj)) {
            throw new InvalidArgumentException("LJ is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%d j', $lj);
        return $this->addToContent($state);
    }

    /**
     * Set miter limit.
     *  
     * @param  mixed  $ml
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setMiterLimit($ml)
    {
        if (!NumberValidator::isValid($ml)) {
            throw new InvalidArgumentException("ML is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s M', Factory::create('Number', $ml)->format());
        return $this->addToContent($state);
    }

    /**
     * Set line dash pattern.
     *
     * @param array $da
     * @param mixed $dp
     * @return mixed
     * @throws InvalidArgumentException if the $da argument is not an array of 'int'.
     * @throws InvalidArgumentException if the $dp argument is not of type 'float' or 'int'.
     */
    public function setLineDashPattern(array $da, $dp)
    {
        if (!IntegersArrayValidator::isValid($da)) {
            throw new InvalidArgumentException("DA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($dp)) {
            throw new InvalidArgumentException("DP is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s d', Factory::create('IntegersArray', $da)->format(), Factory::create('Number', $dp)->format());
        return $this->addToContent($state);
    }

    /**
     * Set flatness tolerance.
     *  
     * @param  mixed  $fl
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' between 0 and 100.
     */
    public function setFlatness($fl)
    {
        if (!NumberValidator::isValid($fl, 0.0, 100.0)) {
            throw new InvalidArgumentException("FL is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s i', Factory::create('Number', $fl)->format());
        return $this->addToContent($state);
    } 
    
    /**
     * Set colour rendering intent.
     *  
     * @param  string  $ri
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not a valid colour rendering intent.
     */
    public function setColourRenderingIntent(string $ri)
    {
        if (!RenderingIntentValidator::isValid($ri)) {
            throw new InvalidArgumentException("RI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s ri', Factory::create('Name', $ri)->format());
        return $this->addToContent($state);
    } 

    /**
     * Set dictionary.
     *  
     * @param  string  $dn
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setDictionary(string $dn)
    {
        if (!StringValidator::isValid($dn)) {
            throw new InvalidArgumentException("DN is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s gs', $dn);
        return $this->addToContent($state);
    } 
}