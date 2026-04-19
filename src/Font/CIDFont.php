<?php

declare(strict_types=1);

namespace Papier\Font;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject};

/**
 * CIDFont dictionary (ISO 32000-1 §9.7.4).
 *
 * A CIDFont is not used directly; it is always the descendant of a Type 0 font.
 * Subtypes: CIDFontType0 (CFF/Type 1) or CIDFontType2 (TrueType/OpenType).
 */
final class CIDFont extends Font
{
    private string      $subtype;
    private string      $cidSystemInfoRegistry;
    private string      $cidSystemInfoOrdering;
    private int         $cidSystemInfoSupplement;
    private ?PdfObject  $descriptorRef = null;
    private int         $dw            = 1000;  // default width
    /** @var array<int,int> cid → width */
    private array       $widths        = [];

    public function __construct(
        string $baseFont,
        string $subtype = 'CIDFontType2',
        string $registry = 'Adobe',
        string $ordering = 'Identity',
        int    $supplement = 0,
    ) {
        parent::__construct();
        $this->subtype                 = $subtype;
        $this->cidSystemInfoRegistry   = $registry;
        $this->cidSystemInfoOrdering   = $ordering;
        $this->cidSystemInfoSupplement = $supplement;
        $this->dictionary->set('Subtype', new PdfName($subtype));
        $this->dictionary->set('BaseFont', new PdfName($baseFont));
    }

    public function getSubtype(): string { return $this->subtype; }

    public function setFontDescriptor(PdfObject $ref): static
    {
        $this->descriptorRef = $ref;
        return $this;
    }

    public function setDefaultWidth(int $dw): static
    {
        $this->dw = $dw;
        return $this;
    }

    /**
     * Set widths for specific CIDs.
     * Format: [c [w1 w2 …]] or [cfirst clast w] (§9.7.4.3).
     */
    public function setWidths(array $widths): static
    {
        $this->widths = $widths;
        return $this;
    }

    public function stringWidth(string $text, float $size): float
    {
        return (mb_strlen($text, 'UTF-8') * $this->dw * $size) / 1000;
    }

    public function getDictionary(): PdfDictionary
    {
        $cidInfo = new PdfDictionary();
        $cidInfo->set('Registry', new \Papier\Objects\PdfString($this->cidSystemInfoRegistry));
        $cidInfo->set('Ordering', new \Papier\Objects\PdfString($this->cidSystemInfoOrdering));
        $cidInfo->set('Supplement', new PdfInteger($this->cidSystemInfoSupplement));
        $this->dictionary->set('CIDSystemInfo', $cidInfo);

        if ($this->descriptorRef !== null) {
            $this->dictionary->set('FontDescriptor', $this->descriptorRef);
        }
        $this->dictionary->set('DW', new PdfInteger($this->dw));

        if (!empty($this->widths)) {
            $wArr = new PdfArray();
            foreach ($this->widths as $cid => $w) {
                $wArr->add(new PdfInteger($cid));
                $inner = new PdfArray();
                $inner->add(new PdfInteger($w));
                $wArr->add($inner);
            }
            $this->dictionary->set('W', $wArr);
        }
        return $this->dictionary;
    }
}
