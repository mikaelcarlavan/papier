<?php

declare(strict_types=1);

namespace Papier\Objects;

use Papier\Filter\FilterFactory;

/**
 * PDF Stream object (ISO 32000-1 §7.3.8).
 *
 * A sequence of bytes, preceded by a dictionary that describes it.
 * Streams are always indirect objects.
 *
 * Required dictionary entries:
 *   Length  – byte count of the stream data after any encoding.
 *   Filter  – name or array of names identifying applied filters.
 *   DecodeParms – parameters for the filters.
 */
final class PdfStream extends PdfObject
{
    private PdfDictionary $dictionary;
    private string        $data       = '';
    private bool          $compressed = false;

    public function __construct(?PdfDictionary $dictionary = null)
    {
        $this->dictionary = $dictionary ?? new PdfDictionary();
    }

    public function getDictionary(): PdfDictionary
    {
        return $this->dictionary;
    }

    /** Set raw (unencoded) stream data. */
    public function setData(string $data): static
    {
        $this->data       = $data;
        $this->compressed = false;
        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Apply FlateDecode compression and update the dictionary.
     * Multiple calls re-encode from the current (already encoded) data;
     * build the filter chain before calling this.
     */
    public function compress(): static
    {
        $encoded = gzcompress($this->data, 6);
        if ($encoded === false) {
            throw new \RuntimeException('Failed to compress stream data.');
        }

        $existing = $this->dictionary->get('Filter');
        if ($existing === null) {
            $this->dictionary->set('Filter', new PdfName('FlateDecode'));
        } elseif ($existing instanceof PdfName) {
            // Wrap both in an array (outer filter applied last on decode)
            $arr = new PdfArray();
            $arr->add(new PdfName('FlateDecode'));
            $arr->add($existing);
            $this->dictionary->set('Filter', $arr);
        } elseif ($existing instanceof PdfArray) {
            $newArr = new PdfArray();
            $newArr->add(new PdfName('FlateDecode'));
            foreach ($existing as $item) {
                $newArr->add($item);
            }
            $this->dictionary->set('Filter', $newArr);
        }

        $this->data       = $encoded;
        $this->compressed = true;
        return $this;
    }

    /**
     * Decode all filters applied to the stream and return raw bytes.
     */
    public function decode(): string
    {
        $data   = $this->data;
        $filter = $this->dictionary->get('Filter');
        $parms  = $this->dictionary->get('DecodeParms');

        $filters = [];
        if ($filter instanceof PdfName) {
            $filters = [$filter->getValue()];
            $parms   = $parms !== null ? [$parms] : [null];
        } elseif ($filter instanceof PdfArray) {
            foreach ($filter as $f) {
                $filters[] = $f instanceof PdfName ? $f->getValue() : '';
            }
            $parmList = [];
            if ($parms instanceof PdfArray) {
                foreach ($parms as $p) {
                    $parmList[] = $p;
                }
            } else {
                $parmList = array_fill(0, count($filters), null);
            }
            $parms = $parmList;
        }

        foreach ($filters as $i => $name) {
            $p    = is_array($parms) ? ($parms[$i] ?? null) : null;
            $data = FilterFactory::decode($name, $data, $p);
        }
        return $data;
    }

    public function toString(): string
    {
        $body = $this->data;
        $this->dictionary->set('Length', new PdfInteger(strlen($body)));

        return $this->dictionary->toString()
             . "\nstream\n"
             . $body
             . "\nendstream";
    }
}
