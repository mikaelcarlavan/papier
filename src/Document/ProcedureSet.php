<?php

namespace Papier\Document;

class ProcedureSet
{
    /**
     * Painting and graphics state
     *
     * @var string
     */
    const GRAPHICS = 'PDF';
  
    /**
     * Text
     *
     * @var string
     */
    const TEXT = 'Text';

    /**
     * Indexed (colour-table) images
     *
     * @var string
     */
    const INDEXED_IMAGES = 'ImageI';

    /**
     * Colour images
     *
     * @var string
     */
    const COLOUR_IMAGES = 'ImageC';

    /**
     * Grayscale images or image masks
     *
     * @var string
     */
    const GRAYSCALE_IMAGES = 'ImageB';
}