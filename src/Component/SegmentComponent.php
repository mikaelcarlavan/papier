<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Helpers\MetricHelper;
use Papier\Papier;
use Papier\Util\Point;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

class SegmentComponent extends BaseComponent
{
	use Color;
	use LineWidth;

	/**
	 * The start point of the line
	 *
	 * @var Point
	 */
	protected Point $startPoint;

	/**
	 * The end point of the line
	 *
	 * @var Point
	 */
	protected Point $endPoint;


	/**
	 * Set end point of the component
	 *
	 * @param  float  $x
	 * @param  float  $y
	 * @return static
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setEndPoint(float $x, float $y): static
	{
		if (!NumberValidator::isValid($x)) {
			throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!NumberValidator::isValid($y)) {
			throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$point = new Point();
		$point->setXY($x, $y);

		$this->endPoint = $point;
		return $this;
	}
	/**
	 * Set end point of the component
	 *
	 * @param  float  $x
	 * @param  float  $y
	 * @return static
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setStartPoint(float $x, float $y): static
	{
		if (!NumberValidator::isValid($x)) {
			throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!NumberValidator::isValid($y)) {
			throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$point = new Point();
		$point->setXY($x, $y);

		$this->startPoint = $point;
		return $this;
	}

	/**
	 * Get start point.
	 *
	 * @return Point
	 */
	public function getStartPoint(): Point
	{
		return $this->startPoint;
	}

	/**
	 * Get start point.
	 *
	 * @return Point
	 */
	public function getEndPoint(): Point
	{
		return $this->endPoint;
	}

	/**
	 * Format component's content.
	 *
	 * @return SegmentComponent
	 */
	function format(): SegmentComponent
	{
		$contents = $this->getContents();
		$contents->save();

		$this->applyColors($contents);
		$this->applyWidth($contents);

		$start = $this->getStartPoint();
		$end = $this->getEndPoint();

		$contents->beginPath(MetricHelper::toUserUnit($start->getX()), MetricHelper::toUserUnit($start->getY()));
		$contents->appendSegment(MetricHelper::toUserUnit($end->getX()), MetricHelper::toUserUnit($end->getY()));

		$strokingColors = $this->getStrokingColor();
		$nonStrokingColors = $this->getNonStrokingColor();

		if ($strokingColors && $nonStrokingColors) {
			$contents->fillAndStroke();
		} else if ($strokingColors) {
			$contents->stroke();
		} else if ($nonStrokingColors) {
			$contents->fill();
		} else {
			$contents->closePath();
		}

		$contents->restore();

		return $this;
	}
}