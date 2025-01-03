<?php

namespace Papier;

use InvalidArgumentException;
use Papier\Component\Base\BaseComponent;
use Papier\Component\CircleComponent;
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
use Papier\Helpers\MetricHelper;
use Papier\Type\AnnotationDictionaryType;
use Papier\Type\CircleAnnotationDictionaryType;
use Papier\Type\DocumentInformationDictionaryType;
use Papier\Type\PageLabelsNumberTreeDictionaryType;
use Papier\Type\PageObjectDictionaryType;
use Papier\Type\SquareAnnotationDictionaryType;
use Papier\Type\TextAnnotationDictionaryType;
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
	 * Add new image component to page.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return ImageComponent
	 */
    public function addImageComponent(PageObjectDictionaryType $page = null): ImageComponent
    {
		/** @var ImageComponent $component */
		$component = $this->addComponent('Papier\Component\ImageComponent', $page);
		return $component;
	}

	/**
	 * Add new text component to page.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return TextComponent
	 */
    public function addTextComponent(PageObjectDictionaryType $page = null): TextComponent
    {
		/** @var TextComponent $component */
		$component = $this->addComponent('Papier\Component\TextComponent', $page);
		return $component;
	}

	/**
	 * Add new circle component to page.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return CircleComponent
	 */
	public function addCircleComponent(PageObjectDictionaryType $page = null): CircleComponent
	{
		/** @var CircleComponent $component */
		$component = $this->addComponent('Papier\Component\CircleComponent', $page);
		return $component;
	}

	/**
	 * Add new rectangle component to page.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return RectangleComponent
	 */
    public function addRectangleComponent(PageObjectDictionaryType $page = null): RectangleComponent
    {
		/** @var RectangleComponent $component */
		$component = $this->addComponent('Papier\Component\RectangleComponent', $page);
		return $component;
	}

	/**
	 * Add new Bezier component to page.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return DrawComponent
	 */
    public function addDrawComponent(PageObjectDictionaryType $page = null): DrawComponent
    {
		/** @var DrawComponent $component */
		$component = $this->addComponent('Papier\Component\DrawComponent', $page);
		return $component;
    }

	/**
	 * Add new segment component to page.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return SegmentComponent
	 */
	public function addSegmentComponent(PageObjectDictionaryType $page = null): SegmentComponent
	{
		/** @var SegmentComponent $component */
		$component = $this->addComponent('Papier\Component\SegmentComponent', $page);
		return $component;
	}

	/**
	 * Create a new component of type
	 *
	 * @template T
	 * @param class-string<T> $class
	 * @param PageObjectDictionaryType|null $page
	 * @return BaseComponent
	 */
	private function addComponent(string $class, PageObjectDictionaryType $page = null): BaseComponent
	{
		/** @var BaseComponent $component */
		$component = Factory::create($class);
		$this->components[] = $component;

		if (is_null($page)) {
			$page = $this->getCurrentPage();
		}

		if (!$page instanceof PageObjectDictionaryType) {
			throw new InvalidArgumentException("Page is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$component->setPage($page);

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
     * @return PageObjectDictionaryType
     */
    public function getCurrentPage(): PageObjectDictionaryType
    {
        /** @var PageObjectDictionaryType $page */
        $page = $this->getBody()->getPageTree()->getKids()->current();
        return $page;
    }

	/**
	 * Get pages labels.
	 *
	 * @return PageLabelsNumberTreeDictionaryType
	 */
	public function getPageLabels(): PageLabelsNumberTreeDictionaryType
	{
		/** @var PageLabelsNumberTreeDictionaryType $page */
		$pageLabels = $this->getBody()->getDocumentCatalog()->getPageLabels();
		return $pageLabels;
	}

	/**
	 * Add page to PDF's content.
	 *
	 * @param array<float> $dimensions Dimensions (width and height) of the page in millimeters.
	 * @return PageObjectDictionaryType
	 */
    public function addPage(array $dimensions): PageObjectDictionaryType
    {
        $page = $this->getBody()->addPage();

		if (!NumbersArrayValidator::isValid($dimensions, 2)) {
			throw new InvalidArgumentException("Dimensions is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$dimensions = [0, 0, MetricHelper::toUserUnit($dimensions[0]), MetricHelper::toUserUnit($dimensions[1])];

		$mediabox = Factory::create('Papier\Type\RectangleNumbersArrayType', $dimensions);

		$page->setMediaBox($mediabox);

        return $page;
    }

	/**
	 * Add text annotation to PDF's content.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return TextAnnotationDictionaryType
	 */
	public function addTextAnnotation(PageObjectDictionaryType $page = null): TextAnnotationDictionaryType
	{
		/** @var TextAnnotationDictionaryType $annot */
		$annot = $this->addAnnotation('Papier\Type\TextAnnotationDictionaryType', $page);
		return $annot;
	}


	/**
	 * Add square annotation to PDF's content.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return SquareAnnotationDictionaryType
	 */
	public function addSquareAnnotation(PageObjectDictionaryType $page = null): SquareAnnotationDictionaryType
	{
		/** @var SquareAnnotationDictionaryType $annot */
		$annot = $this->addAnnotation('Papier\Type\SquareAnnotationDictionaryType', $page);
		return $annot;
	}


	/**
	 * Add circle annotation to PDF's content.
	 *
	 * @param PageObjectDictionaryType|null $page
	 * @return CircleAnnotationDictionaryType
	 */
	public function addCircleAnnotation(PageObjectDictionaryType $page = null): CircleAnnotationDictionaryType
	{
		/** @var CircleAnnotationDictionaryType $annot */
		$annot = $this->addAnnotation('Papier\Type\CircleAnnotationDictionaryType', $page);
		return $annot;
	}

	/**
	 * Create a new annotation of type
	 *
	 * @template T
	 * @param class-string<T> $class
	 * @param PageObjectDictionaryType|null $page
	 * @return AnnotationDictionaryType
	 */
	private function addAnnotation(string $class, PageObjectDictionaryType $page = null): AnnotationDictionaryType
	{
		if (is_null($page)) {
			$page = $this->getCurrentPage();
		}

		if (!$page instanceof PageObjectDictionaryType) {
			throw new InvalidArgumentException("Page is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$annots = $page->getAnnots();

		/** @var AnnotationDictionaryType $annot */
		$annot = Factory::create($class, null, true);
		$annots->push($annot);

		return $annot;
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