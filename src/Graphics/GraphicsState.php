<?php

namespace Papier\Graphics;

use Papier\Base\BaseObject;

use Papier\Validator\NumberValidator;
use Papier\Validator\LineCapStyleValidator;
use Papier\Validator\LineJoinStyleValidator;
use Papier\Validator\OverprintModeValidator;

use InvalidArgumentException;

class GraphicsState extends BaseObject
{
    /**
     * Create a new GraphicsState instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = [];
    }  

    /**
     * Add state to stack
     *  
     * @param  string  $state
     * @return \Papier\Graphics\GraphicsState
     */
    protected function addToStack($state)
    {
        $stack = $this->getStack();
        $stack[] = $state;

        return $this->setStack($stack);
    } 
    
    /**
     * Get stack.
     *  
     * @return array
     */
    protected function getStack()
    {
        return $this->getValue();
    }

    /**
     * Set stack.
     *  
     * @param array $stack
     * @return \Papier\Graphics\GraphicsState
     */
    protected function setStack($stack)
    {
        return $this->setValue($stack);
    }

    /**
     * Erase stack.
     *  
     * @return array
     */
    public function clearStack()
    {
        $stack = [];
        return $this->setStack($stack);
    }

    /**
     * Save stack.
     *  
     * @return \Papier\Graphics\GraphicsState
     */
    public function save()
    {
        $state = 'q';
        return $this->addToStack($state);
    }

    /**
     * Restore stack.
     *  
     * @return \Papier\Graphics\GraphicsState
     */
    public function restore()
    {
        $state = 'Q';
        return $this->addToStack($state);
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
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return \Papier\Graphics\GraphicsState
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
        
        $state = sprintf('%f %f %f %f %f cm', $a, $b, $c, $d, $e, $f);

        return $this->addToStack($state);
    }

    /**
     * Set line width.
     *  
     * @param   mixed   $lw
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Graphics\GraphicsState
     */
    public function setLineWidth($lw)
    {
        if (!NumberValidator::isValid($lw)) {
            throw new InvalidArgumentException("LW is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%f w', $lw);
        return $this->addToStack($state);
    }

    /**
     * Set line cap style.
     *  
     * @param  mixed  $lc
     * @throws InvalidArgumentException if the provided argument is not a valid line cap style.
     * @return \Papier\Graphics\GraphicsState
     */
    public function setLineCapStyle($lc)
    {
        if (!LineCapStyleValidator::isValid($lc)) {
            throw new InvalidArgumentException("LC is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%d J', $lc);
        return $this->addToStack($state);
    }


    /**
     * Set line join style.
     *  
     * @param  mixed  $lj
     * @throws InvalidArgumentException if the provided argument is not a valid line join style.
     * @return \Papier\Graphics\GraphicsState
     */
    public function setLineJoinStyle($lj)
    {
        if (!LineJoinStyleValidator::isValid($lj)) {
            throw new InvalidArgumentException("LJ is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%d j', $lj);
        return $this->addToStack($state);
    }

    /**
     * Set miter limit.
     *  
     * @param  mixed  $ml
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Graphics\GraphicsState
     */
    public function setMiterLimit($ml)
    {
        if (!NumberValidator::isValid($ml)) {
            throw new InvalidArgumentException("ML is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%f M', $ml);
        return $this->addToStack($state);
    }

    /**
     * Set line dash pattern.
     *  
     * @param  array  $da
     * @param  mixed  $dp
     * @throws InvalidArgumentException if the $da argument is not an array of 'float' or 'int' elements.
     * @throws InvalidArgumentException if the $dp argument is not of type 'float' or 'int'.
     * @return \Papier\Graphics\GraphicsState
     */
    public function setLineDashPattern($da, $dp)
    {
        if (!ArrayValidator::isValid($da)) {
            throw new InvalidArgumentException("DA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        foreach ($da as $d) {
            if (!NumberValidator::isValid($d)) {
                throw new InvalidArgumentException("DA is incorrect. See ".__CLASS__." class's documentation for possible values.");
            }         
        }

        if (!NumberValidator::isValid($dp)) {
            throw new InvalidArgumentException("DP is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('[%s] %f d', implode(' ', $da), $dp);
        return $this->addToStack($state);
    }

    /**
     * Set flatness tolerance.
     *  
     * @param  mixed  $fl
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' between 0 and 100.
     * @return \Papier\Graphics\GraphicsState
     */
    public function setFlatness($fl)
    {
        if (!NumberValidator::isValid($fl, 0.0, 100.0)) {
            throw new InvalidArgumentException("FL is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%f i', $fl);
        return $this->addToStack($state);
    }
    
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $stack = $this->getStack();

        $value = '';
        foreach ($stack as $state) {
            $value .= $state . self::EOL_MARKER;
        }      

        return trim($value);
    }    
}