<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfName, PdfObject};

/**
 * Click-to-play sound annotation (`/Subtype /Sound`).
 *
 * When activated, plays the associated sound stream.  Build the stream with
 * {@see \Papier\Multimedia\SoundStream} and pass `getStream()` to
 * {@see self::setSound()}.
 *
 * For a higher-level API that combines drawing and annotation registration,
 * use {@see \Papier\Elements\SoundElement} with `$page->add()`.
 *
 * Example:
 *
 *   $snd = new SoundAnnotation(72, 670, 94, 692);
 *   $snd->setSound($soundStream->getStream())
 *       ->setIcon('Speaker')
 *       ->setContents('Click to play');
 *   $page->addAnnotation($snd);
 */
final class SoundAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Sound'; }

    /**
     * Set the sound stream (`/Sound`).
     *
     * @param PdfObject $sound  A {@see \Papier\Objects\PdfStream} with `/Type /Sound`.
     *                          Build it with {@see \Papier\Multimedia\SoundStream::getStream()}.
     */
    public function setSound(PdfObject $sound): static
    {
        $this->dict->set('Sound', $sound);
        return $this;
    }

    /**
     * Set the icon name (`/Name`).
     *
     * @param string $name  `Speaker` (default) or `Mic`.
     */
    public function setIcon(string $name): static
    {
        $this->dict->set('Name', new PdfName($name));
        return $this;
    }
}
