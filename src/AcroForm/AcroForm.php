<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfString};

/**
 * AcroForm — interactive form dictionary (ISO 32000-1 §12.7.2).
 *
 * The AcroForm dictionary is the root of all interactive form fields in a
 * PDF document.  It is stored at `/AcroForm` in the document catalog and
 * holds:
 *   - The top-level field array (`/Fields`).
 *   - Global defaults for font/appearance (`/DA`, `/DR`).
 *   - Document-wide behaviour flags (`/NeedAppearances`, `/SigFlags`).
 *
 * Usage:
 *   $form = new AcroForm();
 *   $form->setNeedAppearances(true)
 *        ->setDefaultAppearance('/F1 10 Tf 0 g');
 *
 *   $field = new TextField('FirstName');
 *   $field->setRect(72, 700, 300, 720);
 *   $form->addField($field);
 *   $page->addFormField($field);
 *
 *   $doc->setAcroForm($form);
 */
final class AcroForm
{
    private PdfDictionary $dict;

    /** @var FormField[] Top-level form fields. */
    private array $fields = [];

    public function __construct()
    {
        $this->dict = new PdfDictionary();
    }

    /**
     * Add a top-level field to the form.
     *
     * The field must also be registered with its page using
     * {@see \Papier\Structure\PdfPage::addFormField()} so that the writer
     * includes it in both `/Fields` and the page `/Annots` array.
     *
     * @param FormField $field  The field to add.
     */
    public function addField(FormField $field): static
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Return all top-level fields registered with this form.
     *
     * @return FormField[]
     */
    public function getFields(): array { return $this->fields; }

    /**
     * Set the NeedAppearances flag (§12.7.2 Table 218).
     *
     * When true, the viewer regenerates appearance streams for all fields
     * before rendering.  Useful when the appearance streams are absent or
     * stale.  Default: false.
     *
     * @param bool $need  true to request appearance regeneration.
     */
    public function setNeedAppearances(bool $need): static
    {
        $this->dict->set('NeedAppearances', new PdfBoolean($need));
        return $this;
    }

    /**
     * Set the signature flags bitfield (§12.7.2 Table 219).
     *
     * Bit 1 (0x01): SignaturesExist — the document contains at least one
     *               signed signature field.
     * Bit 2 (0x02): AppendOnly — signatures may only be appended, not modified.
     *
     * @param int $flags  Bit-field value.
     */
    public function setSigFlags(int $flags): static
    {
        $this->dict->set('SigFlags', new PdfInteger($flags));
        return $this;
    }

    /**
     * Set the calculate order array (`/CO`) (§12.7.2).
     *
     * An array of indirect references to fields with calculation actions,
     * in the order they should be calculated.
     *
     * @param PdfObject[] $fieldRefs  Indirect references to calculated fields.
     */
    public function setCalculateOrder(array $fieldRefs): static
    {
        $arr = new PdfArray();
        foreach ($fieldRefs as $ref) {
            $arr->add($ref);
        }
        $this->dict->set('CO', $arr);
        return $this;
    }

    /**
     * Set the default resource dictionary (`/DR`) (§12.7.2).
     *
     * Contains fonts and other resources referenced by default appearance
     * strings.  At minimum should contain the font used in `/DA`.
     *
     * @param PdfObject $resources  A resource dictionary (PdfDictionary).
     */
    public function setDefaultResources(PdfObject $resources): static
    {
        $this->dict->set('DR', $resources);
        return $this;
    }

    /**
     * Set the document-wide default appearance (`/DA`) (§12.7.2).
     *
     * Applied to fields that do not specify their own `/DA`.
     * Pass the font resource name returned by
     * {@see \Papier\PdfDocument::addFont()}, the point size, and an optional
     * RGB text colour (each component in [0, 1]; defaults to black).
     *
     * Example:
     *
     *   $regular = $doc->addFont('Helvetica');   // returns e.g. 'F1'
     *   $form->setDefaultAppearance($regular, 10);
     *
     * @param string $fontName  Font resource name (e.g. `'F1'`).
     * @param float  $fontSize  Font size in points.
     * @param float  $r         Red component   [0, 1] (default 0).
     * @param float  $g         Green component [0, 1] (default 0).
     * @param float  $b         Blue component  [0, 1] (default 0).
     */
    public function setDefaultAppearance(
        string $fontName,
        float  $fontSize,
        float  $r = 0.0,
        float  $g = 0.0,
        float  $b = 0.0,
    ): static {
        $fmt = static fn(float $v): string => rtrim(rtrim(number_format($v, 4, '.', ''), '0'), '.');
        $colorOp = ($r === 0.0 && $g === 0.0 && $b === 0.0)
            ? '0 g'
            : "{$fmt($r)} {$fmt($g)} {$fmt($b)} rg";
        $this->dict->set('DA', new PdfString("/{$fontName} {$fmt($fontSize)} Tf {$colorOp}"));
        return $this;
    }

    /**
     * Set a raw default appearance string for advanced use.
     *
     * @param string $da  PDF content-stream fragment, e.g. `/F1 10 Tf 0 g`.
     */
    public function setDefaultAppearanceRaw(string $da): static
    {
        $this->dict->set('DA', new PdfString($da));
        return $this;
    }

    /**
     * Set the document-wide default justification (`/Q`) (§12.7.2).
     *
     * @param int $q  0 = left-justified, 1 = centred, 2 = right-justified.
     */
    public function setQuadding(int $q): static
    {
        $this->dict->set('Q', new PdfInteger($q));
        return $this;
    }

    /** Return the underlying AcroForm dictionary. */
    public function getDictionary(): PdfDictionary { return $this->dict; }
}
