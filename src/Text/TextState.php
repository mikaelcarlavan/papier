<?php

namespace Papier\Text;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\RenderingModeValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait TextState
{
    /**
     * Set character spacing.
     *  
     * @param   mixed   $cs
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setCharacterSpacing($cs)
    {
        if (!NumberValidator::isValid($cs)) {
            throw new InvalidArgumentException("Character spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s Tc', Factory::create('Papier\Type\NumberType', $cs)->format());
        return $this->addToContent($state);
    }

    /**
     * Set word spacing.
     *  
     * @param   mixed   $ws
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setWordSpacing($ws)
    {
        if (!NumberValidator::isValid($ws)) {
            throw new InvalidArgumentException("Word spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s Tw', Factory::create('Papier\Type\NumberType', $ws)->format());
        return $this->addToContent($state);
    }

    /**
     * Set horizontal scaling.
     *  
     * @param   mixed   $hs
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setHorizontalScaling($hs)
    {
        if (!NumberValidator::isValid($hs, 0, 100)) {
            throw new InvalidArgumentException("Horizontal scaling is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s Tz', Factory::create('Papier\Type\NumberType', $hs)->format());
        return $this->addToContent($state);
    }


    /**
     * Set text leading.
     *  
     * @param   mixed   $tl
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setTextLeading($tl)
    {
        if (!NumberValidator::isValid($tl)) {
            throw new InvalidArgumentException("Leading is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s TL', Factory::create('Papier\Type\NumberType', $tl)->format());
        return $this->addToContent($state);
    }

    /**
     * Set font name and size.
     *  
     * @param   mixed   $font
     * @param   mixed   $size
     * @return mixed
     * @throws InvalidArgumentException if the $font argument is not of type 'string'.
     * @throws InvalidArgumentException if the $size argument is not of type 'float' or 'int'.
     */
    public function setFont($font, $size)
    {
        if (!StringValidator::isValid($font)) {
            throw new InvalidArgumentException("Font is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($size)) {
            throw new InvalidArgumentException("Size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %s Tf', Factory::create('Papier\Type\NameType', $font)->format(), Factory::create('Papier\Type\NumberType', $size)->format());
        return $this->addToContent($state);
    }

    /**
     * Set text rendering mode.
     *  
     * @param   int   $rm
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not a valid rendering mode.
     */
    public function setTextRenderingMode(int $rm)
    {
        if (!RenderingModeValidator::isValid($rm)) {
            throw new InvalidArgumentException("Rendering mode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%d Tr', $rm);
        return $this->addToContent($state);
    }

    /**
     * Set text rise.
     *  
     * @param   mixed   $tr
     * @return mixed
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setTextRise($tr)
    {
        if (!NumberValidator::isValid($tr)) {
            throw new InvalidArgumentException("Rise is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s Ts', Factory::create('Papier\Type\NumberType', $tr)->format());
        return $this->addToContent($state);
    }
}