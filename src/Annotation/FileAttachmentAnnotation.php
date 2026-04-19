<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfName, PdfObject};

/**
 * File-attachment annotation (`/Subtype /FileAttachment`).
 *
 * Embeds a file within the annotation; the user can open or save it by
 * double-clicking the icon.
 *
 * Example:
 *
 *   $attach = new FileAttachmentAnnotation(500, 700, 516, 716);
 *   $attach->setFileSpec($fileSpecRef)
 *          ->setIcon('PaperClip')
 *          ->setContents('Attached spreadsheet');
 */
final class FileAttachmentAnnotation extends Annotation
{
    public function getSubtype(): string { return 'FileAttachment'; }

    /**
     * Set the embedded file specification (`/FS`).
     *
     * @param PdfObject $fs  A file specification dictionary or indirect reference.
     *                       Build one with {@see \Papier\Multimedia\FileSpec}.
     */
    public function setFileSpec(PdfObject $fs): static
    {
        $this->dict->set('FS', $fs);
        return $this;
    }

    /**
     * Set the icon name (`/Name`).
     *
     * Standard values: `Graph`, `PaperClip`, `PushPin` (default), `Tag`.
     *
     * @param string $name  Icon name.
     */
    public function setIcon(string $name): static
    {
        $this->dict->set('Name', new PdfName($name));
        return $this;
    }
}
