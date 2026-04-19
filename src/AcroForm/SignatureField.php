<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfName, PdfObject};

/**
 * Digital-signature field (`/FT /Sig`) (ISO 32000-1 §12.7.4.5).
 *
 * A signature field provides a region on the page where a digital signature
 * may be applied.  The `/V` entry is a signature dictionary when signed;
 * use {@see self::setSignature()} to supply a pre-built signature object.
 *
 * Signature fields are typically left unsigned (no `/V`) and the PDF viewer
 * handles the signing process interactively.
 *
 * Example:
 *
 *   $sig = new SignatureField('form.authorSignature');
 *   $sig->setRect(72, 40, 300, 80);
 *   $form->addField($sig);
 *   $page->addFormField($sig);
 */
final class SignatureField extends FormField
{
    /**
     * @param string $name         Fully-qualified field name.
     * @param string $partialName  The `/T` partial name; defaults to $name.
     */
    public function __construct(string $name, string $partialName = '')
    {
        parent::__construct($name, $partialName ?: $name);
        $this->dict->set('FT', new PdfName('Sig'));
    }

    public function getFieldType(): string { return 'Sig'; }

    /**
     * Attach a pre-built signature dictionary (`/V`).
     *
     * The signature dictionary must conform to §12.8.1 (Table 252).  In most
     * workflows this is left unset and filled in by the signing application.
     *
     * @param PdfObject $sig  An indirect reference to, or inline, signature dict.
     */
    public function setSignature(PdfObject $sig): static
    {
        $this->dict->set('V', $sig);
        return $this;
    }
}
