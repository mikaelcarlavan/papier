<?php

namespace Papier\Component;

use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Papier;
use Papier\Text\RenderingMode;
use Papier\Type\ArrayType;


class TextComponent extends BaseComponent
{
    use Color;
    use Transformation;

	/**
	 * Helvetica font
	 *
	 * @var string
	 */
	const HELVETICA_FONT = 'Helvetica';

	/**
	 * Helvetica font
	 *
	 * @var string
	 */
	const COURIER_FONT = 'Courier';

	/**
	 * Symbol font
	 *
	 * @var string
	 */
	const SYMBOL_FONT = 'Symbol';

	/**
	 * Times font
	 *
	 * @var string
	 */
	const TIMES_FONT = 'Times';

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
     * Text of component.
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
     * @return TextComponent
     */
    public function setRenderingMode(int $renderingMode): TextComponent
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
     * @return TextComponent
     */
    public function setTextRise(float $textRise): TextComponent
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
     * @return TextComponent
     */
    public function setWordSpacing(float $wordSpacing): TextComponent
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
     * @return TextComponent
     */
    public function setTextLeading(float $textLeading): TextComponent
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
     * @return TextComponent
     */
    public function setCharacterSpacing(float $characterSpacing): TextComponent
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
     * @return TextComponent
     */
    public function setHorizontalScaling(float $horizontalScaling): TextComponent
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
     * @return TextComponent
     */
    public function setText(string $text): TextComponent
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
     * @return TextComponent
     */
    public function setBaseFont(string $fontName): TextComponent
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
     * @return TextComponent
     */
    public function setFontSize(float $fontSize): TextComponent
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

    function format(): TextComponent
    {
        $page = $this->getPage();
        $fontName = $this->getBaseFont();

        $trueFont = Factory::create('Papier\Type\Type1FontType', null, true)->setBaseFont($fontName);

        $trueFont->setName(sprintf('F%d', $trueFont->getNumber()));

        $font = Factory::create('Papier\Type\DictionaryType');
		$font->setEntry($trueFont->getName(), $trueFont);

        $resources = $page->getResources();
        $resources->setEntry('Font', $font);

        if (!$resources->hasEntry('ProcSet')) {
            $procset = Factory::create('Papier\Type\ArrayType', null, true);
            $resources->setEntry('ProcSet', $procset);
        }

		/** @var ArrayType $procset */
        $procset = $resources->getEntry('ProcSet');

        if (!$procset->has(ProcedureSet::TEXT)) {
            $text = Factory::create('Papier\Type\NameType', ProcedureSet::TEXT);
            $procset->append($text);
        }

        $renderingMode = $this->getRenderingMode();
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

        $transformationMatrix = $this->getTransformationMatrix();

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

        $contents->setTextMatrix(
            $transformationMatrix->getData(0, 0),
            $transformationMatrix->getData(0, 1),
            $transformationMatrix->getData(1, 0),
            $transformationMatrix->getData(1, 1),
            $transformationMatrix->getData(2, 0),
            $transformationMatrix->getData(2, 1)
        );

        $contents->showText($this->getText());
        $contents->endText();

        $contents->restore();

        return $this;
    }
}