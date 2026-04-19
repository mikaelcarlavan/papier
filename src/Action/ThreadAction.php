<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfObject;

/**
 * Follow an article thread (`/S /Thread`).
 *
 * Navigates to the first or a specific bead in an article thread defined in
 * the document's `/Threads` array (§12.4.3).
 */
final class ThreadAction extends Action
{
    /**
     * @param PdfObject      $thread  Indirect reference to the thread dictionary.
     * @param PdfObject|null $bead    Optional specific bead (article section) to jump to.
     */
    public function __construct(PdfObject $thread, ?PdfObject $bead = null)
    {
        parent::__construct();
        $this->dict->set('D', $thread);
        if ($bead !== null) {
            $this->dict->set('B', $bead);
        }
    }

    public function getSubtype(): string { return 'Thread'; }
}
