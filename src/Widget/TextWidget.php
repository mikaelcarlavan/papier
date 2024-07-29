<?php

namespace Papier\Widget;

use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Type\ImageType;


class TextWidget extends BaseWidget
{
    use ColorWidget;

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

    function format(): BaseWidget
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

        $contents = $this->getContents();
        $contents->save();

        $contents->beginText();
        $contents->setFont($trueFont->getName(), $this->getFontSize());

        $strokingColors = $this->getStrokingColor();
        $nonStrokingColors = $this->getNonStrokingColor();

        if ($strokingColors) {
            $contents->setStrokingSpace($this->getStrokingColorSpace());
            $contents->setStrokingColor(...$strokingColors);
        }

        if ($nonStrokingColors) {
            $contents->setNonStrokingSpace($this->getNonStrokingColorSpace());
            $contents->setNonStrokingColor(...$nonStrokingColors);
        }

        $contents->moveToNextLineStartWithOffset($this->getX(), $this->getY());
        $contents->showText($this->getText());
        $contents->endText();

        $contents->restore();

        return $this;
    }
}