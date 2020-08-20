<?php

namespace Papier\Graphics;

use Papier\StreamObject;

use Papier\Validator\NumberValidator;
use Papier\Validator\LineCapStyleValidator;
use Papier\Validator\LineJoinStyleValidator;
use Papier\Validator\OverprintModeValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\RenderingIntentValidator;

use InvalidArgumentException;

trait ClippingPath
{
    /**
     * Modify the clipping path using the nonzero window rule.
     *  
     * @return mixed
     */
    public function modify()
    {
        $state = 'W';
        return $this->addToContent($state);
    }

    /**
     * Modify the clipping path using the even-odd window rule.
     *  
     * @return mixed
     */
    public function evenOddModify()
    {
        $state = 'W*';
        return $this->addToContent($state);
    }
}