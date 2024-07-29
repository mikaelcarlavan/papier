<?php

namespace Papier\Widget;


use Papier\Object\BaseObject;
use Papier\Type\ContentStreamType;
use Papier\Type\PageObjectType;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;
use Papier\Papier;

abstract class BaseWidget
{
    /**
     * The parent of the widget
     *
     * @var PageObjectType
     */
    protected PageObjectType $page;

    /**
     * The horizontal position of the widget
     *
     * @var float
     */
    protected float $x = 0;

    /**
     * The vertical position of the widget
     *
     * @var float
     */
    protected float $y = 0;

    /**
     * Set widget's horizontal position.
     *
     * @param  float  $x
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setX(float $x): BaseWidget
    {
        if (!NumberValidator::isValid($x, 0.0)) {
            throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->x = $x;
        return $this;
    }

    /**
     * Set widget's vertical position.
     *
     * @param  float  $y
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
     */
    public function setY(float $y): BaseWidget
    {
        if (!NumberValidator::isValid($y, 0.0)) {
            throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->y = $y;
        return $this;
    }


    /**
     * Set widget's position.
     *
     * @param  float  $x
     * @param  float  $y
     * @return BaseWidget
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int' and positive.
     */
    public function setXY(float $x, float $y): BaseWidget
    {
        return $this->setX($x)->setY($y);
    }

    /**
     * Get widget's horizontal position.
     *
     * @return float
     */
    public function getX(): float
    {
        return $this->x;
    }

    /**
     * Get widget's vertical position.
     *
     * @return float
     */
    public function getY(): float
    {
        return $this->y;
    }

    /**
     * Set widget's page.
     *
     * @param  PageObjectType  $page
     * @return BaseWidget
     */
    public function setPage(PageObjectType $page): BaseWidget
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get widget's page.
     *
     * @return PageObjectType
     */
    public function getPage(): PageObjectType
    {
        return $this->page;
    }

    /**
     * Get contents.
     *
     * @return ContentStreamType
     */
    public function getContents(): ContentStreamType
    {
        $page = $this->getPage();
        return $page->getContents();
    }

    /**
     * Format widget's content.
     *
     * @return BaseWidget
     */
    abstract function format(): BaseWidget;
}