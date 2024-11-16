<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\NameObject;
use Papier\Object\FunctionObject;
use Papier\Object\StreamObject;

use Papier\Factory\Factory;

use Papier\Validator\BooleanValidator;
use Papier\Validator\NumberValidator;
use Papier\Validator\LineCapStyleValidator;
use Papier\Validator\LineJoinStyleValidator;
use Papier\Validator\OverprintModeValidator;
use Papier\Validator\RenderingIntentValidator;

use InvalidArgumentException;

class GraphicsStateParameterDictionaryType extends DictionaryObject
{
    /**
     * Set line width.
     *  
     * @param  mixed  $lw
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     */
    public function setLW($lw): GraphicsStateParameterDictionaryType
    {
        if (!NumberValidator::isValid($lw)) {
            throw new InvalidArgumentException("LW is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $lw);
        $this->setEntry('LW', $value);
        return $this;
    }

    /**
     * Set line cap style.
     *  
     * @param  mixed  $lc
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid line cap style.
	 */
    public function setLC($lc): GraphicsStateParameterDictionaryType
    {
        if (!LineCapStyleValidator::isValid($lc)) {
            throw new InvalidArgumentException("LC is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $lc);
        $this->setEntry('LC', $value);
        return $this;
    }

    /**
     * Set line join style.
     *  
     * @param  mixed  $lj
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid line join style.
	 */
    public function setLJ($lj): GraphicsStateParameterDictionaryType
    {
        if (!LineJoinStyleValidator::isValid($lj)) {
            throw new InvalidArgumentException("LJ is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $lj);
        $this->setEntry('LJ', $value);
        return $this;
    }

    /**
     * Set miter limit.
     *  
     * @param  mixed  $ml
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
	 */
    public function setML($ml): GraphicsStateParameterDictionaryType
    {
        if (!NumberValidator::isValid($ml)) {
            throw new InvalidArgumentException("ML is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $ml);
        $this->setEntry('ML', $value);
        return $this;
    }

    /**
     * Set dash pattern.
     *  
     * @param  \Papier\Object\ArrayObject  $d
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
	 */
    public function setD(ArrayObject $d): GraphicsStateParameterDictionaryType
    {
        $this->setEntry('D', $d);
        return $this;
    }

    /**
     * Set name of rendering intent.
     *  
     * @param  string  $ri
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid rendering intent.
	 */
    public function setRI($ri): GraphicsStateParameterDictionaryType
    {
        if (!RenderingIntentValidator::isValid($ri)) {
            throw new InvalidArgumentException("RI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $ri);
        $this->setEntry('RI', $value);
        return $this;
    }

    /**
     * Set overprint.
     *  
     * @param  bool $op
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
	 */
    public function setOP(bool $op): GraphicsStateParameterDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $op);

        $this->setEntry('OP', $value);
        $this->setEntry('op', $value);
        return $this;
    } 
 
    /**
     * Set overprint mode.
     *  
     * @param  int  $opm
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid overprint mode.
	 */
    public function setOPM($opm): GraphicsStateParameterDictionaryType
    {
        if (!OverprintModeValidator::isValid($opm)) {
            throw new InvalidArgumentException("OPM is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegerType', $opm);
        $this->setEntry('OPM', $value);
        return $this;
    }

    /**
     * Set font.
     *  
     * @param  \Papier\Object\ArrayObject  $font
     * @return GraphicsStateParameterDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
	 */
    public function setFont(ArrayObject $font): GraphicsStateParameterDictionaryType
    {
        $this->setEntry('Font', $font);
        return $this;
    }

    /**
     * Set black-generation function.
     *  
     * @param FunctionObject $bg
     * @return GraphicsStateParameterDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     */
	public function setBG(FunctionObject $bg): GraphicsStateParameterDictionaryType
    {
        $this->setEntry('BG', $bg);
        return $this;
    }

    /**
     * Set black-generation function.
     *  
     * @param  mixed  $bg
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject' or 'NameObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setBG2($bg): GraphicsStateParameterDictionaryType
    {
        if (!$bg instanceof FunctionObject && !$bg instanceof NameObject) {
            throw new InvalidArgumentException("BG is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($bg instanceof NameObject && $bg->getValue() != 'Default') {
            throw new InvalidArgumentException("UCR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('BG2', $bg);
        return $this;
    }

    /**
     * Set undercolor-removal function.
     *  
     * @param FunctionObject $ucr
     * @return GraphicsStateParameterDictionaryType
	 * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     */
	public function setUCR(FunctionObject $ucr): GraphicsStateParameterDictionaryType
    {
        $this->setEntry('UCR', $ucr);
        return $this;
    }

    /**
     * Set undercolor-removal function.
     *  
     * @param  mixed  $ucr
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject' or 'NameObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setUCR2($ucr): GraphicsStateParameterDictionaryType
    {
        if (!$ucr instanceof FunctionObject && !$ucr instanceof NameObject) {
            throw new InvalidArgumentException("UCR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($ucr instanceof NameObject && $ucr->getValue() != 'Default') {
            throw new InvalidArgumentException("UCR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }
  
        $this->setEntry('UCR2', $ucr);
        return $this;
    }

    /**
     * Set transfer function.
     *  
     * @param  mixed  $tr
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject' or 'NameObject' or 'ArrayObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setTR($tr): GraphicsStateParameterDictionaryType
    {
        if (!$tr instanceof FunctionObject && !$tr instanceof NameObject && !$tr instanceof ArrayObject) {
            throw new InvalidArgumentException("TR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($tr instanceof NameObject && $tr->getValue() != 'Identity') {
            throw new InvalidArgumentException("TR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('TR', $tr);
        return $this;
    }

    /**
     * Set transfer function.
     *  
     * @param  mixed  $tr
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject' or 'NameObject' or 'ArrayObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setTR2($tr): GraphicsStateParameterDictionaryType
    {
        if (!$tr instanceof FunctionObject && !$tr instanceof NameObject && !$tr instanceof ArrayObject) {
            throw new InvalidArgumentException("TR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($tr instanceof NameObject && $tr->getValue() != 'Default') {
            throw new InvalidArgumentException("TR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('TR2', $tr);
        return $this;
    }

    /**
     * Set halftone dictionary or stream or name.
     *  
     * @param  mixed  $ht
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'StreamObject' or 'NameObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setHT($ht): GraphicsStateParameterDictionaryType
    {
        if (!$ht instanceof DictionaryObject && !$ht instanceof NameObject) {
            throw new InvalidArgumentException("HT is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('HT', $ht);
        return $this;
    }

    /**
     * Set flatness tolerance.
     *  
     * @param  mixed  $fl
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setFL($fl): GraphicsStateParameterDictionaryType
    {
        if (!NumberValidator::isValid($fl)) {
            throw new InvalidArgumentException("FL is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $fl);
        $this->setEntry('FL', $value);
        return $this;
    }

    /**
     * Set smoothness tolerance.
     *  
     * @param  mixed  $sm
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setSM($sm): GraphicsStateParameterDictionaryType
    {
        if (!NumberValidator::isValid($sm)) {
            throw new InvalidArgumentException("SM is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $sm);
        $this->setEntry('SM', $value);
        return $this;
    }

    /**
     * Set stroke adjustment.
     *  
     * @param  bool $sa
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setSA(bool $sa): GraphicsStateParameterDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $sa);

        $this->setEntry('SA', $value);
        return $this;
    } 

    /**
     * Set blend mode.
     *  
     * @param  mixed  $bm
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject' or 'NameObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setBM($bm): GraphicsStateParameterDictionaryType
    {
        if (!$bm instanceof ArrayObject && !$bm instanceof NameObject) {
            throw new InvalidArgumentException("BM is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('BM', $bm);
        return $this;
    }

    /**
     * Set soft mask.
     *  
     * @param  mixed  $smask
     * @throws InvalidArgumentException if the provided argument is not of type 'DictionaryObject' or 'NameObject'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setSMask($smask): GraphicsStateParameterDictionaryType
    {
        if (!$smask instanceof DictionaryObject && !$smask instanceof NameObject) {
            throw new InvalidArgumentException("SMask is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('SMask', $smask);
        return $this;
    }

    /**
     * Set stroking alpha constant.
     *  
     * @param  mixed $ca
     * @throws InvalidArgumentException if the provided argument is not of type 'int' or 'float'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setCA($ca): GraphicsStateParameterDictionaryType
    {
        if (!NumberValidator::isValid($ca)) {
            throw new InvalidArgumentException("CA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NumberType', $ca);

        $this->setEntry('CA', $value);
        $this->setEntry('ca', $value);
        return $this;
    } 

    /**
     * Set alpha source.
     *  
     * @param  bool $ais
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setAIS(bool $ais): GraphicsStateParameterDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $ais);

        $this->setEntry('AIS', $value);
        return $this;
    } 

    /**
     * Set knockout.
     *  
     * @param  bool $tk
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return GraphicsStateParameterDictionaryType
	 */
	public function setTK(bool $tk): GraphicsStateParameterDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $tk);

        $this->setEntry('TK', $value);
        return $this;
    } 
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format(): string
	{
        $type = Factory::create('Papier\Type\NameType', 'ExtGState');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}