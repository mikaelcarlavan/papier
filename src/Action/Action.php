<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfDictionary, PdfName, PdfObject};

/**
 * Abstract base for all PDF action dictionaries.
 *
 * Subclasses declare their `/S` value via {@see self::getSubtype()} and add
 * type-specific entries in their constructor.
 */
abstract class Action
{
    protected PdfDictionary $dict;

    public function __construct()
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Action'));
        $this->dict->set('S', new PdfName($this->getSubtype()));
    }

    /**
     * Return the PDF action subtype string (the value of `/S`).
     */
    abstract public function getSubtype(): string;

    /**
     * Chain a subsequent action (`/Next`).
     *
     * After the current action executes, the viewer executes $next.  You may
     * chain an array of actions or a single action dictionary / indirect ref.
     *
     * @param PdfObject $next  A single action dict or an array of action dicts.
     */
    public function setNext(PdfObject $next): static
    {
        $this->dict->set('Next', $next);
        return $this;
    }

    /** Return the underlying action dictionary. */
    public function getDictionary(): PdfDictionary { return $this->dict; }
}
