<?php

declare(strict_types=1);

namespace Papier\Structure;

use Papier\AcroForm\FormField;
use Papier\Content\ContentStream;
use Papier\Elements\AnnotationProvider;
use Papier\Elements\Element;
use Papier\Objects\{PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfName, PdfObject, PdfReal, PdfStream};

/**
 * A single PDF page (ISO 32000-1 §7.7.3).
 *
 * A page carries:
 *   - Geometry: media box, crop box, trim/bleed/art boxes, rotation.
 *   - Content: one or more {@see ContentStream} objects.
 *   - Resources: fonts, XObjects, patterns, shadings, etc. (shared via
 *     {@see PdfResources}).
 *   - Annotations: interactive objects (links, media, forms, etc.).
 *   - Form fields: merged field+widget dictionaries for AcroForm.
 *
 * The most convenient way to add visual content is via the high-level
 * elements API:
 *
 *   $page->add(
 *       Text::write('Hello')->at(72, 720)->font($f, 24),
 *       Rectangle::create(0, 780, 595, 61)->fill(Color::rgb(0.2, 0.3, 0.6)),
 *       Image::fromFile('logo.png')->at(72, 500)->fitWidth(100),
 *   );
 *
 * Elements that implement {@see AnnotationProvider} (e.g. {@see SoundElement},
 * {@see VideoElement}) have their annotations automatically added to the page.
 *
 * For fine-grained control, create a {@see ContentStream} directly and attach
 * it with {@see self::addContent()}.
 *
 * Standard page sizes are available as class constants, e.g.:
 *
 *   $page = $doc->addPage(...PdfPage::SIZE_A4);
 *   $page = $doc->addPage(...PdfPage::SIZE_LETTER);
 */
final class PdfPage
{
    // ── Standard page sizes (width × height in points, 1 pt = 1/72 inch) ─────

    /** ISO A0 — 841 × 1189 mm */
    public const SIZE_A0        = [2383.94, 3370.39];
    /** ISO A1 — 594 × 841 mm */
    public const SIZE_A1        = [1683.78, 2383.94];
    /** ISO A2 — 420 × 594 mm */
    public const SIZE_A2        = [1190.55, 1683.78];
    /** ISO A3 — 297 × 420 mm */
    public const SIZE_A3        = [841.89,  1190.55];
    /** ISO A4 — 210 × 297 mm (default) */
    public const SIZE_A4        = [595.28,   841.89];
    /** ISO A5 — 148 × 210 mm */
    public const SIZE_A5        = [419.53,   595.28];
    /** ISO A6 — 105 × 148 mm */
    public const SIZE_A6        = [297.64,   419.53];
    /** US Letter — 8.5 × 11 in */
    public const SIZE_LETTER    = [612.0,    792.0];
    /** US Legal — 8.5 × 14 in */
    public const SIZE_LEGAL     = [612.0,   1008.0];
    /** US Tabloid — 11 × 17 in */
    public const SIZE_TABLOID   = [792.0,   1224.0];
    /** US Executive — 7.25 × 10.5 in */
    public const SIZE_EXECUTIVE = [522.0,    756.0];

    private PdfDictionary $dictionary;
    private PdfResources  $resources;

    /** @var ContentStream[] */
    private array $contentStreams = [];

    /** @var (\Papier\Annotation\Annotation|PdfObject)[] */
    private array $annotations = [];

    /** @var FormField[] */
    private array $formFields = [];

    /**
     * @param float $width   Page width in points (default A4).
     * @param float $height  Page height in points (default A4).
     */
    public function __construct(
        private float $width  = 595.28,
        private float $height = 841.89,
    ) {
        $this->resources  = new PdfResources();
        $this->dictionary = new PdfDictionary();
        $this->dictionary->set('Type', new PdfName('Page'));
    }

    // ── Page size and boxes ───────────────────────────────────────────────────

    /** Return the page width in points. */
    public function getWidth(): float { return $this->width; }

    /** Return the page height in points. */
    public function getHeight(): float { return $this->height; }

