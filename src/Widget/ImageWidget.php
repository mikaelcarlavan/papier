<?php

namespace Papier\Widget;

use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Filter\FilterType;
use Papier\Graphics\DeviceColourSpace;
use Papier\Object\StringObject;
use Papier\Stream\TextStream;
use Papier\Text\RenderingMode;
use Papier\Type\ArrayType;
use Papier\Type\ContentStreamType;
use Papier\Type\ImageType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\RenderingModeValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;

class ImageWidget extends BaseWidget
{
    /**
     * Name of image.
     *
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * Source of image.
     *
     * @var string|null
     */
    protected ?string $source = null;

    /**
     * The width of the widget
     *
     * @var float
     */
    protected float $width = 0;

    /**
     * The height of the widget
     *
     * @var float
     */
    protected float $height = 0;

    /**
     * The horizontal skewing of the widget
     *
     * @var float
     */
    protected float $skewX = 0;

    /**
     * The vertical skewing of the widget
     *
     * @var float
     */
    protected float $skewY = 0;

    /**
     * Set widget's horizontal skewing.
     *
     * @param  float  $skewX
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setSkewX(float $skewX): BaseWidget
    {
        if (!NumberValidator::isValid($skewX)) {
            throw new InvalidArgumentException("SkewX is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->skewX = $skewX;
        return $this;
    }

    /**
     * Set widget's vertical skewing.
     *
     * @param  float  $skewY
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setSkewY(float $skewY): BaseWidget
    {
        if (!NumberValidator::isValid($skewY)) {
            throw new InvalidArgumentException("SkewY is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->skewY = $skewY;
        return $this;
    }

    /**
     * Get widget's horizontal skewing.
     *
     * @return float
     */
    public function getSkewX(): float
    {
        return $this->skewX;
    }

    /**
     * Get widget's vertical skewing.
     *
     * @return float
     */
    public function getSkewY(): float
    {
        return $this->skewY;
    }

    /**
     * Set widget's width.
     *
     * @param  float  $width
     * @return ImageWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setWidth(float $width): ImageWidget
    {
        if (!NumberValidator::isValid($width, 0.0)) {
            throw new InvalidArgumentException("Width is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->width = $width;
        return $this;
    }

    /**
     * Set widget's height.
     *
     * @param  float  $height
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setHeight(float $height): ImageWidget
    {
        if (!NumberValidator::isValid($height, 0.0)) {
            throw new InvalidArgumentException("Height is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->height = $height;
        return $this;
    }

    /**
     * Get widget's width.
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Get widget's height.
     *
     * @return float
     */
    public function getHeight(): float
    {
        return $this->height;
    }

    /**
     * Set image name.
     *
     * @param string $name
     * @return void
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get image name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set image source.
     *
     * @param string $source
     * @return void
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

    /**
     * Get image source.
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Format widget's content.
     *
     * @return ImageWidget
     */
    public function format(): ImageWidget
    {
        $page = $this->getPage();
        $resources = $page->getResources();

        if (!$resources->hasEntry('XObject')) {
            $image = Factory::create('\Papier\Type\ImageType', null, true);

            $this->setName(sprintf('Im%d', $image->getNumber()));

            $xObject = Factory::create('\Papier\Type\DictionaryType')->setEntry($this->getName(), $image);
            $resources->setEntry('XObject', $xObject);
        }

        if (!$resources->hasEntry('ProcSet')) {
            $procset = Factory::create('\Papier\Type\ArrayType', null, true);
            $resources->setEntry('ProcSet', $procset);
        }

        $dimensions = getimagesize($this->getSource());

        $procset = $resources->getEntry('ProcSet');
        $channels = $dimensions['channels'] ?? 3;
        $bitsPerComponent = $dimensions['bits'] ?? 8;
        $mime = $dimensions['mime'] ?? null;

        $width = $dimensions[0] ?? 0;
        $height = $dimensions[1] ?? 0;

        if (!$procset->has(ProcedureSet::GRAPHICS)) {
            $graphics = Factory::create('\Papier\Type\NameType', ProcedureSet::GRAPHICS);
            $procset->append($graphics);
        }

        if ($channels == 1 && !$procset->has(ProcedureSet::GRAYSCALE_IMAGES)) {
            $imageb = Factory::create('\Papier\Type\NameType', ProcedureSet::GRAYSCALE_IMAGES);
            $procset->append($imageb);
        } elseif (!$procset->has(ProcedureSet::COLOUR_IMAGES)) {
            $imagec = Factory::create('\Papier\Type\NameType', ProcedureSet::COLOUR_IMAGES);
            $procset->append($imagec);
        }

        $xObject = $resources->getEntry('XObject');
        $image = $xObject->getEntry($this->getName());

        $image->setWidth($width);
        $image->setHeight($height);
        $image->setContent(file_get_contents($this->getSource()));

        if ($channels == 3) {
            $image->setColorSpace(DeviceColourSpace::RGB);
        } elseif ($channels == 4) {
            $image->setColorSpace(DeviceColourSpace::CMYK);
        } elseif ($channels == 1) {
            $image->setColorSpace(DeviceColourSpace::GRAY);
        }

        $image->setBitsPerComponent($bitsPerComponent);

        if ($mime == 'image/jpeg') {
            $image->setFilter(FilterType::DCT_DECODE);
        }

        $contents = $this->getContents();

        $contents->setCompression(FilterType::FLATE_DECODE);
        $contents->save();
        $contents->setCTM($this->getWidth(), $this->getSkewY(), $this->getSkewX(), $this->getHeight(), $this->getX(), $this->getY());
        $contents->paintXObject($this->getName());
        $contents->restore();

        return $this;
    }
}