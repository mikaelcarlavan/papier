<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Helpers\MetricHelper;
use Papier\Papier;
use Papier\Text\RenderingMode;
use Papier\Util\Point;
use Papier\Validator\NumberValidator;
use InvalidArgumentException;

class CircleComponent extends BaseComponent
{
	use Color;
	use LineWidth;

	/**
	 * The radius of the component
	 *
	 * @var float
	 */
	protected float $radius = 0;

	/**
	 * The center point of the circle
	 *
	 * @var Point
	 */
	protected Point $centerPoint;

	/**
	 * Rendering mode.
	 *
	 * @var int
	 */
	protected int $renderingMode = RenderingMode::FILL;

	/**
	 * Set rendering mode.
	 *
	 * @param int $renderingMode
	 * @return CircleComponent
	 */
	public function setRenderingMode(int $renderingMode): CircleComponent
	{
		$this->renderingMode = $renderingMode;
		return $this;
	}

	/**
	 * Get rendering mode.
	 *
	 * @return int
	 */
	public function getRenderingMode(): int
	{
		return $this->renderingMode;
	}

	/**
	 * Set center point of the circle
	 *
	 * @param  float  $x
	 * @param  float  $y
	 * @return static
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setCenterPoint(float $x, float $y): static
	{
		if (!NumberValidator::isValid($x)) {
			throw new InvalidArgumentException("X is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		if (!NumberValidator::isValid($y)) {
			throw new InvalidArgumentException("Y is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$point = new Point();
		$point->setXY($x, $y);

		$this->centerPoint = $point;
		return $this;
	}

	/**
	 * Set component's radius.
	 *
	 * @param  float  $radius
	 * @return CircleComponent
	 * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int' and positive.
	 */
	public function setRadius(float $radius): CircleComponent
	{
		if (!NumberValidator::isValid($radius, 0.0)) {
			throw new InvalidArgumentException("Radius is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

		$this->radius = $radius;
		return $this;
	}

	/**
	 * Get center point.
	 *
	 * @return Point
	 */
	public function getCenterPoint(): Point
	{
		return $this->centerPoint;
	}


	/**
	 * Get component's radius.
	 *
	 * @return float
	 */
	public function getRadius(): float
	{
		return $this->radius;
	}

	/**
	 * Format component's content.
	 *
	 * @return CircleComponent
	 */
	function format(): CircleComponent
	{
		$contents = $this->getContents();
		$contents->save();

		$renderingMode = $this->getRenderingMode();

		$contents->setTextRenderingMode($renderingMode);

		$this->applyColors($contents);
		$this->applyWidth($contents);

		$radius = $this->getRadius();
		$centerPoint = $this->getCenterPoint();

		$controlDistance = 4 * (sqrt(2) - 1) / 3;

		$mmToUserUnit = Papier::MM_TO_USER_UNIT;
		$contents->beginPath(MetricHelper::toUserUnit($centerPoint->getX() + $radius), MetricHelper::toUserUnit($centerPoint->getY()));

		// Approximate the circle using 4 Bézier curves
		// First quarter
		$contents->appendCubicBezier(MetricHelper::toUserUnit($centerPoint->getX() + $radius), MetricHelper::toUserUnit($centerPoint->getY() + $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getX() + $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getY() + $radius), MetricHelper::toUserUnit($centerPoint->getX()), MetricHelper::toUserUnit($centerPoint->getY() + $radius));
		// Second quarter
		$contents->appendCubicBezier(MetricHelper::toUserUnit($centerPoint->getX() - $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getY() + $radius), MetricHelper::toUserUnit($centerPoint->getX() - $radius), MetricHelper::toUserUnit($centerPoint->getY() + $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getX() - $radius), MetricHelper::toUserUnit($centerPoint->getY()));
		// Third quarter
		$contents->appendCubicBezier(MetricHelper::toUserUnit($centerPoint->getX() - $radius), MetricHelper::toUserUnit($centerPoint->getY() - $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getX() - $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getY() - $radius), MetricHelper::toUserUnit($centerPoint->getX()), MetricHelper::toUserUnit($centerPoint->getY() - $radius));
		// Fourth quarter
		$contents->appendCubicBezier(MetricHelper::toUserUnit($centerPoint->getX() + $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getY() - $radius), MetricHelper::toUserUnit($centerPoint->getX() + $radius), MetricHelper::toUserUnit($centerPoint->getY() - $radius * $controlDistance), MetricHelper::toUserUnit($centerPoint->getX() + $radius), MetricHelper::toUserUnit($centerPoint->getY()));


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