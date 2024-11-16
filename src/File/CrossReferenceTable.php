<?php

namespace Papier\File;

use Papier\Object\ArrayObject;
use Papier\File\CrossReference\CrossReferenceSection;

class CrossReferenceTable extends ArrayObject
{
    /**
     * Add new section.
     *  
     * @return CrossReferenceSection
     */
    public function addSection(): CrossReferenceSection
    {
        $objects = $this->getSections();

        $object = new CrossReferenceSection();
        $objects[] = $object;

        $this->setObjects($objects);

        return $object;
    }

    /**
     * Get subsections.
     *  
     * @return array
     */
    public function getSections(): array
    {
        return $this->getObjects();
    }  

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $objects = $this->getSections();

        $value = '';
		foreach ($objects as $object) {
			$value .= $object->write();
		}

        return rtrim($value);
    }
}