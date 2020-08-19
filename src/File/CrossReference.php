<?php

namespace Papier\File;

use Papier\Object\DictionaryObject;
use Papier\Base\IndirectObject;

use Papier\File\CrossReferenceTable;
use Papier\Repository\Repository;

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

        $objects = Repository::getInstance()->getObjects();
        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $subsection->addEntry()->setOffset($offset);
                $offset += strlen($object->getObject());
            }
        }

        return $table->format();
    }
}