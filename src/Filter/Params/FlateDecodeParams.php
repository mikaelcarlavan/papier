<?php


namespace Papier\Filter\Params;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Type\Base\DictionaryType;
use Papier\Validator\BitsPerComponentValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\PredictorValidator;

class FlateDecodeParams extends DictionaryType
{
    /**
     * Set predictor.
     *
     * @param int $predictor
     * @return FlateDecodeParams
     */
    public function setPredictor(int $predictor): FlateDecodeParams
    {
        if (!PredictorValidator::isValid($predictor)) {
            throw new InvalidArgumentException("Predictor is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $predictor);

        $this->setEntry('Predictor', $value);
        return $this;
    }

    /**
     * Set the number of bits used to represent each colour component.
     *
     * @param int $bits
     * @return FlateDecodeParams
     */
    public function setBitsPerComponent(int $bits): FlateDecodeParams
    {
        if (!BitsPerComponentValidator::isValid($bits)) {
            throw new InvalidArgumentException("BitsPerComponent is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $bits);

        $this->setEntry('BitsPerComponent', $value);
        return $this;
    }

    /**
     * Set number of interleaved colour components per sample.
     *
     * @param int $colors
     * @return FlateDecodeParams
     */
    public function setColors(int $colors): FlateDecodeParams
    {
        if (!IntegerValidator::isValid($colors, 1)) {
            throw new InvalidArgumentException("Colors is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $colors);

        $this->setEntry('Colors', $value);
        return $this;
    }

    /**
     * Set number columns.
     *
     * @param int $columns
     * @return FlateDecodeParams
     */
    public function setColumns(int $columns): FlateDecodeParams
    {
        if (!IntegerValidator::isValid($columns)) {
            throw new InvalidArgumentException("Columns is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\Base\IntegerType', $columns);

        $this->setEntry('Columns', $value);
        return $this;
    }
}