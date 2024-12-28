<?php

namespace Papier;

use InvalidArgumentException;
use Papier\Component\Base\BaseComponent;
use Papier\Component\DrawComponent;
use Papier\Component\ImageComponent;
use Papier\Component\SegmentComponent;
use Papier\Component\RectangleComponent;
use Papier\Component\TextComponent;
use Papier\Factory\Factory;
use Papier\File\CrossReference;
use Papier\File\FileBody;
use Papier\File\FileHeader;
use Papier\File\FileTrailer;
use Papier\Type\DocumentInformationDictionaryType;
use Papier\Type\PageObjectType;
use Papier\Type\ViewerPreferencesDictionaryType;
use Papier\Validator\NumbersArrayValidator;

class Papier
{
    /**
     * Default DPI
     *
     * @var float
     */
    const DEFAULT_DPI = 72.0;

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
     * Components
     *
     * @var array<BaseComponent>
     */
    private array $components = [];

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
     * Create image component.
     *
     * @return ImageComponent
     */
    public function createImageComponent(): ImageComponent
    {
		/** @var ImageComponent $component */
		$component = $this->createComponent('Papier\Component\ImageComponent');
		return $component;
	}

    /**
     * Create text component.
     *
     * @return TextComponent
     */
    public function createTextComponent(): TextComponent
    {
		/** @var TextComponent $component */
		$component = $this->createComponent('Papier\Component\TextComponent');
		return $component;
	}


    /**
     * Create rectangle component.
     *
     * @return RectangleComponent
     */
    public function createRectangleComponent(): RectangleComponent
    {
		/** @var RectangleComponent $component */
		$component = $this->createComponent('Papier\Component\RectangleComponent');
		return $component;
	}

    /**
     * Create Bezier component.
     *
     * @return DrawComponent
     */
    public function createDrawComponent(): DrawComponent
    {
		/** @var DrawComponent $component */
		$component = $this->createComponent('Papier\Component\DrawComponent');
		return $component;
    }

	/**
	 * Create segment component.
	 *
	 * @return SegmentComponent
	 */
	public function createSegmentComponent(): SegmentComponent
	{
		/** @var SegmentComponent $component */
		$component = $this->createComponent('Papier\Component\SegmentComponent');
		return $component;
	}

	/**
	 * Create a new component of type
	 *
	 * @template T
	 * @param class-string<T> $class
	 * @return BaseComponent
	 * @throws InvalidArgumentException if the provided type's object does not exist.
	 */
	public function createComponent(string $class)
	{
		/** @var BaseComponent $component */
		$component = Factory::create($class);
		$this->components[] = $component;
		return $component;
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
	 * @param array<float> $dimensions Dimensions (width and height) of the page in millimeters.
	 * @return PageObjectType
	 */
    public function addPage(array $dimensions): PageObjectType
    {
        $page = $this->getBody()->addPage();

		if (!NumbersArrayValidator::isValid($dimensions, 2)) {
			throw new InvalidArgumentException("Dimensions is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;
		$dimensions = [0, 0, $mmToUserUnit * $dimensions[0], $mmToUserUnit * $dimensions[1]];

		$mediabox = Factory::create('Papier\Type\RectangleType', $dimensions);

		$page->setMediaBox($mediabox);

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
	 * Get components.
	 *
	 * @return array<BaseComponent>
	 */
    private function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Build PDF's content.
     *
     * @return string
     */
    public function build(): string
    {
        // Build components
        $components = $this->getComponents();

        if (count($components)) {
            foreach ($components as $component) {
                $component->format();
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