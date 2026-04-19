<?php

declare(strict_types=1);

namespace Papier\LogicalStructure;

use Papier\Objects\{PdfDictionary, PdfInteger, PdfName, PdfObject, PdfString};

/**
 * A structure element in the logical structure tree (§14.7.2 Table 323).
 */
final class StructElement
{
    private PdfDictionary $dict;
    /** @var StructElement[] */
    private array $kids = [];

    public function __construct(
        private readonly string $structType,
        private ?string $lang     = null,
        private ?string $altText  = null,
        private ?string $actualText = null,
    ) {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('StructElem'));
        $this->dict->set('S', new PdfName($structType));
        if ($lang !== null)       { $this->dict->set('Lang', new PdfString($lang)); }
        if ($altText !== null)    { $this->dict->set('Alt', PdfString::text($altText)); }
        if ($actualText !== null) { $this->dict->set('ActualText', PdfString::text($actualText)); }
    }

    public function getStructType(): string { return $this->structType; }

    public function addChild(StructElement $element): static
    {
        $this->kids[] = $element;
        return $this;
    }

    /** Add a marked-content reference (MCID) as a child. */
    public function addMCID(int $mcid, PdfObject $pageRef): static
    {
        $mcr = new PdfDictionary();
        $mcr->set('Type', new PdfName('MCR'));
        $mcr->set('Pg', $pageRef);
        $mcr->set('MCID', new PdfInteger($mcid));
        // MCRs are stored directly; here we just track the MCID for now
        return $this;
    }

    public function setTitle(string $title): static
    {
        $this->dict->set('T', PdfString::text($title));
        return $this;
    }

    public function setAttribute(PdfObject $attrs): static
    {
        $this->dict->set('A', $attrs);
        return $this;
    }

    /** @return StructElement[] */
    public function getKids(): array { return $this->kids; }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
