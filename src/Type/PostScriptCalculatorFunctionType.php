<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;
use Papier\Object\IntegerObject;
use Papier\Object\ArrayObject;

use Papier\Functions\FunctionType;

use Papier\Validator\BitsPerSampleValidator;
use Papier\Validator\IntegerValidator;

use InvalidArgumentException;
use RuntimeException;

class PostScriptCalculatorFunctionType extends FunctionObject
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $this->setFunctionType(FunctionType::POSTSCRIPT_CALCULATOR);

        return parent::format();
    }
}