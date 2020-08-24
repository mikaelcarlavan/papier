<?php

namespace Papier\Document;

class Trapped
{
    /**
     * The document has been fully trapped
     *
     * @var string
     */
    const TRUE = 'True';
  
    /**
     * The document has not yet been trapped
     *
     * @var string
     */
    const FALSE = 'False';

    /**
     * Either it is unknown whether the document has been trapped or it has been partly but not yet fully trapped
     *
     * @var string
     */
    const UNKNOWN = 'Unknown';
}