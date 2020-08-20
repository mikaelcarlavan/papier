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

trait PathPainting
{
    /**
     * Stroke the path.
     *  
     * @return mixed
     */
    public function stroke()
    {
        $state = 'S';
        return $this->addToContent($state);
    }

    /**
     * Close and stroke the path.
     *  
     * @return mixed
     */
    public function closeAndStroke()
    {
        $state = 's';
        return $this->addToContent($state);
    }

    /**
     * Fill the path.
     *  
     * @return mixed
     */
    public function fill()
    {
        $state = 'f';
        return $this->addToContent($state);
    }

    /**
     * Fill the path with the even-odd rule.
     *  
     * @return mixed
     */
    public function evenOddFill()
    {
        $state = 'f*';
        return $this->addToContent($state);
    }

    /**
     * Fill and stroke the path with the nonzero window rule.
     *  
     * @return mixed
     */
    public function fillAndStroke()
    {
        $state = 'B';
        return $this->addToContent($state);
    }

    /**
     * Fill and stroke the path with the even-odd window rule.
     *  
     * @return mixed
     */
    public function evenOddFillAndStroke()
    {
        $state = 'B*';
        return $this->addToContent($state);
    }

    /**
     * Close, fill and stroke the path with the nonzero window rule.
     *  
     * @return mixed
     */
    public function closeFillAndStroke()
    {
        $state = 'b';
        return $this->addToContent($state);
    }

    /**
     * Close, fill and stroke the path with the even-odd window rule.
     *  
     * @return mixed
     */
    public function evenOddCloseFillAndStroke()
    {
        $state = 'b*';
        return $this->addToContent($state);
    }

    /**
     * End the path without filling or stroking it.
     *  
     * @return mixed
     */
    public function end()
    {
        $state = 'n';
        return $this->addToContent($state);
    }
}