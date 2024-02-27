<?php

namespace Papier\Object;

use Papier\Validator\FunctionTypeValidator;

use Papier\Functions\FunctionType;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class FunctionObject extends StreamObject
{
 
    /**
     * Set type.
     *  
     * @param  int  $type
     * @return FunctionObject
     * @throws InvalidArgumentException if the provided argument is not a valid function type.
     */
    public function setFunctionType(int $type): FunctionObject
    {
        if (!FunctionTypeValidator::isValid($type)) {
            throw new InvalidArgumentException("FunctionType is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $type);

        $this->setEntry('FunctionType', $value);
        return $this;
    } 

    /**
     * Set domain.
     *  
     * @param  ArrayObject  $domain
     * @return FunctionObject
     * @throws InvalidArgumentException if the provided argument is not an even length 'ArrayObject'.
     * @throws InvalidArgumentException if values of provided argument are not increasing.
     * @throws InvalidArgumentException if values of provided argument are out of boundaries.
     */
    public function setDomain(ArrayObject $domain): FunctionObject
    {
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
     * @param  ArrayObject  $range
     * @return FunctionObject
     * @throws InvalidArgumentException if the provided argument is not an even length 'ArrayObject'.
     * @throws InvalidArgumentException if values of provided argument are not increasing.
     * @throws InvalidArgumentException if values of provided argument are out of boundaries.
     */
    public function setRange(ArrayObject $range): FunctionObject
    {
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
                throw new InvalidArgumentException("Range values are out of boundaries. See ".__CLASS__." class's documentation for possible values.");
            }
        } 

        $this->setEntry('Range', $range);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     * @throws RuntimeException if function type is not defined.
     * @throws RuntimeException if domain is not defined.
     * @throws RuntimeException if function type is set to 'Sampled' or 'PostScript Calculator' and if range is not defined.
     */
    public function format(): string
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