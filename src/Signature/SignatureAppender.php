<?php

declare(strict_types=1);

namespace Papier\Signature;

use Papier\Font\Encoding\WinAnsiEncoding;
use Papier\Objects\{
    PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfName, PdfNull, PdfRaw, PdfReal, PdfStream
};
use Papier\Parser\PdfParser;
use Papier\Writer\IncrementalUpdater;

/**
 * Shared machinery for appending a signature field as an incremental update and
 * filling in its /ByteRange and /Contents (ISO 32000-1 §12.8).  Used by both
 * approval signatures ({@see PdfSigner}) and document timestamps
 * ({@see DocumentTimestamp}).
 */
final class SignatureAppender
{
    /**
     * @param string   $sigDictRaw       Verbatim signature dictionary with the
     *                                    placeholder /ByteRange and /Contents.
     * @param int      $capacity         Reserved /Contents byte length.
     * @param \Closure $produceContents  fn(string $signedBytes): string — returns the
     *                                    DER blob to place in /Contents.
     * @param array|null $visible        ['page'=>int(1-based),'rect'=>[x,y,w,h],'lines'=>string[]] or null.
     */
    public static function append(
        PdfParser $parser,
        string $sigDictRaw,
        int $capacity,
        \Closure $produceContents,
        ?array $visible = null,
        string $fieldTitle = 'Signature1',
    ): string {
        $catalogNum = $parser->getXref()->getCatalogObjectNumber();
        $catalog    = $catalogNum !== null ? $parser->resolveObject($catalogNum) : null;
        if (!$catalog instanceof PdfDictionary) {
            throw new \RuntimeException('Cannot sign: no document catalog.');
        }
        $pages = $parser->getPages();
        if (empty($pages)) {
            throw new \RuntimeException('Cannot sign: document has no pages.');
        }

        $pageIndex = $visible !== null ? max(1, (int) ($visible['page'] ?? 1)) - 1 : 0;
        $pageIndex = min($pageIndex, count($pages) - 1);
        $targetPage = $pages[$pageIndex];
        $targetPageNum = $targetPage->getObjectNumber();

        $updater = new IncrementalUpdater($parser);

        // Signature dictionary (raw, with placeholders).
        $sigNum = $updater->addObject(new PdfRaw($sigDictRaw));

        // Signature field + widget (merged).
        $widget = new PdfDictionary();
        $widget->set('Type', new PdfName('Annot'));
        $widget->set('Subtype', new PdfName('Widget'));
        $widget->set('FT', new PdfName('Sig'));
        $widget->set('T', \Papier\Objects\PdfString::text($fieldTitle));
        $widget->set('V', new PdfIndirectReference($sigNum));

        if ($visible !== null) {
            [$x, $y, $w, $h] = $visible['rect'];
            $rect = new PdfArray();
            foreach ([$x, $y, $x + $w, $y + $h] as $v) { $rect->add(new PdfReal((float) $v)); }
            $widget->set('Rect', $rect);
            $widget->set('F', new PdfInteger(4)); // Print
            $apNum = self::buildAppearance($updater, (float) $w, (float) $h, $visible['lines'] ?? []);
            $ap = new PdfDictionary();
            $ap->set('N', new PdfIndirectReference($apNum));
            $widget->set('AP', $ap);
        } else {
            $rect = new PdfArray();
            foreach ([0, 0, 0, 0] as $v) { $rect->add(new PdfInteger($v)); }
            $widget->set('Rect', $rect);
            $widget->set('F', new PdfInteger(132)); // Print + Locked
        }
        if ($targetPageNum !== null) {
            $widget->set('P', new PdfIndirectReference($targetPageNum));
        }
        $widgetNum = $updater->addObject($widget);

        // AcroForm — merge with an existing one if present.
        $fields = new PdfArray();
        $existingAcro = $catalog->get('AcroForm');
        if ($existingAcro !== null) {
            $acro = $parser->resolve($existingAcro);
            if ($acro instanceof PdfDictionary) {
                $ex = $parser->resolve($acro->get('Fields') ?? new PdfNull());
                if ($ex instanceof PdfArray) {
                    foreach ($ex->getItems() as $f) { $fields->add($f); }
                }
            }
        }
        $fields->add(new PdfIndirectReference($widgetNum));

        $acroForm = new PdfDictionary();
        $acroForm->set('Fields', $fields);
        $acroForm->set('SigFlags', new PdfInteger(3)); // SignaturesExist | AppendOnly
        $acroNum = $updater->addObject($acroForm);

        $catalog->set('AcroForm', new PdfIndirectReference($acroNum));
        $updater->updateObject($catalogNum, $catalog);

        // Add the widget to the target page's /Annots.
        $annots = $parser->resolve($targetPage->get('Annots') ?? new PdfNull());
        $annotArr = $annots instanceof PdfArray ? $annots : new PdfArray();
        $annotArr->add(new PdfIndirectReference($widgetNum));
        $targetPage->set('Annots', $annotArr);
        if ($targetPageNum !== null) {
            $updater->updateObject($targetPageNum, $targetPage);
        }

        return self::patch($updater->build(), $capacity, $produceContents);
    }

