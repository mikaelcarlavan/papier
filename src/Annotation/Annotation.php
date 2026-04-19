<?php

declare(strict_types=1);

namespace Papier\Annotation;

use Papier\Action\Action;
use Papier\Elements\Color;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfObject, PdfReal, PdfString};

/**
 * Abstract base class for all PDF annotation dictionaries (ISO 32000-1 §12.5.2).
 *
 * An annotation is an interactive object attached to a rectangular area on a
 * page.  Every annotation shares a set of common entries (Table 164):
 *
 *   - `/Type /Annot` — always present.
 *   - `/Subtype`     — identifies the annotation type (e.g. `Text`, `Link`).
 *   - `/Rect`        — bounding rectangle in default user space.
 *   - `/Contents`    — optional text shown in a pop-up or used for accessibility.
 *   - `/F`           — annotation flags bitfield (Table 166, see below).
 *   - `/AP`          — appearance stream dictionary (`N`, `R`, `D`).
 *   - `/BS`          — border-style dictionary.
 *   - `/C`           — annotation colour.
 *
 * Annotation flags (§12.5.3 Table 166):
 *   bit 1  (0x001) — Invisible
 *   bit 2  (0x002) — Hidden
 *   bit 3  (0x004) — Print       ← set this to make the annotation printable
 *   bit 4  (0x008) — NoZoom
 *   bit 5  (0x010) — NoRotate
 *   bit 6  (0x020) — NoView
 *   bit 7  (0x040) — ReadOnly
 *   bit 8  (0x080) — Locked
 *   bit 9  (0x100) — ToggleNoView
 *   bit 10 (0x200) — LockedContents
 *
 * Concrete subclasses are defined in {@see AnnotationTypes.php}.
 */
abstract class Annotation
{
    protected PdfDictionary $dict;

