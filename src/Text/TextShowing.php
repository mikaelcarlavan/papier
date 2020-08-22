<?php

namespace Papier\Text;

use Papier\Object\ArrayObject;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RenderingModeValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait TextShowing
{
    /**
     * Show a text string.
     *  
     * @param   string   $text
     * @throws InvalidArgumentException if the provided arguments are not of type 'string'.
     * @return mixed
     */
    public function showText($text)
    {
        if (!StringValidator::isValid($text)) {
            throw new InvalidArgumentException("Text is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        $state = sprintf('%s Tj', Factory::create('LiteralString', $text)->format());
        return $this->addToContent($state);
    }

    /**
     * Move to next line and show a text string.
     *  
     * @param   string   $text
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return mixed
     */
    public function moveToNextLineAndShowText($text)
    {
        if (!StringValidator::isValid($text)) {
            throw new InvalidArgumentException("Text is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        $state = sprintf('%s \'', Factory::create('LiteralString', $text)->format());
        return $this->addToContent($state);
    }

    /**
     * Move to next line and show a text string with given word spacing and given character spacing.
     *  
     * @param   string   $text
     * @param   mixed   $aw
     * @param   mixed   $ac
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @throws InvalidArgumentException if the provided arguments are not of type 'int' or 'float'.
     * @return mixed
     */
    public function moveToNextLineAndShowWTextWithSpacing($text, $aw, $ac)
    {
        if (!StringValidator::isValid($text)) {
            throw new InvalidArgumentException("Text is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        if (!NumberValidator::isValid($aw)) {
            throw new InvalidArgumentException("Word spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($ac)) {
            throw new InvalidArgumentException("Character spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s %s "', 
            Factory::create('Number', $aw)->format(),
            Factory::create('Number', $ac)->format(),
            Factory::create('LiteralString', $text)->format()
        );

        return $this->addToContent($state);
    }

    /**
     * Show one or more texts.
     *  
     * @param   \Papier\Object\ArrayObject   $texts
     * @throws InvalidArgumentException if the provided arguments are not of type 'ArrayObject'.
     * @return mixed
     */
    public function showTexts($texts)
    {
        if (!$texts instanceof ArrayObject) {
            throw new InvalidArgumentException("Texts is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        $state = sprintf('%s TJ', $texts->format());
        return $this->addToContent($state);
    }
}