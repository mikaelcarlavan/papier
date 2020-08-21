<?php

namespace Papier\Graphics;

trait ClippingPath
{
    /**
     * Modify the clipping path using the nonzero window rule.
     *  
     * @return mixed
     */
    public function modify()
    {
        $state = 'W';
        return $this->addToContent($state);
    }

    /**
     * Modify the clipping path using the even-odd window rule.
     *  
     * @return mixed
     */
    public function evenOddModify()
    {
        $state = 'W*';
        return $this->addToContent($state);
    }
}