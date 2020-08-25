<?php

namespace Papier\Graphics;

use Papier\Graphics\PathConstruction;
use Papier\Graphics\PathPainting;
use Papier\Graphics\ClippingPath;

trait Path
{
    use ClippingPath, PathConstruction, PathPainting;
}