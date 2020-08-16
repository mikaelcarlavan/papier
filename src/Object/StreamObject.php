<?php

namespace Papier\Object;

use Papier\Object\IntegerObject;
use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use RunTimeException;
use InvalidArgumentException;

class StreamObject extends DictionaryObject
{
    /**
     * The content of the object.
     *
     * @var mixed
     */
    protected $content;

    /**
     * Get object's stream.
     *
     * @return string
     */
    private function getStream()
    {
        $stream = $this->getContent();

        if ($this->hasEntry('Filter')) {
            $filters = $this->getEntry('Filter');
            $params = $this->getEntry('DecodeParms');

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
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $stream = $this->getStream();
        //$stream = $stream ? $stream->write() : '';

        // Compute length of stream and set it into dictionary
        $length = Factory::getInstance()->createObject('Integer', intval(strlen($stream)), false);
    
        $this->setEntry('Length', $length);

        $value = parent::format();
        
        if (!empty($stream)) {
            $value .= self::EOL_MARKER;
            $value .= 'stream' .self::EOL_MARKER;
            $value .= $stream .self::EOL_MARKER;
            $value .= 'endstream';
        }

        return $value;
    }
}