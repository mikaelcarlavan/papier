<?php

namespace Papier\Component;

use Papier\Papier;
use Papier\Util\Matrix;

trait Transformation
{
    /**
     * Transformation matrix.
     *
     * @var Matrix|null
     */
    protected ?Matrix $transformationMatrix = null;

    /**
     * Get transformation matrix.
     *
     * @return Matrix
     */
	private function getTransformationMatrix(): Matrix
    {
        if (!$this->transformationMatrix) {
            $this->transformationMatrix = Matrix::eye(3);
        }

        return $this->transformationMatrix;
    }

    /**
     * Set transformation matrix.
     *
     * @param Matrix $transformationMatrix
     * @return BaseComponent
     */
    private function setTransformationMatrix(Matrix $transformationMatrix): BaseComponent
    {
        $this->transformationMatrix = $transformationMatrix;
        return $this;
    }

    /**
     * Add scaling
     *
     * @param float $x
     * @param float $y
     * @return BaseComponent
     */
    public function scale(float $x, float $y): BaseComponent
    {
        $matrix = new Matrix(3, 3);
        $matrix->setData(0, 0, $x);
        $matrix->setData(1, 1, $y);
        $matrix->setData(2, 2, 1.0);

        $transformation = $this->getTransformationMatrix();
        $transformation = $transformation->dot($matrix);
        $this->setTransformationMatrix($transformation);

        return $this;
    }

    /**
     * Add skew
     *
     * @param float $a
     * @param float $b
     * @return BaseComponent
     */
    public function skew(float $a, float $b): BaseComponent
    {
        $radA = $a * 2 * pi() / 360;
        $radB = $b * 2 * pi() / 360;

        $matrix = new Matrix(3, 3);
        $matrix->setData(0, 0, 1.0);
        $matrix->setData(0, 1, tan($radA));
        $matrix->setData(1, 0, tan($radB));
        $matrix->setData(1, 1, 1.0);
        $matrix->setData(2, 2, 1.0);

        $transformation = $this->getTransformationMatrix();
        $transformation = $transformation->dot($matrix);
        $this->setTransformationMatrix($transformation);

        return $this;
    }

    /**
     * Add translation
     *
     * @param float $x
     * @param float $y
     * @return BaseComponent
     */
    public function translate(float $x, float $y): BaseComponent
    {
        $mmToUserUnit = Papier::MM_TO_USER_UNIT;

        $matrix = new Matrix(3, 3);
        $matrix->setData(0, 0, 1.0);
        $matrix->setData(1, 1, 1.0);
        $matrix->setData(2, 0, $mmToUserUnit * $x);
        $matrix->setData(2, 1, $mmToUserUnit * $y);
        $matrix->setData(2, 2, 1.0);

        $transformation = $this->getTransformationMatrix();
        $transformation = $transformation->dot($matrix);
        $this->setTransformationMatrix($transformation);

        return $this;
    }

    /**
     * Add rotation (in deg)
     *
     * @param float $angle
     * @return BaseComponent
     */
    public function rotate(float $angle): BaseComponent
    {
        $radians = $angle * 2 * pi() / 360;

        $matrix = new Matrix(3, 3);
        $matrix->setData(0, 0, cos($radians));
        $matrix->setData(0, 1, sin($radians));
        $matrix->setData(1, 0, -sin($radians));
        $matrix->setData(1, 1, cos($radians));
        $matrix->setData(2, 2, 1.0);

        $transformation = $this->getTransformationMatrix();
        $transformation = $transformation->dot($matrix);
        $this->setTransformationMatrix($transformation);

        return $this;
    }
}