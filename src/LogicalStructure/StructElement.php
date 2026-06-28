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
    /**
     * Ordered content: child elements and marked-content references, in reading order.
     * @var array<int, array{type:'elem',elem:StructElement}|array{type:'mcid',mcid:int,pg:PdfObject}>
     */
    private array $content = [];

    public function __construct(
        private readonly string $structType,
        ?string $lang       = null,
        ?string $altText    = null,
        ?string $actualText = null,
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
        $this->kids[]    = $element;
        $this->content[] = ['type' => 'elem', 'elem' => $element];
        return $this;
    }

    /**
     * Attach a span of marked content to this element by its marked-content
     * identifier (§14.7.4).  The $page is the page dictionary on which the
     * marked content appears (e.g. `$page->getDictionary()`); pass it so the
     * writer can build the /ParentTree and per-page /StructParents entries.
     */
    public function addMCID(int $mcid, PdfObject $page): static
    {
        $this->content[] = ['type' => 'mcid', 'mcid' => $mcid, 'pg' => $page];
        return $this;
    }

    /**
     * Ordered content (child elements and MCID references).
     *
     * @return array<int, array{type:'elem',elem:StructElement}|array{type:'mcid',mcid:int,pg:PdfObject}>
     */
    public function getContent(): array { return $this->content; }

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
