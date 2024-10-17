<?php

namespace Papier\Widget;

use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Papier;
use Papier\Text\RenderingMode;
use Papier\Type\ImageType;


class TextWidget extends BaseWidget
{
    use Colors;

    /**
     * Text's font name.
     *
     * @var string
     */
    protected string $fontName = 'Helvetica';

    /**
     * Text's font size.
     *
     * @var float
     */
    protected float $fontSize = 10;

    /**
     * Text of widget.
     *
     * @var string
     */
    protected string $text;

    /**
     * Horizontal scaling.
     *
     * @var float
     */
    protected float $horizontalScaling = 0;

    /**
     * Text rise.
     *
     * @var float
     */
    protected float $textRise = 0;

    /**
     * Word spacing.
     *
     * @var float
     */
    protected float $wordSpacing = 0;

    /**
     * Text leading.
     *
     * @var float
     */
    protected float $textLeading = 0;

    /**
     * Character spacing.
     *
     * @var float
     */
    protected float $characterSpacing = 0;

    /**
     * Rendering mode.
     *
     * @var int
     */
    protected int $renderingMode = RenderingMode::FILL;

    /**
     * Set rendering mode.
     *
     * @param int $renderingMode
     * @return TextWidget
     */
    public function setRenderingMode(int $renderingMode): TextWidget
    {
        $this->renderingMode = $renderingMode;
        return $this;
    }

    /**
     * Get rendering mode.
     *
     * @return int
     */
    public function getRenderingMode(): int
    {
        return $this->renderingMode;
    }

    /**
     * Set text rise.
     *
     * @param float $textRise
     * @return TextWidget
     */
    public function setTextRise(float $textRise): TextWidget
    {
        $this->textRise = $textRise;
        return $this;
    }

    /**
     * Get text rise.
     *
     * @return float
     */
    public function getTextRise(): float
    {
        return $this->textRise;
    }

    /**
     * Set word spacing.
     *
     * @param float $wordSpacing
     * @return TextWidget
     */
    public function setWordSpacing(float $wordSpacing): TextWidget
    {
        $this->wordSpacing = $wordSpacing;
        return $this;
    }

    /**
     * Get word spacing.
     *
     * @return float
     */
    public function getWordSpacing(): float
    {
        return $this->wordSpacing;
    }

    /**
     * Set text leading.
     *
     * @param float $textLeading
     * @return TextWidget
     */
    public function setTextLeading(float $textLeading): TextWidget
    {
        $this->textLeading = $textLeading;
        return $this;
    }

    /**
     * Get text leading.
     *
     * @return float
     */
    public function getTextLeading(): float
    {
        return $this->textLeading;
    }

    /**
     * Set character spacing.
     *
     * @param float $characterSpacing
     * @return TextWidget
     */
    public function setCharacterSpacing(float $characterSpacing): TextWidget
    {
        $this->characterSpacing = $characterSpacing;
        return $this;
    }

    /**
     * Get character spacing.
     *
     * @return float
     */
    public function getCharacterSpacing(): float
    {
        return $this->characterSpacing;
    }

    /**
     * Set horizontal scaling.
     *
     * @param float $horizontalScaling
     * @return TextWidget
     */
    public function setHorizontalScaling(float $horizontalScaling): TextWidget
    {
        $this->horizontalScaling = $horizontalScaling;
        return $this;
    }

    /**
     * Get horizontal scaling.
     *
     * @return float
     */
    public function getHorizontalScaling(): float
    {
        return $this->horizontalScaling;
    }

    /**
     * Set text.
     *
     * @param string $text
     * @return TextWidget
     */
    public function setText(string $text): TextWidget
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Get text.
     *
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * Set basefont (PostScript) name.
     *
     * @param string $fontName
     * @return TextWidget
     */
    public function setBaseFont(string $fontName): TextWidget
    {
        $this->fontName = $fontName;
        return $this;
    }

    /**
     * Get basefont name.
     *
     * @return string $fontName
     */
    public function getBaseFont(): string
    {
        return $this->fontName;
    }


    /**
     * Set font's size.
     *
     * @param float $fontSize
     * @return TextWidget
     */
    public function setFontSize(float $fontSize): TextWidget
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * Get font's size.
     *
     * @return float
     */
    public function getFontSize(): float
    {
        return $this->fontSize;
    }

    function format(): TextWidget
    {
        $page = $this->getPage();
        $fontName = $this->getBaseFont();

        $trueFont = Factory::create('\Papier\Type\Type1FontType', null, true)->setBaseFont($fontName);

        $trueFont->setName(sprintf('F%d', $trueFont->getNumber()));

        $font = Factory::create('\Papier\Type\DictionaryType')->setEntry($trueFont->getName(), $trueFont);

        $resources = $page->getResources();
        $resources->setEntry('Font', $font);

        if (!$resources->hasEntry('ProcSet')) {
            $procset = Factory::create('\Papier\Type\ArrayType', null, true);
            $resources->setEntry('ProcSet', $procset);
        }

        $procset = $resources->getEntry('ProcSet');

        if (!$procset->has(ProcedureSet::TEXT)) {
            $text = Factory::create('\Papier\Type\NameType', ProcedureSet::TEXT);
            $procset->append($text);
        }

        $renderingMode = $this->getRenderingMode();
        $x = $this->getX();
        $y = $this->getY();
        $fontSize = $this->getFontSize();

        $contents = $this->getContents();
        $contents->save();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        $contents->beginText();
        $contents->setFont($trueFont->getName(), $mmToUserUnit * $fontSize);

        $this->applyColors($contents);

        $textRise = $this->getTextRise();
        $textLeading = $this->getTextLeading();
        $horizontalScaling = $this->getHorizontalScaling();
        $wordSpacing = $this->getWordSpacing();
        $characterSpacing = $this->getCharacterSpacing();

        if ($characterSpacing) {
            $contents->setCharacterSpacing($mmToUserUnit * $characterSpacing);
        }

        if ($textRise) {
            $contents->setTextRise($mmToUserUnit * $textRise);
        }

        if ($textLeading) {
            $contents->setTextLeading($mmToUserUnit * $textLeading);
        }

        if ($horizontalScaling) {
            $contents->setHorizontalScaling($mmToUserUnit * $horizontalScaling);
        }

        if ($wordSpacing) {
            $contents->setWordSpacing($mmToUserUnit * $wordSpacing);
        }

        $contents->setTextRenderingMode($renderingMode);

        $contents->moveToNextLineStartWithOffset($mmToUserUnit * $x, $mmToUserUnit * $y);
        $contents->showText($this->getText());
        $contents->endText();

        $contents->restore();

        return $this;
    }
}