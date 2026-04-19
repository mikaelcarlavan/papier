<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfInteger, PdfName};

/**
 * Radio-button field (`/FT /Btn` with Radio + NoToggleToOff bits) (§12.7.4.2).
 *
 * A radio button belongs to a group of sibling fields that share the same
 * parent.  Selecting one button deselects the others.  The value is the
 * export name of the selected button (a PDF name).
 *
 * Example:
 *
 *   $group = new RadioButtonField('form.gender');          // parent node
 *   $male  = new RadioButtonField('form.gender', 'M');
 *   $male->setRect(72, 620, 84, 632)->setSelected('M');
 *   $female = new RadioButtonField('form.gender', 'F');
 *   $female->setRect(120, 620, 132, 632);
 */
final class RadioButtonField extends FormField
{
    /**
     * @param string $name         Fully-qualified field name.
     * @param string $partialName  The `/T` partial name; defaults to $name.
     */
    public function __construct(string $name, string $partialName = '')
    {
        parent::__construct($name, $partialName ?: $name);
        $this->dict->set('FT', new PdfName('Btn'));
        // Radio (bit 16) + NoToggleToOff (bit 15)
        $this->dict->set('Ff', new PdfInteger((1 << 15) | (1 << 14)));
    }

    public function getFieldType(): string { return 'Btn'; }

    /**
     * Pre-select this button by setting `/V` to the given name.
     *
     * @param string $value  The export name of the option to select.
     */
    public function setSelected(string $value): static
    {
        $this->dict->set('V', new PdfName($value));
        return $this;
    }
}
