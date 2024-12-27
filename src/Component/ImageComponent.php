<?php

namespace Papier\Component;

use InvalidArgumentException;
use Papier\Component\Base\BaseComponent;
use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\Filter\FilterType;
use Papier\Filter\Params\FlateDecodeParams;
use Papier\Filter\Predictor;
use Papier\Graphics\DeviceColourSpace;
use Papier\Helpers\ImageHelper;
use Papier\Papier;
use Papier\Type\Base\ArrayType;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\NumberValidator;

class ImageComponent extends BaseComponent
{
    use Transformation;


    /**
     * Valid mimes for image.
     *
     * @var array<string>
     */
    protected array $validMimes = ['image/jpeg', 'image/png'];

    /**
     * Name of image.
     *
     * @var string
     */
    protected string $name;

    /**
     * Source of image.
     *
     * @var string
     */
    protected string $source;

    /**
     * The width of the component
     *
     * @var float
     */
    protected float $width = 0;

    /**
     * The height of the component
     *
     * @var float
     */
    protected float $height = 0;

    /**
     * The horizontal skewing of the component
     *
     * @var float
     */
    protected float $skewX = 0;

    /**
     * The vertical skewing of the component
     *
     * @var float
     */
    protected float $skewY = 0;

    /**
     * Set component's horizontal skewing.
     *
     * @param  float  $skewX
     * @return BaseComponent
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setSkewX(float $skewX): BaseComponent
    {
        if (!NumberValidator::isValid($skewX)) {
            throw new InvalidArgumentException("SkewX is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->skewX = $skewX;
        return $this;
    }

    /**
     * Set component's vertical skewing.
     *
     * @param  float  $skewY
     * @return BaseComponent
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setSkewY(float $skewY): BaseComponent
    {
        if (!NumberValidator::isValid($skewY)) {
            throw new InvalidArgumentException("SkewY is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->skewY = $skewY;
        return $this;
    }

    /**
     * Get component's horizontal skewing.
     *
     * @return float
     */
    public function getSkewX(): float
    {
        return $this->skewX;
    }

    /**
     * Get component's vertical skewing.
     *
     * @return float
     */
    public function getSkewY(): float
    {
        return $this->skewY;
    }

    /**
     * Set component's width.
     *
     * @param  float  $width
     * @return ImageComponent
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setWidth(float $width): ImageComponent
    {
        if (!NumberValidator::isValid($width, 0.0)) {
            throw new InvalidArgumentException("Width is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->width = $width;
        return $this;
    }

    /**
     * Set component's height.
     *
     * @param  float  $height
     * @return ImageComponent
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setHeight(float $height): ImageComponent
    {
        if (!NumberValidator::isValid($height, 0.0)) {
            throw new InvalidArgumentException("Height is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->height = $height;
        return $this;
    }

    /**
     * Get component's width.
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->width;
    }

    /**
     * Get component's height.
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
        if (empty($source)) {
            throw new InvalidArgumentException("Source is empty. See ".__CLASS__." class's documentation for possible values.");
        }

        /*if (!file_exists($source)) {
            throw new InvalidArgumentException("Source not found. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!is_file($source)) {
            throw new InvalidArgumentException("Source is not a file. See ".__CLASS__." class's documentation for possible values.");
        }*/

        $dimensions = getimagesize($source);
        $mime = $dimensions['mime'] ?? null;

