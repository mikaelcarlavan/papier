<?php

namespace Papier\Base;

use Papier\Base\Object;

class Dictionary extends Object
{
    /**
     * Set value for given key.
     *  
     * @param  string  $value
     * @param  string  $key
     * @return \Papier\Base\Dictionary
     */
    protected function setValueForKey($value, $key)
    {
        $values = $this->getValue();
        list($key, $subKey) = $this->getSubKeyFromKey($key);

        if (is_null($subKey)) {
            $values[$key] = $value;
        } else {
            $values[$key][$subKey] = $value;
        }

        return $this->setValue($values);
    }

    /**
     * Add value.
     *  
     * @param  string  $value
     * @return \Papier\Base\Dictionary
     */
    protected function addValue($value)
    {
        $values = $this->getValue();

        $arr = is_array($values) ? $values : array($values);
        $arr[] = $value;

        return $this->setValue($arr);
    }

    /**
     * Add value to given key.
     *  
     * @param  string  $value
     * @param  string  $key
     * @return \Papier\Base\Dictionary
     */
    protected function addValueForKey($value, $key)
    {
        $values = $this->getValueForKey($key);

        $arr = is_array($values) ? $values : array($values);
        $arr[] = $value;

        return $this->setValueForKey($arr, $key);
    }

    /**
     * Get value for given key.
     *  
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    protected function getValueForKey($key, $default = null)
    {
        $values = $this->getValue();
        $value = $values[$key] ?? $default;

        return $value;
    }   
}