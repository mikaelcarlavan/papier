<?php

namespace Papier\Type;

use Papier\Object\StreamObject;

use Papier\Graphics\Graphics;
use Papier\Text\Text;
use Papier\Type\Base\StreamType;

class ContentStreamType extends StreamType
{
    use Graphics;
    use Text;
}