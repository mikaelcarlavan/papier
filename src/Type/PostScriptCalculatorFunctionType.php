<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;

use Papier\Functions\FunctionType as FuncType;
use Papier\Type\Base\FunctionType;

class PostScriptCalculatorFunctionType extends FunctionType
{
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setFunctionType(FuncType::POSTSCRIPT_CALCULATOR);

        return parent::format();
    }
}