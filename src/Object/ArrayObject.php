<?php

namespace Papier\Object;

class ArrayObject extends DictionaryObject
{
    /**
     * Get first object.
     *  
     * @return IndirectObject
     */
    public function first(): IndirectObject
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        $keys = $this->getKeys();

        return $objects[$keys[0]];
    }

    /**
     * Get last object.
     *  
     * @return IndirectObject
     */
    public function last(): IndirectObject
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        $keys = $this->getKeys();
        
        return $objects[$keys[$this->count()-1]];
    }

    /**
     * Check if object has given value.
     *  
     * @param  string  $value
     * @return bool
     */
    public function has(string $value): bool
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        if (count($objects)) {
            foreach ($objects as $object) {
                if ($object->getValue() == $value) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get object at position.
     *  
     * @return IndirectObject
     */
    public function current() : IndirectObject
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        return $objects[$this->position] ?? new NullObject();
    }

    /**
     * Get current position.
     *  
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }


    /**
     * Check if object exist at current position.
     *  
     * @return bool
     */
    public function valid(): bool
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        return isset($objects[$this->position]);
    }

    /**
     * Get number of objects.
     *  
     * @return int
     */
    public function count(): int
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        return count($objects);
    }

    
    /**
     * Append object to array.
     *  
     * @param IndirectObject $object
     * @return ArrayObject
     */
    public function append(IndirectObject $object): ArrayObject
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        $objects[] = $object;
		$this->setObjects($objects);

        return $this;
    }

    /**
     * Append object to array.
     *
     * @param IndirectObject $object
     * @return ArrayObject
     */
    public function push(IndirectObject $object): ArrayObject
    {
        return $this->append($object);
    }

    /**
     * Shift object from array.
     *
     * @return IndirectObject|null
     */
    public function shift(): IndirectObject|null
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        $object = array_shift($objects);
        $this->setObjects($objects);
        return $object;
    }

    /**
     * Pop object from array.
     *
     * @return IndirectObject|null
     */
    public function pop(): IndirectObject|null
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        $object = array_pop($objects);
        $this->setObjects($objects);

        return $object;
    }

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
		/** @var array<IndirectObject> $objects */
        $objects = $this->getObjects();
        $value = '';

        if (count($objects) > 0) {
            foreach ($objects as $object) {
                $value .= ' '.$object->write();
            }         
        }

        return '[' .trim($value). ']';
    }
}