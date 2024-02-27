<?php


namespace Papier\Filter\Params;

use Papier\Factory\Factory;
use Papier\Validator\IntegerValidator;
use InvalidArgumentException;

class LZWDecodeParams extends FlateDecodeParams
{
    /**
     * Set early change.
     *
     * @param int $earlyChange
     * @return LZWDecodeParams
     */
    public function setEarlyChange(int $earlyChange): LZWDecodeParams
    {
        if (!IntegerValidator::isValid($earlyChange)) {
            throw new InvalidArgumentException("EarlyChange is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $earlyChange);

        $this->setEntry('Columns', $value);
        return $this;
    }
}