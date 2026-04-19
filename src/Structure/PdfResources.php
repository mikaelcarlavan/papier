<?php

declare(strict_types=1);

namespace Papier\Structure;

use Papier\Objects\{PdfArray, PdfDictionary, PdfName, PdfObject};

/**
 * Page resource dictionary (ISO 32000-1 §7.8.3).
 *
 * A resource dictionary maps names to objects required by a content stream:
 * fonts, XObjects (images, form XObjects), extended graphics states,
 * colour spaces, patterns, shadings, and optional-content properties.
 *
 * In normal usage you do not need to manipulate this object directly —
 * elements (e.g. {@see \Papier\Elements\Text}, {@see \Papier\Elements\Image})
 * register their resources automatically during `render()`.  Use this class
 * directly when building raw {@see \Papier\Content\ContentStream} objects or
 * adding custom shadings and patterns.
 *
 * A `PdfResources` instance is available via {@see \Papier\Structure\PdfPage::getResources()}.
 */
final class PdfResources
{
    private PdfDictionary $font;
    private PdfDictionary $xObject;
    private PdfDictionary $extGState;
    private PdfDictionary $colorSpace;
    private PdfDictionary $pattern;
    private PdfDictionary $shading;
    private PdfDictionary $properties;
    private ?PdfArray     $procSet = null;

    public function __construct()
    {
        $this->font       = new PdfDictionary();
        $this->xObject    = new PdfDictionary();
        $this->extGState  = new PdfDictionary();
        $this->colorSpace = new PdfDictionary();
        $this->pattern    = new PdfDictionary();
        $this->shading    = new PdfDictionary();
        $this->properties = new PdfDictionary();
    }

    // ── Fonts ─────────────────────────────────────────────────────────────────

    /**
     * Register a font under a resource name (`/Font/<name>`).
     *
     * @param string    $name     Resource key (e.g. `F1`).
     * @param PdfObject $fontRef  Font dictionary or indirect reference.
     */
    public function addFont(string $name, PdfObject $fontRef): static
    {
        $this->font->set($name, $fontRef);
        return $this;
    }

    /** Return the `/Font` sub-dictionary. */
    public function getFonts(): PdfDictionary
    {
        return $this->font;
    }

    // ── XObjects ──────────────────────────────────────────────────────────────

    /**
     * Register an XObject (image or form) under a resource name (`/XObject/<name>`).
     *
     * @param string    $name      Resource key (e.g. `Im0`, `Fm1`).
     * @param PdfObject $xObjRef   XObject stream or indirect reference.
     */
    public function addXObject(string $name, PdfObject $xObjRef): static
    {
        $this->xObject->set($name, $xObjRef);
        return $this;
    }

    /** Return the `/XObject` sub-dictionary. */
    public function getXObjects(): PdfDictionary
    {
        return $this->xObject;
    }

    // ── Extended graphics states ──────────────────────────────────────────────

    /**
     * Register an ExtGState dictionary under a resource name (`/ExtGState/<name>`).
     *
     * Used internally by element rendering to register opacity ExtGState objects
     * (see `Text::registerOpacity()`).  Also use this to add transparency groups,
     * blend modes, or halftone dictionaries.
     *
     * @param string    $name   Resource key (e.g. `GS_op_80`).
     * @param PdfObject $gsRef  ExtGState dictionary or indirect reference.
     */
    public function addExtGState(string $name, PdfObject $gsRef): static
    {
        $this->extGState->set($name, $gsRef);
        return $this;
    }

    /** Return the `/ExtGState` sub-dictionary. */
    public function getExtGStates(): PdfDictionary
    {
        return $this->extGState;
    }

    // ── Colour spaces ─────────────────────────────────────────────────────────

