<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfInteger, PdfName};

/**
 * Push-button field (`/FT /Btn` with bit 17 set) (ISO 32000-1 §12.7.4.2).
 *
 * A push button is momentary; it does not retain a value.  Its behaviour is
 * entirely defined by the actions attached to the widget annotation (e.g. a
 * JavaScript action on the `/A` activation action key).
 *
 * Example:
 *
 *   $btn = new PushButtonField('submit');
 *   $btn->setRect(400, 30, 520, 54);
 *   $form->addField($btn);
 *   $page->addFormField($btn);
 */
final class PushButtonField extends FormField
{
    /**
     * @param string $name         Fully-qualified field name.
     * @param string $partialName  The `/T` partial name; defaults to $name.
     */
    public function __construct(string $name, string $partialName = '')
    {
        parent::__construct($name, $partialName ?: $name);
        $this->dict->set('FT', new PdfName('Btn'));
        $this->dict->set('Ff', new PdfInteger(1 << 16)); // PushButton bit
    }

    public function getFieldType(): string { return 'Btn'; }
}
