<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Font\TrueType\Base\TrueTypeFontTable;
use Papier\Helpers\MetricHelper;
use Papier\Helpers\TrueTypeFontFileHelper;
use Papier\Papier;
use Papier\Text\Encoding;
use Papier\Text\RenderingMode;
use Papier\Type\Base\ArrayType;
use Papier\Type\FontDescriptorDictionaryType;
use Papier\Type\FontDictionaryType;
use Papier\Type\TrueTypeFontDictionaryType;
use Papier\Type\Type1FontDictionaryType;
use RuntimeException;


class TextComponent extends BaseComponent
{
    use Color;
    use Transformation;

	/**
	 * Text's font.
	 *
	 * @var FontDictionaryType
	 */
	protected FontDictionaryType $font;

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
     * @param FontDictionaryType $font
     * @return TextComponent
     */
    public function setFont(FontDictionaryType $font): TextComponent
    {
        $this->font = $font;
        return $this;
    }

    /**
     * Get font.
     *
     * @return FontDictionaryType $font
     */
    public function getFont(): FontDictionaryType
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

	public function getTextWidth(): float
	{
		$text = $this->getText();
		/** @var TrueTypeFontDictionaryType $font */
		$font = $this->getFont();
		$fontSize = $this->getFontSize();
		$horizontalScaling = $this->getHorizontalScaling() ?: 100; // Default 100%
		$characterSpacing = $this->getCharacterSpacing();
		$wordSpacing = $this->getWordSpacing();


		$widths = $font->getWidths()->all();
		$firstChar = intval($font->getFirstChar()->format());
		$lastChar = intval($font->getLastChar()->format());

		for ($charCode = $firstChar; $charCode <= $lastChar; $charCode++) {
			$glyphWidths[$charCode] = array_shift($widths);
		}

		$totalWidthUnits = 0;
		$textLength = mb_strlen($text, 'UTF-8');

		for ($i = 0; $i < $textLength; $i++) {
			$char = mb_substr($text, $i, 1, 'UTF-8');
			$charCode = mb_ord($char, 'UTF-8');

			$glyphWidth = $glyphWidths[$charCode] ?? 0;
			$totalWidthUnits += $glyphWidth;

			if ($i < $textLength - 1) { // No spacing after last character
				$totalWidthUnits += $characterSpacing * 1000;
			}

			if ($char === ' ') {
				$totalWidthUnits += $wordSpacing * 1000;
			}
		}

		$totalWidth = ($totalWidthUnits / 1000) * $fontSize;
		$totalWidth *= $horizontalScaling / 100;

		return $totalWidth;
	}

    function format(): TextComponent
    {
        $page = $this->getPage();
        $font = $this->getFont();

		$font->setName(sprintf('F%d', $font->getNumber()));

        $resources = $page->getResources();

		if (!$resources->hasEntry('Font')) {
			$fontResource = Factory::create('Papier\Type\Base\DictionaryType');
			$resources->setEntry('Font', $fontResource);
		}
		$fontResource = $resources->getEntry('Font');
		$fontResource->setEntry($font->getName(), $font);

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

        $contents->beginText();
        $contents->setFont($font->getName(), MetricHelper::toUserUnit($fontSize));

        $this->applyColors($contents);

        $textRise = $this->getTextRise();
        $textLeading = $this->getTextLeading();
        $horizontalScaling = $this->getHorizontalScaling();
        $wordSpacing = $this->getWordSpacing();
        $characterSpacing = $this->getCharacterSpacing();

        $transformationMatrix = $this->getTransformationMatrix();

        if ($characterSpacing) {
            $contents->setCharacterSpacing(MetricHelper::toUserUnit($characterSpacing));
        }

        if ($textRise) {
            $contents->setTextRise(MetricHelper::toUserUnit($textRise));
        }

        if ($textLeading) {
            $contents->setTextLeading(MetricHelper::toUserUnit($textLeading));
        }

        if ($horizontalScaling) {
            $contents->setHorizontalScaling(MetricHelper::toUserUnit($horizontalScaling));
        }

        if ($wordSpacing) {
            $contents->setWordSpacing(MetricHelper::toUserUnit($wordSpacing));
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

		$text = $this->getText();

		if ($font->hasEntry('Encoding')) {
			$encoding = $font->getEntryValue('Encoding');
			if ($encoding == Encoding::WIN_ANSI) {
				$text = mb_convert_encoding($text, 'windows-1252', 'UTF-8');
			} else if ($encoding == Encoding::MAC_ROMAN) {
				$text = iconv('UTF-8', 'macintosh', $text);
			} else {
				throw new RuntimeException("Encoding not implemented yet. See ".__CLASS__." class's documentation for possible values.");
			}

			$text = Factory::create('Papier\Type\LiteralStringType', $text)->format();
		} else {
			$text = Factory::create('Papier\Type\TextStringType', $text)->format();
		}

        $contents->showText($text);
        $contents->endText();

        $contents->restore();

        return $this;
    }
}