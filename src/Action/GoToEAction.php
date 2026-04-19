<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfObject;

/**
 * Navigate to a destination inside an embedded PDF (`/S /GoToE`) (PDF 1.6+).
 *
 * Targets a destination within a PDF document that is itself embedded as a
 * file attachment inside the current document.
 */
final class GoToEAction extends Action
{
    /**
     * @param PdfObject $fileSpec     File specification identifying the embedded document.
     * @param PdfObject $destination  Destination within the embedded document.
     */
    public function __construct(PdfObject $fileSpec, PdfObject $destination)
    {
        parent::__construct();
        $this->dict->set('F', $fileSpec);
        $this->dict->set('D', $destination);
    }

    public function getSubtype(): string { return 'GoToE'; }
}
