<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfDictionary, PdfInteger, PdfName, PdfReal, PdfStream};

/**
 * Sound stream (ISO 32000-1 §13.3).
 *
 * A sound stream contains raw audio samples in one of four encodings, plus
 * sampling parameters in the stream dictionary.  The stream itself becomes
 * the /Sound entry in a SoundAnnotation or SoundAction.
 *
 * Supported encodings:
 *   Raw    — unsigned integer samples (Signed below covers signed variant)
 *   Signed — two's-complement signed PCM (most common for WAV)
 *   muLaw  — G.711 μ-law (telephony, 8-bit)
 *   ALaw   — G.711 A-law (telephony, 8-bit)
 *
 * Usage:
 *   $sound = SoundStream::fromPcm($rawPcmData, 44100, 2, 16);
 *   $annot = new SoundAnnotation(x1, y1, x2, y2);
 *   $annot->setSound($sound->getStream());
 */
final class SoundStream
{
    private PdfStream $stream;

    private function __construct(
        string $data,
        int    $sampleRate,
        int    $channels,
        int    $bitsPerSample,
        string $encoding,
    ) {
        $this->stream = new PdfStream();
        $this->stream->setData($data);
        $dict = $this->stream->getDictionary();
        $dict->set('Type', new PdfName('Sound'));
        $dict->set('R',    new PdfReal((float) $sampleRate));
        $dict->set('C',    new PdfInteger($channels));
        $dict->set('B',    new PdfInteger($bitsPerSample));
        $dict->set('E',    new PdfName($encoding));
    }

    /**
     * Create from raw signed PCM samples (the most common format).
     * Data layout: interleaved, little-endian.
     */
    public static function fromPcm(
        string $data,
        int $sampleRate    = 44100,
        int $channels      = 1,
        int $bitsPerSample = 16,
    ): self {
        return new self($data, $sampleRate, $channels, $bitsPerSample, 'Signed');
    }

    /**
     * Create from μ-law (G.711) encoded audio.
     * Standard for telephony: 8-bit samples at 8 kHz.
     */
    public static function fromMuLaw(
        string $data,
        int $sampleRate = 8000,
        int $channels   = 1,
    ): self {
        return new self($data, $sampleRate, $channels, 8, 'muLaw');
    }

    /**
     * Create from A-law (G.711) encoded audio.
     * Used in European telephony systems.
     */
    public static function fromALaw(
        string $data,
        int $sampleRate = 8000,
        int $channels   = 1,
    ): self {
        return new self($data, $sampleRate, $channels, 8, 'ALaw');
    }

    /**
     * Load raw signed PCM data from a file.
     * Use this for headerless PCM dumps; for WAV files strip the 44-byte header first.
     */
    public static function fromFile(
        string $path,
        int $sampleRate    = 44100,
        int $channels      = 1,
        int $bitsPerSample = 16,
    ): self {
        if (!is_readable($path)) {
            throw new \RuntimeException("Cannot read sound file: {$path}");
        }
        $data = file_get_contents($path);
        if ($data === false) {
            throw new \RuntimeException("Failed to read sound file: {$path}");
        }
        return self::fromPcm($data, $sampleRate, $channels, $bitsPerSample);
    }

    public function getStream(): PdfStream { return $this->stream; }
}
