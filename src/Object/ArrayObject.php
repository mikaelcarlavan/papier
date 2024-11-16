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
        $objects = $this->getObjects();
        return $objects[$this->position];
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
     * @return IndirectObject
     */
    public function shift(): IndirectObject
    {
        $objects = $this->getObjects();
        $object = array_shift($objects);
        $this->setObjects($objects);
        return $object;
    }

    /**
     * Pop object from array.
     *
     * @return IndirectObject
     */
    public function pop(): IndirectObject
    {
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
        $objects = $this->getObjects();
        $value = '';

        if (is_array($objects) && count($objects) > 0) {
            foreach ($objects as $object) {
                $value .= ' '.$object->write(false);
            }         
        }

        return '[' .trim($value). ']';
    }
}