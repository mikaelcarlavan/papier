<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Functions\FunctionType as FuncType;
use Papier\Object\ArrayObject;
use Papier\Object\FunctionObject;
use Papier\Type\Base\FunctionType;
use Papier\Validator\BitsPerSampleValidator;
use Papier\Validator\IntegerValidator;
use RuntimeException;

class SampledFunctionType extends FunctionType
{
    
    /**
     * Set size.
     *  
     * @param ArrayObject $size
     * @return SampledFunctionType
     */
    public function setSize(ArrayObject $size): SampledFunctionType
    {
        $this->setEntry('Size', $size);
        return $this;
    } 

    /**
     * Set bits per sample.
     *  
     * @param int $bits
     * @return SampledFunctionType
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setBitsPerSample(int $bits): SampledFunctionType
    {
        if (!BitsPerSampleValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerSample is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $bits);

        $this->setEntry('BitsPerSample', $value);
        return $this;
    } 

    /**
     * Set order.
     *  
     * @param int $order
     * @return SampledFunctionType
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setOrder(int $order): SampledFunctionType
    {
        if (!IntegerValidator::isValid($order, 1, 3)) {
            throw new InvalidArgumentException("Order is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $order);

        $this->setEntry('Order', $value);
        return $this;
    } 


    /**
     * Set encode.
     *  
     * @param  ArrayObject  $encode
     * @return SampledFunctionType
     */
    public function setEncode(ArrayObject $encode): SampledFunctionType
    {
        $this->setEntry('Encode', $encode);
        return $this;
    }

    /**
     * Set decode.
     *
     * @param ArrayObject $decode
     * @return SampledFunctionType
     */
    public function setDecode(ArrayObject $decode): SampledFunctionType
    {
        $this->setEntry('Decode', $decode);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setFunctionType(FuncType::SAMPLED);

        if (!$this->hasEntry('BitsPerSample')) {
            throw new RuntimeException("BitsPerSample is missing. See ".__CLASS__." class's documentation for possible values.");
        }

		if ($this->hasEntry('Encode') && $this->hasEntry('Domain') ) {
			/** @var ArrayObject $domain */
			$domain = $this->getEntry('Domain');
			/** @var ArrayObject $encode */
			$encode = $this->getEntry('Encode');

			if (count($encode) != count($domain) ) {
				throw new RuntimeException("Encode size is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}
		}

		if ($this->hasEntry('Decode') && $this->hasEntry('Range') ) {
			/** @var ArrayObject $range */
			$range = $this->getEntry('Range');
			/** @var ArrayObject $decode */
			$decode = $this->getEntry('Decode');

			if (count($decode) != count($range) ) {
				throw new RuntimeException("Decode size is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}
		}

        return parent::format();
    }
}