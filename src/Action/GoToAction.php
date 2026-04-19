<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfArray, PdfObject, PdfString};

/**
 * Navigate to a destination in the same document (`/S /GoTo`).
 *
 * Pass either a **named destination** string or an **explicit destination**
 * array built with one of the `Papier\Destination\*` factory classes.
 *
 * Named destination (string):
 *
 *   $action = new GoToAction('chapter-2');
 *
 * Explicit destination (array from factory):
 *
 *   $action = new GoToAction(XYZDestination::create($page->getDictionary(), 0, 750, 0));
 */
final class GoToAction extends Action
{
    /**
     * @param string|PdfObject $destination  Named destination string or explicit destination array.
     */
    public function __construct(string|PdfObject $destination)
    {
        parent::__construct();
        $this->dict->set('D', is_string($destination) ? new PdfString($destination) : $destination);
    }

    public function getSubtype(): string { return 'GoTo'; }
}
