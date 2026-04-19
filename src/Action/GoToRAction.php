<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfBoolean, PdfObject};

/**
 * Navigate to a destination in a different PDF document (`/S /GoToR`).
 *
 * The target file is identified by a file specification.  The destination may
 * be a page number (PdfInteger), a named destination, or an explicit array.
 *
 * Example:
 *
 *   $action = new GoToRAction($fileSpecDict, new PdfInteger(0), true);
 */
final class GoToRAction extends Action
{
    /**
     * @param PdfObject $fileSpec     File specification for the target document.
     * @param PdfObject $destination  Destination within the target document.
     * @param bool      $newWindow    true to open the target in a new window.
     */
    public function __construct(
        PdfObject $fileSpec,
        PdfObject $destination,
        bool $newWindow = false,
    ) {
        parent::__construct();
        $this->dict->set('F', $fileSpec);
        $this->dict->set('D', $destination);
        if ($newWindow) {
            $this->dict->set('NewWindow', new PdfBoolean(true));
        }
    }

    public function getSubtype(): string { return 'GoToR'; }
}