    /**
     * @param float $x1  Lower-left X of the bounding rectangle in points.
     * @param float $y1  Lower-left Y of the bounding rectangle in points.
     * @param float $x2  Upper-right X of the bounding rectangle in points.
     * @param float $y2  Upper-right Y of the bounding rectangle in points.
     */
    public function __construct(
        protected float $x1,
        protected float $y1,
        protected float $x2,
        protected float $y2,
    ) {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('Annot'));
        $this->dict->set('Subtype', new PdfName($this->getSubtype()));
        $this->setRect($x1, $y1, $x2, $y2);
    }

    /**
     * Return the PDF subtype name for this annotation (e.g. `Text`, `Link`).
     */
    abstract public function getSubtype(): string;

    /**
     * Update the annotation bounding rectangle (`/Rect`).
     *
     * @param float $x1  Lower-left X in points.
     * @param float $y1  Lower-left Y in points.
     * @param float $x2  Upper-right X in points.
     * @param float $y2  Upper-right Y in points.
     */
    protected function setRect(float $x1, float $y1, float $x2, float $y2): static
    {
        $rect = new PdfArray();
        $rect->add(new PdfReal($x1));
        $rect->add(new PdfReal($y1));
        $rect->add(new PdfReal($x2));
        $rect->add(new PdfReal($y2));
        $this->dict->set('Rect', $rect);
        return $this;
    }

    /**
     * Set the annotation contents string (`/Contents`).
     *
     * For most annotation types this is shown in a pop-up when the annotation
     * is activated.  It is also used by screen-reader software.
     *
     * @param string $text  Contents text (UTF-8).
     */
    public function setContents(string $text): static
    {
        $this->dict->set('Contents', PdfString::text($text));
        return $this;
    }

    /**
     * Set the unique annotation name (`/NM`) within the document.
     *
     * Used to target the annotation from JavaScript and GoTo actions.
     *
     * @param string $name  Unique name string.
     */
    public function setName(string $name): static
    {
        $this->dict->set('NM', new PdfString($name));
        return $this;
    }

    /**
     * Set the modification date (`/M`).
     *
     * The value should be a PDF date string of the form
     * `D:YYYYMMDDHHmmSSOHH'mm'` (§7.9.4).
     *
     * @param string $date  PDF date string.
     */
    public function setModDate(string $date): static
    {
        $this->dict->set('M', new PdfString($date));
        return $this;
    }

    /**
     * Set the annotation flags bitfield (`/F`).
     *
     * Common combinations:
     *   - `4`  — Print only (visible on screen and in print)
     *   - `68` — Print + ReadOnly (4 + 64)
     *
     * See Table 166 for the complete list of flag bits.
     *
     * @param int $flags  Bitfield value.
     */
    public function setFlags(int $flags): static
    {
        $this->dict->set('F', new PdfInteger($flags));
        return $this;
    }

    /**
     * Set the annotation colour (`/C`).
     *
     * Used as the background colour of text annotations and the border colour
     * of most other types.  The colour space is preserved: a greyscale
     * `Color` produces a 1-component array, RGB a 3-component array, and
     * CMYK a 4-component array, matching ISO 32000-1 §12.5.2 Table 164.
     *
     * @param Color $color  Use {@see Color::rgb()}, {@see Color::hex()},
     *                      {@see Color::gray()}, or {@see Color::cmyk()}.
     */
    public function setColor(Color $color): static
    {
        $this->dict->set('C', $this->colorToArray($color));
        return $this;
    }

    /**
     * Make the annotation transparent by writing an empty `/C` array.
     *
     * An empty colour array means "no colour" (transparent background or
     * no border) as defined in §12.5.2.
     */
    public function clearColor(): static
    {
        $this->dict->set('C', new PdfArray());
        return $this;
    }

    /**
     * Associate a pop-up annotation (`/Popup`).
     *
     * @param PdfObject $popupRef  Indirect reference to a {@see PopupAnnotation}.
     */
    public function setPopup(PdfObject $popupRef): static
    {
        $this->dict->set('Popup', $popupRef);
        return $this;
    }

    /**
     * Set the action triggered when the annotation is activated (`/A`).
     *
     * Pass any action class from `Papier\Action\*`
     * (e.g. `new URIAction('https://…')`, `new GoToAction('chapter1')`).
     *
     * @param Action|PdfObject $action  Action object or pre-built dictionary.
     */
    public function setAction(Action|PdfObject $action): static
    {
        $this->dict->set('A', $action instanceof Action ? $action->getDictionary() : $action);
        return $this;
    }

    /**
     * Set the border style dictionary (`/BS`).
     *
     * Supersedes the legacy `/Border` entry.  The style dictionary specifies
     * the border width and drawing style for the annotation.
     *
     * @param float  $width  Border line width in points.
     * @param string $style  `S` solid, `D` dashed, `B` beveled, `I` inset,
     *                       `U` underline.
     * @param float[] $dash  Dash array used when $style is `D`
     *                       (e.g. `[3, 2]` → 3-on, 2-off).
     */
    public function setBorderStyle(float $width, string $style = 'S', array $dash = []): static
    {
        $bs = new PdfDictionary();
        $bs->set('Type', new PdfName('Border'));
        $bs->set('W', new PdfReal($width));
        $bs->set('S', new PdfName($style));
        if ($style === 'D' && !empty($dash)) {
            $da = new PdfArray();
            foreach ($dash as $v) {
                $da->add(new PdfReal($v));
            }
            $bs->set('D', $da);
        }
        $this->dict->set('BS', $bs);
        return $this;
    }

    /**
     * Set the legacy border array (`/Border`).
     *
     * Prefer {@see self::setBorderStyle()} for new documents.  The legacy
     * `/Border` entry carries horizontal and vertical corner radii plus width.
     *
     * @param float $hRadius  Horizontal corner radius in points.
     * @param float $vRadius  Vertical corner radius in points.
     * @param float $width    Border width in points (0 = no border).
     */
    public function setBorder(float $hRadius, float $vRadius, float $width): static
    {
        $border = new PdfArray();
        $border->add(new PdfReal($hRadius));
        $border->add(new PdfReal($vRadius));
        $border->add(new PdfReal($width));
        $this->dict->set('Border', $border);
        return $this;
    }

    /**
     * Set the page reference (`/P`).
     *
     * An indirect reference to the page dictionary that contains this
     * annotation.  Optional in the spec, but some viewers require it.
     *
     * @param PdfObject $pageRef  Indirect reference to the page.
     */
    public function setPage(PdfObject $pageRef): static
    {
        $this->dict->set('P', $pageRef);
        return $this;
    }

    /**
     * Set the appearance stream dictionary (`/AP`).
     *
     * Appearance streams override the viewer's default rendering.
     *
     * @param PdfObject      $normal    Normal (default) appearance (`/N`).
     * @param PdfObject|null $rollover  Mouse-over appearance (`/R`).
     * @param PdfObject|null $down      Mouse-down / active appearance (`/D`).
     */
    public function setAppearance(PdfObject $normal, ?PdfObject $rollover = null, ?PdfObject $down = null): static
    {
        $ap = new PdfDictionary();
        $ap->set('N', $normal);
        if ($rollover !== null) { $ap->set('R', $rollover); }
        if ($down !== null)     { $ap->set('D', $down); }
        $this->dict->set('AP', $ap);
        return $this;
    }

    /**
     * Build a PDF colour array from a {@see Color} object.
     *
     * The number of elements matches the colour space (1 = gray, 3 = RGB,
     * 4 = CMYK) so that the correct device colour space is used in the PDF.
     */
    protected function colorToArray(Color $color): PdfArray
    {
        $arr = new PdfArray();
        foreach ($color->toArray() as $v) {
            $arr->add(new PdfReal($v));
        }
        return $arr;
    }

    /** Return the underlying annotation dictionary. */
    public function getDictionary(): PdfDictionary { return $this->dict; }
}
