<?php

namespace Papier\Text;

use Papier\Object\ArrayObject;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait TextShowing
{
    /**
     * Show a text string.
     *  
     * @param   string   $text
     * @return static
     * @throws InvalidArgumentException if the provided arguments are not of type 'string'.
     */
    public function showText(string $text): static
    {
        if (!StringValidator::isValid($text)) {
            throw new InvalidArgumentException("Text is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        $state = sprintf('%s Tj', Factory::create('Papier\Type\LiteralStringType', $text)->format());
		$this->addToContent($state);
        return $this;
    }

    /**
     * Move to next line and show a text string.
     *  
     * @param string $text
     * @return static
     */
    public function moveToNextLineAndShowText(string $text): static
    {
        $state = sprintf('%s \'', Factory::create('Papier\Type\LiteralStringType', $text)->format());
		$this->addToContent($state);
        return $this;
    }

    /**
     * Move to next line and show a text string with given word spacing and given character spacing.
     *  
     * @param   string   $text
     * @param   mixed   $aw
     * @param   mixed   $ac
     * @throws InvalidArgumentException if the $text argument is not of type 'string'.
     * @throws InvalidArgumentException if the $aw and $ac arguments are not of type 'int' or 'float'.
     * @return static
     */
    public function moveToNextLineAndShowTextWithSpacing(string $text, $aw, $ac): static
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
            Factory::create('Papier\Type\NumberType', $aw)->format(),
            Factory::create('Papier\Type\NumberType', $ac)->format(),
            Factory::create('Papier\Type\LiteralStringType', $text)->format()
        );

		$this->addToContent($state);
        return $this;
    }

    /**
     * Show one or more texts.
     *  
     * @param  ArrayObject   $texts
     * @return static
     */
    public function showTexts(ArrayObject $texts): static
    {
        $state = sprintf('%s TJ', $texts->format());
		$this->addToContent($state);
        return $this;
    }
}