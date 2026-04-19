<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfInteger, PdfObject};

/**
 * Reset form fields to their default values (`/S /ResetForm`).
 *
 * When $fields is null, all fields in the document are reset.  When $fields
 * is provided, the `Exclude` flag bit (bit 1) in $flags inverts the sense:
 * if set, all fields *except* those listed are reset.
 */
final class ResetFormAction extends Action
{
    /**
     * @param PdfObject|null $fields  Array of field references/names, or null to reset all.
     * @param int            $flags   Flags bitfield; bit 1 (`Exclude`) inverts the field list.
     */
    public function __construct(?PdfObject $fields = null, int $flags = 0)
    {
        parent::__construct();
        if ($fields !== null) { $this->dict->set('Fields', $fields); }
        if ($flags !== 0)     { $this->dict->set('Flags', new PdfInteger($flags)); }
    }

    public function getSubtype(): string { return 'ResetForm'; }
}
