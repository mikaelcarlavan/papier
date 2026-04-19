<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Dictionary object (ISO 32000-1 §7.3.7).
 *
 * An associative table mapping name keys to PDF object values.
 * Insertion order is preserved (important for reproducible output).
 */
final class PdfDictionary extends PdfObject implements \Countable, \IteratorAggregate
{
    /** @var array<string, PdfObject> keyed by the name value (without leading /) */
    private array $entries = [];

    public function __construct(array $entries = [])
    {
        foreach ($entries as $key => $value) {
            $this->set($key, $value);
        }
    }

    /** Set an entry.  Passing null removes the key. */
    public function set(string $key, PdfObject|null $value): static
    {
        if ($value === null) {
            unset($this->entries[$key]);
        } else {
            $this->entries[$key] = $value;
        }
        return $this;
    }

    public function get(string $key): ?PdfObject
    {
        return $this->entries[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->entries[$key]);
    }

    public function remove(string $key): static
    {
        unset($this->entries[$key]);
        return $this;
    }

    /** @return array<string, PdfObject> */
    public function getEntries(): array
    {
        return $this->entries;
    }

    public function count(): int
    {
        return count($this->entries);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->entries);
    }

    public function toString(): string
    {
        $lines = ['<<'];
        foreach ($this->entries as $key => $value) {
            $lines[] = (new PdfName($key))->toString() . ' ' . $value->toString();
        }
        $lines[] = '>>';
        return implode("\n", $lines);
    }
}
