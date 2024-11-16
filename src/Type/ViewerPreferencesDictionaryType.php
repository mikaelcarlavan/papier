<?php

namespace Papier\Type;


use Papier\Validator\PageModeValidator;
use Papier\Validator\IntegersArrayValidator;
use Papier\Validator\PageBoundariesValidator;
use Papier\Validator\PrintScalingValidator;
use Papier\Validator\DuplexValidator;
use Papier\Validator\DirectionValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;

class ViewerPreferencesDictionaryType extends DictionaryType
{
    /**
     * Set hide tool bars.
     *  
     * @param bool $hidetoolbar
     * @return ViewerPreferencesDictionaryType
     */
    public function setHideToolbar(bool $hidetoolbar): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $hidetoolbar);
        $this->setEntry('HideToolbar', $value);
        return $this;
    }

    /**
     * Set hide menu bar.
     *  
     * @param bool $hidemenubar
     * @return ViewerPreferencesDictionaryType
     */
    public function setHideMenubar(bool $hidemenubar): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $hidemenubar);
        $this->setEntry('HideMenubar', $value);
        return $this;
    }

    /**
     * Set hide windows UI.
     *  
     * @param bool $hidewindowsui
     * @return ViewerPreferencesDictionaryType
     */
    public function setHideWindowsUI(bool $hidewindowsui): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $hidewindowsui);
        $this->setEntry('HideWindowsUI', $value);
        return $this;
    }

    /**
     * Set resize document's window to fit the size of the first displayed page.
     *  
     * @param bool $fitwindow
     * @return ViewerPreferencesDictionaryType
     */
    public function setFitWindow(bool $fitwindow): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $fitwindow);
        $this->setEntry('FitWindow', $value);
        return $this;
    }

    /**
     * Set document's window position in the center of the screen.
     *  
     * @param bool $centerwindow
     * @return ViewerPreferencesDictionaryType
     */
    public function setCenterWindow(bool $centerwindow): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $centerwindow);
        $this->setEntry('CenterWindow', $value);
        return $this;
    }

    /**
     * Set window's title bar with document title.
     *  
     * @param bool $display
     * @return ViewerPreferencesDictionaryType
     */
    public function setDisplayDocTitle(bool $display): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $display);
        $this->setEntry('DisplayDocTitle', $value);
        return $this;
    }

    /**
     * Set document's page mode.
     *  
     * @param string $mode
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid page mode.
     */
    public function setNonFullScreenPageMode(string $mode): ViewerPreferencesDictionaryType
    {
        if (!PageModeValidator::isValid($mode)) {
            throw new InvalidArgumentException("NonFullScreenPageMode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $mode);
        $this->setEntry('NonFullScreenPageMode', $value);
        return $this;
    }

    /**
     * Set ordering order for text.
     *  
     * @param string $direction
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid direction.
     */
    public function setDirection(string $direction): ViewerPreferencesDictionaryType
    {
        if (!DirectionValidator::isValid($direction)) {
            throw new InvalidArgumentException("Direction is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $direction);
        $this->setEntry('Direction', $value);
        return $this;
    }

    /**
     * Set view area.
     *  
     * @param string $area
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid view area.
     */
    public function setViewArea(string $area): ViewerPreferencesDictionaryType
    {
        if (!PageBoundariesValidator::isValid($area)) {
            throw new InvalidArgumentException("ViewArea is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $area);
        $this->setEntry('ViewArea', $value);
        return $this;
    }

    /**
     * Set view clip.
     *  
     * @param string $clip
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid view clip.
     */
    public function setViewClip(string $clip): ViewerPreferencesDictionaryType
    {
        if (!PageBoundariesValidator::isValid($clip)) {
            throw new InvalidArgumentException("ViewClip is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $clip);
        $this->setEntry('ViewClip', $value);
        return $this;
    }

    /**
     * Set print area.
     *  
     * @param string $area
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid print area.
     */
    public function setPrintArea(string $area): ViewerPreferencesDictionaryType
    {
        if (!PageBoundariesValidator::isValid($area)) {
            throw new InvalidArgumentException("PrintArea is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $area);
        $this->setEntry('PrintArea', $value);
        return $this;
    }

    /**
     * Set print clip.
     *  
     * @param string $clip
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid print clip.
     */
    public function setPrintClip(string $clip): ViewerPreferencesDictionaryType
    {
        if (!PageBoundariesValidator::isValid($clip)) {
            throw new InvalidArgumentException("PrintClip is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $clip);
        $this->setEntry('PrintClip', $value);
        return $this;
    }

    /**
     * Set print scaling.
     *  
     * @param string $scaling
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid print scaling.
     */
    public function setPrintScaling(string $scaling): ViewerPreferencesDictionaryType
    {
        if (!PrintScalingValidator::isValid($scaling)) {
            throw new InvalidArgumentException("PrintScaling is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $scaling);
        $this->setEntry('PrintScaling', $value);
        return $this;
    }

    /**
     * Set paper handling option.
     *  
     * @param string $duplex
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not a valid print scaling.
     */
    public function setDuplex(string $duplex): ViewerPreferencesDictionaryType
    {
        if (!DuplexValidator::isValid($duplex)) {
            throw new InvalidArgumentException("Duplex is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\NameType', $duplex);
        $this->setEntry('Duplex', $value);
        return $this;
    }

    /**
     * Set input paper tray based on PDF page size.
     *  
     * @param bool $pick
     * @return ViewerPreferencesDictionaryType
     */
    public function setPickTrayByPDFSize(bool $pick): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\BooleanType', $pick);
        $this->setEntry('PickTrayByPDFSize', $value);
        return $this;
    }

    /**
     * Set print page range.
     *  
     * @param array<int> $range
     * @return ViewerPreferencesDictionaryType
     * @throws InvalidArgumentException if the provided argument is not of type 'array' and if element of the array is not of type 'int'.
     */
    public function setPrintPageRange(array $range): ViewerPreferencesDictionaryType
    {
        if (!IntegersArrayValidator::isValid($range)) {
            throw new InvalidArgumentException("PrintPageRange is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Papier\Type\IntegersArrayType', $range);
        $this->setEntry('PrintPageRange', $value);
        return $this;
    }
    
    /**
     * Set number of copies.
     *  
     * @param int $num
     * @return ViewerPreferencesDictionaryType
     */
    public function setNumCopies(int $num): ViewerPreferencesDictionaryType
    {
        $value = Factory::create('Papier\Type\IntegerType', $num);
        $this->setEntry('NumCopies', $value);
        return $this;
    }
}