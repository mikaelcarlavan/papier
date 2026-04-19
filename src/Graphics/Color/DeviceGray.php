<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfName, PdfObject};

final class DeviceGray extends ColorSpace
{
    public function getName(): string { return 'DeviceGray'; }
    public function getComponentCount(): int { return 1; }
    public function toPdfObject(): PdfObject { return new PdfName('DeviceGray'); }
}
