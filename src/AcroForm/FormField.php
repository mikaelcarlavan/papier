<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Elements\Color;
use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfReal, PdfString};

/**
 * Abstract base class for all AcroForm field types (ISO 32000-1 §12.7.3).
 *
 * Field types defined by the `/FT` entry:
 *   - `Btn` — button (push button, check box, radio button)
 *   - `Tx`  — text field
 *   - `Ch`  — choice (combo box, list box)
 *   - `Sig` — digital signature
 *
 * A field may be terminal (has a widget annotation and appears on the page)
 * or non-terminal (has child fields, no visual representation of its own).
 * Terminal fields are placed by calling {@see self::setRect()}, which merges
 * the field dictionary with a widget annotation dictionary (§12.7.3.3).
 *
 * Field flags (§12.7.3.1 Table 221):
 *   bit 1  — ReadOnly
 *   bit 2  — Required
 *   bit 3  — NoExport
 */
abstract class FormField
{
    protected PdfDictionary $dict;

    /** @var FormField[]  Child fields (for non-terminal nodes). */
    private array $kids = [];

    /** Whether this field has been merged with a widget annotation via setRect(). */
    private bool $widgetMode = false;

    /** Object number allocated by PdfWriter for indirect-reference resolution. */
    private ?int $allocatedObjNum = null;

    /**
     * @param string $fullyQualifiedName  Dot-separated full field name (e.g. `Person.Name`).
     * @param string $partialName         The `/T` (partial) name of this field node.
     */
    public function __construct(
        protected string $fullyQualifiedName,
        protected string $partialName,
    ) {
        $this->dict = new PdfDictionary();
        $this->dict->set('T', new PdfString($partialName));
    }

    /**
     * Return the PDF field type string (`Tx`, `Btn`, `Ch`, or `Sig`).
     */
    abstract public function getFieldType(): string;

    /** Return the partial name (`/T`). */
    public function getPartialName(): string { return $this->partialName; }

    /** Return the fully-qualified name. */
    public function getFullyQualifiedName(): string { return $this->fullyQualifiedName; }

    /**
     * Set the field flags bitfield (`/Ff`).
     *
     * Setting this replaces any previously set flags.  For individual flags
     * prefer {@see self::setReadOnly()} and {@see self::setRequired()}.
     *
     * @param int $flags  Bitfield value (Table 221 + type-specific tables).
     */
    public function setFlags(int $flags): static
    {
        $this->dict->set('Ff', new PdfInteger($flags));
        return $this;
    }

    /**
     * Mark the field as read-only (bit 1 of `/Ff`).
     *
     * A read-only field cannot be modified by the user.
     *
     * @param bool $ro  true to set read-only, false to clear.
     */
    public function setReadOnly(bool $ro = true): static
    {
        $ff = $ro ? 1 : 0;
        $this->dict->set('Ff', new PdfInteger($ff));
        return $this;
    }

    /**
     * Mark the field as required (bit 2 of `/Ff`).
     *
     * A required field must have a non-empty value before the form can be
     * submitted.
     *
     * @param bool $req  true to require, false to make optional.
     */
    public function setRequired(bool $req = true): static
    {
        $current = ($this->dict->get('Ff') instanceof PdfInteger)
            ? $this->dict->get('Ff')->getValue()
            : 0;
        $this->dict->set('Ff', new PdfInteger(
            $req ? ($current | 2) : ($current & ~2)
        ));
        return $this;
    }

    /**
     * Set the default field value (`/DV`).
     *
     * This value is used when the form is reset.
     *
     * @param PdfObject $value  A PdfString, PdfName, or PdfArray depending
     *                          on the field type.
     */
    public function setDefaultValue(PdfObject $value): static
    {
        $this->dict->set('DV', $value);
        return $this;
    }

    /**
     * Set the current field value (`/V`).
     *
     * @param PdfObject $value  Current value; type depends on the field.
     */
    public function setValue(PdfObject $value): static
    {
        $this->dict->set('V', $value);
        return $this;
    }

    /**
     * Set the tooltip (`/TU`) shown on mouse-over.
     *
     * @param string $tip  Tooltip text (UTF-8).
     */
    public function setToolTip(string $tip): static
    {
        $this->dict->set('TU', PdfString::text($tip));
        return $this;
    }

    /**
     * Set the mapping name (`/TM`) used in export formats.
     *
     * @param string $name  Mapping name (e.g. for FDF export).
     */
    public function setMappingName(string $name): static
    {
        $this->dict->set('TM', new PdfString($name));
        return $this;
    }

    /**
     * Add a child field (for non-terminal fields only).
     *
     * @param FormField $kid  Child field to append.
     */
    public function addKid(FormField $kid): static
    {
        $this->kids[] = $kid;
        return $this;
    }

    /**
     * Return all child fields.
     *
     * @return FormField[]
     */
    public function getKids(): array { return $this->kids; }

