<?php

namespace Papier\Type;

use Papier\Document\AnnotationHighlightMode;
use Papier\Factory\Factory;
use Papier\Font\TrueType\TrueTypeFontHorizontalMetricsTable;
use Papier\Helpers\MetricHelper;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\AnnotationHighlightModeValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;

class LinkAnnotationDictionaryType extends AnnotationDictionaryType
{
	/**
	 * Set action that shall be performed when the link
	 * annotation is activated
	 *
	 * @param DictionaryType $a
	 * @return LinkAnnotationDictionaryType
	 */
	public function setA(DictionaryType $a): LinkAnnotationDictionaryType
	{
		$this->setEntry('A', $a);
		return $this;
	}


	/**
	 * Set annotation’s highlighting mode, the visual effect
	 * that shall be used when the mouse button is pressed or held down
	 * inside its active area
	 *
	 * @param  string  $h
	 * @return LinkAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setH(string $h): LinkAnnotationDictionaryType
	{
		if (!AnnotationHighlightModeValidator::isValid($h)) {
			throw new InvalidArgumentException("H is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $h);

		$this->setEntry('H', $value);
		return $this;
	}


	/**
	 * Set URI action formerly
	 * associated with this annotation
	 *
	 * @param DictionaryType $pa
	 * @return LinkAnnotationDictionaryType
	 */
	public function setPA(DictionaryType $pa): LinkAnnotationDictionaryType
	{
		$this->setEntry('PA', $pa);
		return $this;
	}

	/**
	 * Set array of 8 × n numbers specifying the
	 * coordinates of n quadrilaterals in default user space that comprise the
	 * region in which the link should be activated
	 *
	 * @param array $quadPoints
	 * @return LinkAnnotationDictionaryType
	 */
	public function setQuadPoints(array $quadPoints): LinkAnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($quadPoints)) {
			throw new InvalidArgumentException("Quad points is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$nQuadPoints = count($quadPoints);

		if ($nQuadPoints % 8 != 0) {
			throw new InvalidArgumentException("Quad points size should be a multiple of 8. See ".__CLASS__." class's documentation for possible values.");
		}

		$nQuadPoints = MetricHelper::toUserUnit($nQuadPoints);

		$value = Factory::create('Papier\Type\Base\ArrayType', $nQuadPoints);

		$this->setEntry('QuadPoints', $value);
		return $this;
	}

	/**
	 * Set border style dictionary
	 *
	 * @param BorderStyleDictionaryType $bs
	 * @return LinkAnnotationDictionaryType
	 */
	public function setBS(BorderStyleDictionaryType $bs): LinkAnnotationDictionaryType
	{
		$this->setEntry('BS', $bs);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Link');
		$this->setEntry('Subtype', $type);

		return parent::format();
	}
}