    /**
     * Register a colour space under a resource name (`/ColorSpace/<name>`).
     *
     * Required for ICC-based, Pattern, Separation, and DeviceN colour spaces.
     * Device colour spaces (DeviceGray/RGB/CMYK) do not need registration.
     *
     * @param string    $name  Resource key.
     * @param PdfObject $cs    Colour-space array or indirect reference.
     */
    public function addColorSpace(string $name, PdfObject $cs): static
    {
        $this->colorSpace->set($name, $cs);
        return $this;
    }

    /** Return the `/ColorSpace` sub-dictionary. */
    public function getColorSpaces(): PdfDictionary
    {
        return $this->colorSpace;
    }

    // ── Patterns ──────────────────────────────────────────────────────────────

    /**
     * Register a pattern under a resource name (`/Pattern/<name>`).
     *
     * @param string    $name     Resource key.
     * @param PdfObject $pattern  Tiling or shading pattern stream / dictionary.
     */
    public function addPattern(string $name, PdfObject $pattern): static
    {
        $this->pattern->set($name, $pattern);
        return $this;
    }

    /** Return the `/Pattern` sub-dictionary. */
    public function getPatterns(): PdfDictionary
    {
        return $this->pattern;
    }

    // ── Shadings ──────────────────────────────────────────────────────────────

    /**
     * Register a shading dictionary under a resource name (`/Shading/<name>`).
     *
     * @param string    $name    Resource key.
     * @param PdfObject $shading Shading dictionary or indirect reference.
     */
    public function addShading(string $name, PdfObject $shading): static
    {
        $this->shading->set($name, $shading);
        return $this;
    }

    /** Return the `/Shading` sub-dictionary. */
    public function getShadings(): PdfDictionary
    {
        return $this->shading;
    }

    // ── Optional-content properties ───────────────────────────────────────────

    /**
     * Register an optional-content group reference (`/Properties/<name>`).
     *
     * @param string    $name  Resource key (e.g. `OC1`).
     * @param PdfObject $prop  OCG or OCMD dictionary / indirect reference.
     */
    public function addProperties(string $name, PdfObject $prop): static
    {
        $this->properties->set($name, $prop);
        return $this;
    }

    /** Return the `/Properties` sub-dictionary. */
    public function getProperties(): PdfDictionary
    {
        return $this->properties;
    }

    // ── ProcSet (legacy, §14.2) ───────────────────────────────────────────────

    /**
     * Set the procedure-set array (`/ProcSet`) for PDF 1.0–1.3 compatibility.
     *
     * Modern viewers ignore this entry.  Standard values: `PDF`, `Text`,
     * `ImageB` (grayscale images), `ImageC` (colour images), `ImageI`
     * (indexed-colour images).
     *
     * @param string[] $names  One or more procedure-set name strings.
     */
    public function setProcSet(array $names): static
    {
        $arr = new PdfArray();
        foreach ($names as $n) {
            $arr->add(new PdfName($n));
        }
        $this->procSet = $arr;
        return $this;
    }

    // ── Serialisation ─────────────────────────────────────────────────────────

    /**
     * Build and return the complete resource dictionary.
     *
     * Only non-empty sub-dictionaries are included.  Called by the writer
     * when serialising a page dictionary.
     */
    public function toDictionary(): PdfDictionary
    {
        $dict = new PdfDictionary();

        if (count($this->font) > 0) {
            $dict->set('Font', $this->font);
        }
        if (count($this->xObject) > 0) {
            $dict->set('XObject', $this->xObject);
        }
        if (count($this->extGState) > 0) {
            $dict->set('ExtGState', $this->extGState);
        }
        if (count($this->colorSpace) > 0) {
            $dict->set('ColorSpace', $this->colorSpace);
        }
        if (count($this->pattern) > 0) {
            $dict->set('Pattern', $this->pattern);
        }
        if (count($this->shading) > 0) {
            $dict->set('Shading', $this->shading);
        }
        if (count($this->properties) > 0) {
            $dict->set('Properties', $this->properties);
        }
        if ($this->procSet !== null) {
            $dict->set('ProcSet', $this->procSet);
        }

        return $dict;
    }
}
