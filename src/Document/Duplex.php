<?php

namespace Papier\Document;

class Duplex
{
    /**
     * Print single-sided
     *
     * @var string
     */
    const SIMPLEX = 'Simplex';
  
    /**
     * Duplex and flip on the short edge of the sheet
     *
     * @var string
     */
    const DUPLEX_FLIP_SHORT_EDGE = 'DuplexFlipShortEdge';

    /**
     * Duplex and flip on the long edge of the sheet
     *
     * @var string
     */
    const DUPLEX_FLIP_LONG_EDGE = 'DuplexFlipLongEdge';
}