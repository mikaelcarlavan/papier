<?php

namespace Papier\Text;

use Papier\Validator\NumbersArrayValidator;
use Papier\Validator\NumberValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait TextPositioning
{
    /**
     * Move to the start of the next line, offset from the start of the current line.
     *  
     * @param   mixed   $tx
     * @param   mixed   $ty
     * @return mixed
     * @throws InvalidArgumentException if one of the provided arguments are not of type 'float' or 'int'.
     */
    public function moveToNextLineStartWithOffset($tx, $ty)
    {
        if (!NumberValidator::isValid($tx)) {
            throw new InvalidArgumentException("TX is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        if (!NumberValidator::isValid($ty)) {
            throw new InvalidArgumentException("TY is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
        
        $state = sprintf('%s %s Td', Factory::create('Papier\Type\NumberType', $tx)->format(), Factory::create('Papier\Type\NumberType', $ty)->format());
        return $this->addToContent($state);
    }

    /**
     * Move to the start of the next line, offset from the start of the current line and set the leading.
     *  
     * @param   mixed   $tx
     * @param   mixed   $ty
     * @return mixed
     * @throws InvalidArgumentException if one of the provided arguments are not of type 'float' or 'int'.
     */
    public function moveToNextLineStartWithOffsetAndSetLeading($tx, $ty)
    {
        if (!NumberValidator::isValid($tx)) {
            throw new InvalidArgumentException("TX is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        if (!NumberValidator::isValid($ty)) {
            throw new InvalidArgumentException("TY is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%s %s TD', Factory::create('Papier\Type\NumberType', $tx)->format(), Factory::create('Papier\Type\NumberType', $ty)->format());
        return $this->addToContent($state);
    }

    /**
     * Set the text matrix.
     *  
     * @param   mixed   $a
     * @param   mixed   $b
     * @param   mixed   $c
     * @param   mixed   $d
     * @param   mixed   $e
     * @param   mixed   $f
     * @return mixed
     * @throws InvalidArgumentException if one of the provided arguments are not of type 'float' or 'int'.
     */
    public function setTextMatrix($a, $b, $c, $d, $e, $f)
    {
        $components = [
            'A' => $a,
            'B' => $b,
            'C' => $c,
            'D' => $d,
            'E' => $e,
            'F' => $f,
        ];

        $this->checkMatrixComponents($components);

        $state = sprintf('%s %s %s %s %s %s Tm', 
            Factory::create('Papier\Type\NumberType', $a)->format(),
            Factory::create('Papier\Type\NumberType', $b)->format(),
            Factory::create('Papier\Type\NumberType', $c)->format(),
            Factory::create('Papier\Type\NumberType', $d)->format(),
            Factory::create('Papier\Type\NumberType', $e)->format(),
            Factory::create('Papier\Type\NumberType', $f)->format()
        );
        
        return $this->addToContent($state);
    }

    /**
     * Move to the start of the next line.
     *  
     * @return mixed
     */
    public function moveToNextLineStart()
    {
        $state = 'T*';
        return $this->addToContent($state);
    }

    /**
     * Check matrix components.
     *
     * @param array<float> $components
     * @return bool
     * @throws InvalidArgumentException if one of the provided argument is not 'float' or 'int'.
     */
    private function checkMatrixComponents(array $components): bool
    {
		if (!NumbersArrayValidator::isValid($components)) {
			throw new InvalidArgumentException("Components is incorrect. See ".__CLASS__." class's documentation for possible values.");
		}

        return true;
    }
}