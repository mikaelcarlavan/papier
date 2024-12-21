<?php

namespace Papier\Component;

use Papier\Text\RenderingMode;
use Papier\Type\ContentStreamType;

trait Color
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
     * @var array<float>
     */
    protected array $nonStrokingColor = [];

    /**
     * The stroking colour
     *
     * @var array<float>
     */
    protected array $strokingColor = [];

    /**
     * Set stroking colour.
     *
     * @param mixed $colors
     * @return static
     */
    public function setStrokingColor(...$colors): static
    {
        $this->strokingColor = $colors;
        return $this;
    }

    /**
     * Get stroking colour.
     *
     * @return array<float>
     */
    public function getStrokingColor(): array
    {
        return $this->strokingColor;
    }

    /**
     * Set non-stroking colour.
     *
     * @param mixed $colors
     * @return static
     */
    public function setNonStrokingColor(...$colors): static
    {
        $this->nonStrokingColor = $colors;
        return $this;
    }

    /**
     * Get non-stroking colour.
     *
     * @return array<float>
     */
    public function getNonStrokingColor(): array
    {
        return $this->nonStrokingColor;
    }

    /**
     * Set stroking colour space.
     *
     * @param string $space
     * @return static
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
     * @return static
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

	/**
	 * Set colors to content stream.
	 *
	 * @param ContentStreamType $contents
	 * @return static
	 */
    public function applyColors(ContentStreamType &$contents): static
    {
        $strokingColors = $this->getStrokingColor();
        $nonStrokingColors = $this->getNonStrokingColor();

        if ($strokingColors) {
            $contents->setStrokingSpace($this->getStrokingColorSpace());
            $contents->setStrokingColor(...$strokingColors);
        }

        if ($nonStrokingColors) {
            $contents->setNonStrokingSpace($this->getNonStrokingColorSpace());
            $contents->setNonStrokingColor(...$nonStrokingColors);
        }

        return $this;
    }
}