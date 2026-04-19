<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * Abstract base for all PDF objects (ISO 32000-1 §7.3).
 *
 * Every PDF object may be either direct (embedded inline) or indirect
 * (allocated in the body with an object number and generation number).
 */
abstract class PdfObject
{
    private ?int $objectNumber     = null;
    private int  $generationNumber = 0;

    /** Render the object to its PDF byte representation. */
    abstract public function toString(): string;

    public function __toString(): string
    {
        return $this->toString();
    }

    // ── Indirect object identity ──────────────────────────────────────────────

    public function setObjectNumber(int $number, int $generation = 0): static
    {
        $this->objectNumber     = $number;
        $this->generationNumber = $generation;
        return $this;
    }

    public function getObjectNumber(): ?int
    {
        return $this->objectNumber;
    }

    public function getGenerationNumber(): int
    {
        return $this->generationNumber;
    }

    public function isIndirect(): bool
    {
        return $this->objectNumber !== null;
    }

    /**
     * Render this object as an indirect object definition.
     *   <n> <g> obj … endobj
     */
    public function toIndirectObject(): string
    {
        if ($this->objectNumber === null) {
            throw new \LogicException('Object number not assigned.');
        }
        return "{$this->objectNumber} {$this->generationNumber} obj\n"
             . $this->toString()
             . "\nendobj\n";
    }

    /**
     * Return an indirect reference to this object.
     *   <n> <g> R
     */
    public function getReference(): PdfIndirectReference
    {
        if ($this->objectNumber === null) {
            throw new \LogicException('Cannot reference an object without an object number.');
        }
        return new PdfIndirectReference($this->objectNumber, $this->generationNumber);
    }
}
