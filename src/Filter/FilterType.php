<?php

namespace Papier\Filter;

class FilterType
{
    /**
     * ASCIIHex filter
     *
     * @var string
     */
    const ASCIIHEX_DECODE = 'ASCIIHexDecode';

    /**
     * ASCII85 filter
     *
     * @var string
     */
    const ASCII85_DECODE = 'ASCII85Decode';

    /**
     * JPX filter
     *
     * @var string
     */
    const JPX_DECODE = 'JPXDecode';

    /**
     * DCT filter
     *
     * @var string
     */
    const DCT_DECODE = 'DCTDecode';

    /**
     * CCITTX Fax filter
     *
     * @var string
     */
    const CCITT_FAX_DECODE = 'CITTFaxDecode';
    
    /**
     * JBIG2 filter
     *
     * @var string
     */
    const JBIG2_DECODE = 'JBIG2Decode';

    /**
     * Run-Length filter
     *
     * @var string
     */
    const RUN_LENGTH_DECODE = 'RunLengthDecode';

    /**
     * LZW filter
     *
     * @var string
     */
    const LZW_DECODE = 'LZWDecode';

    /**
     * Flate filter
     *
     * @var string
     */
    const FLATE_DECODE = 'FlateDecode';
}