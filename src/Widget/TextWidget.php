<?php

namespace Papier\Widget;

use Papier\Factory\Factory;
use Papier\Object\DictionaryObject;
use Papier\Papier;
use Papier\Type\DocumentCatalogType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;

class TextWidget extends BaseWidget
{
    /**
     * The value of the text color.
     *
     * @var array
     */
    protected array $textColor = array(0, 0, 0);

    /**
     * The name of the text font.
     *
     * @var string
     */
    protected string $font;

    /**
     * The size of the text font.
     *
     * @var int
     */
    protected int $fontSize = 10;

    /**
     * Get text color.
     *
     * @return array
     */
    public function getTextColor(): array
    {
        return $this->textColor;
    }

    /**
     * Set text color.
     *
     * @param array $textColor
     * @return TextWidget
     * @throws InvalidArgumentException if the $textColor argument is not an array.
     */
    public function setTextColor(array $textColor): TextWidget
    {
        if (!ArrayValidator::isValid($textColor)) {
            throw new InvalidArgumentException("Text's color is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->textColor = $textColor;
        return $this;
    }

    /**
     * Get font.
     *
     * @return string
     */
    public function getFont(): string
    {
        return $this->font;
    }

    /**
     * Set font.
     *
     * @param string $font
     * @return TextWidget
     * @throws InvalidArgumentException if the $textColor argument is not a string.
     */
    public function setFont(string $font): TextWidget
    {
        if (!StringValidator::isValid($font)) {
            throw new InvalidArgumentException("Font is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->font = $font;
        return $this;
    }

    /**
     * Get font size.
     *
     * @return int
     */
    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    /**
     * Set font size.
     *
     * @param int $fontSize
     * @return TextWidget
     * @throws InvalidArgumentException if the $fontSize argument is not an int.
     */
    public function setFontSize(int $fontSize): TextWidget
    {
        if (!IntegerValidator::isValid($fontSize)) {
            throw new InvalidArgumentException("Font's size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * Format widget.
     *
     * @return void
     */
    public function format(): void
    {
        $page = $this->pdf->getCurrentPage();

        $contents = $page->getContents();

        $contents->setNonStrokingRGBColour($this->getTextColor());

        $contents->beginText();
        $contents->setFont($this->getFont(), $this->getFontSize());
        $contents->setCharacterSpacing(-2);
        $contents->moveToNextLineStartWithOffset($this->getX(), $this->getY());
        $contents->showText($this->getText());
        $contents->endText();

    }
}