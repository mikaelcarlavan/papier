<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfArray, PdfBoolean, PdfObject};

/**
 * Show or hide one or more annotations (`/S /Hide`).
 *
 * Example — hide a button annotation after it is clicked:
 *
 *   $action = new HideAction($buttonAnnotRef, true);
 */
final class HideAction extends Action
{
    /**
     * @param PdfObject|PdfObject[] $annotations  A single annotation reference or
     *                                            an array of references / names.
     * @param bool                  $hide         true to hide, false to show.
     */
    public function __construct(PdfObject|array $annotations, bool $hide = true)
    {
        parent::__construct();
        if (is_array($annotations)) {
            $arr = new PdfArray();
            foreach ($annotations as $a) { $arr->add($a); }
            $this->dict->set('T', $arr);
        } else {
            $this->dict->set('T', $annotations);
        }
        $this->dict->set('H', new PdfBoolean($hide));
    }

    public function getSubtype(): string { return 'Hide'; }
}
