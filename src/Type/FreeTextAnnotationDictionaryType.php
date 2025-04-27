<?php

namespace Papier\Type;

use Papier\Document\AnnotationState\AnnotationState;
use Papier\Factory\Factory;
use Papier\Helpers\MetricHelper;
use Papier\Validator\AnnotationITValidator;
use Papier\Validator\AnnotationJustificationValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\LineEndingStyleValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\StringValidator;
use InvalidArgumentException;
use RuntimeException;

class FreeTextAnnotationDictionaryType extends AnnotationDictionaryType
{
	/**
	 * Set default appearance string that shall be used in formatting
	 * the text
	 *
	 * @param  ContentStreamType  $da
	 * @return FreeTextAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setDA(ContentStreamType $da): FreeTextAnnotationDictionaryType
	{
		$content = $da->getContent();
		// Remove EOL
		$content = str_replace(\Papier\Object\BaseObject::EOL_MARKER, "", $content);
		$value = Factory::create('Papier\Type\LiteralStringType', $content);

		$this->setEntry('DA', $value);
		return $this;
	}

	/**
	 * Set code specifying the form of quadding (justification)
	 * that shall be used in displaying the annotation’s text
	 *
	 * @param  int  $q
	 * @return FreeTextAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setQ(int $q): FreeTextAnnotationDictionaryType
	{
		if (!AnnotationJustificationValidator::isValid($q)) {
			throw new InvalidArgumentException("Q is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\IntegerType', $q);

		$this->setEntry('Q', $value);
		return $this;
	}

	/**
	 * Set default style string
	 *
	 * @param  string  $ds
	 * @return FreeTextAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setDS(string $ds): FreeTextAnnotationDictionaryType
	{
		if (!StringValidator::isValid($ds)) {
			throw new InvalidArgumentException("DS is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\TextStringType', $ds);

		$this->setEntry('DS', $value);
		return $this;
	}

	/**
	 * Set rich text string
	 * that shall be used to generate the appearance of the annotation
	 *
	 * @param mixed $rc
	 * @return FreeTextAnnotationDictionaryType
	 */
	public function setRC(mixed $rc): FreeTextAnnotationDictionaryType
	{
		if (!$rc instanceof TextStreamType && !StringValidator::isValid($rc)) {
			throw new InvalidArgumentException("RC is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = $rc instanceof TextStreamType ? $rc : Factory::create('Papier\Type\TextStringType', $rc);

		$this->setEntry('RC', $value);
		return $this;
	}

	/**
	 * Set border effect dictionary
	 *
	 * @param BorderEffectDictionaryType $be
	 * @return FreeTextAnnotationDictionaryType
	 */
	public function setBE(BorderEffectDictionaryType $be): FreeTextAnnotationDictionaryType
	{
		$this->setEntry('BE', $be);
		return $this;
	}

	/**
	 * Set name describing the intent of the markup annotation
	 *
	 * @param  string  $it
	 * @return FreeTextAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setIT(string $it): FreeTextAnnotationDictionaryType
	{
		if (!AnnotationITValidator::isValid($it)) {
			throw new InvalidArgumentException("IT is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $it);

		$this->setEntry('IT', $value);
		return $this;
	}

	/**
	 * Set array of
	 * four or six numbers specifying a callout line attached to the free text
	 * annotation
	 *
	 * @param array $cl
	 * @return FreeTextAnnotationDictionaryType
	 */
	public function setCL(array $cl): FreeTextAnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($cl, 4) && !ArrayValidator::isValid($cl, 6)) {
			throw new InvalidArgumentException("CL is not valid. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $cl);

		$this->setEntry('CL', $value);
		return $this;
	}

	/**
	 * Set rectangle that shall describe the numerical differences between two rectangles: the Rect entry of the
	 * annotation and the actual boundaries of the underlying square or circle.
	 *
	 * @param  array  $rd
	 * @return FreeTextAnnotationDictionaryType
	 */
	public function setRD(array $rd): FreeTextAnnotationDictionaryType
	{
		if (!NumbersArrayValidator::isValid($rd, 4)) {
			throw new InvalidArgumentException("RD is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$rd = MetricHelper::toUserUnit($rd);

		$value = Factory::create('Papier\Type\RectangleNumbersArrayType', $rd);
		$this->setEntry('RD', $value);
		return $this;
	}

	/**
	 * Set border style dictionary
	 *
	 * @param BorderStyleDictionaryType $bs
	 * @return FreeTextAnnotationDictionaryType
	 */
	public function setBS(BorderStyleDictionaryType $bs): FreeTextAnnotationDictionaryType
	{
		$this->setEntry('BS', $bs);
		return $this;
	}

	/**
	 * Set line ending style that shall be used in drawing the callout line
	 *
	 * @param  string  $le
	 * @return FreeTextAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setLE(string $le): FreeTextAnnotationDictionaryType
	{
		if (!LineEndingStyleValidator::isValid($le)) {
			throw new InvalidArgumentException("LE is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $le);

		$this->setEntry('LE', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'FreeText');
		$this->setEntry('Subtype', $type);

		if (!$this->hasEntry('DA')) {
			$this->setDA('');
		}

		return parent::format();
	}
}