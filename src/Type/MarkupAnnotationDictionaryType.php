<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DateType;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\AnnotationTypeValidator;
use Papier\Validator\DateValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\RelationshipTypeValidator;
use Papier\Validator\StringValidator;
use RuntimeException;
use InvalidArgumentException;
class MarkupAnnotationDictionaryType extends AnnotationDictionaryType
{
	/**
	 * Set text label that shall be displayed in the title bar of the annotation’s pop-up window when open and active
	 *
	 * @param  string  $t
	 * @return MarkupAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setT(string $t): MarkupAnnotationDictionaryType
	{
		if (!StringValidator::isValid($t)) {
			throw new InvalidArgumentException("T is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\TextStringType', $t);

		$this->setEntry('T', $value);
		return $this;
	}

	/**
	 * Set pop-up annotation for entering or editing the text associated with this annotation
	 *
	 * @param DictionaryType $popup
	 * @return MarkupAnnotationDictionaryType
	 */
	public function setPopup(DictionaryType $popup): MarkupAnnotationDictionaryType
	{
		$this->setEntry('Popup', $popup);
		return $this;
	}

	/**
	 * Set opacity value that shall be used in painting the annotation
	 *
	 * @param  mixed  $ca
	 * @return MarkupAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'int' or 'float'.
	 */
	public function setCA(mixed $ca): MarkupAnnotationDictionaryType
	{
		if (!NumberValidator::isValid($ca, 0, 1)) {
			throw new InvalidArgumentException("CA is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\NumberType', $ca);

		$this->setEntry('CA', $value);
		return $this;
	}

	/**
	 * Set date and time when the annotation was created
	 *
	 * @param  mixed  $creationDate
	 * @return MarkupAnnotationDictionaryType
	 */
	public function setCreationDate(mixed $creationDate): MarkupAnnotationDictionaryType
	{
		if (!DateValidator::isValid($creationDate)) {
			throw new InvalidArgumentException("CreationDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\DateType', $creationDate);

		$this->setEntry('CreationDate', $value);
		return $this;
	}

	/**
	 * Set reference to the annotation that this annotation is “in reply to.”
	 *
	 * @param DictionaryType $irt
	 * @return MarkupAnnotationDictionaryType
	 */
	public function setIRT(DictionaryType $irt): MarkupAnnotationDictionaryType
	{
		$this->setEntry('IRT', $irt);
		return $this;
	}

	/**
	 * Set text representing a short description of the subject being addressed by the annotation
	 *
	 * @param  string  $subj
	 * @return MarkupAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setSubj(string $subj): MarkupAnnotationDictionaryType
	{
		if (!StringValidator::isValid($subj)) {
			throw new InvalidArgumentException("Subj is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\TextStringType', $subj);

		$this->setEntry('Subj', $value);
		return $this;
	}

	/**
	 * Set relationship (the “reply type”) between this annotation and one specified by IRT.
	 *
	 * @param  string  $rt
	 * @return MarkupAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setRT(string $rt): MarkupAnnotationDictionaryType
	{
		if (!RelationshipTypeValidator::isValid($rt)) {
			throw new InvalidArgumentException("RT is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $rt);

		$this->setEntry('RT', $value);
		return $this;
	}

	/**
	 * Set name describing the intent of the markup annotation
	 *
	 * @param  string  $it
	 * @return MarkupAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setIT(string $it): MarkupAnnotationDictionaryType
	{
		if (!StringValidator::isValid($it)) {
			throw new InvalidArgumentException("IT is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $it);

		$this->setEntry('IT', $value);
		return $this;
	}

	/**
	 * Set dictionary specifying data that shall be associated with the annotation
	 *
	 * @param ExDataDictionaryType $exData
	 * @return MarkupAnnotationDictionaryType
	 */
	public function setExData(ExDataDictionaryType $exData): MarkupAnnotationDictionaryType
	{
		$this->setEntry('ExData', $exData);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		if ($this->hasEntry('RT') && !$this->hasEntry('IRT')) {
			throw new RuntimeException("IRT is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		return parent::format();
	}
}