<?php

namespace Papier\Object;

use Papier\Factory\Factory;

use Papier\Type\DictionaryType;
use Papier\Type\FileSpecificationDictionaryType;
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
     * Set compression method of stream.
     *
     * @var ?string
     */
    protected ?string $compression = null;

    /**
     * Get object's stream.
     *
     * @return string
     */
    private function getStream(): string
    {
        $stream = $this->getContent();
        $compression = $this->getCompression();

        if (!is_null($compression)) {
            $class = 'Papier\Filter\\'.$compression.'Filter';
            if (class_exists($class)) {
                $stream = $class::process($stream);
                $this->setFilter($compression);
            } else {
                throw new InvalidArgumentException("Compression $compression is not implemented. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        return $stream ?? '';
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
     * Get stream's compression.
     *
     * @return string|null
     */
    protected function getCompression(): ?string
    {
        return $this->compression;
    }

    /**
     * Set stream's compression.
     *
     * @param  string  $compression
     * @return StreamObject
     */
    public function setCompression(string $compression): StreamObject
    {
        $this->compression = $compression;
        return $this;
    }

    /**
     * Add data to content.
     *
     * @param string $data
     * @return StreamObject
     */
    protected function addToContent(string $data): StreamObject
    {
        $content = $this->getContent();
        $content.= $data;
        $content.= self::EOL_MARKER;

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

        $value = $filter instanceof ArrayObject ? $filter : Factory::create('Papier\Type\NameType', $filter);

        $this->setEntry('Filter', $value);
        return $this;
    } 

    /**
     * Set decode parameters.
     *  
     * @param  DictionaryType  $parms
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'ArrayObject'.
     */
    public function setDecodeParms(DictionaryType $parms): StreamObject
    {
        $this->setEntry('DecodeParms', $parms);
        return $this;
    } 

    /**
     * Set file specification.
     *
     * @param  FileSpecificationDictionaryType $f
     * @return StreamObject
     */
    public function setF(FileSpecificationDictionaryType $f): StreamObject
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

        $value = $ffilter instanceof ArrayObject ? $ffilter : Factory::create('Papier\Type\NameType', $ffilter);

        $this->setEntry('FFilter', $value);
        return $this;
    } 

    /**
     * Set file decode parameters.
     *  
     * @param  ArrayObject  $parms
     * @return StreamObject
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'ArrayObject'.
     */
    public function setFDecodeParms(ArrayObject $parms): StreamObject
    {
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
        $value = Factory::create('Papier\Type\IntegerType', $dl);

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
        $length = Factory::create('Papier\Type\IntegerType', strlen($stream));
    
        $this->setEntry('Length', $length);

        $value = parent::format();

        $value .= self::EOL_MARKER;
        $value .= 'stream' .self::EOL_MARKER;
        if (!empty($stream)) {
            $value .= $stream .self::EOL_MARKER;
        }
        $value .= 'endstream';

        return $value;
    }
}