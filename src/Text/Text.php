<?php

namespace Papier\Text;

use Papier\Text\TextObject;
use Papier\Text\TextPositioning;
use Papier\Text\TextShowing;
use Papier\Text\TextState;

trait Text
{
    use TextObject, TextPositioning, TextShowing, TextState;
}