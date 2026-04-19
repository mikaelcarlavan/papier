<?php

declare(strict_types=1);

namespace Papier\OptionalContent;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject, PdfString};

/**
 * Optional content group (layer) (ISO 32000-1 §8.11.2).
 *
 * An OCG is the fundamental unit of optional content.  It has a name and
 * a state (ON or OFF).  Groups may be arranged in a display hierarchy.
 */
final class OCGroup
{
    private PdfDictionary $dict;

    public function __construct(private readonly string $name)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('OCG'));
        $this->dict->set('Name', PdfString::text($name));
    }

    public function getName(): string { return $this->name; }

    /** Set usage metadata (e.g., Print, View, Zoom, etc.). */
    public function setUsage(PdfObject $usage): static
    {
        $this->dict->set('Usage', $usage);
        return $this;
    }

    /** Set intent — View, Design, or All. */
    public function setIntent(string|array $intent): static
    {
        if (is_string($intent)) {
            $this->dict->set('Intent', new PdfName($intent));
        } else {
            $arr = new PdfArray();
            foreach ($intent as $i) { $arr->add(new PdfName($i)); }
            $this->dict->set('Intent', $arr);
        }
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
