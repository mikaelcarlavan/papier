<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfDictionary, PdfString};

/**
 * Media permissions (ISO 32000-1 §13.2.4.2).
 *
 * Controls how the viewer may access the media data referenced by a MediaClip.
 * The /TF (temp-file) flag determines whether the viewer may write the media
 * to a temporary file for playback.
 */
final class MediaPermissions
{
    /** Viewer may write the data to a temporary file. */
    public const TEMP_ACCESS   = 'TEMPACCESS';

    /** No temp-file operations permitted. */
    public const NO_OPS        = 'NOOPS';

    /** Data must never be written to a file (e.g. DRM-protected content). */
    public const NEVER_WRITTEN = 'NEVERWRITTEN';

    private PdfDictionary $dict;

    public function __construct(string $tempFileAccess = self::TEMP_ACCESS)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('TF', new PdfString($tempFileAccess));
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
