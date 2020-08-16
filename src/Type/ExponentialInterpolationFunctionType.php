<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;
use Papier\Object\RealObject;
use Papier\Object\ArrayObject;

use Papier\Functions\FunctionType;

use Papier\Validator\RealValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class ExponentialInterpolationFunctionType extends FunctionObject
{
    
    /**
     * Set C0 (function result when x = 0.0).
     *  
     * @param  \Papier\Object\ArrayObject  $c0
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\ExponentialInterpolationFunctionType
     */
    public function setC0($c0)
    {
        if (!$c0 instanceof ArrayObject) {
            throw new InvalidArgumentException("C0 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('C0', $c0);
        return $this;
    } 

    /**
     * Set C1 (function result when x = 1.0).
     *  
     * @param  \Papier\Object\ArrayObject  $c1
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\ExponentialInterpolationFunctionType
     */
    public function setC1($c1)
    {
        if (!$c1 instanceof ArrayObject) {
            throw new InvalidArgumentException("C1 is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('C1', $c1);
        return $this;
    } 

    /**
     * Set interpolation exponent.
     *  
     * @param  float  $N
     * @throws InvalidArgumentException if the provided argument is not of type 'float'.
     * @return \Papier\Type\ExponentialInterpolationFunctionType
     */
    public function setN($N)
    {
        if (!RealValidator::isValid($N)) {
            throw new InvalidArgumentException("N is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::getInstance()->createObject('Real', $N, false);

        $this->setEntry('N', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $this->setFunctionType(FunctionType::EXPONENTIAL_INTERPOLATION);

        if (!$this->hasEntry('N')) {
            throw new RuntimeException("N is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}