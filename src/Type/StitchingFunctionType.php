<?php

namespace Papier\Type;

use Papier\Object\FunctionObject;
use Papier\Object\ArrayObject;

use Papier\Functions\FunctionType as FuncType;

use Papier\Type\Base\FunctionType;
use RuntimeException;

class StitchingFunctionType extends FunctionType
{
    
    /**
     * Set functions.
     *  
     * @param ArrayObject $functions
     * @return StitchingFunctionType
     */
    public function setFunctions(ArrayObject $functions): StitchingFunctionType
    {
        $this->setEntry('Functions', $functions);
        return $this;
    } 

    /**
     * Set encode.
     *  
     * @param  ArrayObject  $encode
     * @return StitchingFunctionType
     */
    public function setEncode(ArrayObject $encode): StitchingFunctionType
    {
        $this->setEntry('Encode', $encode);
        return $this;
    } 

    /**
     * Set bounds.
     *  
     * @param  ArrayObject  $bounds
     * @return StitchingFunctionType
     */
    public function setBounds(ArrayObject $bounds): StitchingFunctionType
    {
        $this->setEntry('Bounds', $bounds);
        return $this;
    } 

    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
    {
        $this->setFunctionType(FuncType::STITCHING);

        if (!$this->hasEntry('Functions')) {
            throw new RuntimeException("Functions is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Encode')) {
            throw new RuntimeException("Encode is missing. See ".__CLASS__." class's documentation for possible values.");
        }

        if (!$this->hasEntry('Bounds')) {
            throw new RuntimeException("Bounds is missing. See ".__CLASS__." class's documentation for possible values.");
        }

		/** @var ArrayObject $functions */
		$functions = $this->getEntry('Functions');
		/** @var ArrayObject $bounds */
		$bounds = $this->getEntry('Bounds');
		/** @var ArrayObject $encode */
		$encode = $this->getEntry('Encode');

        $k = count($functions);

        if (count($bounds) != ($k - 1)) {
            throw new RuntimeException("Bounds size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if (count($encode) != (2 * $k)) {
            throw new RuntimeException("Encode size is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

		// Domain is mandatory here but check in parent method
		// Only test if entry is available and compliant
		if ($this->hasEntry('Domain')) {
			/** @var ArrayObject $domain */
			$domain = $this->getEntry('Domain');

			if (count($domain) != 2) {
				throw new RuntimeException("Domain size is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}

			/** @var array<mixed> $value */
			$value = $bounds->getValue();
			if (count($value) > 0) {
				if ($domain->first() > min($value) || $domain->last() < max($value)) {
					throw new RuntimeException("Domain is incorrect. See ".__CLASS__." class's documentation for possible values.");
				}
			} else {
				throw new RuntimeException("Bounds size is incorrect. See ".__CLASS__." class's documentation for possible values.");
			}
		}

        return parent::format();
    }
}