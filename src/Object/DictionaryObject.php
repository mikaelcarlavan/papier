<?php

namespace Papier\Object;

use Countable;
use InvalidArgumentException;
use Iterator;
use Papier\Factory\Factory;
use Papier\Validator\ArrayValidator;

/**
 * @implements Iterator<mixed, mixed>
 */
class DictionaryObject extends IndirectObject implements Countable, Iterator
{
    /**
     * The value of the current position.
     *
     * @var int
     */
    protected int $position = 0;

    /**
     * Create a new DictionaryObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = [];
        $this->position = 0;
    }  

    /**
     * Reset current position.
     *  
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Get object at position.
     *  
     * @return mixed
     */
    public function current(): mixed
    {
        $keys = $this->getKeys();
        $objects = $this->getObjects();
        return $objects[$keys[$this->position]];
    }

    /**
     * Get current position.
     *  
     * @return mixed
     */
    public function key() : mixed
    {
        $keys = $this->getKeys();
        return $keys[$this->position];
    }

    /**
     * Increment current position.
     *  
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Check if object exist at current position.
     *  
     * @return bool
     */
    public function valid(): bool
    {
        $keys = $this->getKeys();
        return isset($keys[$this->position]);
    }

    /**
     * Check if object has given key.
     *  
     * @param  mixed  $key
     * @return bool
     */
    public function hasEntry(mixed $key): bool
    {
        $objects = $this->getObjects();
        return isset($objects[$key]);
    }

    /**
     * Set object for given key.
     *
     * @param  mixed  $key
     * @param  mixed  $object
     * @return DictionaryObject
     */
    protected function setObjectForKey(mixed $key, mixed $object): DictionaryObject
    {
        $objects = $this->getObjects();
        $objects[$key] = $object;

        return $this->setObjects($objects);
    }

    /**
     * Get value for given key.
     *  
     * @param  mixed  $key
     * @return mixed
     */
    protected function getObjectForKey(mixed $key): mixed
    {
        $objects = $this->getObjects();
        return $objects[$key] ?? new NullObject();
    }  
    

    /**
     * Set entry for given key.
     *      
     * @param  mixed  $key
     * @param  mixed  $object
     * @return DictionaryObject
     */
    public function setEntry(mixed $key, mixed $object): DictionaryObject
    {
        $this->setObjectForKey($key, $object);
        return $this;
    }

    /**
     * Unset entry for given key.
     *      
     * @param  string  $key
     * @return DictionaryObject
     */
    public function unsetEntry(string $key): DictionaryObject
    {
        $objects = $this->getObjects();
        unset($objects[$key]);

        return $this->setObjects($objects);
    }

    /**
     * Get entry from dictionary.
     *      
     * @param  mixed  $key
     * @return mixed
     */
    public function getEntry(mixed $key)
    {
        return $this->getObjectForKey($key);
    }

    /**
     * Get entry value from dictionary.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getEntryValue(string $key): mixed
    {
		/** @var BaseObject $object */
        $object = $this->getObjectForKey($key);
        return $object->getValue();
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
     * Get objects.
     *  
     * @return array<int|string, BaseObject>
     */
    public function getObjects(): array
    {
		/** @var array<int|string, BaseObject> $objects */
        $objects = $this->getValue();
        return $objects;
    }

    /**
     * Get keys.
     *  
     * @return array<mixed>
     */
    public function getKeys(): array
    {
        $objects = $this->getObjects();
        return array_keys($objects);
    }

    /**
     * Erase objects.
     *
     * @return DictionaryObject
     */
    public function clearObjects(): DictionaryObject
    {
		$this->clearValue();
        return $this;
    }

    /**
     * Set objects.
     * 
     * @param  array<mixed>   $objects
     * @return DictionaryObject
     * @throws InvalidArgumentException if the provided argument is not an array of 'IndirectObject'.
     */
    protected function setObjects(array $objects): DictionaryObject
    {
        if (!ArrayValidator::isValid($objects)) {
            throw new InvalidArgumentException("Object's list is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $this->setValue($objects);
        return $this;
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
        foreach ($objects as $key => $object) {
			$name = Factory::create('Papier\Type\Base\NameType', $key);
            $value .= $name->format() .' '. $object->write();
        }

        return '<< ' .$value. ' >>';
    }
}