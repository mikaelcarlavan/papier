<?php


namespace Papier\Validator;

use Papier\Validator\Base\Validator;
use Papier\Filter\Predictor;

class PredictorValidator implements Validator
{
    /**
     * Predictor allowed values.
     *
     * @var array
     */
    const PREDICTORS = array(
        Predictor::NONE,
        Predictor::TIFF_2,
        Predictor::PNG_NONE,
        Predictor::PNG_SUB,
        Predictor::PNG_UP,
        Predictor::PNG_AVERAGE,
        Predictor::PNG_PAETH,
        Predictor::PNG_OPTIMUM
    );


    /**
     * Test if given parameter is a valid predictor.
     *
     * @param  int  $value
     * @return bool
     */
    public static function isValid($value): bool
    {
        return IntegerValidator::isValid($value) && in_array($value, self::PREDICTORS);
    }
}