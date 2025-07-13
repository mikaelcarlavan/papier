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
use Papier\Text\TextAlign;
use Papier\Type\Base\ArrayType;
use Papier\Type\FontDescriptorDictionaryType;
use Papier\Type\FontDictionaryType;
use Papier\Type\TrueTypeFontDictionaryType;
use Papier\Type\Type1FontDictionaryType;
use Papier\Validator\NumberValidator;
use RuntimeException;
use InvalidArgumentException;

class TextComponent extends BaseComponent
{
    use Color;
    use Transformation;
	use Width;
	use Position;

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
	 * Text align.
	 *
	 * @var string
	 */
	protected string $textAlign = TextAlign::LEFT;

	/**
	 * The space between two lines
	 *
	 * @var float
	 */
	protected float $interlineSpacing = 0;

	/**
	 * Set component's interline spacing.
	 *
	 * @param  float  $interlineSpacing
	 * @return static
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setInterlineSpacing(float $interlineSpacing): static
	{
		if (!NumberValidator::isValid($interlineSpacing)) {
			throw new InvalidArgumentException("Interline spacing is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->interlineSpacing = $interlineSpacing;
		return $this;
	}

	/**
	 * Get component's interline spacing.
	 *
	 * @return float
	 */
	public function getInterlineSpacing(): float
	{
		return $this->interlineSpacing;
	}

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
	 * Set text align.
	 *
	 * @param string $textAlign
	 * @return TextComponent
	 */
	public function setTextAlign(string $textAlign): TextComponent
	{
		$this->textAlign = $textAlign;
		return $this;
	}

