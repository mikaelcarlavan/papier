<?php

namespace Papier\Widget;

trait ColorWidget
{
    /**
     * The non-stroking colour space
     *
     * @var string
     */
    protected string $nonStrokingColorSpace;

    /**
     * The stroking colour space
     *
     * @var string
     */
    protected string $strokingColorSpace;

    /**
     * The non-stroking colour
     *
     * @var array
     */
    protected array $nonStrokingColor = [];

    /**
     * The stroking colour
     *
     * @var array
     */
    protected array $strokingColor = [];

    /**
     * Set stroking colour.
     *
     * @param mixed $colors
     * @return ColorWidget
     */
    public function setStrokingColor(...$colors): static
    {
        $this->strokingColor = $colors;
        return $this;
    }

    /**
     * Get stroking colour.
     *
     * @return array
     */
    public function getStrokingColor(): array
    {
        return $this->strokingColor;
    }

    /**
     * Set non-stroking colour.
     *
     * @param mixed $colors
     * @return ColorWidget
     */
    public function setNonStrokingColor(...$colors): static
    {
        $this->nonStrokingColor = $colors;
        return $this;
    }

    /**
     * Get non-stroking colour.
     *
     * @return array
     */
    public function getNonStrokingColor(): array
    {
        return $this->nonStrokingColor;
    }

    /**
     * Set stroking colour space.
     *
     * @param string $space
     * @return ColorWidget
     */
    public function setStrokingColorSpace(string $space): static
    {
        $this->strokingColorSpace = $space;
        return $this;
    }

    /**
     * Get non-stroking colour space.
     *
     * @return string
     */
    public function getStrokingColorSpace(): string
    {
        return $this->strokingColorSpace;
    }

    /**
     * Set non-stroking colour space.
     *
     * @param string $space
     * @return ColorWidget
     */
    public function setNonStrokingColorSpace(string $space): static
    {
        $this->nonStrokingColorSpace = $space;
        return $this;
    }

    /**
     * Get non-stroking colour space.
     *
     * @return string
     */
    public function getNonStrokingColorSpace(): string
    {
        return $this->nonStrokingColorSpace;
    }
}