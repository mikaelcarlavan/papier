<?php

namespace Papier\Widget;

use Papier\Papier;

class DrawWidget extends BaseWidget
{
    use Color;
    use LineWidth;

    /**
     * The path
     *
     * @var array
     */
    protected array $path = [];

    /**
     * Get path.
     *
     * @return array
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
     * @return DrawWidget
     */
    public function addPoint(float $x1, float $y1): DrawWidget
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
     * @return DrawWidget
     */
    public function addPointWithFinalPointAsControlPoint(float $x1, float $y1, float $x3, float $y3): DrawWidget
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
     * @return DrawWidget
     */
    public function addPointWithInitialPointAsControlPoint(float $x2, float $y2, float $x3, float $y3): DrawWidget
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
     * @return DrawWidget
     */
    public function addPointWithControlPoints(float $x1, float $y1, float $x2, float $y2, float $x3, float $y3): DrawWidget
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

    function format(): DrawWidget
    {
        $contents = $this->getContents();
        $contents->save();

        $this->applyColors($contents);

        $points = $this->getPath();

        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        if (count($points)) {
            $point = array_shift($points);
            if (is_array($point)) {
                $contents->beginPath($mmToUserUnit * $point['x'], $mmToUserUnit * $point['y']);
            }

            foreach ($points as $point) {
                if (isset($point['ctrl'])) {
                    $ctrl = $point['ctrl'];
                    if (isset($ctrl['initial']) && $ctrl['final']) {
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