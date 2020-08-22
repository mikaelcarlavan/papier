<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\ArrayObject;
use Papier\Object\NameObject;
use Papier\OBject\FunctionObject;
use Papier\OBject\StreamObject;

use Papier\Factory\Factory;

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
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setLW($lw)
    {
        if (!NumberValidator::isValid($lw)) {
            throw new InvalidArgumentException("LW is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $lw);
        $this->setEntry('LW', $value);
        return $this;
    }

    /**
     * Set line cap style.
     *  
     * @param  mixed  $lc
     * @throws InvalidArgumentException if the provided argument is not a valid line cap style.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setLC($lc)
    {
        if (!LineCapStyleValidator::isValid($lc)) {
            throw new InvalidArgumentException("LC is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $lc);
        $this->setEntry('LC', $value);
        return $this;
    }

    /**
     * Set line join style.
     *  
     * @param  mixed  $lj
     * @throws InvalidArgumentException if the provided argument is not a valid line join style.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setLJ($lj)
    {
        if (!LineJoinStyleValidator::isValid($lj)) {
            throw new InvalidArgumentException("LJ is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $lj);
        $this->setEntry('LJ', $value);
        return $this;
    }

    /**
     * Set miter limit.
     *  
     * @param  mixed  $ml
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setML($ml)
    {
        if (!NumberValidator::isValid($ml)) {
            throw new InvalidArgumentException("ML is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $ml);
        $this->setEntry('ML', $value);
        return $this;
    }

    /**
     * Set dash pattern.
     *  
     * @param  \Papier\Object\ArrayObject  $d
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setD($d)
    {
        if (!$d instanceof ArrayObject) {
            throw new InvalidArgumentException("D is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('D', $d);
        return $this;
    }

    /**
     * Set name of rendering intent.
     *  
     * @param  string  $ri
     * @throws InvalidArgumentException if the provided argument is not a valid rendering intent.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setRI($ri)
    {
        if (!RenderingIntentValidator::isValid($ri)) {
            throw new InvalidArgumentException("RI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $ri);
        $this->setEntry('RI', $value);
        return $this;
    }

    /**
     * Set overprint.
     *  
     * @param  bool $op
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setOP($op)
    {
        if (!BooleanValidator::isValid($op)) {
            throw new InvalidArgumentException("OP is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $op);

        $this->setEntry('OP', $value);
        $this->setEntry('op', $value);
        return $this;
    } 
 
    /**
     * Set overprint mode.
     *  
     * @param  int  $opm
     * @throws InvalidArgumentException if the provided argument is not a valid overprint mode.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setOPM($opm)
    {
        if (!OverprintModeValidator::isValid($opm)) {
            throw new InvalidArgumentException("OPM is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Integer', $opm);
        $this->setEntry('OPM', $value);
        return $this;
    }

    /**
     * Set font.
     *  
     * @param  \Papier\Object\ArrayObject  $font
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setFont($font)
    {
        if (!$font instanceof ArrayObject) {
            throw new InvalidArgumentException("Font is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('Font', $font);
        return $this;
    }

    /**
     * Set black-generation function.
     *  
     * @param  \Papier\Object\FunctionObject  $bg
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setBG($bg)
    {
        if (!$bg instanceof FunctionObject) {
            throw new InvalidArgumentException("BG is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('BG', $bg);
        return $this;
    }

    /**
     * Set black-generation function.
     *  
     * @param  mixed  $bg
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject' or 'NameObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setBG2($bg)
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
     * @param  \Papier\Object\FunctionObject  $ucr
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setUCR($ucr)
    {
        if (!$ucr instanceof FunctionObject) {
            throw new InvalidArgumentException("UCR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $this->setEntry('UCR', $ucr);
        return $this;
    }

    /**
     * Set undercolor-removal function.
     *  
     * @param  mixed  $ucr
     * @throws InvalidArgumentException if the provided argument is not of type 'FunctionObject' or 'NameObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setUCR2($ucr)
    {
        if (!$ucr instanceof FunctionObject && !$ucr instanceof NameObject) {
            throw new InvalidArgumentException("UCR is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        if ($ucr instanceof NameObject && $urc->getValue() != 'Default') {
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
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setTR($tr)
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
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setTR2($tr)
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
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setHT($ht)
    {
        if (!$ht instanceof DictionaryObject && !$ht instanceof StreamObject && !$ht instanceof NameObject) {
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
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setFL($fl)
    {
        if (!NumberValidator::isValid($fl)) {
            throw new InvalidArgumentException("FL is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $fl);
        $this->setEntry('FL', $value);
        return $this;
    }

    /**
     * Set smoothness tolerance.
     *  
     * @param  mixed  $sm
     * @throws InvalidArgumentException if the provided argument is not of type 'float' or 'int'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setSM($sm)
    {
        if (!NumberValidator::isValid($sm)) {
            throw new InvalidArgumentException("SM is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $sm);
        $this->setEntry('SM', $value);
        return $this;
    }

    /**
     * Set stroke adjustment.
     *  
     * @param  bool $sa
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setSA($sa)
    {
        if (!BooleanValidator::isValid($sa)) {
            throw new InvalidArgumentException("SA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $sa);

        $this->setEntry('SA', $value);
        return $this;
    } 

    /**
     * Set blend mode.
     *  
     * @param  mixed  $bm
     * @throws InvalidArgumentException if the provided argument is not of type 'ArrayObject' or 'NameObject'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setBM($bm)
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
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setSMask($smask)
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
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setCA($ca)
    {
        if (!NumberValidator::isValid($ca)) {
            throw new InvalidArgumentException("CA is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Number', $ca);

        $this->setEntry('CA', $value);
        $this->setEntry('ca', $value);
        return $this;
    } 

    /**
     * Set alpha source.
     *  
     * @param  bool $ais
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setAIS($ais)
    {
        if (!BooleanValidator::isValid($ais)) {
            throw new InvalidArgumentException("AIS is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $ais);

        $this->setEntry('AIS', $value);
        return $this;
    } 

    /**
     * Set knockout.
     *  
     * @param  bool $tk
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\GraphicsStateParameterDictionaryType
     */
    public function setTK($tk)
    {
        if (!BooleanValidator::isValid($tk)) {
            throw new InvalidArgumentException("TK is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $sa);

        $this->setEntry('TK', $value);
        return $this;
    } 
    /**
     * Format object's value.
     *
     * @return string
     */
    public function format()
    {
        $type = Factory::create('Name', 'ExtGState');
        $this->setEntry('Type', $type);

        return parent::format();
    }
}