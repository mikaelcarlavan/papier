<?php

namespace Papier\Text;

trait TextObject
{
    /**
     * Begin a text object.
     *  
     * @return mixed
     */
    public function begin()
    {
        $state = 'BT';
        return $this->addToContent($state);
    }

    /**
     * End a text object.
     *  
     * @return mixed
     */
    public function end()
    {
        $state = 'ET';
        return $this->addToContent($state);
    }
}