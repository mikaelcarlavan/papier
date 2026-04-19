<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfName, PdfObject};

final class DeviceN extends ColorSpace
{
    /**
     * @param string[]   $names          Colorant or process component names.
     * @param ColorSpace $alternateSpace Fallback colour space.
     * @param PdfObject  $tintTransform  Function mapping n tint values to alternate.
     * @param PdfObject|null $attributes Optional attributes dictionary.
     */
    public function __construct(
        private readonly array      $names,
        private readonly ColorSpace $alternateSpace,
        private readonly PdfObject  $tintTransform,
        private readonly ?PdfObject $attributes = null,
    ) {}

    public function getName(): string { return 'DeviceN'; }
    public function getComponentCount(): int { return count($this->names); }

    public function toPdfObject(): PdfObject
    {
        $namesArr = new PdfArray();
        foreach ($this->names as $n) {
            $namesArr->add(new PdfName($n));
        }
        $arr = new PdfArray();
        $arr->add(new PdfName('DeviceN'));
        $arr->add($namesArr);
        $arr->add($this->alternateSpace->toPdfObject());
        $arr->add($this->tintTransform);
        if ($this->attributes !== null) {
            $arr->add($this->attributes);
        }
        return $arr;
    }
}
