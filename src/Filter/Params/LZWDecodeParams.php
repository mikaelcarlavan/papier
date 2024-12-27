<?php


namespace Papier\Filter\Params;

use InvalidArgumentException;
use Papier\Factory\Factory;
use Papier\Validator\IntegerValidator;

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

        $value = Factory::create('Papier\Type\Base\IntegerType', $earlyChange);

        $this->setEntry('Columns', $value);
        return $this;
    }
}