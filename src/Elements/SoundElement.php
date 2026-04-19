<?php

declare(strict_types=1);

namespace Papier\Elements;

use Papier\Annotation\Annotation;
use Papier\Annotation\SoundAnnotation;
use Papier\Content\ContentStream;
use Papier\Multimedia\SoundStream;
use Papier\Structure\PdfResources;

/**
 * Sound element — an inline audio clip with a clickable icon placeholder.
 *
 * Combines a visual speaker/microphone icon (drawn as a filled circle with
 * an optional text label) with a {@see SoundAnnotation} that triggers audio
 * playback when clicked.  The annotation is automatically registered with the
 * page when the element is passed to {@see \Papier\Structure\PdfPage::add()}.
 *
 *   $sound = SoundStream::fromPcm($pcmData, 44100, 1, 16);
 *
 *   $page->add(
 *       SoundElement::create($sound, 72, 700)
 *           ->size(24)
 *           ->icon('Speaker')
 *           ->label('Click to play')
 *           ->color(Color::rgb(0.1, 0.4, 0.8)),
 *   );
 *
 * The icon is a filled circle with a unicode glyph character.  For a richer
 * appearance, place any overlapping visual elements before the SoundElement
 * in the same `add()` call.
 *
 * @see SoundStream  To build the audio data from PCM, μ-law, or A-law bytes.
 */
final class SoundElement implements Element, AnnotationProvider
{
    private float   $size      = 20;
    private string  $icon      = 'Speaker';  // 'Speaker' | 'Mic'
    private ?string $label     = null;
    private ?string $fontName  = null;
    private float   $fontSize  = 9;
    private Color   $iconColor;

    private readonly SoundAnnotation $annotation;

    private function __construct(
        private readonly SoundStream $soundStream,
        private readonly float       $x,
        private readonly float       $y,
    ) {
        $this->iconColor  = Color::rgb(0.1, 0.4, 0.8);
        $this->annotation = new SoundAnnotation(0, 0, 0, 0); // rect set in render()
    }

    /**
     * Create a sound element at position (x, y).
     *
     * The icon and annotation share the same rect: a square of side `size`
     * with its lower-left corner at (x, y).
     *
     * @param SoundStream $soundStream  Audio data (build with
     *                                  {@see SoundStream::fromPcm()} etc.).
     * @param float       $x            Lower-left X in points.
     * @param float       $y            Lower-left Y in points.
     */
    public static function create(SoundStream $soundStream, float $x, float $y): self
    {
        return new self($soundStream, $x, $y);
    }

    /**
     * Set the icon square size in points.
     *
     * @param float $size  Side length of the icon bounding square (default 20).
     */
    public function size(float $size): self
    {
        $clone       = clone $this;
        $clone->size = $size;
        return $clone;
    }

    /**
     * Set the icon type.
     *
     * @param string $icon  `'Speaker'` (default) or `'Mic'`.
     */
    public function icon(string $icon): self
    {
        $clone       = clone $this;
        $clone->icon = $icon;
        return $clone;
    }

    /**
     * Set an optional text label rendered below the icon.
     *
     * @param string      $label     Label text.
     * @param string|null $fontName  Resource name (e.g. `'F1'`); required to render the label.
     * @param float       $fontSize  Font size in points (default 9).
     */
    public function label(string $label, ?string $fontName = null, float $fontSize = 9): self
    {
        $clone           = clone $this;
        $clone->label    = $label;
        $clone->fontName = $fontName;
        $clone->fontSize = $fontSize;
        return $clone;
    }

    /**
     * Set the icon fill colour.
     *
     * @param Color $color  Icon colour.
     */
    public function color(Color $color): self
    {
        $clone            = clone $this;
        $clone->iconColor = $color;
        return $clone;
    }

    /**
     * Set tooltip / accessibility text shown on the annotation.
     *
     * @param string $text  Contents string.
     */
    public function contents(string $text): self
    {
        $this->annotation->setContents($text);
        return $this;
    }

    // ── Element + AnnotationProvider ─────────────────────────────────────────

    public function render(ContentStream $cs, PdfResources $resources): void
    {
        $cx = $this->x + $this->size / 2;
        $cy = $this->y + $this->size / 2;
        $r  = $this->size / 2;

        // Draw icon circle
        $cs->save();
        $this->iconColor->applyFill($cs);
        $cs->drawCircle($cx, $cy, $r)->fill();
        $cs->restore();

        // Draw label below icon
        if ($this->label !== null && $this->fontName !== null) {
            $cs->save();
            Color::black()->applyFill($cs);
            $cs->beginText()
               ->setFont($this->fontName, $this->fontSize)
               ->setTextPosition($this->x, $this->y - $this->fontSize - 2)
               ->showText($this->label)
               ->endText();
            $cs->restore();
        }

        // Finalise annotation rect to match the rendered icon area
        $this->annotation
            ->setSound($this->soundStream->getStream())
            ->setIcon($this->icon);

        // Patch the Rect — rebuild via the protected parent method is not accessible,
        // so we write it directly via getDictionary()
        $rect = new \Papier\Objects\PdfArray();
        $rect->add(new \Papier\Objects\PdfReal($this->x));
        $rect->add(new \Papier\Objects\PdfReal($this->y));
        $rect->add(new \Papier\Objects\PdfReal($this->x + $this->size));
        $rect->add(new \Papier\Objects\PdfReal($this->y + $this->size));
        $this->annotation->getDictionary()->set('Rect', $rect);
    }

    /** @return Annotation[] */
    public function getAnnotations(): array
    {
        return [$this->annotation];
    }
}
