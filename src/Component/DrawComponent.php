<?php

namespace Papier\Component;

use Papier\Component\Base\BaseComponent;
use Papier\Papier;
use Papier\Util\Point;

class DrawComponent extends BaseComponent
{
    use Color;
    use LineWidth;

    /**
     * The path
     *
     * @var array<array{point: Point, ctrl?: array{initial?: Point, final?: Point}}>
     */
    protected array $path = [];

    /**
     * Get path.
     *
     * @return array<array{point: Point, ctrl?: array{initial?: Point, final?: Point}}>
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * Add point to path.
     *
     * @param float $x1
     * @param float $y1
     * @return DrawComponent
     */
    public function addPoint(float $x1, float $y1): DrawComponent
    {
		$p = new Point();
		$p->setXY($x1, $y1);

        $this->path[] = [
			'point' => $p
		];


        return $this;
    }

    /**
     * Add point to path using destination point as control point
     *
     * @param float $x1
     * @param float $y1
     * @param float $x3
     * @param float $y3
     * @return DrawComponent
     */
    public function addPointWithFinalPointAsControlPoint(float $x1, float $y1, float $x3, float $y3): DrawComponent
    {
		$point = new Point();
		$point->setXY($x3, $y3);

		$final = new Point();
		$final->setXY($x1, $y1);

        $this->path[] = [
            'point' => $point,
            'ctrl' => [
                'final' => $final
            ]
        ];

        return $this;
    }

    /**
     * Add point to path using current point as control point
     *
     * @param float $x2
     * @param float $y2
     * @param float $x3
     * @param float $y3
     * @return DrawComponent
     */
    public function addPointWithInitialPointAsControlPoint(float $x2, float $y2, float $x3, float $y3): DrawComponent
    {
		$point = new Point();
		$point->setXY($x3, $y3);

		$initial = new Point();
		$initial->setXY($x2, $y2);

        $this->path[] = [
            'point' => $point,
            'ctrl' => [
                'initial' => $initial
            ]
        ];

        return $this;
    }

    /**
     * Add point to path.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param float $x3
     * @param float $y3
     * @return DrawComponent
     */
    public function addPointWithControlPoints(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): DrawComponent
    {
		$point = new Point();
		$point->setXY($x3, $y3);

		$initial = new Point();
		$initial->setXY($x1, $y1);

		$final = new Point();
		$final->setXY($x2, $y2);

		$this->path[] = [
			'point' => $point,
			'ctrl' => [
				'initial' => $initial,
				'final' => $final
			]
		];

        return $this;
    }

    function format(): DrawComponent
    {
        $contents = $this->getContents();
        $contents->save();

        $this->applyColors($contents);

        $points = $this->getPath();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        if (count($points)) {
			$point = array_shift($points);
			/** @var Point $p */
			$p = $point['point'];
			$contents->beginPath($mmToUserUnit * $p->getX(), $mmToUserUnit * $p->getY());

            foreach ($points as $point) {
				if (isset($point['ctrl'])) {
					$ctrl = (array)$point['ctrl'];
                    if (isset($ctrl['initial']) && isset($ctrl['final'])) {
						/** @var Point $initialPoint */
						/** @var Point $finalPoint */
                        $initialPoint = $ctrl['initial'];
                        $finalPoint = $ctrl['final'];
                        $contents->appendCubicBezier($mmToUserUnit * $p->getX(), $mmToUserUnit * $p->getY(), $mmToUserUnit * $initialPoint->getX(), $mmToUserUnit * $initialPoint->getY(), $mmToUserUnit * $finalPoint->getX(), $mmToUserUnit * $finalPoint->getY());
                    } else if (isset($ctrl['initial'])) {
						/** @var Point $initialPoint */
						$initialPoint = $ctrl['initial'];
                        $contents->appendCubicBezier2a($mmToUserUnit * $p->getX(), $mmToUserUnit * $p->getY(), $mmToUserUnit * $initialPoint->getX(), $mmToUserUnit * $initialPoint->getY());
                    } else if (isset($ctrl['final'])) {
						/** @var Point $finalPoint */
						$finalPoint = $ctrl['final'];
                        $contents->appendCubicBezier2b($mmToUserUnit * $p->getX(), $mmToUserUnit * $p->getY(), $mmToUserUnit * $finalPoint->getX(), $mmToUserUnit * $finalPoint->getY());
                    }
                } else {
                    $contents->appendSegment($mmToUserUnit * $p->getX(), $mmToUserUnit * $p->getY());
                }
            }
        }


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