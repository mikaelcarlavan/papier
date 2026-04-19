<?php

declare(strict_types=1);

namespace Papier\Action;

use Papier\Objects\{PdfBoolean, PdfObject, PdfReal};

/**
 * Play a sound (`/S /Sound`).
 *
 * The sound object must be a PDF Sound stream built with
 * {@see \Papier\Multimedia\SoundStream}.
 *
 * Example:
 *
 *   $action = new SoundAction($soundStream->getStream());
 *   $action->setVolume(0.8)->setSynchronous(true);
 */
final class SoundAction extends Action
{
    /**
     * @param PdfObject $sound  A PDF Sound stream object.
     */
    public function __construct(PdfObject $sound)
    {
        parent::__construct();
        $this->dict->set('Sound', $sound);
    }

    /**
     * Set the playback volume (`/Volume`).
     *
     * @param float $v  Volume in the range −1.0 (silence) to 1.0 (full); default 1.0.
     */
    public function setVolume(float $v): static
    {
        $this->dict->set('Volume', new PdfReal($v));
        return $this;
    }

    /**
     * Set synchronous playback (`/Synchronous`).
     *
     * When true, the action does not return until the sound finishes playing.
     *
     * @param bool $s  true for synchronous playback.
     */
    public function setSynchronous(bool $s): static
    {
        $this->dict->set('Synchronous', new PdfBoolean($s));
        return $this;
    }

    public function getSubtype(): string { return 'Sound'; }
}
