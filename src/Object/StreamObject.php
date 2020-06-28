<?php

namespace Papier\Object;

use Papier\Object\IntegerObject;
use Papier\Object\DictionaryObject;
use Exception;

class StreamObject extends IndirectObject
{
    /**
     * The content of the object.
     *
     * @var mixed
     */
    protected $stream;
    
    /**
     * Create a new ArrayObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = new DictionaryObject();
        parent::__construct();
    } 

    /**
     * Get object's dictionary.
     *
     * @return string
     */
    protected function getDictionary()
    {
        return $this->getValue();
    }

    /**
     * Get object's stream.
     *
     * @return string
     */
    protected function getStream()
    {
        return $this->stream;
    }

    /**
     * Set object's stream.
     *  
     * @param  mixed  $stream
     * @return \Papier\Object\StreamObject
     */
    protected function setStream($stream)
    {
        $this->stream = $stream;
        return $this;
    } 

    /**
     * Add entry to stream's dictionnary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Object\StreamObject
     */
    protected function addEntry($key, $object)
    {
        $this->getDictionary()->setObjectForKey($key, $object);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $dictionary = $this->getDictionary();
        $stream = $this->getStream();
        $stream = $stream ? $stream->format() : '';

        // Compute length of stream and set it into dictionary
        try {
            $length = strlen($stream);
            $length = new IntegerObject();
            $length->setValue(intval($length));
    
            $dictionary->addEntry('Length', $length);
        } catch (Exception $e) {
            throw $e;
        }

        $value = '';
        if ($dictionary) {
            $value .= $dictionary->write();
        }
        $value .= 'stream' . $this->EOL_MARKER;
        $value .= $stream ? $stream . $this->EOL_MARKER : '';
        $value .= 'endstream';

        return $value;
    }
}