        if (!in_array($mime, $this->validMimes)) {
            throw new InvalidArgumentException("Source is not valid. See ".__CLASS__." class's documentation for possible values.");
        }

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
     * Format component's content.
     *
     * @return ImageComponent
     */
    public function format(): ImageComponent
    {
        $page = $this->getPage();
        $resources = $page->getResources();

        $image = Factory::create('Papier\Type\ImageType', null, true);
        $this->setName(sprintf('Im%d', $image->getNumber()));

        if (!$resources->hasEntry('XObject')) {
            $xObject = Factory::create('Papier\Type\Base\DictionaryType');
            $resources->setEntry('XObject', $xObject);
        }

        if (!$resources->hasEntry('ProcSet')) {
            $procset = Factory::create('Papier\Type\Base\ArrayType', null, true);
            $resources->setEntry('ProcSet', $procset);
        }

		$source = $this->getSource();
        $dimensions = getimagesize($source);

		/** @var ArrayType $procset */
        $procset = $resources->getEntry('ProcSet');
        $channels = $dimensions['channels'] ?? 3;
        $bitsPerComponent = $dimensions['bits'] ?? 8;
        $mime = $dimensions['mime'] ?? null;

        $width = $dimensions[0] ?? 0;
        $height = $dimensions[1] ?? 0;
        $ratio = $height > 0 ? $width / $height : 1;

        if (!$procset->has(ProcedureSet::GRAPHICS)) {
            $graphics = Factory::create('Papier\Type\Base\NameType', ProcedureSet::GRAPHICS);
            $procset->append($graphics);
        }

        if ($channels == 1 && !$procset->has(ProcedureSet::GRAYSCALE_IMAGES)) {
            $imageb = Factory::create('Papier\Type\Base\NameType', ProcedureSet::GRAYSCALE_IMAGES);
            $procset->append($imageb);
        } elseif (!$procset->has(ProcedureSet::COLOUR_IMAGES)) {
            $imagec = Factory::create('Papier\Type\Base\NameType', ProcedureSet::COLOUR_IMAGES);
            $procset->append($imagec);
        }

		$name = $this->getName();
		/** @var DictionaryType $xObject */
        $xObject = $resources->getEntry('XObject');
        $xObject->setEntry($name, $image);

        $image->setWidth($width);
        $image->setHeight($height);

        if ($mime == 'image/jpeg') {
            $image->setFilter(FilterType::DCT_DECODE);
        } else if ($mime == 'image/png') {
            $image->setFilter(FilterType::FLATE_DECODE);

            $params = new FlateDecodeParams();
            $params->setPredictor(Predictor::PNG_OPTIMUM);
            $params->setColumns($width);
            $params->setBitsPerComponent($bitsPerComponent);
            $params->setColors($channels);

            $image->setDecodeParms($params);
        }

		list($data, $mask) = ImageHelper::getDataFromSource($source);
        $image->setContent($data);

		if ($mask) {
			// Add new image as mask
			$sMask = Factory::create('Papier\Type\ImageType', null, true);

			$sMask->setWidth($width);
			$sMask->setHeight($height);

			if ($mime == 'image/png') {
				$sMask->setFilter(FilterType::FLATE_DECODE);

				$maskParams = new FlateDecodeParams();
				$maskParams->setPredictor(Predictor::PNG_OPTIMUM);
				$maskParams->setColumns($width);
				$maskParams->setBitsPerComponent($bitsPerComponent);
				$maskParams->setColors(1);

				$sMask->setDecodeParms($maskParams);
				$sMask->setColorSpace(DeviceColourSpace::GRAY);
			}


			$sMask->setBitsPerComponent($bitsPerComponent);
			$sMask->setContent($mask);

			$image->setSMask($sMask);
		}

        if ($channels == 3) {
            $image->setColorSpace(DeviceColourSpace::RGB);
        } elseif ($channels == 4) {
            $image->setColorSpace(DeviceColourSpace::CMYK);
        } elseif ($channels == 1) {
            $image->setColorSpace(DeviceColourSpace::GRAY);
        }

        $image->setBitsPerComponent($bitsPerComponent);

        $contents = $this->getContents();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        $desiredWidth = $this->getWidth();
        $desiredHeight = $this->getHeight();

        $withInUI = 0;
        $heightInUI = 0;
        if ($desiredWidth && $desiredHeight) {
            $withInUI = $desiredWidth;
            $heightInUI = $desiredHeight;
        } else if ($desiredWidth) {
            $withInUI = $desiredWidth;
            $heightInUI = $withInUI / $ratio;
        } else if ($desiredHeight) {
            $heightInUI = $desiredHeight;
            $withInUI = $heightInUI * $ratio;
        } else {
            $withInUI = $width;
            $heightInUI = $height;
        }

        $image->setWidth($width);
        $image->setHeight($height);

        $transformationMatrix = $this->getTransformationMatrix();

        // For Images, CTM shall contain dimensions and not multiply factors
        $scaleX = $transformationMatrix->getData(0, 0); // Is equal to 1 if no scale applied
        $scaleY = $transformationMatrix->getData(1, 1); // IS equal to 1 if no scale applied

        $this->scale($scaleX * $withInUI * $mmToUserUnit, $scaleY * $heightInUI * $mmToUserUnit);

        $transformationMatrix = $this->getTransformationMatrix();

        $contents->save();
        $contents->setCTM(
            $transformationMatrix->getData(0, 0),
            $transformationMatrix->getData(0, 1),
            $transformationMatrix->getData(1, 0),
            $transformationMatrix->getData(1, 1),
            $transformationMatrix->getData(2, 0),
            $transformationMatrix->getData(2, 1)
        );
        $contents->setCompression(FilterType::FLATE_DECODE);
        $contents->paintXObject($this->getName());
        $contents->restore();

        return $this;
    }
}