<?php

namespace Papier\Filter;

use Papier\Filter\Base\Filter;
use Papier\Validator\StringValidator;

use RuntimeException;
use InvalidArgumentException;

class ASCII85EncodeFilter extends Filter
{
    /**
     * Process value.
     *  
     * @param  string  $value
     * @param  array  $param
     * @return string
     * @throws InvalidArgumentException if the provided argument is not a string.
     */
    public static function process(string $value, array $param = array()): string
    {
        return ASCII85Filter::decode($value, $param);
    }
}