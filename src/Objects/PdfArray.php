<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Array object (ISO 32000-1 §7.3.6).
 *
 * An ordered, one-dimensional collection of PDF objects.
 * Elements need not all be of the same type.
 */
final class PdfArray extends PdfObject implements \Countable, \IteratorAggregate
{
    /** @var PdfObject[] */
    private array $items = [];

    public function __construct(PdfObject ...$items)
    {
        $this->items = $items;
    }

    /** Append a value to the array. */
    public function add(PdfObject $object): static
    {
        $this->items[] = $object;
        return $this;
    }

    /** Set element at a specific index. */
    public function set(int $index, PdfObject $object): static
    {
        $this->items[$index] = $object;
        return $this;
    }

    /** Get element at a specific index. */
    public function get(int $index): ?PdfObject
    {
        return $this->items[$index] ?? null;
    }

    /** @return PdfObject[] */
    public function getItems(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    public function toString(): string
    {
        $parts = array_map(fn(PdfObject $o) => $o->toString(), $this->items);
        return '[' . implode(' ', $parts) . ']';
    }
}
