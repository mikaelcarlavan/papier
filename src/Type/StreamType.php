<?php

namespace Papier\Type;

use Papier\Object\StreamObject;

use Papier\Graphics\GraphicsState;
use Papier\Graphics\PathConstruction;
use Papier\Graphics\PathPainting;
use Papier\Graphics\ClippingPath;

class StreamType extends StreamObject
{
    use GraphicsState, PathConstruction, PathPainting, ClippingPath;
}