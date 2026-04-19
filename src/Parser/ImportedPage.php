<?php

declare(strict_types=1);

namespace Papier\Parser;

use Papier\Objects\{
    PdfArray, PdfDictionary, PdfIndirectReference,
    PdfInteger, PdfName, PdfNull, PdfObject, PdfReal, PdfStream
};

/**
 * Wraps a parsed PDF page so it can be embedded as a Form XObject in a new document.
 *
 * The source page content stream and all its resources (fonts, images, colour
 * spaces, …) are deep-copied and resolved so that no indirect references point
 * into the source document's object table.  The result is a self-contained
 * Form XObject stream ready to be registered on a {@see \Papier\Structure\PdfPage}.
 *
 * Use {@see \Papier\PdfDocument::importPage()} as the high-level entry point:
 *
 *   $source   = PdfDocument::open('blank_form.pdf');
 *   $imported = ImportedPage::fromParser($source, 1);   // page 1
 *
 *   $doc  = PdfDocument::create();
 *   $font = $doc->addFont('Helvetica');
 *   $page = $doc->importPage($imported);
 *   $page->add(Text::write('John Doe')->at(150, 620)->font($font, 12));
 *   $doc->save('filled_form.pdf');
 */
final class ImportedPage
{
    private static int $counter = 0;

    private readonly string    $resourceName;
    private readonly PdfStream $formXObject;

    private function __construct(
        private readonly float $width,
        private readonly float $height,
        PdfStream $formXObject,
    ) {
        $this->resourceName = 'Fm' . (++self::$counter);
        $this->formXObject  = $formXObject;
    }

    /**
     * Create an ImportedPage from a parser and a 1-based page number.
     *
     * @param PdfParser $parser      A fully parsed document (from {@see \Papier\PdfDocument::open()}).
     * @param int       $pageNumber  1-based page number to import.
     *
     * @throws \OutOfRangeException  If the page number is out of range.
     */
    public static function fromParser(PdfParser $parser, int $pageNumber = 1): self
    {
        $pages = $parser->getPages();
        if ($pageNumber < 1 || $pageNumber > count($pages)) {
            throw new \OutOfRangeException(
                "Page $pageNumber is out of range (document has " . count($pages) . ' pages).'
            );
        }
        return self::fromPageDict($pages[$pageNumber - 1], $parser);
    }

    /**
     * Create an ImportedPage directly from a resolved page dictionary.
     */
    public static function fromPageDict(PdfDictionary $pageDict, PdfParser $parser): self
    {
        [$width, $height] = self::readMediaBox($pageDict, $parser);

        $content   = self::readContentBytes($pageDict, $parser);
        $resources = self::deepCopyResources($pageDict, $parser);

        $stream = new PdfStream();
        $dict   = $stream->getDictionary();
        $dict->set('Type',     new PdfName('XObject'));
        $dict->set('Subtype',  new PdfName('Form'));
        $dict->set('FormType', new PdfInteger(1));

        $bbox = new PdfArray();
        $bbox->add(new PdfReal(0.0));
        $bbox->add(new PdfReal(0.0));
        $bbox->add(new PdfReal($width));
        $bbox->add(new PdfReal($height));
        $dict->set('BBox', $bbox);

        if ($resources !== null) {
            $dict->set('Resources', $resources);
        }

        $stream->setData($content);
        $stream->compress();

        return new self($width, $height, $stream);
    }

    /** Page width in points. */
    public function getWidth(): float { return $this->width; }

    /** Page height in points. */
    public function getHeight(): float { return $this->height; }

    /**
     * Unique XObject resource name for this import (e.g. `Fm1`).
     *
     * Stable for the lifetime of this object — use it as the key when calling
     * `ContentStream::drawXObject()` and `PdfResources::addXObject()`.
     */
    public function getResourceName(): string { return $this->resourceName; }

    /** Form XObject stream.  Consumed by {@see \Papier\PdfDocument::importPage()}. */
    public function getFormXObject(): PdfStream { return $this->formXObject; }

