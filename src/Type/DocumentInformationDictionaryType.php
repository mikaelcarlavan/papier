<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\StringValidator;
use Papier\Validator\DateValidator;
use Papier\Validator\TrappedValidator;

use InvalidArgumentException;

class DocumentInformationDictionaryType extends DictionaryObject
{
    /**
     * Set title.
     * 
     * @param   string  $title
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setTitle($title)
    {
        if (!StringValidator::isValid($title)) {
            throw new InvalidArgumentException("Title is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $title);

        $this->setEntry('Title', $value);
        return $this;
    } 

    /**
     * Set author.
     * 
     * @param   string  $author
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setAuthor($author)
    {
        if (!StringValidator::isValid($author)) {
            throw new InvalidArgumentException("Author is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $author);

        $this->setEntry('Author', $value);
        return $this;
    } 

    /**
     * Set subject.
     * 
     * @param   string  $subject
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setSubject($subject)
    {
        if (!StringValidator::isValid($subject)) {
            throw new InvalidArgumentException("Subject is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $subject);

        $this->setEntry('Subject', $value);
        return $this;
    } 

    /**
     * Set keywords.
     * 
     * @param   string  $keywords
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setKeywords($keywords)
    {
        if (!StringValidator::isValid($keywords)) {
            throw new InvalidArgumentException("Keywords is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $keywords);

        $this->setEntry('Keywords', $value);
        return $this;
    } 

    /**
     * Set creator.
     * 
     * @param   string  $creator
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setCreator($creator)
    {
        if (!StringValidator::isValid($creator)) {
            throw new InvalidArgumentException("Creator is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $creator);

        $this->setEntry('Creator', $value);
        return $this;
    } 

    /**
     * Set producer.
     * 
     * @param   string  $producer
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setProducer($producer)
    {
        if (!StringValidator::isValid($producer)) {
            throw new InvalidArgumentException("Producer is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $producer);

        $this->setEntry('Producer', $value);
        return $this;
    } 

    /**
     * Set creation date.
     *
     * @param   string  $date
     * @throws InvalidArgumentException if the provided argument is not a valid date.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setCreationDate($date)
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("CreationDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Date', $date);

        $this->setEntry('CreationDate', $value);
        return $this;
    }

    /**
     * Set modification date.
     * 
     * @param   string  $date
     * @throws InvalidArgumentException if the provided argument is not a valid date.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setModDate($date)
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("ModDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('TextString', $date);

        $this->setEntry('ModDate', $value);
        return $this;
    }

    /**
     * Set trapped.
     * 
     * @param   string  $trapped
     * @throws InvalidArgumentException if the provided argument is not a valid trapped.
     * @return \Papier\Type\DocumentInformationDictionaryType
     */
    public function setTrapped($trapped)
    {
        if (!TrappedValidator::isValid($trapped)) {
            throw new InvalidArgumentException("Trapped is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $trapped);

        $this->setEntry('Trapped', $value);
        return $this;
    }
}