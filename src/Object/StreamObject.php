<?php

namespace Papier\Object;

use Papier\Base\IntegerObject;
use Papier\Object\DictionaryObject;
use RunTimeException;

class StreamObject extends IndirectObject
{
    /**
     * The content of the object.
     *
     * @var mixed
     */
    protected $content;
    
    /**
     * Create a new StreamObject instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->value = new DictionaryObject();
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
    private function getStream()
    {
        $stream = $this->getContent();

        // Apply filters
        $dictionary = $this->getDictionary();

        if ($dictionary->hasKey('Filter')) {
            $filters = $dictionary['Filter'];
            $params = $dictionary['DecodeParms'];

            if (is_array($filters) && count($filters) > 0) {
                foreach ($filters as $i => $name) {

                    $class = 'Papier\Filter\\'.$name.'Filter';
                    if (class_exists($class)) {
                        $param = $params[$i];
                        $stream = $class::encode($stream, $param);
                    } else {
                        throw new InvalidArgumentException("Filter $name is not implemented. See ".__CLASS__." class's documentation for possible values.");
                    }
                }
            }
        }

        return $stream;
    }

    /**
     * Get object's content.
     *
     * @return string
     */
    protected function getContent()
    {
        return $this->content;
    }

    /**
     * Set object's content.
     *  
     * @param  mixed  $content
     * @return \Papier\Object\StreamObject
     */
    protected function setContent($content)
    {
        $this->content = $content;
        return $this;
    } 

    /**
     * Add entry to stream's dictionnary.
     *      
     * @param  string  $key
     * @param  mixed  $object
     * @return \Papier\Object\StreamObject
     */
    private function addEntry($key, $object)
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
        $stream = $stream ? $stream->write() : '';

        // Compute length of stream and set it into dictionary
        $length = strlen($stream);
        $length = new IntegerObject();
        $length->setValue(intval($length));
    
        $this->addEntry('Length', $length);

        $value = '';
        $value .= $dictionary->write();
        $value .= 'stream' .self::EOL_MARKER;
        $value .= $stream ?? '';
        $value .= 'endstream';

        return $value;
    }
}