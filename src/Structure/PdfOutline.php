<?php

declare(strict_types=1);

namespace Papier\Structure;

/**
 * Document outline (bookmarks) root container (ISO 32000-1 §12.3.3).
 *
 * The outline is a tree of {@see PdfOutlineItem} objects whose structure
 * mirrors the document's chapter/section hierarchy.  Viewers render it as
 * a collapsible bookmark panel.
 *
 * Example:
 *
 *   $outline  = new PdfOutline();
 *   $chapter1 = new PdfOutlineItem('Chapter 1');
 *   $chapter1->setDestination(new PdfString('chapter1-anchor'));
 *
 *   $section  = new PdfOutlineItem('Section 1.1');
 *   $section->setDestination(new PdfString('section1.1-anchor'))->setBold(true);
 *   $chapter1->addChild($section);
 *
 *   $outline->addItem($chapter1);
 *   $doc->setOutline($outline);
 */
final class PdfOutline
{
    /** @var PdfOutlineItem[] Top-level bookmark items. */
    private array $rootItems = [];

    /**
     * Add a top-level outline item (chapter-level bookmark).
     *
     * @param PdfOutlineItem $item  The bookmark to append.
     */
    public function addItem(PdfOutlineItem $item): static
    {
        $this->rootItems[] = $item;
        return $this;
    }

    /**
     * Return all top-level items.
     *
     * @return PdfOutlineItem[]
     */
    public function getRootItems(): array
    {
        return $this->rootItems;
    }
}
