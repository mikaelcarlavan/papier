<?php

namespace Papier\Base;

abstract class BaseObject
{
    /**
     * The value of the object.
     *
     * @var mixed
     */
    protected $value;

    /**
     * End-of-line marker.
     *
     * @var string
     */
    const EOL_MARKER = "\r\n"; 

    /**
     * Magical method.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->write();
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        return $this->getValue();
    }

    /**
     * Get object's value.
     *
     * @return string
     */
    protected function getValue()
    {
        return $this->value;
    }

        
    /**
     * Clear object's value.
     *
     * @return string
     */
    protected function clearValue()
    {
        $this->setValue(null);
        return $this;
    }

    /**
     * Set object's value.
     *  
     * @param  mixed  $value
     * @return BaseObject
     */
    protected function setValue($value)
    {
        $this->value = $value;
        return $this;
    } 

    /**
     * Write object's value.
     *
     * @return string
     */
    public function write()
    {
        return $this->format(). self::EOL_MARKER;
    }
}