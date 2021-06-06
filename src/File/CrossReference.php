<?php

namespace Papier\File;

use Papier\Object\DictionaryObject;

use Papier\Repository\Repository;

use Papier\Validator\IntegerValidator;

use InvalidArgumentException;

class CrossReference extends DictionaryObject
{
    /**
     * Offset of the cross-reference.
     *
     * @var int
     */
    protected $offset = 0;

    /**
    * Instance of the object.
    *
    * @var CrossReference
    */
    protected static $instance = null;
   
    /**
    * Get instance of cross-reference.
    *
    * @return CrossReference
    */
    public static function getInstance() 
    {
        if (is_null(self::$instance)) {
            self::$instance = new CrossReference();  
        }

        return self::$instance;
    }

    /**
     * Set cross-reference's offset.
     *  
     * @param  int  $offset
     * @return CrossReference
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setOffset(int $offset): CrossReference
    {
        if (!IntegerValidator::isValid($offset, 0)) {
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
    protected function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
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