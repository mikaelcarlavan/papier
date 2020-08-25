<?php

namespace Papier\Graphics;

use Papier\Graphics\GraphicsState;
use Papier\Graphics\Colour;
use Papier\Graphics\Path;
use Papier\Graphics\Shading;
use Papier\Graphics\XObject;

trait Graphics
{
    use Path, Colour, GraphicsState, Shading, XObject;
}