<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

trait Size
{
	/**
	 * The width of the component
	 *
	 * @var float
	 */
	protected float $width = 0;

	/**
	 * The height of the component
	 *
	 * @var float
	 */
	protected float $height = 0;

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
	 * Get component's width.
	 *
	 * @return float
	 */
	public function getWidth(): float
	{
		return $this->width;
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