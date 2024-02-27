<?php

namespace Papier\Base;

abstract class BaseObject
{
    /**
     * The value of the object.
     *
     * @var mixed
     */
    protected mixed $value;

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
    public function format(): string
    {
        return $this->getValue();
    }

    /**
     * Get object's value.
     *
     * @return mixed
     */
    protected function getValue(): mixed
    {
        return $this->value;
    }

        
    /**
     * Clear object's value.
     *
     * @return BaseObject
     */
    protected function clearValue(): BaseObject
    {
        return $this->setValue(null);
    }

    /**
     * Set object's value.
     *  
     * @param  mixed  $value
     * @return BaseObject
     */
    protected function setValue(mixed $value): BaseObject
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Write object's value.
     *
     * @return string
     */
    public function write(): string
    {
        return $this->format(). self::EOL_MARKER;
    }
}