    /**
     * Set both page dimensions.
     *
     * @param float $width   New width in points.
     * @param float $height  New height in points.
     */
    public function setSize(float $width, float $height): static
    {
        $this->width  = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Swap width and height to produce a landscape-oriented page.
     *
     * Equivalent to `setSize($height, $width)`.
     */
    public function setLandscape(): static
    {
        [$this->width, $this->height] = [$this->height, $this->width];
        return $this;
    }

    /**
     * Set the crop box (visible area after trimming in a viewer).
     *
     * @param float $x1  Lower-left X.
     * @param float $y1  Lower-left Y.
     * @param float $x2  Upper-right X.
     * @param float $y2  Upper-right Y.
     */
    public function setCropBox(float $x1, float $y1, float $x2, float $y2): static
    {
        $this->dictionary->set('CropBox', $this->makeBox($x1, $y1, $x2, $y2));
        return $this;
    }

    /**
     * Set the bleed box (area to which page content bleeds when printing).
     *
     * @param float $x1  Lower-left X.
     * @param float $y1  Lower-left Y.
     * @param float $x2  Upper-right X.
     * @param float $y2  Upper-right Y.
     */
    public function setBleedBox(float $x1, float $y1, float $x2, float $y2): static
    {
        $this->dictionary->set('BleedBox', $this->makeBox($x1, $y1, $x2, $y2));
        return $this;
    }

    /**
     * Set the trim box (intended final dimensions after trimming).
     *
     * @param float $x1  Lower-left X.
     * @param float $y1  Lower-left Y.
     * @param float $x2  Upper-right X.
     * @param float $y2  Upper-right Y.
     */
    public function setTrimBox(float $x1, float $y1, float $x2, float $y2): static
    {
        $this->dictionary->set('TrimBox', $this->makeBox($x1, $y1, $x2, $y2));
        return $this;
    }

    /**
     * Set the art box (area of page content meaningful when displaying).
     *
     * @param float $x1  Lower-left X.
     * @param float $y1  Lower-left Y.
     * @param float $x2  Upper-right X.
     * @param float $y2  Upper-right Y.
     */
    public function setArtBox(float $x1, float $y1, float $x2, float $y2): static
    {
        $this->dictionary->set('ArtBox', $this->makeBox($x1, $y1, $x2, $y2));
        return $this;
    }

    /**
     * Set the page rotation (§7.7.3.3).
     *
     * The page is rotated clockwise by the given angle before display.
     *
     * @param int $degrees  Rotation in degrees; must be a multiple of 90.
     *                      Valid values: 0 (default), 90, 180, 270.
     */
    public function setRotation(int $degrees): static
    {
        $this->dictionary->set('Rotate', new PdfInteger($degrees));
        return $this;
    }

    // ── Presentation ──────────────────────────────────────────────────────────

    /**
     * Attach a page transition effect (§12.4.4).
     *
     * Transitions are visible in presentation (full-screen) mode.  Build a
     * transition with {@see PageTransition}:
     *
     *   $page->setTransition(
     *       (new PageTransition(PageTransition::WIPE, 0.5))->setDirection(270)
     *   );
     *
     * @param PageTransition $transition  The transition effect and duration.
     */
    public function setTransition(PageTransition $transition): static
    {
        $this->dictionary->set('Trans', $transition->getDictionary());
        return $this;
    }

    /**
     * Set the automatic page-advance duration (§12.4.4).
     *
     * After $seconds seconds, the viewer automatically advances to the next
     * page in presentation mode.  Requires a compatible viewer (e.g.
     * Adobe Acrobat in full-screen mode).
     *
     * @param float $seconds  Duration in seconds before auto-advance.
     */
    public function setDuration(float $seconds): static
    {
        $this->dictionary->set('Dur', new PdfReal($seconds));
        return $this;
    }

    // ── Content streams ────────────────────────���──────────────────────────────

    /**
     * Append a raw content stream to the page.
     *
     * Use this when you need direct access to PDF operators (transformations,
     * custom shading, marked content, etc.) that are not covered by the
     * high-level elements API.
     *
     * @param ContentStream $stream  The stream to append.
     */
    public function addContent(ContentStream $stream): static
    {
        $this->contentStreams[] = $stream;
        return $this;
    }

    /**
     * Add one or more high-level elements to this page.
     *
     * Each element is rendered into its own {@see ContentStream} so that
     * graphics-state side-effects from one element cannot leak into another.
     *
     * Elements that implement {@see AnnotationProvider} (such as
     * {@see \Papier\Elements\SoundElement} and
     * {@see \Papier\Elements\VideoElement}) have their annotations
     * automatically registered with the page.
     *
     *   $page->add(
     *       Text::write('Hello')->at(72, 720)->font($f, 24),
     *       Rectangle::create(72, 60, 451, 2)->fill(Color::gray(0.8)),
     *   );
     *
     * @param Element ...$elements  One or more elements to render.
     */
    public function add(Element ...$elements): static
    {
        foreach ($elements as $element) {
            $cs = new ContentStream();
            $element->render($cs, $this->resources);
            $this->contentStreams[] = $cs;

            if ($element instanceof AnnotationProvider) {
                foreach ($element->getAnnotations() as $annot) {
                    $this->annotations[] = $annot;
                }
            }
        }
        return $this;
    }

    /**
     * Return all content streams attached to this page.
     *
     * @return ContentStream[]
     */
    public function getContentStreams(): array
    {
        return $this->contentStreams;
    }

    // ── Resources ─────────────────────────────────────────────────────────────

    /**
     * Return the page resource dictionary.
     *
     * Use this to register additional resources (shadings, patterns,
     * ExtGStates, etc.) when building content via {@see self::addContent()}.
     */
    public function getResources(): PdfResources
    {
        return $this->resources;
    }

    // ── Annotations ────────────────────────���────────────────────────────��─────

    /**
     * Add an annotation to the page.
     *
     * Annotations are added to the page's `/Annots` array when the PDF is
     * serialised.  Use the concrete annotation classes in
     * `Papier\Annotation\*` (e.g. {@see \Papier\Annotation\LinkAnnotation}).
     *
     * @param \Papier\Annotation\Annotation|PdfObject $annotation  The annotation to add.
     */
    public function addAnnotation(\Papier\Annotation\Annotation|PdfObject $annotation): static
    {
        $this->annotations[] = $annotation;
        return $this;
    }

    /**
     * Return all annotations registered with this page.
     *
     * @return (\Papier\Annotation\Annotation|PdfObject)[]
     */
    public function getAnnotations(): array
    {
        return $this->annotations;
    }

    // ── Form fields ───────────────────────────────────────────────────────────

    /**
     * Associate a merged field+widget form field with this page (§12.7.3.3).
     *
     * The field must have `setRect()` called before being added so that it
     * carries a `/Subtype /Widget` entry.  The writer includes the field in
     * both the page `/Annots` array and the AcroForm `/Fields` array.
     *
     * @param FormField $field  The form field (text field, check box, etc.).
     */
    public function addFormField(FormField $field): static
    {
        $this->formFields[] = $field;
        return $this;
    }

    /**
     * Return all form fields registered with this page.
     *
     * @return FormField[]
     */
    public function getFormFields(): array
    {
        return $this->formFields;
    }

    // ── Arbitrary extra entries ───────────────────────────────────────────────

    /**
     * Set an arbitrary entry in the page dictionary.
     *
     * Use this to add entries not exposed by the typed setters above.
     *
     * @param string    $key    PDF name key (without the leading `/`).
     * @param PdfObject $value  Value object.
     */
    public function setEntry(string $key, PdfObject $value): static
    {
        $this->dictionary->set($key, $value);
        return $this;
    }

    /** Return the underlying page dictionary. */
    public function getDictionary(): PdfDictionary
    {
        return $this->dictionary;
    }

    // ── Private helpers ────────────────────��──────────────────────────────────

    /** Build a PDF rectangle array [x1, y1, x2, y2]. */
    private function makeBox(float $x1, float $y1, float $x2, float $y2): PdfArray
    {
        $a = new PdfArray();
        $a->add(new PdfReal($x1));
        $a->add(new PdfReal($y1));
        $a->add(new PdfReal($x2));
        $a->add(new PdfReal($y2));
        return $a;
    }
}
