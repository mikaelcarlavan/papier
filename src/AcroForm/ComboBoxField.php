<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfArray, PdfInteger, PdfName, PdfString};

/**
 * Combo-box (drop-down) choice field (`/FT /Ch` with FLAG_COMBO) (§12.7.4.4).
 *
 * A combo box shows a single selected item and drops down a list when clicked.
 * If FLAG_EDIT is set, the user may also type a custom value.
 *
 * Example:
 *
 *   $country = new ComboBoxField('form.country');
 *   $country->addOption('us', 'United States')
 *           ->addOption('gb', 'United Kingdom')
 *           ->addOption('ca', 'Canada')
 *           ->buildOpt()
 *           ->setRect(72, 600, 250, 616);
 */
final class ComboBoxField extends FormField
{
    /** Combo-box display flag — distinguishes combo from list box (bit 18). */
    public const FLAG_COMBO = 1 << 17;
    /** Allow the user to type a custom value (bit 19). */
    public const FLAG_EDIT  = 1 << 18;
    /** Sort the displayed options alphabetically (bit 20). */
    public const FLAG_SORT  = 1 << 19;

    /** @var array<string, string>  Export-value => display-label pairs. */
    private array $options = [];

    /**
     * @param string $name         Fully-qualified field name.
     * @param string $partialName  The `/T` partial name; defaults to $name.
     */
    public function __construct(string $name, string $partialName = '')
    {
        parent::__construct($name, $partialName ?: $name);
        $this->dict->set('FT', new PdfName('Ch'));
        $this->dict->set('Ff', new PdfInteger(self::FLAG_COMBO));
    }

    public function getFieldType(): string { return 'Ch'; }

    /**
     * Add a single option to the list.
     *
     * @param string $value  Export value stored in `/V` when selected.
     * @param string $label  Display label; defaults to $value.
     */
    public function addOption(string $value, string $label = ''): static
    {
        $this->options[$value] = $label ?: $value;
        return $this;
    }

    /**
     * Replace all options at once.
     *
     * @param array<string, string> $options  Map of export-value => label.
     */
    public function setOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Build and write the `/Opt` array from the registered options.
     *
     * Called automatically by {@see self::getDictionary()} — no need to call
     * this manually.  When value equals label a plain string is written;
     * otherwise a `[value, label]` sub-array is used (§12.7.4.4 Table 231).
     */
    public function buildOpt(): static
    {
        $opt = new PdfArray();
        foreach ($this->options as $value => $label) {
            if ($value === $label) {
                $opt->add(new PdfString((string)$value));
            } else {
                $pair = new PdfArray();
                $pair->add(new PdfString((string)$value));
                $pair->add(new PdfString($label));
                $opt->add($pair);
            }
        }
        $this->dict->set('Opt', $opt);
        return $this;
    }

    public function getDictionary(): \Papier\Objects\PdfDictionary
    {
        if (!empty($this->options)) {
            $this->buildOpt();
        }
        return parent::getDictionary();
    }
}
