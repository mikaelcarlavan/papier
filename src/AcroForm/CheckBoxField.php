<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfName};

/**
 * Check-box field (`/FT /Btn`, no PushButton/Radio bits) (ISO 32000-1 §12.7.4.2).
 *
 * A check box has two states: on (the `$onValue` name) and off (`Off`).
 * The default on-value is `Yes`; change it when multiple check boxes share
 * the same fully-qualified name in a radio-button group.
 *
 * Example:
 *
 *   $cb = new CheckBoxField('form.agreeToTerms', 'agreeToTerms', 'Yes');
 *   $cb->setRect(72, 650, 84, 662);
 *   $form->addField($cb);
 *   $page->addFormField($cb);
 */
final class CheckBoxField extends FormField
{
    /**
     * @param string $name         Fully-qualified field name.
     * @param string $partialName  The `/T` partial name; defaults to $name.
     * @param string $onValue      Export value when checked (default `Yes`).
     */
    public function __construct(
        string $name,
        string $partialName = '',
        private string $onValue = 'Yes',
    ) {
        parent::__construct($name, $partialName ?: $name);
        $this->dict->set('FT', new PdfName('Btn'));
        $this->dict->set('V', new PdfName('Off'));
    }

    public function getFieldType(): string { return 'Btn'; }

    /** Set the field value to the on-value (check the box). */
    public function check(): static { $this->dict->set('V', new PdfName($this->onValue)); return $this; }

    /** Set the field value to `Off` (uncheck the box). */
    public function uncheck(): static { $this->dict->set('V', new PdfName('Off')); return $this; }

    /** Return the export name used when the box is checked. */
    public function getOnValue(): string { return $this->onValue; }
}
