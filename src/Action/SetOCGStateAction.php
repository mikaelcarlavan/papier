<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfBoolean, PdfObject};

/**
 * Change the visibility of optional content groups (`/S /SetOCGState`) (PDF 1.5+).
 *
 * The state array alternates between state-change operators (`ON`, `OFF`,
 * `Toggle`) and indirect references to OCG (optional content group)
 * dictionaries.
 *
 * Example:
 *
 *   $state = new PdfArray();
 *   $state->add(new PdfName('ON'));
 *   $state->add($ocgRef);
 *   $action = new SetOCGStateAction($state);
 */
final class SetOCGStateAction extends Action
{
    /**
     * @param PdfObject $stateArray  Array of state-change entries.
     * @param bool      $preserveRB  true (default) preserves radio-button group
     *                               exclusivity when toggling.
     */
    public function __construct(PdfObject $stateArray, bool $preserveRB = true)
    {
        parent::__construct();
        $this->dict->set('State', $stateArray);
        if (!$preserveRB) {
            $this->dict->set('PreserveRB', new PdfBoolean(false));
        }
    }

    public function getSubtype(): string { return 'SetOCGState'; }
}
