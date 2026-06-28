<?php

declare(strict_types=1);

namespace Papier\Graphics\Shading;

use Papier\Objects\{PdfArray, PdfInteger, PdfReal, PdfStream};

/**
 * Base class for the stream-based mesh shadings (ISO 32000-1 §8.7.4.5.5–§8.7.4.5.7):
 * free-form (4) and lattice-form (5) Gouraud triangle meshes, Coons patch
 * meshes (6), and tensor-product patch meshes (7).
 *
 * A mesh shading is a stream whose data is a packed sequence of vertices or
 * patches.  This class collects geometry as floats and, at build time, derives
 * the /Decode ranges from the geometry bounds and packs the bytes using
 * byte-aligned sample sizes (8-bit flag, 16-bit coordinates, 8-bit colour
 * components) so the output is exact and easy to validate.
 */
abstract class MeshShading extends Shading
{
    protected const COORD_MAX = 0xFFFF;       // 16-bit coordinates
    protected const COMP_MAX  = 0xFF;         // 8-bit colour components

    protected int $components;

    /** Collected colour components across all geometry, for the colour Decode range. */
    protected array $allX = [];
    protected array $allY = [];

    /** Explicit coordinate bounds [xmin, xmax, ymin, ymax]; auto-derived when null. */
    protected ?array $coordRange = null;

    public function __construct(int $shadingType, string $colorSpace, ?int $components = null)
    {
        parent::__construct($shadingType, $colorSpace);
        $this->components = $components ?? self::componentsFor($colorSpace);
    }

    /** Override the auto-derived coordinate bounds used for /Decode. */
    public function setCoordinateRange(float $xmin, float $xmax, float $ymin, float $ymax): static
    {
        $this->coordRange = [$xmin, $xmax, $ymin, $ymax];
        return $this;
    }

    /** Number of colour components implied by a device colour space name. */
    protected static function componentsFor(string $cs): int
    {
        return match ($cs) {
            'DeviceGray', 'CalGray', 'G'        => 1,
            'DeviceCMYK', 'CMYK'                => 4,
            default                            => 3, // DeviceRGB and friends
        };
    }

    /** Build the packed stream data (implemented per shading type). */
    abstract protected function encodeData(): string;

    /** Coordinate bounds: explicit if set, else derived from collected points. */
    protected function bounds(): array
    {
        if ($this->coordRange !== null) {
            return $this->coordRange;
        }
        $xmin = $this->allX ? min($this->allX) : 0.0;
        $xmax = $this->allX ? max($this->allX) : 1.0;
        $ymin = $this->allY ? min($this->allY) : 0.0;
        $ymax = $this->allY ? max($this->allY) : 1.0;
        if ($xmax <= $xmin) { $xmax = $xmin + 1.0; }
        if ($ymax <= $ymin) { $ymax = $ymin + 1.0; }
        return [$xmin, $xmax, $ymin, $ymax];
    }

    protected function packCoord(float $v, float $min, float $max): string
    {
        $t = ($v - $min) / ($max - $min);
        $t = max(0.0, min(1.0, $t));
        return pack('n', (int) round($t * self::COORD_MAX));
    }

    protected function packColor(array $color): string
    {
        $out = '';
        for ($i = 0; $i < $this->components; $i++) {
            $c = $color[$i] ?? 0.0;
            $c = max(0.0, min(1.0, $c));
            $out .= chr((int) round($c * self::COMP_MAX));
        }
        return $out;
    }

    /** Common /Decode array: coordinate bounds followed by [0 1] per colour component. */
    protected function decodeArray(): PdfArray
    {
        [$xmin, $xmax, $ymin, $ymax] = $this->bounds();
        $decode = new PdfArray();
        foreach ([$xmin, $xmax, $ymin, $ymax] as $v) {
            $decode->add(new PdfReal($v));
        }
        for ($i = 0; $i < $this->components; $i++) {
            $decode->add(new PdfReal(0.0));
            $decode->add(new PdfReal(1.0));
        }
        return $decode;
    }

    public function toDictionary(): \Papier\Objects\PdfDictionary
    {
        // Mesh shadings are streams; the dictionary alone is not usable.
        return $this->toStream()->getDictionary();
    }

    /**
     * Build the shading as a {@see PdfStream} suitable for
     * {@see \Papier\Structure\PdfResources::addShading()}.
     */
    public function toStream(): PdfStream
    {
        $data = $this->encodeData();

        $dict = $this->dictionary;
        $dict->set('BitsPerCoordinate', new PdfInteger(16));
        $dict->set('BitsPerComponent', new PdfInteger(8));
        $dict->set('Decode', $this->decodeArray());
        $this->decorateDictionary($dict);

        $stream = new PdfStream($dict);
        $stream->setData($data);
        $stream->compress();
        return $stream;
    }

    /** Hook for subclasses to add type-specific dictionary entries (BitsPerFlag, VerticesPerRow). */
    protected function decorateDictionary(\Papier\Objects\PdfDictionary $dict): void
    {
    }
}
