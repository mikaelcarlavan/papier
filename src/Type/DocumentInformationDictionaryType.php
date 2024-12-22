<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;

use Papier\Factory\Factory;

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
     */
    public function setTitle(string $title): DocumentInformationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $title);

        $this->setEntry('Title', $value);
        return $this;
    } 

    /**
     * Set author.
     * 
     * @param   string  $author
     * @return DocumentInformationDictionaryType
     */
    public function setAuthor(string $author): DocumentInformationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $author);

        $this->setEntry('Author', $value);
        return $this;
    } 

    /**
     * Set subject.
     * 
     * @param   string  $subject
     * @return DocumentInformationDictionaryType
     */
    public function setSubject(string $subject): DocumentInformationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $subject);

        $this->setEntry('Subject', $value);
        return $this;
    } 

    /**
     * Set keywords.
     * 
     * @param   string  $keywords
     * @return DocumentInformationDictionaryType
     */
    public function setKeywords(string $keywords): DocumentInformationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $keywords);

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
    public function setCreator(string $creator): DocumentInformationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $creator);

        $this->setEntry('Creator', $value);
        return $this;
    } 

    /**
     * Set producer.
     * 
     * @param   string  $producer
     * @return DocumentInformationDictionaryType
     */
    public function setProducer(string $producer): DocumentInformationDictionaryType
    {
        $value = Factory::create('Papier\Type\TextStringType', $producer);

        $this->setEntry('Producer', $value);
        return $this;
    } 

    /**
     * Set creation date.
     *
     * @param  \DateTime|string  $date
     * @return DocumentInformationDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid date.
     */
    public function setCreationDate(\DateTime|string $date): DocumentInformationDictionaryType
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("CreationDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\DateType', $date);

        $this->setEntry('CreationDate', $value);
        return $this;
    }

    /**
     * Set modification date.
     * 
     * @param  \DateTime|string  $date
     * @return DocumentInformationDictionaryType
     *@throws InvalidArgumentException if the provided argument is not a valid date.
     */
    public function setModDate(\DateTime|string $date): DocumentInformationDictionaryType
    {
        if (!DateValidator::isValid($date)) {
            throw new InvalidArgumentException("ModDate is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\TextStringType', $date);

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
    public function setTrapped(string $trapped): DocumentInformationDictionaryType
    {
        if (!TrappedValidator::isValid($trapped)) {
            throw new InvalidArgumentException("Trapped is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $trapped);

        $this->setEntry('Trapped', $value);
        return $this;
    }
}