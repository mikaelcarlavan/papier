<?php

namespace Papier\File\CrossReference;

use Papier\Object\ArrayObject;

class CrossReferenceSection extends ArrayObject
{
    /**
     * Add new subsection.
     *  
     * @return CrossReferenceSubSection
     */
    public function addSubsection(): CrossReferenceSubSection
    {
        $objects = $this->getSubsections();

        $object = new CrossReferenceSubSection();
        $objects[] = $object;

        $this->setObjects($objects);

        return $object;
    }

    /**
     * Get subsections.
     *  
     * @return array<CrossReferenceSubSection>
     */
    public function getSubsections(): array
    {
		/** @var array<CrossReferenceSubSection> $objects */
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
        $objects = $this->getSubsections();

        $value = 'xref'. self::EOL_MARKER;
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $value .= $object->write();
            }
        }

        return $value;
    }
}