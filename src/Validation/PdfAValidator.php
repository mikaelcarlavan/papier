<?php

declare(strict_types=1);

namespace Papier\Validation;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfNull, PdfStream};
use Papier\Parser\PdfParser;

/**
 * Structural PDF/A conformance checker (subset of ISO 19005).
 *
 * This verifies the key, machine-checkable requirements that catch the common
 * ways a file fails PDF/A — it is NOT a substitute for a full validator such as
 * veraPDF, but it reliably flags regressions in Papier's own output.
 *
 * Checks: no encryption; an sRGB/GTS_PDFA1 OutputIntent with an embedded
 * DestOutputProfile; an uncompressed XMP /Metadata packet declaring pdfaid;
 * a document /ID; and that every font used is embedded (no standard-14 fonts).
 * For conformance level "A" it also requires Tagged PDF (/MarkInfo, structure tree).
 */
final class PdfAValidator
{
    /**
     * Validate PDF bytes; returns a list of human-readable violations
     * (empty array = structurally conformant).
     *
     * @return string[]
     */
    public static function validate(string $pdf): array
    {
        $parser = new PdfParser($pdf);
        try {
            $parser->parse();
        } catch (\Throwable $e) {
            return ['Document could not be parsed: ' . $e->getMessage()];
        }

        $issues  = [];
        $catalog = $parser->getCatalog();
        if ($catalog === null) {
            return ['No document catalog.'];
        }

        if ($parser->isEncrypted()) {
            $issues[] = 'Document is encrypted (forbidden in PDF/A).';
        }

        // /ID required.
        if ($parser->getXref()->getFileId1() === '') {
            $issues[] = 'Missing document /ID in the trailer.';
        }

        // OutputIntent.
        $oi = $parser->resolve($catalog->get('OutputIntents') ?? new PdfNull());
        $hasPdfaIntent = false;
        if ($oi instanceof PdfArray) {
            foreach ($oi->getItems() as $entry) {
                $intent = $parser->resolve($entry);
                if ($intent instanceof PdfDictionary
                    && ($intent->get('S') instanceof PdfName)
                    && $intent->get('S')->getValue() === 'GTS_PDFA1'
                    && $parser->resolve($intent->get('DestOutputProfile') ?? new PdfNull()) instanceof PdfStream) {
                    $hasPdfaIntent = true;
                }
            }
        }
        if (!$hasPdfaIntent) {
            $issues[] = 'Missing a GTS_PDFA1 OutputIntent with an embedded DestOutputProfile.';
        }

        // XMP metadata: present, uncompressed, declares pdfaid.
        $metaRef = $catalog->get('Metadata');
        $meta    = $parser->resolve($metaRef ?? new PdfNull());
        if (!$meta instanceof PdfStream) {
            $issues[] = 'Missing XMP /Metadata stream.';
        } else {
            if ($meta->getDictionary()->get('Filter') !== null) {
                $issues[] = 'XMP /Metadata stream must not be filtered/compressed.';
            }
            $xmp = $meta->decode();
            if (!str_contains($xmp, 'pdfaid')) {
                $issues[] = 'XMP metadata does not declare PDF/A identification (pdfaid).';
            }
        }

        // Conformance level: read from XMP.
        $level = '';
        if ($meta instanceof PdfStream && preg_match('/<pdfaid:conformance>\s*([A-Za-z])/', $meta->decode(), $m)) {
            $level = strtoupper($m[1]);
        }

        // Fonts must be embedded.
        foreach (self::fontIssues($parser) as $i) {
            $issues[] = $i;
        }

        // Level A requires tagging.
        if ($level === 'A' && !$parser->isTagged()) {
            $issues[] = 'Conformance level A requires Tagged PDF (/MarkInfo /Marked true and a structure tree).';
        }

        return $issues;
    }

    public static function isValid(string $pdf): bool
    {
        return self::validate($pdf) === [];
    }

    /** @return string[] */
    private static function fontIssues(PdfParser $parser): array
    {
        $issues = [];
        $seen   = [];
        foreach ($parser->getPages() as $page) {
            $res = $parser->resolve($page->get('Resources') ?? new PdfNull());
            if (!$res instanceof PdfDictionary) {
                continue;
            }
            $fonts = $parser->resolve($res->get('Font') ?? new PdfNull());
            if (!$fonts instanceof PdfDictionary) {
                continue;
            }
            foreach ($fonts->getEntries() as $ref) {
                $font = $parser->resolve($ref);
                if (!$font instanceof PdfDictionary) {
                    continue;
                }
                $base = $font->get('BaseFont');
                $key  = $base instanceof PdfName ? $base->getValue() : spl_object_id($font);
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                if (!self::isFontEmbedded($parser, $font)) {
                    $name = $base instanceof PdfName ? $base->getValue() : '(unnamed)';
                    $issues[] = "Font '$name' is not embedded (forbidden in PDF/A).";
                }
            }
        }
        return $issues;
    }

    private static function isFontEmbedded(PdfParser $parser, PdfDictionary $font): bool
    {
        $subtype = $font->get('Subtype');
        if ($subtype instanceof PdfName && $subtype->getValue() === 'Type0') {
            $desc = $parser->resolve($font->get('DescendantFonts') ?? new PdfNull());
            if ($desc instanceof PdfArray) {
                $cid = $parser->resolve($desc->get(0) ?? new PdfNull());
                if ($cid instanceof PdfDictionary) {
                    return self::descriptorHasFile($parser, $cid);
                }
            }
            return false;
        }
        // Type 3 fonts embed their glyphs as content streams (always "embedded").
        if ($subtype instanceof PdfName && $subtype->getValue() === 'Type3') {
            return true;
        }
        return self::descriptorHasFile($parser, $font);
    }

    private static function descriptorHasFile(PdfParser $parser, PdfDictionary $font): bool
    {
        $desc = $parser->resolve($font->get('FontDescriptor') ?? new PdfNull());
        if (!$desc instanceof PdfDictionary) {
            return false;
        }
        foreach (['FontFile', 'FontFile2', 'FontFile3'] as $k) {
            if ($desc->get($k) !== null) {
                return true;
            }
        }
        return false;
    }
}
