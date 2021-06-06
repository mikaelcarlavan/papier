<?php

namespace Papier\Object;

use Papier\Base\IndirectObject;
use Papier\Validator\BooleanValidator;
use InvalidArgumentException;

class BooleanObject extends IndirectObject
{
    /**
    * Set object's value.
    *
    * @param  mixed  $value
    * @return BooleanObject
    */
    public function setValue($value): BooleanObject
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
        return $this->getValue();
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
        $value = $this->getValue();
        return $value ? 'true' : 'false';
    }
}