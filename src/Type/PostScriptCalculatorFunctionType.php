<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;

use Papier\Functions\FunctionType;

class PostScriptCalculatorFunctionType extends FunctionObject
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setFunctionType(FunctionType::POSTSCRIPT_CALCULATOR);

        return parent::format();
    }
}