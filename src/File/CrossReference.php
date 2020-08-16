<?php

namespace Papier\File;

use Papier\Object\DictionaryObject;
use Papier\Base\IndirectObject;

use Papier\File\CrossReferenceTable;

use Papier\Validator\IntValidator;

use InvalidArgumentException;

class CrossReference extends DictionaryObject
{
    /**
     * Offset of the crossreference.
     *
     * @var int
     */
    protected $offset = 0;

    /**
    * Instance of the object.
    *
    * @var \Papier\File\CrossReference
    */
    protected static $instance = null;
   
    /**
    * Get instance of crossreference.
    *
    * @return \Papier\File\CrossReference
    */
    public static function getInstance() 
    {
        if(is_null(self::$instance)) {
            self::$instance = new CrossReference();  
        }

        return self::$instance;
    }

    /**
     * Set crossreference's offset.
     *  
     * @param  int  $offset
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\File\CrossReference
     */
    public function setOffset($offset)
    {
        if (!IntValidator::isValid($offset, 0)) {
            throw new InvalidArgumentException("Offset is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->offset = $offset;
        return $this;
    } 

    /**
     * Get entry's offset.
     *
     * @return int
     */
    protected function getOffset()
    {
        return $this->offset;
    }

    /**
     * Add object to crossreference.
     *  
     * @param  \Papier\Base\IndirectObject  $object
     * @return \Papier\File\CrossReference
     */
    public function addObject($object)
    {        
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $objects = $this->getObjects();
        $objects[$object->getNumber()] = $object;

        return $this->setObjects($objects);
    }

    /**
     * Remove object from crossreference.
     *  
     * @param  \Papier\Base\IndirectObject  $object
     * @return \Papier\File\CrossReference
     */
    public function removeObject($object)
    {
        if (!$object instanceof IndirectObject) {
            throw new InvalidArgumentException("Object is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $objects = $this->getObjects();
        unset($objects[$object->getNumber()]);

        return $this->setObjects($objects);
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $table = new CrossReferenceTable();
        $subsection = $table->addSection()->addSubsection();

        $subsection->addEntry()->setFree()->setGeneration(65535);

        $offset = $this->getOffset();

        $objects = $this->getObjects();
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $subsection->addEntry()->setOffset($offset);
                $offset += strlen($object->getObject());
            }
        }

        return $table->format();
    }
}