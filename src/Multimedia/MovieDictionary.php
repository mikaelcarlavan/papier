<?php

declare(strict_types=1);

namespace Papier\Multimedia;

use Papier\Objects\{PdfArray, PdfBoolean, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfReal};

/**
 * Movie dictionary (ISO 32000-1 §13.4.2).
 *
 * The Movie dictionary is the /Movie entry of a MovieAnnotation.
 * It points to the media file and describes its natural size, rotation,
 * and an optional poster image shown before playback.
 *
 * Usage:
 *   $movie = MovieDictionary::fromFile('clip.mp4')
 *       ->setAspect(1280, 720)
 *       ->setPoster(true);
 *
 *   $annot = new MovieAnnotation(x1, y1, x2, y2);
 *   $annot->setMovie($movie->getDictionary())
 *         ->setTitle('My Video');
 */
final class MovieDictionary
{
    private PdfDictionary $dict;

    public function __construct(PdfObject $fileSpec)
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('F', $fileSpec);
    }

    /** Create from an external file path (reference only — file not embedded). */
    public static function fromFile(string $path): self
    {
        return new self(FileSpec::external($path)->getDictionary());
    }

    /** Create from a pre-built file specification. */
    public static function fromSpec(PdfObject $fileSpec): self
    {
        return new self($fileSpec);
    }

    /**
     * Natural size of the movie frame in pixels.
     * If omitted, the viewer uses the annotation rectangle.
     */
    public function setAspect(int $width, int $height): static
    {
        $aspect = new PdfArray();
        $aspect->add(new PdfInteger($width));
        $aspect->add(new PdfInteger($height));
        $this->dict->set('Aspect', $aspect);
        return $this;
    }

    /** Rotation in degrees: 0, 90, 180, 270. */
    public function setRotate(int $degrees): static
    {
        $this->dict->set('Rotate', new PdfInteger($degrees));
        return $this;
    }

    /**
     * Poster frame shown before or instead of playing:
     *   false — no poster
     *   true  — use the first frame of the movie
     *   PdfObject — a Form XObject to use as the poster image
     */
    public function setPoster(bool|PdfObject $poster): static
    {
        $this->dict->set('Poster', is_bool($poster) ? new PdfBoolean($poster) : $poster);
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
