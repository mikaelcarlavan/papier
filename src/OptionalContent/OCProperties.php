<?php

declare(strict_types=1);

namespace Papier\OptionalContent;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfString};

/**
 * Optional content properties dictionary (ISO 32000-1 §8.11.4).
 * Referenced from the document catalog as /OCProperties.
 */
final class OCProperties
{
    /** @var OCGroup[] */
    private array $allOCGs       = [];
    /** @var array  Default viewing configuration */
    private array $defaultConfig = [];

    /**
     * Register an optional content group (layer) with this document.
     *
     * All groups that appear anywhere in the document must be registered here.
     *
     * @param OCGroup $group  The layer to register.
     */
    public function addOCG(OCGroup $group): static
    {
        $this->allOCGs[] = $group;
        return $this;
    }

    /**
     * Set the default viewing configuration (`/D` entry).
     *
     * Determines which groups are visible when the document is first opened.
     *
     * @param string    $name       Configuration name (shown in viewer UI).
     * @param OCGroup[] $on         Groups that are ON by default.
     * @param OCGroup[] $off        Groups that are OFF by default.
     * @param string    $baseState  Initial state for unlisted groups: `'ON'`, `'OFF'`, or `'Unchanged'`.
     */
    public function setDefaultConfig(
        string $name,
        array  $on        = [],
        array  $off       = [],
        string $baseState = 'ON',
    ): static {
        $this->defaultConfig = compact('name', 'on', 'off', 'baseState');
        return $this;
    }

    public function toDictionary(): PdfDictionary
    {
        $dict = new PdfDictionary();

        $ocgs = new PdfArray();
        foreach ($this->allOCGs as $group) { $ocgs->add($group->getDictionary()); }
        $dict->set('OCGs', $ocgs);

        $config = $this->buildConfig($this->defaultConfig);
        $dict->set('D', $config);

        return $dict;
    }

    private function buildConfig(array $cfg): PdfDictionary
    {
        $c = new PdfDictionary();
        if (!empty($cfg['name'])) {
            $c->set('Name', PdfString::text($cfg['name']));
        }
        if (!empty($cfg['baseState'])) {
            $c->set('BaseState', new PdfName($cfg['baseState']));
        }
        if (!empty($cfg['on'])) {
            $arr = new PdfArray();
            foreach ($cfg['on'] as $group) {
                $arr->add($group instanceof OCGroup ? $group->getDictionary() : $group);
            }
            $c->set('ON', $arr);
        }
        if (!empty($cfg['off'])) {
            $arr = new PdfArray();
            foreach ($cfg['off'] as $group) {
                $arr->add($group instanceof OCGroup ? $group->getDictionary() : $group);
            }
            $c->set('OFF', $arr);
        }
        $c->set('ListMode', new PdfName('AllPages'));
        return $c;
    }
}
