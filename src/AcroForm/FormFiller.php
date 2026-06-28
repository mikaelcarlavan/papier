<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Font\Encoding\WinAnsiEncoding;
use Papier\Objects\{
    PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfName, PdfNull, PdfObject, PdfReal, PdfStream, PdfString
};
use Papier\Parser\PdfParser;
use Papier\Writer\IncrementalUpdater;

/**
 * Fills the fields of an existing AcroForm and saves via an incremental update
 * (ISO 32000-1 §12.7).  Text fields get a regenerated appearance stream so the
 * value is visible in every viewer; check boxes/radios set their on/off state.
 *
 * Example:
 *
 *   $filler = new FormFiller(file_get_contents('form.pdf'));
 *   $filler->setText('person.name', 'Alice')
 *          ->setCheckbox('subscribe', true);
 *   file_put_contents('filled.pdf', $filler->save());
 */
final class FormFiller
{
    private PdfParser $parser;

    /** @var array<string, array{objNum:int, dict:PdfDictionary}> terminal fields by name */
    private array $fields = [];

    /** @var array<string, array{type:string, value:mixed}> queued edits by field name */
    private array $edits = [];

    private ?int $helvObjNum = null;

    public function __construct(string $pdf)
    {
        $this->parser = new PdfParser($pdf);
        $this->parser->parse();
        $this->collectFields();
    }

    /** Field names discovered in the document. @return string[] */
    public function getFieldNames(): array
    {
        return array_keys($this->fields);
    }

    /** Set a text (or choice) field's value. */
    public function setText(string $name, string $value): static
    {
        $this->edits[$name] = ['type' => 'text', 'value' => $value];
        return $this;
    }

    /** Set a check box / radio on or off (true → its on-state, or pass the state name). */
    public function setCheckbox(string $name, bool|string $on): static
    {
        $this->edits[$name] = ['type' => 'button', 'value' => $on];
        return $this;
    }

    /** Generic setter inferring the field type. */
    public function setValue(string $name, mixed $value): static
    {
        return is_bool($value)
            ? $this->setCheckbox($name, $value)
            : $this->setText($name, (string) $value);
    }

    /** Build the filled PDF bytes (original + appended revision). */
    public function save(): string
    {
        $updater = new IncrementalUpdater($this->parser);

        foreach ($this->edits as $name => $edit) {
            if (!isset($this->fields[$name])) {
                continue;
            }
            $objNum = $this->fields[$name]['objNum'];
            $dict   = $this->fields[$name]['dict'];

            if ($edit['type'] === 'button') {
                $state = $edit['value'] === true ? 'Yes' : ($edit['value'] === false ? 'Off' : (string) $edit['value']);
                $dict->set('V',  new PdfName($state));
                $dict->set('AS', new PdfName($state));
            } else {
                $value = (string) $edit['value'];
                $dict->set('V', PdfString::text($value));
                $this->buildTextAppearance($dict, $value, $updater);
            }
            $updater->updateObject($objNum, $dict);
        }

        return $updater->build();
    }

    /** Write the filled PDF to a file. */
    public function saveAs(string $path): void
    {
        if (file_put_contents($path, $this->save()) === false) {
            throw new \RuntimeException("Cannot write PDF to: $path");
        }
    }

    // ── internals ───────────────────────────────────────────────────────────────

    private function collectFields(): void
    {
        $catalog = $this->parser->getCatalog();
        if ($catalog === null) {
            return;
        }
        $acro = $this->parser->resolve($catalog->get('AcroForm') ?? new PdfNull());
        if (!$acro instanceof PdfDictionary) {
            return;
        }
        $fields = $this->parser->resolve($acro->get('Fields') ?? new PdfNull());
        if (!$fields instanceof PdfArray) {
            return;
        }
        foreach ($fields->getItems() as $ref) {
            $this->walkField($ref, '');
        }
    }

    private function walkField(PdfObject $ref, string $parentName): void
    {
        if (!$ref instanceof PdfIndirectReference) {
            return;
        }
        $objNum = $ref->getObjectNumber();
        $dict   = $this->parser->resolveObject($objNum);
        if (!$dict instanceof PdfDictionary) {
            return;
        }

        $t    = $dict->get('T');
        $part = $t instanceof PdfString ? $this->decodeString($t->getValue()) : '';
        $name = $parentName === '' ? $part : ($part === '' ? $parentName : "$parentName.$part");

        $kids = $dict->get('Kids');
        if ($kids instanceof PdfArray && $this->hasFieldKids($kids)) {
            foreach ($kids->getItems() as $kid) {
                $this->walkField($kid, $name);
            }
            return;
        }

        $this->fields[$name] = ['objNum' => $objNum, 'dict' => $dict];
    }

    private function hasFieldKids(PdfArray $kids): bool
    {
        foreach ($kids->getItems() as $kid) {
            $k = $this->parser->resolve($kid);
            if ($k instanceof PdfDictionary && $k->get('T') !== null) {
                return true;
            }
        }
        return false;
    }

