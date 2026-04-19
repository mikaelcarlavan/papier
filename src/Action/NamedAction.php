<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfName;

/**
 * Execute a predefined named action (`/S /Named`).
 *
 * Standard names (§12.6.4.11 Table 212):
 *   `NextPage`, `PrevPage`, `FirstPage`, `LastPage`.
 *
 * Example:
 *
 *   $action = new NamedAction('NextPage');
 */
final class NamedAction extends Action
{
    /**
     * @param string $name  Predefined action name.
     */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->dict->set('N', new PdfName($name));
    }

    public function getSubtype(): string { return 'Named'; }
}
