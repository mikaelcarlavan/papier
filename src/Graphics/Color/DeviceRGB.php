<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfName, PdfObject};

final class DeviceRGB extends ColorSpace
{
    public function getName(): string { return 'DeviceRGB'; }
    public function getComponentCount(): int { return 3; }
    public function toPdfObject(): PdfObject { return new PdfName('DeviceRGB'); }
}
