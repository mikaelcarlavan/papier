<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfDictionary, PdfInteger, PdfName, PdfStream, PdfString};

/**
 * File specification dictionary (ISO 32000-1 §7.11).
 *
 * A file specification can reference an external file (by path/URI) or embed
 * the file data directly inside the PDF as an EmbeddedFile stream.
 *
 * Usage:
 *   FileSpec::external('video.mp4', 'video/mp4')   // reference by name only
 *   FileSpec::embedded('/path/to/audio.wav')       // embed file content
 *   FileSpec::fromBytes('clip.mp4', $bytes, 'video/mp4')
 */
final class FileSpec
{
    private PdfDictionary $dict;

    private function __construct(string $filename, ?string $mimeType = null, ?string $data = null)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Filespec'));
        $this->dict->set('F',    new PdfString($filename));
        $this->dict->set('UF',   new PdfString($filename));

        if ($data !== null) {
            $efStream = new PdfStream();
            $efStream->setData($data);
            $efStream->getDictionary()->set('Type', new PdfName('EmbeddedFile'));
            if ($mimeType !== null) {
                $efStream->getDictionary()->set('Subtype', new PdfString($mimeType));
            }
            $params = new PdfDictionary();
            $params->set('Size', new PdfInteger(strlen($data)));
            $efStream->getDictionary()->set('Params', $params);

            $ef = new PdfDictionary();
            $ef->set('F', $efStream);
            $this->dict->set('EF', $ef);
        }
    }

    /**
     * External reference — viewer looks up the file by name when opening.
     * The file must be accessible alongside the PDF.
     */
    public static function external(string $path, ?string $mimeType = null): self
    {
        return new self(basename($path), $mimeType);
    }

    /**
     * Embed a file from disk into the PDF as an EmbeddedFile stream.
     * The file content is stored inside the PDF — no external dependency.
     */
    public static function embedded(string $path, ?string $mimeType = null): self
    {
        if (!is_readable($path)) {
            throw new \RuntimeException("Cannot read file: {$path}");
        }
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \RuntimeException("Failed to read file: {$path}");
        }
        return new self(basename($path), $mimeType, $data);
    }

    /** Embed raw bytes already in memory. */
    public static function fromBytes(string $filename, string $data, ?string $mimeType = null): self
    {
        return new self($filename, $mimeType, $data);
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