    /**
     * Enable merged field+widget mode and set the annotation rectangle
     * (ISO 32000-1 §12.7.3.3).
     *
     * When a field has exactly one widget annotation, the field and widget
     * dictionaries may be merged into a single object for compactness.
     * After calling this method, the field dictionary carries `/Type /Annot`,
     * `/Subtype /Widget`, and `/Rect`, and the writer includes it in both
     * the page `/Annots` array and the AcroForm `/Fields` array.
     *
     * @param float $x1  Lower-left X in points.
     * @param float $y1  Lower-left Y in points.
     * @param float $x2  Upper-right X in points.
     * @param float $y2  Upper-right Y in points.
     */
    public function setRect(float $x1, float $y1, float $x2, float $y2): static
    {
        $this->widgetMode = true;
        $this->dict->set('Type',    new PdfName('Annot'));
        $this->dict->set('Subtype', new PdfName('Widget'));
        $rect = new PdfArray();
        $rect->add(new PdfReal($x1));
        $rect->add(new PdfReal($y1));
        $rect->add(new PdfReal($x2));
        $rect->add(new PdfReal($y2));
        $this->dict->set('Rect', $rect);
        return $this;
    }

    /**
     * Return true if this field has been merged with a widget annotation via
     * {@see self::setRect()}.
     */
    public function isWidget(): bool { return $this->widgetMode; }

    /**
     * Set the default appearance for this field (`/DA`).
     *
     * Controls the font, size, and colour used to render the field value.
     * Pass the font resource name (e.g. the string returned by
     * {@see \Papier\PdfDocument::addFont()}), the point size, and an optional
     * RGB text colour (each component in [0, 1]; defaults to black).
     *
     * Example:
     *
     *   $regular = $doc->addFont('Helvetica');      // returns e.g. 'F1'
     *   $field->setDefaultAppearance($regular, 10);
     *   $field->setDefaultAppearance($regular, 11, 0.2, 0.2, 0.8); // blue text
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
     * Set a raw default appearance string (`/DA`) for advanced use.
     *
     * Use this only when you need to produce a non-standard DA string.
     * For normal cases prefer {@see self::setDefaultAppearance()}.
     *
     * @param string $da  PDF content-stream fragment, e.g. `/F1 10 Tf 0 g`.
     */
    public function setDefaultAppearanceRaw(string $da): static
    {
        $this->dict->set('DA', new PdfString($da));
        return $this;
    }

    /**
     * Set the widget highlight mode (`/H`).
     *
     * Controls the visual feedback when the field's widget is activated.
     *
     * @param string $h  `N` = none, `I` = invert, `O` = outline,
     *                   `P` = push (default), `T` = toggle.
     */
    public function setHighlightMode(string $h): static
    {
        $this->dict->set('H', new PdfName($h));
        return $this;
    }

    /**
     * Set the widget background colour (`/MK/BG`).
     *
     * Accepts a {@see Color} object or three RGB floats.
     *
     * @param Color|float $colorOrR  Color object, or the red component [0, 1].
     * @param float|null  $g         Green component (only when passing raw floats).
     * @param float|null  $b         Blue  component (only when passing raw floats).
     */
    public function setBackgroundColor(Color|float $colorOrR, ?float $g = null, ?float $b = null): static
    {
        [$r, $gv, $bv] = $colorOrR instanceof Color
            ? $colorOrR->toRgb()
            : [$colorOrR, $g ?? 0.0, $b ?? 0.0];
        $arr = new PdfArray();
        $arr->add(new PdfReal($r)); $arr->add(new PdfReal($gv)); $arr->add(new PdfReal($bv));
        $this->getOrCreateMK()->set('BG', $arr);
        return $this;
    }

    /**
     * Set the widget border colour (`/MK/BC`).
     *
     * Accepts a {@see Color} object or three RGB floats.
     *
     * @param Color|float $colorOrR  Color object, or the red component [0, 1].
     * @param float|null  $g         Green component (only when passing raw floats).
     * @param float|null  $b         Blue  component (only when passing raw floats).
     */
    public function setBorderColor(Color|float $colorOrR, ?float $g = null, ?float $b = null): static
    {
        [$r, $gv, $bv] = $colorOrR instanceof Color
            ? $colorOrR->toRgb()
            : [$colorOrR, $g ?? 0.0, $b ?? 0.0];
        $arr = new PdfArray();
        $arr->add(new PdfReal($r)); $arr->add(new PdfReal($gv)); $arr->add(new PdfReal($bv));
        $this->getOrCreateMK()->set('BC', $arr);
        return $this;
    }

    /**
     * Set the parent field reference (`/Parent`).
     *
     * Used by the writer when building a hierarchical field tree.
     *
     * @param PdfObject $parentRef  Indirect reference to the parent field.
     */
    public function setParent(PdfObject $parentRef): static
    {
        $this->dict->set('Parent', $parentRef);
        return $this;
    }

    /** Return the underlying field dictionary. */
    public function getDictionary(): PdfDictionary { return $this->dict; }

    /**
     * Record the indirect-object number allocated for this field.
     *
     * @internal  Called by PdfWriter during serialisation.
     * @param int $num  Object number.
     */
    public function setAllocatedObjNum(int $num): void { $this->allocatedObjNum = $num; }

    /**
     * Return the allocated object number, or null before serialisation.
     *
     * @internal  Used by PdfWriter to build parent references.
     */
    public function getAllocatedObjNum(): ?int { return $this->allocatedObjNum; }

    /** @deprecated Use setRect() for the merged field+widget approach. */
    public function setWidget(PdfObject $widgetRef): static
    {
        return $this;
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /** Return or create the appearance characteristics dictionary (`/MK`). */
    private function getOrCreateMK(): PdfDictionary
    {
        $mk = $this->dict->get('MK');
        if (!($mk instanceof PdfDictionary)) {
            $mk = new PdfDictionary();
            $this->dict->set('MK', $mk);
        }
        return $mk;
    }
}
