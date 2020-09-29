<?php

namespace Papier\Text;

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
        
        $state = sprintf('%s %s Td', Factory::create('Number', $tx)->format(), Factory::create('Number', $ty)->format());
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

        $state = sprintf('%s %s TD', Factory::create('Number', $tx)->format(), Factory::create('Number', $ty)->format());
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

        $state = sprintf('%s %s %s %s %s %s Tm', 
            Factory::create('Number', $a)->format(), 
            Factory::create('Number', $b)->format(),
            Factory::create('Number', $c)->format(),
            Factory::create('Number', $d)->format(),
            Factory::create('Number', $e)->format(),
            Factory::create('Number', $f)->format()
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
}