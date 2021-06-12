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
    protected int $offset = 0;

    /**
    * Instance of the object.
    *
    * @var ?CrossReference
    */
    protected static ?CrossReference $instance = null;

    /**
     * Crossreferencee's table.
     *
     * @var CrossReferenceTable
     */
    protected CrossReferenceTable $table;

    /**
    * Get instance of cross-reference.
    *
    * @return CrossReference
    */
    public static function getInstance(): CrossReference
    {
        if (is_null(self::$instance)) {
            self::$instance = new CrossReference();
            self::$instance->table = new CrossReferenceTable();
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
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * Get crossreference's table.
     *
     * @return CrossReferenceTable
     */
    public function getTable(): CrossReferenceTable
    {
        return $this->table;
    }

    /**
     * Clear crossreference's table.
     *
     * @return CrossReference
     */
    public function clearTable(): CrossReference
    {
        $this->table = new CrossReferenceTable();
        return $this;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        return $this->getTable()->format();
    }
}