<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Functions\FunctionType as FunType;
use Papier\Object\ArrayObject;
use Papier\Type\Base\FunctionType;
use RuntimeException;

class ExponentialInterpolationFunctionType extends FunctionType
{
    
    /**
     * Set C0 (function result when x = 0.0).
     *  
     * @param  ArrayObject  $c0
     * @return ExponentialInterpolationFunctionType
     */
    public function setC0(ArrayObject $c0): ExponentialInterpolationFunctionType
    {
        $this->setEntry('C0', $c0);
        return $this;
    } 

    /**
     * Set C1 (function result when x = 1.0).
     *  
     * @param  ArrayObject  $c1
     * @return ExponentialInterpolationFunctionType
     */
    public function setC1(ArrayObject $c1): ExponentialInterpolationFunctionType
    {
        $this->setEntry('C1', $c1);
        return $this;
    } 

    /**
     * Set interpolation exponent.
     *  
     * @param  float  $N
     * @return ExponentialInterpolationFunctionType
     */
    public function setN(float $N): ExponentialInterpolationFunctionType
    {
        $value = Factory::create('Papier\Type\Base\RealType', $N);

        $this->setEntry('N', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setFunctionType(FunType::EXPONENTIAL_INTERPOLATION);

        if (!$this->hasEntry('N')) {
            throw new RuntimeException("N is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}