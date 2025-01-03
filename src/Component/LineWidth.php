<?php

namespace Papier\Component;

use Papier\Helpers\MetricHelper;
use Papier\Papier;
use Papier\Type\ContentStreamType;

trait LineWidth
{
    /**
     * Line width.
     *
     * @var float
     */
    protected float $lineWidth = 0;

    /**
     * Set line's width.
     *
     * @param  float  $lineWidth
     * @return static
     */
    public function setLineWidth(float $lineWidth): static
    {
        $this->lineWidth = $lineWidth;
        return $this;
    }

    /**
     * Get line's cap width.
     *
     * @return float
     */
    public function getLineWidth(): float
    {
        return $this->lineWidth;
    }

	/**
	 * Set line with to content stream.
	 *
	 * @param ContentStreamType $contents
	 * @return static
	 */
	public function applyWidth(ContentStreamType &$contents): static
	{
		$lineWidth = $this->getLineWidth();
		if ($lineWidth > 0) {
			$contents->setLineWidth(MetricHelper::toUserUnit($lineWidth));
		}

		return $this;
	}
}