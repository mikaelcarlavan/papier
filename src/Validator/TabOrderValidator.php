<?php

namespace Papier\Validator;

use Papier\Document\TabOrder;

class TabOrderValidator extends StringValidator
{
    /**
     * Tab orders.
     *
     * @var array<string>
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
    public static function isValid($value): bool
    {
        return parent::isValid($value) && in_array($value, self::TAB_ORDERS);
    }
}