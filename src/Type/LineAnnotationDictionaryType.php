<?php

namespace Papier\Type;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\AnnotationITValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\LineEndingStyleValidator;
use Papier\Validator\StringValidator;
use RuntimeException;

class LineAnnotationDictionaryType extends DictionaryType
{
	/**
	 * Set the line's start and end points.
	 *
	 * @param array $l
	 * @return LineAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not a valid array with two points.
	 */
	public function setL(array $l): LineAnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($l, 2)) {
			throw new InvalidArgumentException("L is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		// Ensure each point contains 2 numbers (x, y coordinates)
		foreach ($l as $point) {
			if (!is_array($point) || count($point) !== 2) {
				throw new InvalidArgumentException("Point is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $l);
		$this->setEntry('L', $value);
		return $this;
	}

	/**
	 * Set the line ending style (start and end of the line).
	 *
	 * @param string $le
	 * @return LineAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not a valid line ending style.
	 */
	public function setLE(string $le): LineAnnotationDictionaryType
	{
		if (!LineEndingStyleValidator::isValid($le)) {
			throw new InvalidArgumentException("LE is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\NameType', $le);
		$this->setEntry('LE', $value);
		return $this;
	}

	/**
	 * Set border style dictionary.
	 *
	 * @param BorderStyleDictionaryType $bs
	 * @return LineAnnotationDictionaryType
	 */
	public function setBS(BorderStyleDictionaryType $bs): LineAnnotationDictionaryType
	{
		$this->setEntry('BS', $bs);
		return $this;
	}

	/**
	 * Set color of the line annotation (optional).
	 *
	 * @param array $c
	 * @return LineAnnotationDictionaryType
	 * @throws InvalidArgumentException if the color array is not valid.
	 */
	public function setC(array $c): LineAnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($c, 3)) {
			throw new InvalidArgumentException("C is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $c);
		$this->setEntry('C', $value);
		return $this;
	}

	/**
	 * Set the rectangle that defines the position of the annotation.
	 *
	 * @param array $rect
	 * @return LineAnnotationDictionaryType
	 * @throws InvalidArgumentException if the rectangle array is not valid.
	 */
	public function setRect(array $rect): LineAnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($rect, 4)) {
			throw new InvalidArgumentException("Rect is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $rect);
		$this->setEntry('Rect', $value);
		return $this;
	}

	/**
	 * Set the annotation's intent (optional).
	 *
	 * @param string $it
	 * @return LineAnnotationDictionaryType
	 * @throws InvalidArgumentException if the intent is not valid.
	 */
	public function setIT(string $it): LineAnnotationDictionaryType
	{
		if (!AnnotationITValidator::isValid($it)) {
			throw new InvalidArgumentException("IT is incorrect. Please refer to the PDF specification for valid intents.");
		}

		$value = Factory::create('Papier\Type\Base\NameType', $it);
		$this->setEntry('IT', $value);
		return $this;
	}


	/**
	 * Format the object into a string (final PDF syntax).
	 *
	 * @return string
	 */
	public function format(): string
	{
		// Set the type of annotation as Line.
		$type = Factory::create('Papier\Type\Base\NameType', 'Line');
		$this->setEntry('Subtype', $type);

		if (!$this->hasEntry('Rect')) {
			throw new RuntimeException("Shading is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!$this->hasEntry('L')) {
			throw new RuntimeException("Shading is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		// Ensure mandatory entries (like Rect and L) are present.
		if (!$this->hasEntry('Rect')) {
			throw new InvalidArgumentException("Rect is required for a Line annotation.");
		}
		if (!$this->hasEntry('L')) {
			throw new InvalidArgumentException("L is required for a Line annotation.");
		}

		return parent::format();
	}
}