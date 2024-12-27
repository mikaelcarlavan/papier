<?php

namespace Papier\Type;

use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\ArrayValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RenditionTypeValidator;
use InvalidArgumentException;

class MediaCriteriaDictionaryType extends DictionaryType
{
	/**
	 * Set user's preference for whether to hear audio descriptions
	 *
	 * @param bool $a
	 * @return MediaCriteriaDictionaryType
	 */
	public function setA(bool $a): MediaCriteriaDictionaryType
	{
		if (!BooleanValidator::isValid($a)) {
			throw new InvalidArgumentException("A is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\BooleanType', $a);

		$this->setEntry('A', $value);

		return $this;
	}

	/**
	 * Set user's preference for whether to see text captions
	 *
	 * @param bool $c
	 * @return MediaCriteriaDictionaryType
	 */
	public function setC(bool $c): MediaCriteriaDictionaryType
	{
		if (!BooleanValidator::isValid($c)) {
			throw new InvalidArgumentException("C is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\BooleanType', $c);

		$this->setEntry('C', $value);

		return $this;
	}

	/**
	 * Set user's preference for whether to hear audio overdubs
	 *
	 * @param bool $o
	 * @return MediaCriteriaDictionaryType
	 */
	public function setO(bool $o): MediaCriteriaDictionaryType
	{
		if (!BooleanValidator::isValid($o)) {
			throw new InvalidArgumentException("O is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\BooleanType', $o);

		$this->setEntry('O', $value);

		return $this;
	}

	/**
	 * Set user's preference for whether to see subtitles
	 *
	 * @param bool $s
	 * @return MediaCriteriaDictionaryType
	 */
	public function setS(bool $s): MediaCriteriaDictionaryType
	{
		if (!BooleanValidator::isValid($s)) {
			throw new InvalidArgumentException("S is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\BooleanType', $s);

		$this->setEntry('S', $value);

		return $this;
	}

	/**
	 * Set system’s bandwidth (in bits per second)
	 *
	 * @param int $r
	 * @return MediaCriteriaDictionaryType
	 */
	public function setR(int $r): MediaCriteriaDictionaryType
	{
		if (!IntegerValidator::isValid($r)) {
			throw new InvalidArgumentException("R is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\IntegerType', $r);

		$this->setEntry('R', $value);

		return $this;
	}

	/**
	 * Set dictionary specifying the minimum bit depth
	 *
	 * @param  DictionaryType  $d
	 * @return MediaCriteriaDictionaryType
	 */
	public function setD(DictionaryType $d): MediaCriteriaDictionaryType
	{
		$this->setEntry('D', $d);
		return $this;
	}

	/**
	 * Set dictionary specifying the minimum screen size
	 *
	 * @param  DictionaryType  $z
	 * @return MediaCriteriaDictionaryType
	 */
	public function setZ(DictionaryType $z): MediaCriteriaDictionaryType
	{
		$this->setEntry('Z', $z);
		return $this;
	}

	/**
	 * Set array of software identifier objects
	 *
	 * @param  array<mixed>  $v
	 * @return MediaCriteriaDictionaryType
	 */
	public function setV(array $v): MediaCriteriaDictionaryType
	{
		if (!ArrayValidator::isValid($v)) {
			throw new InvalidArgumentException("V is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $v);

		$this->setEntry('V', $value);
		return $this;
	}

	/**
	 * Set array of name objects specifying a minimum and optionally a maximum PDF language version
	 *
	 * @param  array<mixed>  $p
	 * @return MediaCriteriaDictionaryType
	 */
	public function setP(array $p): MediaCriteriaDictionaryType
	{
		if (!ArrayValidator::isValid($p)) {
			throw new InvalidArgumentException("P is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $p);

		$this->setEntry('P', $value);
		return $this;
	}

	/**
	 * Set array of language identifiers
	 *
	 * @param  array<mixed>  $l
	 * @return MediaCriteriaDictionaryType
	 */
	public function setL(array $l): MediaCriteriaDictionaryType
	{
		if (!ArrayValidator::isValid($l)) {
			throw new InvalidArgumentException("L is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$value = Factory::create('Papier\Type\Base\ArrayType', $l);

		$this->setEntry('L', $value);
		return $this;
	}

	/**
	 * Format object's value.
	 *
	 * @return string
	 */
	public function format(): string
	{
		$type = Factory::create('Papier\Type\Base\NameType', 'MediaCriteria');
		$this->setEntry('Type', $type);

		return parent::format();
	}
}