<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfBoolean, PdfObject};

/**
 * Launch an external application or open/print a file (`/S /Launch`).
 *
 * On most modern viewers this is blocked by default due to security
 * restrictions.  Pass a file specification pointing to the application or
 * document to launch.
 */
final class LaunchAction extends Action
{
    /**
     * @param PdfObject $fileSpec   File specification of the application or document.
     * @param bool      $newWindow  true to open in a new window.
     */
    public function __construct(PdfObject $fileSpec, bool $newWindow = false)
    {
        parent::__construct();
        $this->dict->set('F', $fileSpec);
        if ($newWindow) {
            $this->dict->set('NewWindow', new PdfBoolean(true));
        }
    }

    public function getSubtype(): string { return 'Launch'; }
}
