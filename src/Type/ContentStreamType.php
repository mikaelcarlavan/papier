<?php

namespace Papier\Type;

use Papier\Object\StreamObject;

use Papier\Graphics\GraphicsState;
use Papier\Graphics\PathConstruction;
use Papier\Graphics\PathPainting;
use Papier\Graphics\ClippingPath;

use Papier\Graphics\Graphics;
use Papier\Text\Text;

class ContentStreamType extends StreamObject
{
    use Graphics;
    use Text;
}