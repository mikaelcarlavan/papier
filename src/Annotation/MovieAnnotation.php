<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Objects\{PdfObject, PdfString};

/**
 * Movie annotation for embedded video playback (`/Subtype /Movie`).
 *
 * Attaches a movie (video file) to a rectangular region on the page.  Build
 * the movie dictionary with {@see \Papier\Multimedia\MovieDictionary} and the
 * activation parameters with {@see \Papier\Multimedia\MovieActivation}.
 *
 * Note: the `Movie` annotation type is considered legacy in PDF 1.5+; prefer
 * {@see ScreenAnnotation} with a {@see \Papier\Multimedia\MediaRendition} for
 * new documents.
 *
 * Example:
 *
 *   $movie = new MovieAnnotation(72, 400, 372, 625);
 *   $movie->setMovie(MovieDictionary::fromFile('demo.avi')->getDictionary())
 *         ->setTitle('Demo')
 *         ->setActivation(
 *             (new MovieActivation())->setMode(MovieActivation::MODE_ONCE)->getDictionary()
 *         );
 */
final class MovieAnnotation extends Annotation
{
    public function getSubtype(): string { return 'Movie'; }

    /**
     * Set the movie dictionary (`/Movie`).
     *
     * @param PdfObject $movie  Pass {@see \Papier\Multimedia\MovieDictionary::getDictionary()}.
     */
    public function setMovie(PdfObject $movie): static
    {
        $this->dict->set('Movie', $movie);
        return $this;
    }

    /**
     * Set the annotation title (`/T`).
     *
     * Used by MovieAction to identify this annotation as the playback target.
     *
     * @param string $title  Unique title string.
     */
    public function setTitle(string $title): static
    {
        $this->dict->set('T', new PdfString($title));
        return $this;
    }

    /**
     * Set the movie activation dictionary (`/A`).
     *
     * Controls playback parameters when the annotation is activated.
     *
     * @param PdfObject $activation  Pass {@see \Papier\Multimedia\MovieActivation::getDictionary()}.
     */
    public function setActivation(PdfObject $activation): static
    {
        $this->dict->set('A', $activation);
        return $this;
    }
}
