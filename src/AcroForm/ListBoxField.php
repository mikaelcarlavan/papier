<?php

declare(strict_types=1);

namespace Papier\AcroForm;

use Papier\Objects\{PdfArray, PdfInteger, PdfName, PdfString};

/**
 * List-box choice field (`/FT /Ch` without FLAG_COMBO) (ISO 32000-1 §12.7.4.4).
 *
 * A list box displays several rows simultaneously.  When FLAG_MULTI_SELECT is
 * set, the user may select more than one item; in that case `/V` becomes an
 * array of names.
 *
 * Example:
 *
 *   $list = new ListBoxField('form.skills');
 *   $list->addOption('php', 'PHP')
 *        ->addOption('js',  'JavaScript')
 *        ->addOption('py',  'Python')
 *        ->setMultiSelect(true)
 *        ->buildOpt()
 *        ->setRect(72, 540, 250, 600);
 */
final class ListBoxField extends FormField
{
    /** Allow selecting more than one item (bit 22). */
    public const FLAG_MULTI_SELECT  = 1 << 21;
    /** Commit the selection immediately on click without a submit (bit 26). */
    public const FLAG_COMMIT_ON_SEL = 1 << 25;

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
        // No Combo flag → list box
    }

    public function getFieldType(): string { return 'Ch'; }

    /**
     * Add a single option.
     *
     * @param string $value  Export value stored when selected.
     * @param string $label  Display label; defaults to $value.
     */
    public function addOption(string $value, string $label = ''): static
    {
        $this->options[$value] = $label ?: $value;
        return $this;
    }

    /**
     * Toggle multi-select mode (FLAG_MULTI_SELECT / bit 22).
     *
     * @param bool $ms  true to allow multiple selections.
     */
    public function setMultiSelect(bool $ms = true): static
    {
        $ff = ($this->dict->get('Ff') instanceof PdfInteger ? $this->dict->get('Ff')->getValue() : 0);
        $this->dict->set('Ff', new PdfInteger($ms ? ($ff | self::FLAG_MULTI_SELECT) : ($ff & ~self::FLAG_MULTI_SELECT)));
        return $this;
    }

    /**
     * Build and write the `/Opt` array from the registered options.
     *
     * Called automatically by {@see self::getDictionary()} — no need to call
     * this manually.  Each entry is a `[export-value, display-label]` sub-array
     * per §12.7.4.4 Table 231.
     */
    public function buildOpt(): static
    {
        $opt = new PdfArray();
        foreach ($this->options as $value => $label) {
            $pair = new PdfArray();
            $pair->add(new PdfString((string)$value));
            $pair->add(new PdfString($label));
            $opt->add($pair);
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
