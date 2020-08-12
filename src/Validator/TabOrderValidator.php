<?php

namespace Papier\Validator;

use Papier\Document\TabOrder;
use Papier\Validator\StringValidator;

class TabOrderValidator implements StringValidator
{
    /**
     * Tab orders.
     *
     * @var array
     */
    const TAB_ORDERS = array(
        TabOrder::ROW,
        TabOrder::COLUMN,
        TabOrder::STRUCTURE,
    );


     /**
     * Test if given parameter is a valid tab order.
     * 
     * @param  mixed  $value
     * @return bool
     */
    public static function isValid($value)
    {
        $isValid = parent::isValid($value) && in_array($value, self::TAB_ORDERS);
        return $isValid;
    }
}