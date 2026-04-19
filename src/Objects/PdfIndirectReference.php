<?php

declare(strict_types=1);

namespace Papier\Objects;

/**
 * PDF Indirect Reference (ISO 32000-1 §7.3.10).
 *
 * A reference to an indirect object using its object number and generation
 * number: `<n> <g> R`.
 */
final class PdfIndirectReference extends PdfObject
{
    public function __construct(
        private readonly int $objectNumber,
        private readonly int $generationNumber = 0,
    ) {}

    public static function to(PdfObject $object): self
    {
        if (!$object->isIndirect()) {
            throw new \InvalidArgumentException('Cannot create a reference to a direct object.');
        }
        return new self($object->getObjectNumber(), $object->getGenerationNumber());
    }

    public function getObjectNumber(): ?int
    {
        return $this->objectNumber;
    }

    public function getGenerationNumber(): int
    {
        return $this->generationNumber;
    }

    public function toString(): string
    {
        return "{$this->objectNumber} {$this->generationNumber} R";
    }
}
