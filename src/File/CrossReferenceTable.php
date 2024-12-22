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
     * @return array<CrossReferenceSection>
     */
    public function getSections(): array
    {
		/** @var array<CrossReferenceSection> $objects */
		$objects = $this->getObjects();
        return $objects;
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