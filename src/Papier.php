<?php

namespace Papier;

use Papier\File\FileHeader;
use Papier\File\FileTrailer;
use Papier\File\FileBody;

use Papier\File\CrossReference;

use Papier\Type\DocumentInformationDictionaryType;
use Papier\Type\PageObjectType;
use Papier\Type\ViewerPreferencesDictionaryType;

class Papier
{
    /**
     * Number of decimals for real numbers
     *
     * @var int
     */
    const MAX_DECIMALS = 5;

     /**
     * Header
     *
     * @var FileHeader
     */
    private FileHeader $header;

     /**
     * Body
     *
     * @var FileBody
     */
    private FileBody $body;

     /**
     * Trailer
     *
     * @var FileTrailer
     */
    private FileTrailer $trailer;

    /**
     * Create a new Papier instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->header = new FileHeader();
        $this->trailer = new FileTrailer();
        $this->body = new FileBody();

        $this->setVersion(4);
    } 

    /**
     * Get header.
     *
     * @return FileHeader
     */
    public function getHeader(): FileHeader
    {
        return $this->header;
    }

    /**
     * Get body.
     *
     * @return FileBody
     */
    public function getBody(): FileBody
    {
        return $this->body;
    }

    /**
     * Get trailer.
     *
     * @return FileTrailer
     */
    public function getTrailer(): FileTrailer
    {
        return $this->trailer;
    }

    /**
     * Get info.
     *
     * @return DocumentInformationDictionaryType
     */
    public function getInfo(): DocumentInformationDictionaryType
    {
        return $this->getTrailer()->getInfo();
    }

    /**
     * Set PDF version.
     *  
     * @param  int  $version
     * @return Papier
     */
    public function setVersion(int $version): Papier
    {
        $this->getHeader()->setVersion($version);
        return $this;
    } 


    /**
     * Add page to PDF's content.
     *
     * @return PageObjectType
     */
    public function addPage(): PageObjectType
    {
        return $this->getBody()->addPage();
    } 


    /**
     * Get viewer preferences.
     *  
     * @return ViewerPreferencesDictionaryType
     */
    public function getViewerPreferences(): ViewerPreferencesDictionaryType
    {
        $body = $this->getBody();
        return $body->getDocumentCatalog()->getViewerPreferences();
    } 

    /**
     * Build PDF's content.
     *
     * @return string
     */
    public function build(): string
    {
        // $this->check();
        
        $header = $this->getHeader();
        $body = $this->getBody();
        $trailer = $this->getTrailer();
        
        $trailer->setRoot($body->getDocumentCatalog());

        $crossreference = CrossReference::getInstance();

        $content  = $header->write();

        $crossreference->setOffset(strlen($content));

        $content .= $body->format();

        $trailer->setCrossReferenceOffset(strlen($content));

        $content .= $crossreference->write();
        $content .= $trailer->write();

        return $content;
    }

    /**
     * Save PDF's content.
     *
     * @param string $filename
     * @return bool
     */
    public function save(string $filename): bool
    {
        $content = $this->build();
        return file_put_contents($filename, trim($content)) !== false;
    } 

    /**
     * Check if PDF can be build.
     *
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }
}