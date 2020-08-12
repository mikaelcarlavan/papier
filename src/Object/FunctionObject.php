<?php

namespace Papier\Object;

use Papier\Object\StreamObject;
use Papier\Object\ArrayObject;
use Papier\Object\IntegerObject;

use Papier\Validator\FunctionTypeValidator;

use Papier\Functions\FunctionType;


use InvalidArgumentException;
use RuntimeException;

class FunctionObject extends StreamObject
{
 
    /**
     * Set type.
     *  
     * @param  int  $type
     * @throws InvalidArgumentException if the provided argument is not a valid function type.
     * @return \Papier\Object\FunctionObject
     */
    public function setFunctionType($type)
    {
        if (!FunctionTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("FunctionType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = new IntegerObject();
        $value->setValue($type);

        $this->setEntry('FunctionType', $value);
        return $this;
    } 

    /**
     * Set domain.
     *  
     * @param  int  $domain
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Object\FunctionObject
     */
    public function setDomain($domain)
    {
        if (!$domain instanceof ArrayObject) {
            throw new InvalidArgumentException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (count($domain) % 2 != 0) {
            throw new InvalidArgumentException("Domain should be even length. See ".__CLASS__." class's documentation for possible values.");
        }

        $m = intval(count($domain)/2);

        for ($i = 0; $i < $m; $i++) {
            if ($domain->getEntry(2*$i) > $domain->getEntry(1+2*$i)) {
                throw new InvalidArgumentException("Domain values should be increasing. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        for ($i = 0; $i < $m; $i++) {
            if ($domain->getEntry($i) < $domain->getEntry(2*$i)  || $domain->getEntry($i) > $domain->getEntry(1+2*$i)) {
                throw new InvalidArgumentException("Domain values are out of boundaries. See ".__CLASS__." class's documentation for possible values.");
            }
        }  


        $this->setEntry('Domain', $domain);
        return $this;
    } 

    /**
     * Set range.
     *  
     * @param  int  $range
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Object\FunctionObject
     */
    public function setRange($range)
    {
        if (!$range instanceof ArrayObject) {
            throw new InvalidArgumentException("Range is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (count($range) % 2 != 0) {
            throw new InvalidArgumentException("Range should be even length. See ".__CLASS__." class's documentation for possible values.");
        }

        $n = intval(count($range)/2);

        for ($i = 0; $i < $n; $i++) {
            if ($range->getEntry(2*$i) > $range->getEntry(1+2*$i)) {
                throw new InvalidArgumentException("Range values should be increasing. See ".__CLASS__." class's documentation for possible values.");
            }
        }

        for ($i = 0; $i < $n; $i++) {
            if ($range->getEntry($i) < $range->getEntry(2*$i) || $range->getEntry($i) > $range->getEntry(1+2*$i)) {
                throw new InvalidArgumentException("RAnge values are out of boundaries. See ".__CLASS__." class's documentation for possible values.");
            }
        } 

        $this->setEntry('Range', $range);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        if (!$this->hasEntry('FunctionType')) {
            throw new RuntimeException("FunctionType is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Domain')) {
            throw new RuntimeException("Domain is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        $type = $this->getEntry('FunctionType')->getValue();

        if (($type == FunctionType::SAMPLED || $type == FunctionType::POSTSCRIPT_CALCULATOR) && !$this->hasEntry('Range')) {
            throw new RuntimeException("Range is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        return parent::format();
    }
}