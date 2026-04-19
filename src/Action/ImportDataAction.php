<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfObject;

/**
 * Import form data from an FDF file (`/S /ImportData`).
 *
 * The file specification must point to an FDF (Forms Data Format) file.
 */
final class ImportDataAction extends Action
{
    /**
     * @param PdfObject $fileSpec  File specification for the FDF source file.
     */
    public function __construct(PdfObject $fileSpec)
    {
        parent::__construct();
        $this->dict->set('F', $fileSpec);
    }

    public function getSubtype(): string { return 'ImportData'; }
}
