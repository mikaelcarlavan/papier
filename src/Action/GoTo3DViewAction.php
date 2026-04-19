<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\PdfObject;

/**
 * Activate a view within a 3D annotation (`/S /GoTo3DView`) (PDF 1.6+).
 *
 * Changes the active 3D view displayed inside a {@see \Papier\Annotation\ThreeDAnnotation}.
 *
 * Example:
 *
 *   $action = new GoTo3DViewAction($threeDAnnotRef, new PdfString('Front'));
 */
final class GoTo3DViewAction extends Action
{
    /**
     * @param PdfObject $annotation  Indirect reference to the 3D annotation.
     * @param PdfObject $view        The target view: a 3D view dictionary, a
     *                               name (`Default`, `B` for first bookmark), or
     *                               an integer (view index).
     */
    public function __construct(PdfObject $annotation, PdfObject $view)
    {
        parent::__construct();
        $this->dict->set('TA', $annotation);
        $this->dict->set('V', $view);
    }

    public function getSubtype(): string { return 'GoTo3DView'; }
}
