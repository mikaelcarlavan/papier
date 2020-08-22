<?php

namespace Papier\Graphics;

use Papier\Graphics\GraphicsState;
use Papier\Graphics\PathConstruction;
use Papier\Graphics\PathPainting;
use Papier\Graphics\ClippingPath;
use Papier\Graphics\DeviceColour;

trait Graphics
{
    use GraphicsState, PathConstruction, PathPainting, ClippingPath, DeviceColour;
}