<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Helpers\MetricHelper;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;
use Papier\Object\StringObject;
use Papier\Papier;
use Papier\Type\Base\DateType;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\AnnotationTypeValidator;
use Papier\Validator\ArrayValidator;
use Papier\Validator\DateValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\StringValidator;
use RuntimeException;
use InvalidArgumentException;

class AnnotationDictionaryType extends DictionaryType
{
	/**
	 * Set subtype.
	 *
	 * @param  string  $subtype
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 * @return AnnotationDictionaryType
	 */
	public function setSubtype(string $subtype): AnnotationDictionaryType
	{
		if (!AnnotationTypeValidator::isValid($subtype)) {
			throw new InvalidArgumentException("Subtype is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $subtype);

		$this->setEntry('Subtype', $value);
		return $this;
	}

	/**
	 * Set rectangle defining the location of the annotation on the page in default user space units.
	 *
	 * @param  array  $rect
	 * @return AnnotationDictionaryType
	 */
	public function setRect(array $rect): AnnotationDictionaryType
	{
		if (!NumbersArrayValidator::isValid($rect)) {
			throw new InvalidArgumentException("Contents is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$rect = MetricHelper::toUserUnit($rect);

		$value = Factory::create('Papier\Type\RectangleNumbersArrayType', $rect);
		$this->setEntry('Rect', $value);
		return $this;
	}

	/**
	 * Set text that shall be displayed for the annotation
	 *
	 * @param  string  $contents
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setContents(string $contents): AnnotationDictionaryType
	{
		if (!StringValidator::isValid($contents)) {
			throw new InvalidArgumentException("Contents is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\TextStringType', $contents);

		$this->setEntry('Contents', $value);
		return $this;
	}

	/**
	 * Set page object with which this annotation is associated.
	 *
	 * @param DictionaryType $p
	 * @return AnnotationDictionaryType
	 */
	public function setP(DictionaryType $p): AnnotationDictionaryType
	{
		$this->setEntry('P', $p);
		return $this;
	}

	/**
	 * Set text string uniquely identifying it among all the annotations on its page.
	 *
	 * @param  string  $nm
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setNM(string $nm): AnnotationDictionaryType
	{
		if (!StringValidator::isValid($nm)) {
			throw new InvalidArgumentException("NM is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\TextStringType', $nm);

		$this->setEntry('NM', $value);
		return $this;
	}

	/**
	 * Set date and time when the annotation was most recently modified
	 *
	 * @param  mixed  $m
	 * @return AnnotationDictionaryType
	 */
	public function setM(mixed $m): AnnotationDictionaryType
	{
		if (!DateValidator::isValid($m)) {
			throw new InvalidArgumentException("M is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\DateType', $m);

		$this->setEntry('M', $value);
		return $this;
	}

	/**
	 * Set flags specifying various characteristics of the annotation
	 *
	 * @param  int  $f
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'int'.
	 */
	public function setF(int $f): AnnotationDictionaryType
	{
		if (!AnnotationTypeValidator::isValid($f)) {
			throw new InvalidArgumentException("F is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\IntegerType', $f);

		$this->setEntry('F', $value);
		return $this;
	}

	/**
	 * Set how the annotation shall be presented visually on the page
	 *
	 * @param DictionaryType $ap
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setAP(DictionaryType $ap): AnnotationDictionaryType
	{
		$this->setEntry('AP', $ap);
		return $this;
	}

	/**
	 * Set applicable appearance stream from an appearance sub-dictionary
	 *
	 * @param  string  $as
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'string'.
	 */
	public function setAS(string $as): AnnotationDictionaryType
	{
		if (!StringValidator::isValid($as)) {
			throw new InvalidArgumentException("AS is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\NameType', $as);

		$this->setEntry('AS', $value);
		return $this;
	}

	/**
	 * Set the characteristics of the annotation’s border, which shall be drawn as a rounded rectangle
	 *
	 * @param  array  $border
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'array'.
	 */
	public function setBorder(array $border): AnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($border)) {
			throw new InvalidArgumentException("Border is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\ArrayType', $border);

		$this->setEntry('Border', $value);
		return $this;
	}

	/**
	 * Set colour used for the annotation
	 *
	 * @param  mixed  $colors
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'array'.
	 */
	public function setC(...$colors): AnnotationDictionaryType
	{
		if (!ArrayValidator::isValid($colors)) {
			throw new InvalidArgumentException("C is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\ArrayType', $colors);

		$this->setEntry('C', $value);
		return $this;
	}

	/**
	 * Set annotation’s entry in the structural parent tree
	 *
	 * @param  int  $structParent
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject'.
	 */
	public function setStructParent(int $structParent): AnnotationDictionaryType
	{
		if (!IntegerValidator::isValid($structParent)) {
			throw new InvalidArgumentException("StructParent is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}
		$value = Factory::create('Papier\Type\Base\IntegerType', $structParent);

		$this->setEntry('StructParent', $value);
		return $this;
	}

	/**
	 * Set optional content properties for the annotation
	 *
	 * @param DictionaryType $oc
	 * @return AnnotationDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryType'.
	 */
	public function setOC(DictionaryType $oc): AnnotationDictionaryType
	{
		$this->setEntry('OC', $oc);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'Annot');
		$this->setEntry('Type', $type);

		if (!$this->hasEntry('Subtype')) {
			throw new RuntimeException("Subtype is missing. See ".__CLASS__." class's documentation for possible values.");
		}

		return parent::format();
	}

	/**
	 * Create object from string.
	 *
	 * @param string $data
	 * @return AnnotationDictionaryType
	 */
	public static function fromString(string $data): AnnotationDictionaryType
	{
		$object = new AnnotationDictionaryType();

		// Parse the dictionary using parent parser
		$dict = parent::fromString($data);

		// Copy all entries into AnnotationDictionaryType
		foreach ($dict->getKeys() as $key) {
			$object->setEntry($key, $dict->getEntry($key));
		}

		// Wrap specific entries in the correct object types
		$stringKeys = ['Subtype', 'Contents', 'NM', 'AS'];
		foreach ($stringKeys as $key) {
			if ($object->hasEntry($key) && !($object->getEntry($key) instanceof StringObject)) {
				$value = $object->getEntry($key);
				$object->setEntry($key, Factory::create('Papier\Object\StringObject', $value));
			}
		}

		if ($object->hasEntry('Rect') && !($object->getEntry('Rect') instanceof RectangleNumbersArrayType)) {
			$value = $object->getEntry('Rect');
			$object->setEntry('Rect', Factory::create('Papier\Type\RectangleNumbersArrayType', $value));
		}

		if ($object->hasEntry('P') && !($object->getEntry('P') instanceof DictionaryType)) {
			$value = $object->getEntry('P');
			$object->setEntry('P', Factory::create('Papier\Type\Base\DictionaryType', $value));
		}

		if ($object->hasEntry('M') && !($object->getEntry('M') instanceof DateType)) {
			$value = $object->getEntry('M');
			$object->setEntry('M', Factory::create('Papier\Type\Base\DateType', $value));
		}

		$intKeys = ['F', 'StructParent'];
		foreach ($intKeys as $key) {
			if ($object->hasEntry($key) && !($object->getEntry($key) instanceof IntegerObject)) {
				$value = $object->getEntry($key);
				$object->setEntry($key, Factory::create('Papier\Object\IntegerObject', $value));
			}
		}

		$arrayKeys = ['Border', 'C'];
		foreach ($arrayKeys as $key) {
			if ($object->hasEntry($key) && !($object->getEntry($key) instanceof ArrayObject)) {
				$value = $object->getEntry($key);
				$object->setEntry($key, Factory::create('Papier\Object\ArrayObject', $value));
			}
		}

		$dictKeys = ['AP', 'OC'];
		foreach ($dictKeys as $key) {
			if ($object->hasEntry($key) && !($object->getEntry($key) instanceof DictionaryType)) {
				$value = $object->getEntry($key);
				$object->setEntry($key, Factory::create('Papier\Type\Base\DictionaryType', $value));
			}
		}

		return $object;
	}

}