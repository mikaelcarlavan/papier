<?php

namespace Papier\Util;

use Papier\Validator\IntegerValidator;
use Papier\Validator\NumberValidator;

class Matrix
{
    /**
     * Matrix's rows
     *
     * @var int
     */
    protected int $rows = 0;

    /**
     * Matrix's columns
     *
     * @var int
     */
    protected int $columns = 0;

    /**
     * Data
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Create a new Matrix instance.
     *
     * @param int $rows
     * @param int $columns
     * @return void
     */
    public function __construct(int $rows, int $columns)
    {
        $this->init($rows, $columns);
    }

    /**
     * Get rows.
     *
     * @return int
     */
    public function getRows(): int
    {
        return $this->rows;
    }

    /**
     * Get columns.
     *
     * @return int
     */
    public function getColumns(): int
    {
        return $this->columns;
    }

    /**
     * Init data of matrix.
     *
     * @param int $rows
     * @param int $columns
     * @return void
     */
    protected function init(int $rows, int $columns): void
    {
        if (!IntegerValidator::isValid($rows, 0) || !IntegerValidator::isValid($columns, 0)) {
            throw new \InvalidArgumentException("Size is incorrect. See " . __CLASS__ . " class's documentation for possible values.");
        }

        $this->rows = $rows;
        $this->columns = $columns;

        $this->data = array_fill(0, $rows, array());
        for ($i = 0; $i < $rows; $i++) {
            $this->data[$i] = array_fill(0, $columns, 0);
        }
    }

    /**
     * Set data of matrix.
     *
     * @param int $row
     * @param int $column
     * @param float $value
     * @return void
     */
    public function setData(int $row, int $column, float $value): void
    {
        if (!IntegerValidator::isValid($row, 0, $this->getRows() - 1) || !IntegerValidator::isValid($column, 0, $this->getColumns() - 1)) {
            throw new \InvalidArgumentException("Coordinates are incorrect. See " . __CLASS__ . " class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($value)) {
            throw new \InvalidArgumentException("Value is incorrect. See " . __CLASS__ . " class's documentation for possible values.");
        }

        $this->data[$row][$column] = $value;
    }

    /**
     * Get data of matrix.
     *
     * @param int $row
     * @param int $column
     * @return float
     */
    public function getData(int $row, int $column): float
    {
        if (!IntegerValidator::isValid($row, 0, $this->getRows() - 1) || !IntegerValidator::isValid($column, 0, $this->getColumns() - 1)) {
            throw new \InvalidArgumentException("Coordinates are incorrect. See " . __CLASS__ . " class's documentation for possible values.");
        }

        return  $this->data[$row][$column];
    }

    /**
     * Get maximum value of matrix.
     *
     * @return float
     */
    protected function getMax(): float
    {
        $rows = $this->getRows();
        $columns = $this->getColumns();

        $max = null;
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $columns; $j++) {
                if (is_null($max)) {
                    $max = $this->getData($i, $j);
                } else {
                    $max = max($max, $this->getData($i, $j));
                }
            }
        }

        return $max;
    }

    /**
     * Dump matrix data
     *
     * @return void
     */
    public function dump(): void
    {
        $rows = $this->getRows();
        $columns = $this->getColumns();

        $digits = 1;
        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $columns; $j++) {
                $digits = max($digits, strlen((string)$this->getData($i, $j)));
            }
        }

		$digits = intval($digits);

        for ($i = 0; $i < $rows; $i++) {
            echo "|";
            for ($j = 0; $j < $columns; $j++) {
                echo " ".str_pad((string)$this->getData($i, $j), $digits, " ", STR_PAD_LEFT)." |";
            }
            echo "\r\n";
        }
    }

    /**
     * Dot matrix
     *
     * @param Matrix $t
     * @return Matrix
     */
    public function dot(Matrix $t): Matrix
    {
        $mRows = $this->getRows();
        $tColumns = $t->getColumns();

        if (($tColumns != $mRows)) {
            throw new \InvalidArgumentException("Sizes are incorrect. See " . __CLASS__ . " class's documentation for possible values.");
        }

        $tRows = $t->getRows();
        $mColumns = $this->getColumns();

        $rows = $tRows;
        $columns = $mColumns;

        $length = $tColumns;

        $m = new Matrix($rows, $columns);

        for ($i = 0; $i < $rows; $i++) {
            for ($j = 0; $j < $columns; $j++) {
                $dot = 0;
                for ($l = 0; $l < $length; $l++) {
                    $dot += ($t->getData($i, $l) * $this->getData($l, $j));
                }
                $m->setData($i, $j, $dot);
            }
        }

        return $m;
    }

    /**
     * Returns identity matrix
     *
     * @param int $size
     * @return Matrix
     */
    public static function eye(int $size): Matrix
    {
        $matrix = new Matrix($size, $size);

        for ($i = 0; $i < $size; $i++) {
            $matrix->setData($i, $i, 1.0);
        }

        return $matrix;
    }
}