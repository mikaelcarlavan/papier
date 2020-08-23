<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;

use Papier\Factory\Factory;

use Papier\Validator\StringValidator;

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
        return $extension;
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
        return $extension;
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
        return $extension;
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
        return $extension;
    } 
}