<?php

namespace Papier\Object;

use InvalidArgumentException;
use Papier\Validator\BooleanValidator;

class BooleanObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param mixed $value
    * @return BooleanObject
    */
    public function setValue(mixed $value): BooleanObject
    {
        if (!BooleanValidator::isValid($value)) {
            throw new InvalidArgumentException("Boolean is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        parent::setValue($value);
        return $this;
    }   

    /**
    * Set value to true.
    *
    * @return BooleanObject
     */
    public function setTrue(): BooleanObject
    {
        return $this->setValue(true);
    }

    /**
     * Set value to false.
     *  
     * @return BooleanObject
     */
    public function setFalse(): BooleanObject
    {
        return $this->setValue(false);
    }

    /**
     * Returns if boolean is true.
     *  
     * @return bool
     */
    public function isTrue(): bool
    {
		/** @var bool $value */
		$value = $this->getValue();
        return $value;
    }
    
    /**
     * Returns if boolean is false.
     *  
     * @return bool
     */
    public function isFalse(): bool
    {
        return !$this->isTrue();
    }
    
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
		/** @var bool $value */
        $value = $this->getValue();
        return $value ? 'true' : 'false';
    }
}