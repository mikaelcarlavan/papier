<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;
use Papier\Graphics\Image\JpegImage;
use Papier\Graphics\Image\PngImage;
use Papier\Structure\PdfResources;

/**
 * Raster image element — embeds a JPEG or PNG into a page as an XObject.
 *
 * Images are registered in the page's XObject resource dictionary the first
 * time they are rendered.  Identical images (same byte content) share a
 * single XObject across multiple placements thanks to a content-hash based
 * resource name.
 *
 *   Image::fromJpeg(file_get_contents('photo.jpg'))
 *       ->at(72, 500)
 *       ->size(200, 150);
 *
 *   Image::fromFile('/path/to/image.png')
 *       ->at(72, 400)
 *       ->fitWidth(300)   // scale proportionally to 300 pt wide
 *       ->opacity(0.6);
 *
 * Coordinate and size values are in user-space points.  The `x`/`y` values
 * specify the lower-left corner of the image rectangle.
 */
final class Image implements Element
{
    private float $x           = 0;
    private float $y           = 0;
    private float $width       = 0;   // 0 → use natural pixel width as points
    private float $height      = 0;   // 0 → use natural pixel height as points
    private float $opacity     = 1.0;

    private function __construct(
        private readonly string $bytes,
        private readonly string $type,           // 'jpeg' | 'png'
        private readonly int    $naturalWidth,
        private readonly int    $naturalHeight,
    ) {}

    // ── Factories ─────────────────────────────────────────────────────────────

    /**
     * Create from raw JPEG bytes.
     *
     * @param string $bytes  Raw JPEG file content (e.g. from `file_get_contents()`).
     */
    public static function fromJpeg(string $bytes): self
    {
        $size = @getimagesizefromstring($bytes);
        return new self($bytes, 'jpeg', $size[0] ?? 0, $size[1] ?? 0);
    }

    /**
     * Create from raw PNG bytes.
     *
     * PNG images with an alpha channel are handled automatically: the alpha
     * channel is extracted into a separate greyscale SMask XObject and linked
     * to the colour image dictionary.
     *
     * @param string $bytes  Raw PNG file content.
     */
    public static function fromPng(string $bytes): self
    {
        $size = @getimagesizefromstring($bytes);
        return new self($bytes, 'png', $size[0] ?? 0, $size[1] ?? 0);
    }

    /**
     * Load a JPEG or PNG image from disk, auto-detecting the type.
     *
     * The type is determined by checking the PNG magic bytes (`\x89PNG`).
     * All other files are treated as JPEG.
     *
     * @param string $path  Absolute or relative filesystem path.
     * @throws \RuntimeException  If the file cannot be read.
     */
    public static function fromFile(string $path): self
    {
        $bytes = file_get_contents($path);
        if ($bytes === false) {
            throw new \RuntimeException("Cannot read image file: $path");
        }
        $type = str_starts_with($bytes, "\x89PNG") ? 'png' : 'jpeg';
        $size = @getimagesizefromstring($bytes);
        return new self($bytes, $type, $size[0] ?? 0, $size[1] ?? 0);
    }

    // ── Fluent position / size ─────────────────────────────────────────────────

    /**
     * Set the lower-left corner of the image rectangle.
     *
     * @param float $x  Horizontal position in points.
     * @param float $y  Vertical position in points (lower-left origin).
     */
    public function at(float $x, float $y): self
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }

    /**
     * Set explicit display dimensions in points.
     *
     * Both dimensions are required; if either is 0, the natural pixel size
     * is used for that axis.
     *
     * @param float $width   Display width in points.
     * @param float $height  Display height in points.
     */
    public function size(float $width, float $height): self
    {
        $this->width  = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Scale the image proportionally so its displayed width equals $width.
     *
     * The height is derived from the natural aspect ratio.  Has no effect
     * if the natural width is unknown (zero).
     *
     * @param float $width  Target display width in points.
     */
    public function fitWidth(float $width): self
    {
        $this->width  = $width;
        $this->height = $this->naturalHeight > 0
            ? $width * ($this->naturalHeight / max(1, $this->naturalWidth))
            : $width;
        return $this;
    }

    /**
     * Scale the image proportionally so its displayed height equals $height.
     *
     * The width is derived from the natural aspect ratio.  Has no effect
     * if the natural height is unknown (zero).
     *
     * @param float $height  Target display height in points.
     */
    public function fitHeight(float $height): self
    {
        $this->height = $height;
        $this->width  = $this->naturalWidth > 0
            ? $height * ($this->naturalWidth / max(1, $this->naturalHeight))
            : $height;
        return $this;
    }

    /**
     * Set the overall opacity of the image.
     *
     * Implemented via a named ExtGState (`/ca` fill-alpha parameter).
     *
     * @param float $opacity  0.0 = fully transparent, 1.0 = fully opaque.
     */
    public function opacity(float $opacity): self
    {
        $this->opacity = max(0.0, min(1.0, $opacity));
        return $this;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render(ContentStream $cs, PdfResources $resources): void
    {
        $w = $this->width  ?: (float) $this->naturalWidth;
        $h = $this->height ?: (float) $this->naturalHeight;

        // Stable XObject name based on content hash — deduplicates identical images
        $hash = substr(md5($this->bytes), 0, 12);
        $name = 'Im_' . $hash;

        if (!$resources->getXObjects()->has($name)) {
            if ($this->type === 'jpeg') {
                $img = new JpegImage($this->bytes);
            } else {
                $img  = new PngImage($this->bytes);
                $mask = $img->getSMaskStream();
                if ($mask !== null) {
                    $maskName = $name . '_mask';
                    $resources->addXObject($maskName, $mask);
                    $img->getStream()->getDictionary()->set('SMask', $mask);
                }
            }
            $resources->addXObject($name, $img->getStream());
        }

        $cs->save();
        if ($this->opacity < 1.0) {
            Text::registerOpacity($this->opacity, $cs, $resources);
        }
        $cs->transform($w, 0, 0, $h, $this->x, $this->y)
           ->drawXObject($name)
           ->restore();
    }
}
