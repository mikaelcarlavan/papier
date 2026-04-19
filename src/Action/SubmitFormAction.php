<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfInteger, PdfObject};

/**
 * Submit form-field data to a URL (`/S /SubmitForm`).
 *
 * The target URL is specified by a file specification with a URL string.
 * Flags (Table 228) control the submission format (FDF, HTML, XFDF, PDF).
 *
 * Example:
 *
 *   $urlSpec = new PdfDictionary();
 *   $urlSpec->set('Type', new PdfName('Filespec'));
 *   $urlSpec->set('F', new PdfString('https://example.com/submit'));
 *   $action = new SubmitFormAction($urlSpec);
 */
final class SubmitFormAction extends Action
{
    /**
     * @param PdfObject      $fileSpec  File specification containing the target URL.
     * @param PdfObject|null $fields    Array of field references/names to include/exclude.
     * @param int            $flags     Submission flags bitfield (Table 228).
     */
    public function __construct(
        PdfObject $fileSpec,
        ?PdfObject $fields = null,
        int $flags = 0,
    ) {
        parent::__construct();
        $this->dict->set('F', $fileSpec);
        if ($fields !== null) { $this->dict->set('Fields', $fields); }
        if ($flags !== 0)     { $this->dict->set('Flags', new PdfInteger($flags)); }
    }

    public function getSubtype(): string { return 'SubmitForm'; }
}
