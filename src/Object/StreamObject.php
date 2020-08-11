<?php

namespace Papier\Object;

use Papier\Base\IntegerObject;
use Papier\Object\DictionaryObject;
use RunTimeException;

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

        if ($this->hasKey('Filter')) {
            $filters = $this->getObjectForKey('Filter');
            $params = $this->getObjectForKey('DecodeParms');

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
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $stream = $this->getStream();
        $stream = $stream ? $stream->write() : '';

        // Compute length of stream and set it into dictionary
        $length = strlen($stream);
        $length = new IntegerObject();
        $length->setValue(intval($length));
    
        $this->addEntry('Length', $length);

        $value = '';
        $value .= parent::format() . self::EOL_MARKER;
        $value .= 'stream' .self::EOL_MARKER;
        $value .= $stream ?? '';
        $value .= 'endstream';

        return $value;
    }
}