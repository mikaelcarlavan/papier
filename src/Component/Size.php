<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

trait Size
{
	use Width, Height;
}