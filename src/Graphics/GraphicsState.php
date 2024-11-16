<?php

namespace Papier\Graphics;

use Papier\Validator\NumberValidator;
use Papier\Validator\LineCapStyleValidator;
use Papier\Validator\LineJoinStyleValidator;
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
        $components = [
            'A' => $a,
            'B' => $b,
            'C' => $c,
            'D' => $d,
            'E' => $e,
            'F' => $f,
        ];

        $this->checkCTMComponents($components);

        $state = sprintf('%s %s %s %s %s %s cm', 
            Factory::create('Papier\Type\NumberType', $a)->format(), 
            Factory::create('Papier\Type\NumberType', $b)->format(), 
            Factory::create('Papier\Type\NumberType', $c)->format(), 
            Factory::create('Papier\Type\NumberType', $d)->format(), 
            Factory::create('Papier\Type\NumberType', $e)->format(),  
            Factory::create('Papier\Type\NumberType', $f)->format()
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
        
        $state = sprintf('%s w', Factory::create('Papier\Type\NumberType', $lw)->format());
        return $this->addToContent($state);
    }

    /**
     * Set line cap style.
     *  
     * @param  int  $lc
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not a valid line cap style.
     */
    public function setLineCapStyle(int $lc)
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

        $state = sprintf('%s M', Factory::create('Papier\Type\NumberType', $ml)->format());
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

        $state = sprintf('%s %s d', Factory::create('Papier\Type\IntegersArrayType', $da)->format(), Factory::create('Papier\Type\NumberType', $dp)->format());
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

        $state = sprintf('%s i', Factory::create('Papier\Type\NumberType', $fl)->format());
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

        $state = sprintf('%s ri', Factory::create('Papier\Type\NameType', $ri)->format());
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
        $state = sprintf('%s gs', $dn);
        return $this->addToContent($state);
    }

    /**
     * Check CTM components.
     *
     * @param array $components
     * @return bool
     * @throws InvalidArgumentException if one of the provided argument is not 'float' or 'int'.
     */
    private function checkCTMComponents(array $components): bool
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