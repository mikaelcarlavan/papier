<?php

namespace Papier\Text;

use Papier\Validator\NumberValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\IntegerValidator;
use Papier\Validator\RenderingModeValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

trait TextPositioning
{
    /**
     * Move to the start of the next line, offset from the start of the current line.
     *  
     * @param   mixed   $tx
     * @param   mixed   $ty
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return mixed
     */
    public function moveToNextLineStartWithOffset($tx, $ty)
    {
        if (!NumberValidator::isValid($tx)) {
            throw new InvalidArgumentException("TX is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        if (!NumberValidator::isValid($ty)) {
            throw new InvalidArgumentException("TY is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%f %f Td', $tx, $ty);
        return $this->addToContent($state);
    }

    /**
     * Move to the start of the next line, offset from the start of the current line and set the leading.
     *  
     * @param   mixed   $tx
     * @param   mixed   $ty
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return mixed
     */
    public function moveToNextLineStartWithOffsetAndSetLeading($tx, $ty)
    {
        if (!NumberValidator::isValid($tx)) {
            throw new InvalidArgumentException("TX is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        if (!NumberValidator::isValid($ty)) {
            throw new InvalidArgumentException("TY is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%f %f TD', $tx, $ty);
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
     * @throws InvalidArgumentException if the provided arguments are not of type 'float' or 'int'.
     * @return mixed
     */
    public function setTextMatrix($a, $b, $c, $d, $e, $f)
    {
        if (!NumberValidator::isValid($a)) {
            throw new InvalidArgumentException("A is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        if (!NumberValidator::isValid($b)) {
            throw new InvalidArgumentException("B is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($c)) {
            throw new InvalidArgumentException("C is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($d)) {
            throw new InvalidArgumentException("D is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($e)) {
            throw new InvalidArgumentException("E is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!NumberValidator::isValid($f)) {
            throw new InvalidArgumentException("F is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $state = sprintf('%f %f %f %f %f %f Tm', $a, $b, $c, $d, $e, $f);
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
}