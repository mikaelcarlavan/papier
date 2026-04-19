<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfObject;

/**
 * Trigger a page-transition effect during a scripted presentation (`/S /Trans`).
 *
 * Used inside JavaScript to programmatically trigger a transition.  The
 * transition dictionary has the same structure as a page `/Trans` entry —
 * build it with {@see \Papier\Structure\PageTransition}.
 */
final class TransitionAction extends Action
{
    /**
     * @param PdfObject|null $transDict  Transition dictionary; omit to use the
     *                                   page's own `/Trans` entry.
     */
    public function __construct(?PdfObject $transDict = null)
    {
        parent::__construct();
        if ($transDict !== null) {
            $this->dict->set('Trans', $transDict);
        }
    }

    public function getSubtype(): string { return 'Trans'; }
}
