<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Helpers\MetricHelper;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\NumbersArrayValidator;
use InvalidArgumentException;

class SquareAnnotationDictionaryType extends AnnotationDictionaryType
{
	/**
	 * Set border style dictionary
	 *
	 * @param BorderStyleDictionaryType $bs
	 * @return SquareAnnotationDictionaryType
	 */
	public function setBS(BorderStyleDictionaryType $bs): SquareAnnotationDictionaryType
	{
		$this->setEntry('BS', $bs);
		return $this;
	}

	/**
	 * Set interior color with which to fill the annotation’s
	 * rectangle or ellipse
	 *
	 * @param  mixed  $colors
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'array'.
	 */
	public function setIC(...$colors): AnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($colors)) {
			throw new InvalidArgumentException("IC is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\NumbersArrayType', $colors);

		$this->setEntry('IC', $value);
		return $this;
	}

	/**
	 * Set border effect dictionary
	 *
	 * @param BorderEffectDictionaryType $be
	 * @return SquareAnnotationDictionaryType
	 */
	public function setBE(BorderEffectDictionaryType $be): SquareAnnotationDictionaryType
	{
		$this->setEntry('BE', $be);
		return $this;
	}

	/**
	 * Set rectangle that shall describe the numerical differences between two rectangles: the Rect entry of the
	 * annotation and the actual boundaries of the underlying square or circle.
	 *
	 * @param  array  $rd
	 * @return AnnotationDictionaryType
	 */
	public function setRD(array $rd): AnnotationDictionaryType
	{
		if (!NumbersArrayValidator::isValid($rd)) {
			throw new InvalidArgumentException("RD is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$rd = MetricHelper::toUserUnit($rd);

		$value = Factory::create('Papier\Type\RectangleNumbersArrayType', $rd);
		$this->setEntry('RD', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Square');
		$this->setEntry('Subtype', $type);

		return parent::format();
	}
}