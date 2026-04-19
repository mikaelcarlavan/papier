<?php

declare(strict_types=1);

namespace Papier\LogicalStructure;

use Papier\Objects\{PdfDictionary, PdfName};

/**
 * Root of the structure tree (StructTreeRoot).
 */
final class StructTreeRoot
{
    private PdfDictionary $dict;
    /** @var StructElement[] */
    private array $kids = [];

    public function __construct()
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('StructTreeRoot'));
    }

    public function addChild(StructElement $element): static
    {
        $this->kids[] = $element;
        return $this;
    }

    /** @return StructElement[] */
    public function getKids(): array { return $this->kids; }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
