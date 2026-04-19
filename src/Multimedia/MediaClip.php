<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfDictionary, PdfName, PdfObject, PdfString};

/**
 * MediaClipData (ISO 32000-1 §13.2.3.2).
 *
 * A MediaClip describes a media asset: it points to a file specification
 * (external or embedded) and records the MIME type so the viewer knows which
 * player to use.  One MediaClip is referenced by one or more Renditions.
 *
 * Common MIME types:
 *   'video/mp4'      'video/webm'    'video/quicktime'
 *   'audio/mpeg'     'audio/wav'     'audio/ogg'
 *   'application/x-shockwave-flash'
 *
 * Usage:
 *   $clip = MediaClip::fromFile('intro.mp4', 'video/mp4');
 *   $clip = MediaClip::fromEmbedded('/srv/assets/intro.mp4', 'video/mp4');
 */
final class MediaClip
{
    private PdfDictionary $dict;

    private function __construct(PdfObject $fileSpec, string $mimeType, ?string $name = null)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('MediaClip'));
        $this->dict->set('S',    new PdfName('MCD'));

        if ($name !== null) {
            $this->dict->set('N', new PdfString($name));
        }

        $this->dict->set('D',  $fileSpec);
        $this->dict->set('CT', new PdfString($mimeType));

        // Default: viewer may copy to a temp file for playback
        $this->dict->set('P', (new MediaPermissions(MediaPermissions::TEMP_ACCESS))->getDictionary());
    }

    /**
     * Reference an external file by path.
     * The file must be accessible when the PDF is opened.
     */
    public static function fromFile(string $path, string $mimeType, ?string $name = null): self
    {
        return new self(
            FileSpec::external($path, $mimeType)->getDictionary(),
            $mimeType,
            $name ?? basename($path),
        );
    }

    /**
     * Embed the media file directly into the PDF.
     * Larger PDF but self-contained — no external dependency.
     */
    public static function fromEmbedded(string $path, string $mimeType, ?string $name = null): self
    {
        return new self(
            FileSpec::embedded($path, $mimeType)->getDictionary(),
            $mimeType,
            $name ?? basename($path),
        );
    }

    /** Use a pre-built file specification dictionary. */
    public static function fromSpec(PdfObject $fileSpec, string $mimeType, ?string $name = null): self
    {
        return new self($fileSpec, $mimeType, $name);
    }

    /** Override the default (TEMPACCESS) permissions. */
    public function setPermissions(MediaPermissions $perms): static
    {
        $this->dict->set('P', $perms->getDictionary());
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
