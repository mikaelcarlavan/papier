<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfInteger, PdfName, PdfString};

/**
 * Single-line or multi-line text entry field (`/FT /Tx`) (ISO 32000-1 §12.7.4.3).
 *
 * Text-field-specific flags (Table 228) are exposed as `FLAG_*` constants so
 * they can be combined and passed to {@see FormField::setFlags()}.
 *
 * Example:
 *
 *   $name = new TextField('form.name', 'name');
 *   $name->setMaxLength(50)
 *        ->setRect(100, 700, 400, 720)
 *        ->setDefaultAppearance($fontName, 12);
 */
final class TextField extends FormField
{
    /** Allow multiple lines of text (bit 13). */
    public const FLAG_MULTILINE          = 1 << 12;
    /** Mask characters for password entry (bit 14). */
    public const FLAG_PASSWORD           = 1 << 13;
    /** Field value is a file-path string (bit 21). */
    public const FLAG_FILE_SELECT        = 1 << 20;
    /** Suppress spell-checking (bit 23). */
    public const FLAG_DO_NOT_SPELL_CHECK = 1 << 22;
    /** Disable horizontal scrolling; clip at MaxLen (bit 24). */
    public const FLAG_DO_NOT_SCROLL      = 1 << 23;
    /** Divide into MaxLen equal cells — requires MaxLen (bit 25). */
    public const FLAG_COMB               = 1 << 24;
    /** Allow rich text (RTF) values (bit 26). */
    public const FLAG_RICH_TEXT          = 1 << 25;

    /**
     * @param string $name         Fully-qualified field name (dot-separated path).
     * @param string $partialName  The `/T` partial name; defaults to $name.
     */
    public function __construct(string $name, string $partialName = '')
    {
        parent::__construct($name, $partialName ?: $name);
        $this->dict->set('FT', new PdfName('Tx'));
    }

    public function getFieldType(): string { return 'Tx'; }

    /**
     * Set the maximum number of characters (`/MaxLen`).
     *
     * Required when {@see FLAG_COMB} is set.  Ignored if `/MaxLen` is absent.
     *
     * @param int $maxLen  Maximum character count (positive integer).
     */
    public function setMaxLength(int $maxLen): static
    {
        $this->dict->set('MaxLen', new PdfInteger($maxLen));
        return $this;
    }

    /**
     * Toggle multi-line mode (FLAG_MULTILINE / bit 13).
     *
     * @param bool $ml  true to allow multiple lines, false for single-line.
     */
    public function setMultiline(bool $ml = true): static
    {
        $ff = ($this->dict->get('Ff') instanceof PdfInteger ? $this->dict->get('Ff')->getValue() : 0);
        $this->dict->set('Ff', new PdfInteger($ml ? ($ff | self::FLAG_MULTILINE) : ($ff & ~self::FLAG_MULTILINE)));
        return $this;
    }

    /**
     * Toggle password mode (FLAG_PASSWORD / bit 14).
     *
     * Characters are displayed as bullets or asterisks.  The value is still
     * stored in the PDF and is readable; do not use for real secrets.
     *
     * @param bool $pw  true to mask characters.
     */
    public function setPassword(bool $pw = true): static
    {
        $ff = ($this->dict->get('Ff') instanceof PdfInteger ? $this->dict->get('Ff')->getValue() : 0);
        $this->dict->set('Ff', new PdfInteger($pw ? ($ff | self::FLAG_PASSWORD) : ($ff & ~self::FLAG_PASSWORD)));
        return $this;
    }



    /**
     * Toggle rich-text (RTF) mode (FLAG_RICH_TEXT / bit 26).
     *
     * When enabled, `/V` holds an RTF string and `/RV` holds the rich-text
     * value.  Most viewers have limited support for this feature.
     *
     * @param bool $rt  true to enable rich text.
     */
    public function setRichText(bool $rt = true): static
    {
        $ff = ($this->dict->get('Ff') instanceof PdfInteger ? $this->dict->get('Ff')->getValue() : 0);
        $this->dict->set('Ff', new PdfInteger($rt ? ($ff | self::FLAG_RICH_TEXT) : ($ff & ~self::FLAG_RICH_TEXT)));
        return $this;
    }
}