    /**
     * Build and attach an /AP /N appearance stream for a text field value.
     */
    private function buildTextAppearance(PdfDictionary $field, string $value, IncrementalUpdater $updater): void
    {
        $rect = $this->parser->resolve($field->get('Rect') ?? new PdfNull());
        if (!$rect instanceof PdfArray) {
            return; // no widget rectangle — let the viewer render via NeedAppearances
        }
        $r = array_map(
            fn($o) => ($o instanceof PdfReal || $o instanceof PdfInteger) ? (float) $o->getValue() : 0.0,
            $rect->getItems(),
        );
        $w = abs(($r[2] ?? 0) - ($r[0] ?? 0));
        $h = abs(($r[3] ?? 0) - ($r[1] ?? 0));
        if ($w <= 0 || $h <= 0) {
            return;
        }

        [$fontName, $size, $colorOp] = $this->parseDA($field);
        if ($size <= 0) {
            $size = max(6.0, min(12.0, $h - 4.0));
        }
        $baseline = max(2.0, ($h - $size) / 2.0 + $size * 0.2);

        $encoded = WinAnsiEncoding::fromUtf8($value);
        $escaped = strtr($encoded, ['\\' => '\\\\', '(' => '\\(', ')' => '\\)', "\r" => '', "\n" => '']);

        $content = "/Tx BMC\nq\n1 1 " . $this->f($w - 2) . ' ' . $this->f($h - 2) . " re W n\n"
                 . "BT\n/" . $fontName . ' ' . $this->f($size) . " Tf\n" . $colorOp . "\n"
                 . '2 ' . $this->f($baseline) . " Td\n($escaped) Tj\nET\nQ\nEMC";

        $ap = new PdfStream();
        $apDict = $ap->getDictionary();
        $apDict->set('Type', new PdfName('XObject'));
        $apDict->set('Subtype', new PdfName('Form'));
        $bbox = new PdfArray();
        foreach ([0.0, 0.0, $w, $h] as $v) { $bbox->add(new PdfReal($v)); }
        $apDict->set('BBox', $bbox);

        // Resources referencing a Helvetica font for the DA font name.
        $fontRes = new PdfDictionary();
        $fontRes->set($fontName, new PdfIndirectReference($this->helvetica($updater)));
        $res = new PdfDictionary();
        $res->set('Font', $fontRes);
        $apDict->set('Resources', $res);

        $ap->setData($content);

        $apNum = $updater->addObject($ap);
        $apEntry = new PdfDictionary();
        $apEntry->set('N', new PdfIndirectReference($apNum));
        $field->set('AP', $apEntry);
    }

    /**
     * Parse the field's (or AcroForm's) /DA into [fontName, size, colorOps].
     *
     * @return array{0:string, 1:float, 2:string}
     */
    private function parseDA(PdfDictionary $field): array
    {
        $da = '';
        $daObj = $field->get('DA');
        if ($daObj instanceof PdfString) {
            $da = $daObj->getValue();
        } else {
            $catalog = $this->parser->getCatalog();
            $acro = $catalog ? $this->parser->resolve($catalog->get('AcroForm') ?? new PdfNull()) : null;
            if ($acro instanceof PdfDictionary && $acro->get('DA') instanceof PdfString) {
                $da = $acro->get('DA')->getValue();
            }
        }

        $fontName = 'Helv';
        $size     = 0.0;
        $colorOp  = '0 g';
        if (preg_match('#/([A-Za-z0-9]+)\s+([\d.]+)\s+Tf#', $da, $m)) {
            $fontName = $m[1];
            $size     = (float) $m[2];
            // Colour operators follow the Tf clause.
            $after = trim(substr($da, (int) strpos($da, 'Tf') + 2));
            if ($after !== '') {
                $colorOp = $after;
            }
        }
        return [$fontName, $size, $colorOp];
    }

    /** Allocate (once) a Helvetica font object for appearance resources. */
    private function helvetica(IncrementalUpdater $updater): int
    {
        if ($this->helvObjNum !== null) {
            return $this->helvObjNum;
        }
        $font = new PdfDictionary();
        $font->set('Type', new PdfName('Font'));
        $font->set('Subtype', new PdfName('Type1'));
        $font->set('BaseFont', new PdfName('Helvetica'));
        $font->set('Encoding', new PdfName('WinAnsiEncoding'));
        $this->helvObjNum = $updater->addObject($font);
        return $this->helvObjNum;
    }

    private function decodeString(string $val): string
    {
        if (str_starts_with($val, "\xFE\xFF")) {
            return (string) mb_convert_encoding(substr($val, 2), 'UTF-8', 'UTF-16BE');
        }
        return $val;
    }

    private function f(float $v): string
    {
        return rtrim(rtrim(sprintf('%.2F', $v), '0'), '.');
    }
}
