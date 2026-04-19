<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfString;

/**
 * Execute a JavaScript script (`/S /JavaScript`).
 *
 * The script is stored in `/JS` as a PDF string or stream.  JavaScript is
 * supported by Adobe Acrobat and most full-featured viewers; avoid relying on
 * it in situations where compatibility is important.
 *
 * Example:
 *
 *   $action = new JavaScriptAction('app.alert("Hello from PDF!");');
 */
final class JavaScriptAction extends Action
{
    /**
     * @param string $script  JavaScript source code.
     */
    public function __construct(string $script)
    {
        parent::__construct();
        $this->dict->set('JS', new PdfString($script));
    }

    public function getSubtype(): string { return 'JavaScript'; }
}
