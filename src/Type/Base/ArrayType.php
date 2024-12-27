<?php

namespace Papier\Type\Base;

use Papier\Object\ArrayObject;

class ArrayType extends ArrayObject
{
    /**
     * Move to previous position.
     *
     */
    public function previous(): void
    {
        $this->position--;
        if (!$this->valid()) {
            // Restore position
            $this->position++;
        }
    }

    /**
     * Move to next position.
     *
     */
    public function next(): void
    {
        $this->position++;
        if (!$this->valid()) {
            // Restore position
            $this->position--;
        }
    }


    /**
     * Move to given position.
     *
     */
    public function moveTo(int $position): void
    {
        $oldPosition = $this->position;
        $this->position = $position;
        if (!$this->valid()) {
            // Restore position
            $this->position = $oldPosition;
        }
    }
}