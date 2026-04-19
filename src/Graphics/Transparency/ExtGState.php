<?php

declare(strict_types=1);

namespace Papier\Graphics\Transparency;

use Papier\Objects\{PdfBoolean, PdfDictionary, PdfName, PdfObject, PdfReal};

/**
 * Extended graphics state parameter dictionary (ISO 32000-1 §8.4.5).
 *
 * Used with the `gs` operator to set multiple graphics state parameters
 * simultaneously, including PDF 1.4 transparency parameters.
 */
final class ExtGState
{
    private PdfDictionary $dict;

    public function __construct()
    {
        $this->dict = new PdfDictionary();
        $this->dict->set('Type', new PdfName('ExtGState'));
    }

    /** Line width (LW). */
    public function setLineWidth(float $w): static { $this->dict->set('LW', new PdfReal($w)); return $this; }

    /** Line cap (LC): 0=butt, 1=round, 2=square. */
    public function setLineCap(int $lc): static { $this->dict->set('LC', new \Papier\Objects\PdfInteger($lc)); return $this; }

    /** Line join (LJ): 0=miter, 1=round, 2=bevel. */
    public function setLineJoin(int $lj): static { $this->dict->set('LJ', new \Papier\Objects\PdfInteger($lj)); return $this; }

    /** Miter limit (ML). */
    public function setMiterLimit(float $ml): static { $this->dict->set('ML', new PdfReal($ml)); return $this; }

    /** Stroke alpha (CA) — PDF 1.4. */
    public function setStrokeAlpha(float $alpha): static { $this->dict->set('CA', new PdfReal($alpha)); return $this; }

    /** Fill alpha (ca) — PDF 1.4. */
    public function setFillAlpha(float $alpha): static { $this->dict->set('ca', new PdfReal($alpha)); return $this; }

    /** Alpha source (AIS) — if true, use alpha as shape; if false, use as opacity. */
    public function setAlphaIsShape(bool $ais): static { $this->dict->set('AIS', new PdfBoolean($ais)); return $this; }

    /** Blend mode (BM) — PDF 1.4. */
    public function setBlendMode(string $mode): static { $this->dict->set('BM', new PdfName($mode)); return $this; }

    /** Soft mask (SMask) — PDF 1.4. */
    public function setSoftMask(PdfObject $softMask): static { $this->dict->set('SMask', $softMask); return $this; }

    /** Text knockout flag (TK). */
    public function setTextKnockout(bool $tk): static { $this->dict->set('TK', new PdfBoolean($tk)); return $this; }

    /** Overprint flag for stroking (OP). */
    public function setOverprintStroke(bool $op): static { $this->dict->set('OP', new PdfBoolean($op)); return $this; }

    /** Overprint flag for filling (op). */
    public function setOverprintFill(bool $op): static { $this->dict->set('op', new PdfBoolean($op)); return $this; }

    /** Overprint mode (OPM). */
    public function setOverprintMode(int $opm): static { $this->dict->set('OPM', new \Papier\Objects\PdfInteger($opm)); return $this; }

    /** Flatness tolerance (FL). */
    public function setFlatness(float $fl): static { $this->dict->set('FL', new PdfReal($fl)); return $this; }

    /** Smoothness tolerance (SM). */
    public function setSmoothness(float $sm): static { $this->dict->set('SM', new PdfReal($sm)); return $this; }

    /** Stroke adjustment (SA). */
    public function setStrokeAdjust(bool $sa): static { $this->dict->set('SA', new PdfBoolean($sa)); return $this; }

    /** Font (Font): [ref, size]. */
    public function setFont(PdfObject $fontRef, float $size): static
    {
        $arr = new \Papier\Objects\PdfArray();
        $arr->add($fontRef);
        $arr->add(new PdfReal($size));
        $this->dict->set('Font', $arr);
        return $this;
    }

    /** Colour rendering intent (RI). */
    public function setRenderingIntent(string $ri): static { $this->dict->set('RI', new PdfName($ri)); return $this; }

    public function getDictionary(): PdfDictionary { return $this->dict; }
}
