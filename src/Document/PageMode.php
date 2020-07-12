<?php

namespace Papier\Document;

class PageMode
{
    /**
     * Use none (neither document outline nor thumbnail images visible)
     *
     * @var string
     */
    const USE_NONE_MODE = 'UseNone';
  
    /**
     * Use outlines (document outline visible)
     *
     * @var string
     */
    const USE_OUTLINES_MODE = 'UseOutlines';

    /**
     * Use thumbs (thumbnail images visible)
     *
     * @var string
     */
    const USE_THUMBS_MODE = 'UseThumbs';

    /**
     * Full screen mode (full-screen mode, with no menu bar, window controls, or any other window visible)
     *
     * @var string
     */
    const FULL_SCREEN_MODE = 'FullScreen';

    /**
     * Use OC (optional content group panel visible)
     *
     * @var string
     */
    const USE_OC_MODE = 'UseOC';

    /**
     * Use attachments (attachments panel visible)
     *
     * @var string
     */
    const USE_ATTACHMENTS_MODE = 'UseAttachments';
}