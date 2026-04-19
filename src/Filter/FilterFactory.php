<?php

declare(strict_types=1);

namespace Papier\Filter;

use Papier\Objects\PdfObject;

/**
 * Creates filter instances and provides encode/decode convenience methods.
 * Covers all filters defined in ISO 32000-1 §7.4.
 */
final class FilterFactory
{
    /** @var array<string, class-string<FilterInterface>> */
    private static array $filters = [
        'FlateDecode'    => FlateDecode::class,
        'Fl'             => FlateDecode::class,   // abbreviated name
        'LZWDecode'      => LZWDecode::class,
        'LZW'            => LZWDecode::class,
        'ASCIIHexDecode' => ASCIIHexDecode::class,
        'AHx'            => ASCIIHexDecode::class,
        'ASCII85Decode'  => ASCII85Decode::class,
        'A85'            => ASCII85Decode::class,
        'RunLengthDecode'=> RunLengthDecode::class,
        'RL'             => RunLengthDecode::class,
        'CCITTFaxDecode' => CCITTFaxDecode::class,
        'CCF'            => CCITTFaxDecode::class,
        'JBIG2Decode'    => JBIG2Decode::class,
        'DCTDecode'      => DCTDecode::class,
        'DCT'            => DCTDecode::class,
        'JPXDecode'      => JPXDecode::class,
        'Crypt'          => Crypt::class,
        'Identity'       => Crypt::class,
    ];

    public static function create(string $name): FilterInterface
    {
        $class = self::$filters[$name] ?? null;
        if ($class === null) {
            throw new \InvalidArgumentException("Unknown PDF filter: $name");
        }
        return new $class();
    }

    public static function encode(string $filterName, string $data, ?PdfObject $params = null): string
    {
        return self::create($filterName)->encode($data, $params);
    }

    public static function decode(string $filterName, string $data, ?PdfObject $params = null): string
    {
        return self::create($filterName)->decode($data, $params);
    }

    /** Register a custom filter. */
    public static function register(string $name, string $class): void
    {
        if (!is_a($class, FilterInterface::class, true)) {
            throw new \InvalidArgumentException("$class must implement FilterInterface.");
        }
        self::$filters[$name] = $class;
    }
}
