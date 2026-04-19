<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Content\ContentStream;

/**
 * Immutable colour value used by high-level elements.
 *
 * Supports the three device colour models defined in ISO 32000-1 §8.6:
 *   - DeviceRGB  — {@see self::rgb()}, {@see self::hex()}
 *   - DeviceCMYK — {@see self::cmyk()}
 *   - DeviceGray — {@see self::gray()}, {@see self::black()}, {@see self::white()}
 *
 * All component values are normalised floats in the range [0.0, 1.0].
 * Colors are applied to a {@see ContentStream} by calling
 * {@see self::applyFill()} or {@see self::applyStroke()}.
 *
 * Examples:
 *   Color::rgb(0.2, 0.4, 0.8)
 *   Color::hex('#3366cc')      // CSS-style hex, 3- or 6-digit
 *   Color::gray(0.5)           // mid-grey
 *   Color::cmyk(0, 0.5, 1, 0) // orange
 *   Color::black()
 *   Color::white()
 */
final class Color
{
    private const MODE_RGB  = 'rgb';
    private const MODE_CMYK = 'cmyk';
    private const MODE_GRAY = 'gray';

    /**
     * @param string  $mode        One of the MODE_* constants.
     * @param float[] $components  Colour component values in [0, 1].
     */
    private function __construct(
        private readonly string $mode,
        private readonly array  $components,
    ) {}

    // ── Factories ─────────────────────────────────────────────────────────────

    /**
     * Create an RGB colour.
     *
     * @param float $r Red channel   [0, 1].
     * @param float $g Green channel [0, 1].
     * @param float $b Blue channel  [0, 1].
     */
    public static function rgb(float $r, float $g, float $b): self
    {
        return new self(self::MODE_RGB, [$r, $g, $b]);
    }

    /**
     * Parse a CSS-style hexadecimal colour string.
     *
     * Accepts both 3-digit (#rgb) and 6-digit (#rrggbb) forms, with or
     * without the leading hash.
     *
     * @param string $hex  e.g. '#3366cc', '3366cc', '#f60', 'f60'.
     */
    public static function hex(string $hex): self
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        return new self(self::MODE_RGB, [
            hexdec(substr($hex, 0, 2)) / 255,
            hexdec(substr($hex, 2, 2)) / 255,
            hexdec(substr($hex, 4, 2)) / 255,
        ]);
    }

    /**
     * Create a greyscale colour.
     *
     * @param float $gray Luminance [0 = black, 1 = white].
     */
    public static function gray(float $gray): self
    {
        return new self(self::MODE_GRAY, [$gray]);
    }

    /**
     * Create a CMYK colour.
     *
     * @param float $c Cyan    [0, 1].
     * @param float $m Magenta [0, 1].
     * @param float $y Yellow  [0, 1].
     * @param float $k Key (black) [0, 1].
     */
    public static function cmyk(float $c, float $m, float $y, float $k): self
    {
        return new self(self::MODE_CMYK, [$c, $m, $y, $k]);
    }

    /** Pure black (DeviceGray 0.0). */
    public static function black(): self { return self::gray(0.0); }

    /** Pure white (DeviceGray 1.0). */
    public static function white(): self { return self::gray(1.0); }

    // ── Apply to ContentStream ────────────────────────────────────────────────

    /**
     * Return the RGB components as `[r, g, b]` (each in [0, 1]).
     *
     * Greyscale colours are expanded to `[v, v, v]`.
     * CMYK colours are converted using a simple formula.
     *
     * @return float[]
     */
    public function toRgb(): array
    {
        return match ($this->mode) {
            self::MODE_RGB  => $this->components,
            self::MODE_GRAY => array_fill(0, 3, $this->components[0]),
            self::MODE_CMYK => [
                (1 - $this->components[0]) * (1 - $this->components[3]),
                (1 - $this->components[1]) * (1 - $this->components[3]),
                (1 - $this->components[2]) * (1 - $this->components[3]),
            ],
        };
    }

    /**
     * Return the raw colour components as a plain float array.
     *
     * The number of elements depends on the colour space:
     *   RGB → 3 values, CMYK → 4 values, Greyscale → 1 value.
     *
     * @return float[]
     */
    public function toArray(): array
    {
        return $this->components;
    }

    /**
     * Emit the appropriate non-stroking (fill) colour operator into $cs.
     *
     * Chooses between `g` (grey), `rg` (RGB), and `k` (CMYK) depending
     * on the colour model.
     */
    public function applyFill(ContentStream $cs): void
    {
        match ($this->mode) {
            self::MODE_RGB  => $cs->setFillRGB(...$this->components),
            self::MODE_CMYK => $cs->setFillCMYK(...$this->components),
            self::MODE_GRAY => $cs->setFillGray($this->components[0]),
        };
    }

    /**
     * Emit the appropriate stroking colour operator into $cs.
     *
     * Chooses between `G` (grey), `RG` (RGB), and `K` (CMYK) depending
     * on the colour model.
     */
    public function applyStroke(ContentStream $cs): void
    {
        match ($this->mode) {
            self::MODE_RGB  => $cs->setStrokeRGB(...$this->components),
            self::MODE_CMYK => $cs->setStrokeCMYK(...$this->components),
            self::MODE_GRAY => $cs->setStrokeGray($this->components[0]),
        };
    }
}
