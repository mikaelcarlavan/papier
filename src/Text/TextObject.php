<?php

namespace Papier\Text;

trait TextObject
{
    /**
     * Begin a text object.
     *  
     * @return mixed
     */
    public function beginText()
    {
        $state = 'BT';
        return $this->addToContent($state);
    }

    /**
     * End a text object.
     *  
     * @return mixed
     */
    public function endText()
    {
        $state = 'ET';
        return $this->addToContent($state);
    }
}