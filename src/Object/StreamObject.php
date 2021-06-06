<?php

namespace Papier\Object;

use Papier\Factory\Factory;

use Papier\Type\FileSpecificationStringType;

use Papier\Validator\StringValidator;

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
    private function getStream(): string
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
     * @return string|null
     */
    protected function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set object's content.
     *  
     * @param  mixed  $content
     * @return StreamObject
     */
    public function setContent($content): StreamObject
    {
        $this->content = $content;
        return $this;
    } 

    /**
     * Add data to content.
     *  
     * @param string $data
     * @param bool $withEndOfLine
     * @return StreamObject
     */
    protected function addToContent(string $data, bool $withEndOfLine = true): StreamObject
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
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     */
    public function setFilter($filter): StreamObject
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
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'ArrayObject'.
     */
    public function setDecodeParms($parms): StreamObject
    {
        if (!$parms instanceof DictionaryObject && !$parms instanceof ArrayObject) {
            throw new InvalidArgumentException("DecodeParms is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('DecodeParms', $parms);
        return $this;
    } 

    /**
     * Set file specification.
     *
     * @param  FileSpecificationStringType $f
     * @return StreamObject
     */
    public function setF(FileSpecificationStringType $f): StreamObject
    {
        $this->setEntry('F', $f);
        return $this;
    } 

    /**
     * Set file filter.
     *  
     * @param  mixed  $ffilter
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'string' or 'ArrayObject'.
     */
    public function setFFilter($ffilter): StreamObject
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
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'ArrayObject'.
     */
    public function setFDecodeParms($parms): StreamObject
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
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'int'.
     */
    public function setDL(int $dl): StreamObject
    {
        $value = Factory::create('Integer', $dl);

        $this->setEntry('DL', $value);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $stream = $this->getStream();

        // Compute length of stream and set it into dictionary
        $length = Factory::create('Integer', strlen($stream));
    
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