    // ── Private helpers ───────────────────────────────────────────────────────

    /** @return array{float, float} [width, height] */
    private static function readMediaBox(PdfDictionary $pageDict, PdfParser $parser): array
    {
        $mbRef = $pageDict->get('MediaBox');
        if ($mbRef === null) { return [595.28, 841.89]; }

        $mb = $parser->resolve($mbRef);
        if (!$mb instanceof PdfArray) { return [595.28, 841.89]; }

        $items = $mb->getItems();
        $w = isset($items[2]) ? self::toFloat($items[2]) : 595.28;
        $h = isset($items[3]) ? self::toFloat($items[3]) : 841.89;
        return [$w, $h];
    }

    private static function readContentBytes(PdfDictionary $pageDict, PdfParser $parser): string
    {
        $ref = $pageDict->get('Contents');
        if ($ref === null) { return ''; }

        $obj = $parser->resolve($ref);

        if ($obj instanceof PdfStream) {
            return $obj->decode();
        }

        if ($obj instanceof PdfArray) {
            $bytes = '';
            foreach ($obj->getItems() as $item) {
                $s = $parser->resolve($item);
                if ($s instanceof PdfStream) {
                    $bytes .= $s->decode();
                }
            }
            return $bytes;
        }

        return '';
    }

    private static function deepCopyResources(PdfDictionary $pageDict, PdfParser $parser): ?PdfDictionary
    {
        $resRef = $pageDict->get('Resources');
        if ($resRef === null) { return null; }

        $res = $parser->resolve($resRef);
        if (!$res instanceof PdfDictionary) { return null; }

        $copy = self::copyObj($res, $parser, []);
        return $copy instanceof PdfDictionary ? $copy : null;
    }

    /**
     * Deep-copy a PDF object, resolving all indirect references.
     *
     * Scalars (PdfName, PdfString, PdfInteger, PdfReal, PdfBoolean, PdfNull)
     * are returned as-is; they are effectively immutable value types.
     *
     * Streams are copied with their raw (possibly encoded) data and their
     * full dictionary intact, so the original compression is preserved.
     * Only /Length is dropped because it is recalculated at serialisation time.
     *
     * @param array<int, true> $seen  Object numbers on the current resolution
     *                                path — guards against reference cycles.
     */
    private static function copyObj(PdfObject $obj, PdfParser $parser, array $seen): PdfObject
    {
        if ($obj instanceof PdfIndirectReference) {
            $num = $obj->getObjectNumber();
            if (isset($seen[$num])) {
                return new PdfNull();
            }
            $resolved = $parser->resolveObject($num);
            if ($resolved === null) { return new PdfNull(); }
            return self::copyObj($resolved, $parser, $seen + [$num => true]);
        }

        if ($obj instanceof PdfStream) {
            $copy = new PdfStream();
            foreach ($obj->getDictionary()->getEntries() as $key => $val) {
                if ($key === 'Length') { continue; }
                $copy->getDictionary()->set($key, self::copyObj($val, $parser, $seen));
            }
            // Preserve the raw (possibly still-encoded) bytes and any /Filter entry
            $copy->setData($obj->getData());
            return $copy;
        }

        if ($obj instanceof PdfDictionary) {
            $copy = new PdfDictionary();
            foreach ($obj->getEntries() as $key => $val) {
                $copy->set($key, self::copyObj($val, $parser, $seen));
            }
            return $copy;
        }

        if ($obj instanceof PdfArray) {
            $copy = new PdfArray();
            foreach ($obj->getItems() as $item) {
                $copy->add(self::copyObj($item, $parser, $seen));
            }
            return $copy;
        }

        return $obj;
    }

    private static function toFloat(PdfObject $obj): float
    {
        return ($obj instanceof PdfReal || $obj instanceof PdfInteger)
            ? (float) $obj->getValue()
            : 0.0;
    }
}
