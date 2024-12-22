<?php

namespace Papier\Component;

use Papier\Papier;

class DrawComponent extends BaseComponent
{
    use Color;
    use LineWidth;

    /**
     * The path
     *
     * @var array<array{x: float, y:float, ctrl?: array{initial?: array{x:float, y:float}, final?: array{x:float, y:float}}}>
     */
    protected array $path = [];

    /**
     * Get path.
     *
     * @return array<array{x: float, y:float, ctrl?: array{initial?: array{x:float, y:float}, final?: array{x:float, y:float}}}>
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
        $this->path[] = [
            'x' => $x1,
            'y' => $y1
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
        $this->path[] = [
            'x' => $x3,
            'y' => $y3,
            'ctrl' => [
                'final' => [
                    'x' => $x1,
                    'y' => $y1
                ]
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
        $this->path[] = [
            'x' => $x3,
            'y' => $y3,
            'ctrl' => [
                'initial' => [
                    'x' => $x2,
                    'y' => $y2
                ]
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
        $this->path[] = [
            'x' => $x3,
            'y' => $y3,
            'ctrl' => [
                'initial' => [
                    'x' => $x1,
                    'y' => $y1
                ],
                'final' => [
                    'x' => $x2,
                    'y' => $y2
                ]
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
			$contents->beginPath($mmToUserUnit * $point['x'], $mmToUserUnit * $point['y']);

            foreach ($points as $point) {
				if (isset($point['ctrl'])) {
					$ctrl = (array)$point['ctrl'];
                    if (isset($ctrl['initial']) && isset($ctrl['final'])) {
                        $initialPoint = $ctrl['initial'];
                        $finalPoint = $ctrl['final'];
                        $contents->appendCubicBezier($mmToUserUnit * $point['x'], $mmToUserUnit * $point['y'], $mmToUserUnit * $initialPoint['x'], $mmToUserUnit * $initialPoint['y'], $mmToUserUnit * $finalPoint['x'], $mmToUserUnit * $finalPoint['y']);
                    } else if (isset($ctrl['initial'])) {
                        $initialPoint = $ctrl['initial'];

                        $contents->appendCubicBezier2a($mmToUserUnit * $point['x'], $mmToUserUnit * $point['y'], $mmToUserUnit * $initialPoint['x'], $mmToUserUnit * $initialPoint['y']);
                    } else if (isset($ctrl['final'])) {
                        $finalPoint = $ctrl['final'];

                        $contents->appendCubicBezier2b($mmToUserUnit * $point['x'], $mmToUserUnit * $point['y'], $mmToUserUnit * $finalPoint['x'], $mmToUserUnit * $finalPoint['y']);
                    }
                } else {
                    $contents->appendSegment($mmToUserUnit * $point['x'], $mmToUserUnit * $point['y']);
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