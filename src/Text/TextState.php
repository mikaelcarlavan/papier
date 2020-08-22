<?php

namespace Papier\Text;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RenderingModeValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait TextState
{
    /**
     * Set character spacing.
     *  
     * @param   mixed   $cs
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return mixed
     */
    public function setCharacterSpacing($cs)
    {
        if (!NumberValidator::isValid($cs)) {
            throw new InvalidArgumentException("Character spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%f Tc', $cs);
        return $this->addToContent($state);
    }

    /**
     * Set word spacing.
     *  
     * @param   mixed   $ws
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return mixed
     */
    public function setWordSpacing($ws)
    {
        if (!NumberValidator::isValid($ws)) {
            throw new InvalidArgumentException("Word spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%f Tw', $ws);
        return $this->addToContent($state);
    }

    /**
     * Set horizontal scale.
     *  
     * @param   mixed   $hs
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return mixed
     */
    public function setHorizontalScale($hs)
    {
        if (!NumberValidator::isValid($hs, 0, 100)) {
            throw new InvalidArgumentException("Horizontal scale is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%f Tz', $hs);
        return $this->addToContent($state);
    }


    /**
     * Set text leading.
     *  
     * @param   mixed   $tl
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return mixed
     */
    public function setTextLeading($tl)
    {
        if (!NumberValidator::isValid($tl)) {
            throw new InvalidArgumentException("Leading is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%f TL', $tl);
        return $this->addToContent($state);
    }

    /**
     * Set font name and size.
     *  
     * @param   mixed   $font
     * @param   mixed   $size
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return mixed
     */
    public function setFont($font, $size)
    {
        if (!StringValidator::isValid($font)) {
            throw new InvalidArgumentException("Font is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($size)) {
            throw new InvalidArgumentException("Size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %f Tf', Factory::create('Name', $font)->format(), $size);
        return $this->addToContent($state);
    }

    /**
     * Set text rendering mode.
     *  
     * @param   int   $rm
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return mixed
     */
    public function setTextRenderingMode($rm)
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
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return mixed
     */
    public function setTextRise($tr)
    {
        if (!NumberValidator::isValid($tr)) {
            throw new InvalidArgumentException("Rise is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%f Ts', $tr);
        return $this->addToContent($state);
    }
}