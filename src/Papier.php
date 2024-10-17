<?php

namespace Papier;

use Papier\Document\ProcedureSet;
use Papier\Factory\Factory;
use Papier\File\FileHeader;
use Papier\File\FileTrailer;
use Papier\File\FileBody;

use Papier\File\CrossReference;

use Papier\Object\NullObject;
use Papier\Type\DocumentInformationDictionaryType;
use Papier\Type\PageObjectType;
use Papier\Type\ViewerPreferencesDictionaryType;
use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;
use Papier\Widget\DrawWidget;
use Papier\Widget\ImageWidget;
use Papier\Widget\RectangleWidget;
use Papier\Widget\TextWidget;

class Papier
{
    /**
     * Default DPI
     *
     * @var float
     */
    const DEFAULT_DPI = 72;

    /**
     * Convert factor from mm to user unit
     *
     * @var float
     */
    const MM_TO_USER_UNIT = self::DEFAULT_DPI / 25.4;
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
     * Widgets
     *
     * @var array
     */
    private array $widgets = [];

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

        $this->setVersion(3);
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
     * Create image widget.
     *
     * @return ImageWidget
     */
    public function createImageWidget(): ImageWidget
    {
        $widget = new ImageWidget();
        $widget->setPage($this->getCurrentPage());

        $this->widgets[] = $widget;
        return $widget;
    }

    /**
     * Create text widget.
     *
     * @return TextWidget
     */
    public function createTextWidget(): TextWidget
    {
        $widget = new TextWidget();
        $widget->setPage($this->getCurrentPage());

        $this->widgets[] = $widget;
        return $widget;
    }


    /**
     * Create rectangle widget.
     *
     * @return RectangleWidget
     */
    public function createRectangleWidget(): RectangleWidget
    {
        $widget = new RectangleWidget();
        $widget->setPage($this->getCurrentPage());

        $this->widgets[] = $widget;
        return $widget;
    }

    /**
     * Create Bezier widget.
     *
     * @return DrawWidget
     */
    public function createDrawWidget(): DrawWidget
    {
        $widget = new DrawWidget();
        $widget->setPage($this->getCurrentPage());

        $this->widgets[] = $widget;
        return $widget;
    }

    /**
     * Set current PDF's page.
     *
     * @param int $page
     * @return Papier
     */
    public function setCurrentPage(int $page): Papier
    {
        $this->getBody()->getPageTree()->getKids()->moveTo($page);
        return $this;
    }

    /**
     * Get current PDF's page.
     *
     * @return PageObjectType
     */
    public function getCurrentPage(): PageObjectType
    {
        /** @var PageObjectType $page */
        $page = $this->getBody()->getPageTree()->getKids()->current();
        return $page;
    }

    /**
     * Add page to PDF's content.
     *
     * @param array $dimensions Dimensions (width and height) of the page in millimeters.
     * @param float $dpi Resolution of the page (72 by default, i.e. 72 points par inch).
     * @return PageObjectType
     */
    public function addPage(array $dimensions): PageObjectType
    {
        $page = $this->getBody()->addPage();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;
        $page->setMediaBox([0, 0, $mmToUserUnit * $dimensions[0], $mmToUserUnit * $dimensions[1]]);

        return $page;
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
     * Get viewer preferences.
     *
     * @return ViewerPreferencesDictionaryType
     */
    private function getWidgets(): array
    {
        return $this->widgets;
    }

    /**
     * Build PDF's content.
     *
     * @return string
     */
    public function build(): string
    {
        // Build widgets
        $widgets = $this->getWidgets();

        if (count($widgets)) {
            foreach ($widgets as $widget) {
                $widget->format();
            }
        }

        $header = $this->getHeader();
        $body = $this->getBody();
        $trailer = $this->getTrailer();

        $trailer->setRoot($body->getDocumentCatalog());

        $crossReference = CrossReference::getInstance();
        $crossReference->clearTable();

        $content  = $header->write();

        $crossReference->setOffset(strlen($content));

        $content .= $body->format();

        $trailer->setCrossReferenceOffset(strlen($content));

        $content .= $crossReference->write();
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
     * Check if PDF can be built.
     *
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }
}