<?php

namespace Papier\Validator\Base;

interface Validator
{
    /**
     * Test if given string is valid for validator
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value);
}