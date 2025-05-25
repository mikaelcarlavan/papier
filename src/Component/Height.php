<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

trait Height
{

	/**
	 * The height of the component
	 *
	 * @var float
	 */
	protected float $height = 0;

	/**
	 * Set component's height.
	 *
	 * @param  float  $height
	 * @return static
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setHeight(float $height): static
	{
		if (!NumberValidator::isValid($height, 0.0)) {
			throw new InvalidArgumentException("Height is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->height = $height;
		return $this;
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
}