<?php

namespace Papier\Object;

use Papier\Object\IntegerObject;
use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

use Papier\Validator\StringValidator;

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
     * Add data to content.
     *  
     * @param  string  $data
     * @param  bool  $withEndOfLine
     * @return \Papier\Object\StreamObject
     */
    protected function addToContent($data, $withEndOfLine = true)
    {
        $content = $this->getContent();
        $content.= $data;
        $content.= $withEndOfLine ? self::EOL_MARKER : '';

        return $this->setContent($content);
    } 

    /**
     * Set filter.
     *  
     * @param  mixed  $filter
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return \Papier\Object\StreamObject
     */
    public function setFilter($filter)
    {
        if (!StringValidator::isValid($filter) && !$filter instanceof ArrayObject) {
            throw new InvalidArgumentException("Filter is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $filter instanceof ArrayObject ? $filter : Factory::create('Name', $filter);

        $this->setEntry('Filter', $value);
        return $this;
    } 

    /**
     * Set decode parameters.
     *  
     * @param  mixed  $parms
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'ArrayObject'.
     * @return \Papier\Object\StreamObject
     */
    public function setDecodeParms($parms)
    {
        if (!$parms instanceof DictionaryObject && !$parms instanceof ArrayObject) {
            throw new InvalidArgumentException("DecodeParms is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('DecodeParms', $parms);
        return $this;
    } 

    /**
     * Set file filter.
     *  
     * @param  mixed  $ffilter
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     * @return \Papier\Object\StreamObject
     */
    public function setFFilter($ffilter)
    {
        if (!StringValidator::isValid($ffilter) && !$ffilter instanceof ArrayObject) {
            throw new InvalidArgumentException("FFilter is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = $ffilter instanceof ArrayObject ? $ffilter : Factory::create('Name', $ffilter);

        $this->setEntry('FFilter', $value);
        return $this;
    } 

    /**
     * Set file decode parameters.
     *  
     * @param  mixed  $parms
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'ArrayObject'.
     * @return \Papier\Object\StreamObject
     */
    public function setFDecodeParms($parms)
    {
        if (!$parms instanceof DictionaryObject && !$parms instanceof ArrayObject) {
            throw new InvalidArgumentException("FDecodeParms is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('FDecodeParms', $parms);
        return $this;
    } 

    /**
     * Set length of decoded stream.
     *  
     * @param  int  $dl
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     * @return \Papier\Object\StreamObject
     */
    public function setDL($dl)
    {
        if (!IntegerValidator::isValid($dl)) {
            throw new InvalidArgumentException("DL is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $dl);

        $this->setEntry('DL', $value);
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

        // Compute length of stream and set it into dictionary
        $length = Factory::create('Integer', intval(strlen($stream)));
    
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