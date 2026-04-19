<?php

declare(strict_types=1);

namespace Papier\Graphics\Color;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfStream};

/**
 * ICCBased colour space (ISO 32000-1 §8.6.5.5).
 *
 * Specifies a device-independent colour space using an ICC colour profile.
 * The profile is embedded as a stream.
 */
final class ICCBased extends ColorSpace
{
    private PdfStream $profileStream;

    public function __construct(
        private readonly string $profileData,
        private readonly int    $components,   // 1, 3, or 4
        private readonly string $alternate = 'DeviceRGB',
    ) {
        $this->profileStream = new PdfStream();
        $this->profileStream->getDictionary()->set('N', new PdfInteger($components));
        $this->profileStream->getDictionary()->set('Alternate', new PdfName($alternate));
        $this->profileStream->setData($profileData);
        $this->profileStream->compress();
    }

    public function getName(): string { return 'ICCBased'; }
    public function getComponentCount(): int { return $this->components; }

    public function getProfileStream(): PdfStream { return $this->profileStream; }

    public function toPdfObject(): PdfObject
    {
        $arr = new PdfArray();
        $arr->add(new PdfName('ICCBased'));
        // The stream must be an indirect reference; callers must register it
        $arr->add($this->profileStream);
        return $arr;
    }
}
