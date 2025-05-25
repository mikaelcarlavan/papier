<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

trait Width
{
	/**
	 * The width of the component
	 *
	 * @var float
	 */
	protected float $width = 0;

	/**
	 * Set component's width.
	 *
	 * @param  float  $width
	 * @return static
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setWidth(float $width): static
	{
		if (!NumberValidator::isValid($width, 0.0)) {
			throw new InvalidArgumentException("Width is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->width = $width;
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
}