	/**
	 * Get text align.
	 *
	 * @return string
	 */
	public function getTextAlign(): string
	{
		return $this->textAlign;
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
	 * Get text lines.
	 *
	 * @return array<string>
	 */
	public function getTextLines(): array
	{
		$text = $this->getText();

		$width = $this->getWidth();
		$lines = [];

		if ($width <= 0) {
			$lines[] = $text;
			return $lines;
		}

		$words = preg_split('/\s+/', $text);
		$currentLine = '';

		if (is_array($words)) {
			foreach ($words as $word) {
				$testLine = $currentLine === '' ? $word : $currentLine . ' ' . $word;
				$testWidth = $this->getTextWidth($testLine);

				if ($testWidth <= $width || $currentLine === '') {
					$currentLine = $testLine;
				} else {
					$lines[] = $currentLine;
					$currentLine = $word;
				}
			}
		}

		if ($currentLine !== '') {
			$lines[] = $currentLine;
		}

		return $lines;
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

	/**
	 * Get text height.
	 *
	 * @param string $text
	 * @return float
	 */
	public function getTextHeight(string $text = ''): float
	{
		// $text = $this->getText();
		/** @var TrueTypeFontDictionaryType $font */
		$font = $this->getFont();
		$fontSize = $this->getFontSize();

		$fontDescriptor = $font->getFontDescriptor();

		$lineGap = $font->getLineGap();
		$capHeight = $fontDescriptor->getCapHeightValue();

		$lineHeightUnits = $capHeight + $lineGap;

		$lines = preg_split("/\r\n|\r|\n/", $text);
		if (is_array($lines)) {
			$lineCount = max(count($lines), 1);
		} else {
			$lineCount = 1;
		}

		$lineCount = floatval($lineCount);
		$totalHeight = (floatval($lineHeightUnits) / 1000) * $fontSize * $lineCount;

		return $totalHeight;
	}

	/**
	 * Get text width
	 *
	 * @param string $text
	 * @return float
	 */

	public function getTextWidth(string $text = ''): float
	{
		// $text = is_null($text) ? $this->getText() : $text;

		/** @var TrueTypeFontDictionaryType $font */
		$font = $this->getFont();
		$fontSize = $this->getFontSize();
		$horizontalScaling = $this->getHorizontalScaling() ?: 100; // Default 100%
		$characterSpacing = $this->getCharacterSpacing();
		$wordSpacing = $this->getWordSpacing();


		$widths = $font->getWidths()->all();
		$charCodes = $font->getCharCodes();

		$glyphWidths = [];
		foreach ($charCodes as $charCode) {
			$glyphWidths[$charCode] = array_shift($widths);
		}

		$totalWidthUnits = 0;

		$chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
		$textLength = count($chars);

		$leftCharCode = null;
		$kerningPairs = $font->getKerningPairs();
		$charSpacingUnits = $characterSpacing * 1000;
		$wordSpacingUnits = $wordSpacing * 1000;

		for ($i = 0; $i < $textLength; $i++) {
			$char = $chars[$i];
			$charCode = mb_ord($char, 'UTF-8');

			$glyphWidth = $glyphWidths[$charCode] ?? $font->getDefaultAdvanceWidth();
			$totalWidthUnits += $glyphWidth;

			// Kerning
			if ($leftCharCode !== null) {
				$kerning = $kerningPairs[$leftCharCode][$charCode] ?? 0;
				$totalWidthUnits += $kerning;
			}

			if ($i < $textLength - 1) { // No spacing after last character
				$totalWidthUnits += $charSpacingUnits;
			}

			if ($char === ' ') {
				$totalWidthUnits += $wordSpacingUnits;
			}

			$leftCharCode = $charCode;
		}

		$totalWidth = ($totalWidthUnits / 1000) * $fontSize;
		$totalWidth *= $horizontalScaling / 100;

		return $totalWidth;
	}

	/**
	 * Estimate component's height.
	 *
	 * @return float
	 */
	public function estimateHeight(): float
	{
		$height = 0;
		$lines = $this->getTextLines();

		foreach ($lines as $line) {
			$height += $this->getTextHeight($line);
			$height += $this->getInterlineSpacing();
		}

		$height -= $this->getInterlineSpacing();
		return $height;
	}

	/**
	 * Estimate bounding box
	 *
	 * @return array
	 */
	public function getBoundingBox(): array
	{
		// Bounding box differs from just X, Y, width and height because PDF
		// starts drawing at the bottom-left of the text
		$height = 0;
		$lines = $this->getTextLines();

		$line = array_shift($lines);
		$firstLineHeight = $this->getTextHeight($line);

		$height += $firstLineHeight + $this->getInterlineSpacing();
		foreach ($lines as $line) {
			$height += ($this->getTextHeight($line) + $this->getInterlineSpacing());
		}

		$height -= $this->getInterlineSpacing();
		$box = [$this->getX(), $this->getY() + $firstLineHeight - $height, $this->estimateWidth(), $height];

		return $box;
	}

	/**
	 * Estimate component's height.
	 *
	 * @return float
	 */
	public function estimateWidth(): float
	{
		$width = $this->getWidth();
		return $width > 0 ? $width : $this->getTextWidth($this->getText());
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

		$x = $this->getX();
		$y = $this->getY();

		$this->translate($x, $y);

		$lines = $this->getTextLines();

		list($boxX, $boxY, $boxWidth, $boxHeight) = $this->getBoundingBox();

		$interlineSpacing = $this->getInterlineSpacing();

		foreach ($lines as $line) {
			$height = $this->getTextHeight($line);

			$offsetX = 0;
			if ($this->getTextAlign() == TextAlign::RIGHT) {
				$offsetX = $boxWidth - $this->getTextWidth($line);
			} else if ($this->getTextAlign() == TextAlign::CENTER) {
				$offsetX = ($boxWidth - $this->getTextWidth($line)) / 2.0;
			}

			$this->translate($offsetX, 0);
			$transformationMatrix = $this->getTransformationMatrix();

			$contents->setTextMatrix(
				$transformationMatrix->getData(0, 0),
				$transformationMatrix->getData(0, 1),
				$transformationMatrix->getData(1, 0),
				$transformationMatrix->getData(1, 1),
				$transformationMatrix->getData(2, 0),
				$transformationMatrix->getData(2, 1)
			);

			if ($font->hasEntry('Encoding')) {
				$encoding = $font->getEntryValue('Encoding');
				if ($encoding == Encoding::WIN_ANSI) {
					$line = mb_convert_encoding($line, 'windows-1252', 'UTF-8');
				} else if ($encoding == Encoding::MAC_ROMAN) {
					$line = iconv('UTF-8', 'macintosh', $line);
				} else {
					throw new RuntimeException("Encoding not implemented yet. See ".__CLASS__." class's documentation for possible values.");
				}

				$text = Factory::create('Papier\Type\LiteralStringType', $line)->format();
			} else {
				$text = Factory::create('Papier\Type\TextStringType', $line)->format();
			}

			$contents->showText($text);

			$this->translate(-$offsetX, -($height + $interlineSpacing));
		}

        $contents->endText();

        $contents->restore();

        return $this;
    }
}