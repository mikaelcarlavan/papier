<?php

declare(strict_types=1);

namespace Papier\Graphics\Image;

use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfStream};

/**
 * PDF Image XObject (ISO 32000-1 §8.9).
 *
 * A sampled image described by its width, height, colour space, bits per
 * component, and pixel data.  The stream data may be compressed.
 */
abstract class PdfImage
{
    protected PdfStream $stream;
    protected int       $width;
    protected int       $height;
    protected int       $bitsPerComponent = 8;
    protected string    $colorSpace       = 'DeviceRGB';
    protected ?string   $mask             = null;
    protected bool      $imageMask        = false;
    protected ?PdfObject $softMask        = null;
    protected ?PdfObject $decode          = null;
    protected bool      $interpolate      = false;

    public function __construct()
    {
        $this->stream = new PdfStream();
        $this->stream->getDictionary()->set('Type', new PdfName('XObject'));
        $this->stream->getDictionary()->set('Subtype', new PdfName('Image'));
    }

    public function getWidth(): int { return $this->width; }
    public function getHeight(): int { return $this->height; }

    public function setColorSpace(string $cs): static
    {
        $this->colorSpace = $cs;
        return $this;
    }

    public function setBitsPerComponent(int $bpc): static
    {
        $this->bitsPerComponent = $bpc;
        return $this;
    }

    public function setInterpolate(bool $interpolate): static
    {
        $this->interpolate = $interpolate;
        return $this;
    }

    public function setImageMask(bool $imageMask): static
    {
        $this->imageMask = $imageMask;
        return $this;
    }

    public function setSoftMask(PdfObject $softMask): static
    {
        $this->softMask = $softMask;
        return $this;
    }

    public function setDecode(PdfObject $decode): static
    {
        $this->decode = $decode;
        return $this;
    }

    /** Build and return the fully populated image stream. */
    public function getStream(): PdfStream
    {
        $dict = $this->stream->getDictionary();
        $dict->set('Width', new PdfInteger($this->width));
        $dict->set('Height', new PdfInteger($this->height));

        if ($this->imageMask) {
            $dict->set('ImageMask', new PdfBoolean(true));
        } else {
            // ColorSpace may have been set directly (e.g. Indexed array) — don't overwrite.
            if (!$dict->has('ColorSpace') && $this->colorSpace !== '') {
                $dict->set('ColorSpace', new PdfName($this->colorSpace));
            }
            $dict->set('BitsPerComponent', new PdfInteger($this->bitsPerComponent));
        }

        if ($this->interpolate) {
            $dict->set('Interpolate', new PdfBoolean(true));
        }
        if ($this->softMask !== null) {
            $dict->set('SMask', $this->softMask);
        }
        if ($this->decode !== null) {
            $dict->set('Decode', $this->decode);
        }

        return $this->stream;
    }
}
