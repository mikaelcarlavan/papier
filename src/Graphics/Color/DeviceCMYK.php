<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfName, PdfObject};

final class DeviceCMYK extends ColorSpace
{
    public function getName(): string { return 'DeviceCMYK'; }
    public function getComponentCount(): int { return 4; }
    public function toPdfObject(): PdfObject { return new PdfName('DeviceCMYK'); }
}
