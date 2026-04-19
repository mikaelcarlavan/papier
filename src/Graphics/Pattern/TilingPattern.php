<?php

declare(strict_types=1);

namespace Papier\Graphics\Pattern;

use Papier\Content\ContentStream;
use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfReal, PdfStream};
use Papier\Structure\PdfResources;

/**
 * Tiling pattern (ISO 32000-1 §8.7.3).
 *
 * A tiling pattern is a small graphical figure (tile) that is replicated
 * at fixed intervals to fill the area to be painted.
 *
 * PatternType: 1
 * PaintType:   1 = coloured, 2 = uncoloured
 * TilingType:  1 = constant spacing, 2 = no distortion, 3 = fastest rendering
 */
final class TilingPattern
{
    private PdfStream     $stream;
    private PdfResources  $resources;
    private ContentStream $content;

    public function __construct(
        private float $xStep,
        private float $yStep,
        private float $bboxX1 = 0.0,
        private float $bboxY1 = 0.0,
        private float $bboxX2 = 1.0,
        private float $bboxY2 = 1.0,
        private int   $paintType = 1,
        private int   $tilingType = 1,
    ) {
        $this->resources = new PdfResources();
        $this->content   = new ContentStream();
        $this->stream    = new PdfStream();

        $dict = $this->stream->getDictionary();
        $dict->set('Type', new PdfName('Pattern'));
        $dict->set('PatternType', new PdfInteger(1));
        $dict->set('PaintType', new PdfInteger($paintType));
        $dict->set('TilingType', new PdfInteger($tilingType));

        // BBox
        $bbox = new PdfArray();
        $bbox->add(new PdfReal($bboxX1));
        $bbox->add(new PdfReal($bboxY1));
        $bbox->add(new PdfReal($bboxX2));
        $bbox->add(new PdfReal($bboxY2));
        $dict->set('BBox', $bbox);

        $dict->set('XStep', new PdfReal($xStep));
        $dict->set('YStep', new PdfReal($yStep));
    }

    public function getContent(): ContentStream { return $this->content; }
    public function getResources(): PdfResources { return $this->resources; }

    public function getStream(): PdfStream
    {
        $this->stream->getDictionary()->set('Resources', $this->resources->toDictionary());
        $data = $this->content->getBuffer();
        $this->stream->setData($data);
        if ($this->content->isCompressed()) {
            $this->stream->compress();
        }
        return $this->stream;
    }
}
