<?php

namespace Papier\Type;

use Papier\Document\AnnotationState\AnnotationState;
use Papier\Factory\Factory;
use Papier\Validator\BooleanValidator;
use Papier\Validator\StringValidator;
use RuntimeException;
use InvalidArgumentException;

class TextAnnotationDictionaryType extends MarkupAnnotationDictionaryType
{
	/**
	 * Set whether the annotation shall initially be displayed open
	 *
	 * @param bool $open
	 * @return TextAnnotationDictionaryType
	 */
	public function setOpen(bool $open): TextAnnotationDictionaryType
	{
		if (!BooleanValidator::isValid($open)) {
			throw new InvalidArgumentException("Open is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\BooleanType', $open);
		$this->setEntry('Open', $value);
		return $this;
	}

	/**
	 * Set name of an icon that shall be used in displaying the
	 * annotation
	 *
	 * @param  string  $name
	 * @return TextAnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setName(string $name): TextAnnotationDictionaryType
	{
		if (!StringValidator::isValid($name)) {
			throw new InvalidArgumentException("Name is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $name);

		$this->setEntry('Name', $value);
		return $this;
	}

	/**
	 * Set state to which the original annotation shall be set
	 *
	 * @param  AnnotationState  $state
	 * @return TextAnnotationDictionaryType
	 */
	public function setState(AnnotationState $state): TextAnnotationDictionaryType
	{
		$value = Factory::create('Papier\Type\TextStringType', $state::STATE);
		$this->setEntry('State', $value);

		$value = Factory::create('Papier\Type\TextStringType', $state::STATE_MODEL);
		$this->setEntry('StateModel', $value);

		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Text');
		$this->setEntry('Subtype', $type);

		return parent::format();
	}
}