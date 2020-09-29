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
    * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
    */
    public function setValue($value)
    {
        if (!BooleanValidator::isValid($value)) {
            throw new InvalidArgumentException("Boolean is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::setValue($value);
    }   

    /**
    * Set value to true.
    *
    * @return BooleanObject
     */
    public function setTrue()
    {
        return $this->setValue(true);
    }

    /**
     * Set value to false.
     *  
     * @return BooleanObject
     */
    public function setFalse()
    {
        return $this->setValue(false);
    }

    /**
     * Returns if boolean is true.
     *  
     * @return bool
     */
    public function isTrue()
    {
        return $this->getValue();
    }
    
    /**
     * Returns if boolean is false.
     *  
     * @return bool
     */
    public function isFalse()
    {
        return !$this->isTrue();
    }
    
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $value = $this->getValue();

        return $value ? 'true' : 'false';
    }
}