    /** Compute the real /ByteRange, run the content producer, patch /Contents. */
    public static function patch(string $pdf, int $capacity, \Closure $produceContents): string
    {
        // The newly appended signature is last; patch its /Contents (not an
        // earlier signature's) so existing signatures stay intact.
        $cPos = strrpos($pdf, '/Contents <');
        if ($cPos === false) {
            throw new \RuntimeException('Signature placeholder not found.');
        }
        $ltPos = $cPos + strlen('/Contents <') - 1;   // position of '<'
        $gtPos = $ltPos + 1 + $capacity * 2;          // position of '>'

        $len1   = $ltPos;
        $start2 = $gtPos + 1;
        $len2   = strlen($pdf) - $start2;

        $brPlaceholder = '/ByteRange [0000000000 0000000000 0000000000 0000000000]';
        $brReal = sprintf('/ByteRange [%010d %010d %010d %010d]', 0, $len1, $start2, $len2);
        $pdf = str_replace($brPlaceholder, $brReal, $pdf);

        $signedData = substr($pdf, 0, $len1) . substr($pdf, $start2, $len2);

        $der = $produceContents($signedData);
        $hex = bin2hex($der);
        if (strlen($hex) > $capacity * 2) {
            throw new \RuntimeException('Signature exceeds reserved capacity; raise the capacity.');
        }
        $hex = str_pad($hex, $capacity * 2, '0');

        return substr($pdf, 0, $ltPos + 1) . $hex . substr($pdf, $gtPos);
    }

    /** Build a visible signature appearance Form XObject. */
    private static function buildAppearance(IncrementalUpdater $updater, float $w, float $h, array $lines): int
    {
        // Helvetica font for the appearance text.
        $font = new PdfDictionary();
        $font->set('Type', new PdfName('Font'));
        $font->set('Subtype', new PdfName('Type1'));
        $font->set('BaseFont', new PdfName('Helvetica'));
        $font->set('Encoding', new PdfName('WinAnsiEncoding'));
        $fontNum = $updater->addObject($font);

        $size = 8.0;
        $content = "q\n0.4 0.4 0.4 RG 0.7 w\n0 0 " . self::f($w) . ' ' . self::f($h) . " re S\n";
        $content .= "BT\n/Helv " . self::f($size) . " Tf\n0 g\n";
        $y = $h - $size - 2;
        foreach ($lines as $i => $line) {
            $enc = WinAnsiEncoding::fromUtf8((string) $line);
            $esc = strtr($enc, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)', "\r" => '', "\n" => '']);
            $content .= self::f(4) . ' ' . self::f($i === 0 ? $y : -($size + 2)) . " Td\n($esc) Tj\n";
        }
        $content .= "ET\nQ";

        $ap = new PdfStream();
        $d = $ap->getDictionary();
        $d->set('Type', new PdfName('XObject'));
        $d->set('Subtype', new PdfName('Form'));
        $bbox = new PdfArray();
        foreach ([0.0, 0.0, $w, $h] as $v) { $bbox->add(new PdfReal($v)); }
        $d->set('BBox', $bbox);
        $fontRes = new PdfDictionary();
        $fontRes->set('Helv', new PdfIndirectReference($fontNum));
        $res = new PdfDictionary();
        $res->set('Font', $fontRes);
        $d->set('Resources', $res);
        $ap->setData($content);

        return $updater->addObject($ap);
    }

    private static function f(float $v): string
    {
        return rtrim(rtrim(sprintf('%.2F', $v), '0'), '.');
    }
}
