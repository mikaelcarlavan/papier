<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setTitle(string $title)
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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setAuthor(string $author)
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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setSubject(string $subject)
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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setKeywords(string $keywords)
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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setCreator(string $creator)
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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'string'.
     */
    public function setProducer(string $producer)
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
     * @param  mixed  $date
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid date.
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
     * @param   mixed  $date
     * @return DocumentInformationDictionaryType
     *@throws InvalidArgumentException if the provided argument is not a valid date.
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
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid trapped.
     */
    public function setTrapped(string $trapped)
    {
        if (!TrappedValidator::isValid($trapped)) {
            throw new InvalidArgumentException("Trapped is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $trapped);

        $this->setEntry('Trapped', $value);
        return $this;
    }
}