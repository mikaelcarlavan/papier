<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Papier;
use Papier\Text\RenderingMode;
use Papier\Type\Base\ArrayType;
use Papier\Type\FontType;
use Papier\Type\Type1FontType;


class TextComponent extends BaseComponent
{
    use Color;
    use Transformation;

	/**
	 * Text's font.
	 *
	 * @var FontType
	 */
	protected FontType $font;

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
     * Set font.
     *
     * @param FontType $font
     * @return TextComponent
     */
    public function setFont(FontType $font): TextComponent
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Get font.
     *
     * @return FontType $font
     */
    public function getFont(): FontType
    {
        return $this->font;
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
        $font = $this->getFont();

		//$trueFont = Factory::create('Papier\Type\Type1FontType', null, true)->setBaseFont(Type1FontType::HELVETICA_FONT);
		//$trueFont->setName(sprintf('F%d', $trueFont->getNumber()));

		$font->setName(sprintf('F%d', $font->getNumber()));

        $fontResource = Factory::create('Papier\Type\Base\DictionaryType');
		$fontResource->setEntry($font->getName(), $font);

        $resources = $page->getResources();
        $resources->setEntry('Font', $fontResource);

        if (!$resources->hasEntry('ProcSet')) {
            $procset = Factory::create('Papier\Type\Base\ArrayType', null, true);
            $resources->setEntry('ProcSet', $procset);
        }

		/** @var ArrayType $procset */
        $procset = $resources->getEntry('ProcSet');

        if (!$procset->has(ProcedureSet::TEXT)) {
            $text = Factory::create('Papier\Type\Base\NameType', ProcedureSet::TEXT);
            $procset->append($text);
        }

        $renderingMode = $this->getRenderingMode();
        $fontSize = $this->getFontSize();

        $contents = $this->getContents();
        $contents->save();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        $contents->beginText();
        $contents->setFont($font->getName(), $mmToUserUnit * $fontSize);

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