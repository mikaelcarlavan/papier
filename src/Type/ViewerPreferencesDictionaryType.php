<?php

namespace Papier\Type;

use Papier\Object\DictionaryObject;
use Papier\Object\NameObject;
use Papier\Object\ArrayObject;
use Papier\Object\StreamObject;
use Papier\Object\BooleanObject;

use Papier\Validator\PageLayoutValidator;
use Papier\Validator\StringValidator;
use Papier\Validator\PageModeValidator;
use Papier\Validator\BooleanValidator;
use Papier\Validator\IntegersArrayValidator;
use Papier\Validator\PageBoundariesValidator;

use Papier\Factory\Factory;

use InvalidArgumentException;
use RuntimeException;

class ViewerPreferencesDictionaryType extends DictionaryType
{
    /**
     * Set hide tool bars.
     *  
     * @param  bool  $hidetoolbar
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setHideToolbar($hidetoolbar)
    {
        if (!BooleanValidator::isValid($hidetoolbar)) {
            throw new InvalidArgumentException("HideToolbar is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $hidetoolbar);
        $this->setEntry('HideToolbar', $value);
        return $this;
    }

    /**
     * Set hide menu bar.
     *  
     * @param  bool  $hidemenubar
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setHideMenubar($hidemenubar)
    {
        if (!BooleanValidator::isValid($hidemenubar)) {
            throw new InvalidArgumentException("HideMenubar is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $hidemenubar);
        $this->setEntry('HideMenubar', $value);
        return $this;
    }

    /**
     * Set hide windows UI.
     *  
     * @param  bool  $hidewindowsui
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setHideWindowsUI($hidewindowsui)
    {
        if (!BooleanValidator::isValid($hidewindowsui)) {
            throw new InvalidArgumentException("HideWindowsUI is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $hidewindowsui);
        $this->setEntry('HideWindowsUI', $value);
        return $this;
    }

    /**
     * Set resize document's window to fit the size of the first displayed page.
     *  
     * @param  bool  $fitwindow
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setFitWindow($fitwindow)
    {
        if (!BooleanValidator::isValid($fitwindow)) {
            throw new InvalidArgumentException("FitWindow is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $fitwindow);
        $this->setEntry('FitWindow', $value);
        return $this;
    }

    /**
     * Set document's window position in the center of the screen.
     *  
     * @param  bool  $centerwindow
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setCenterWindow($centerwindow)
    {
        if (!BooleanValidator::isValid($centerwindow)) {
            throw new InvalidArgumentException("CenterWindow is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $centerwindow);
        $this->setEntry('CenterWindow', $value);
        return $this;
    }

    /**
     * Set window's title bar with document title.
     *  
     * @param  bool  $display
     * @throws InvalidArgumentException if the provided argument is not of type 'bool'.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setDisplayDocTitle($display)
    {
        if (!BooleanValidator::isValid($display)) {
            throw new InvalidArgumentException("DisplayDocTitle is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Boolean', $display);
        $this->setEntry('DisplayDocTitle', $value);
        return $this;
    }

    /**
     * Set document's page mode.
     *  
     * @param  string  $mode
     * @throws InvalidArgumentException if the provided argument is not a valid page mode.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setNonFullScreenPageMode($mode)
    {
        if (!PageModeValidator::isValid($mode)) {
            throw new InvalidArgumentException("NonFullScreenPageMode is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $mode);
        $this->setEntry('NonFullScreenPageMode', $value);
        return $this;
    }

    /**
     * Set ordering order for text.
     *  
     * @param  string  $direction
     * @throws InvalidArgumentException if the provided argument is not a valid direction.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setDirection($direction)
    {
        if (!DirectionValidator::isValid($direction)) {
            throw new InvalidArgumentException("Direction is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $direction);
        $this->setEntry('Direction', $value);
        return $this;
    }

    /**
     * Set view area.
     *  
     * @param  string  $area
     * @throws InvalidArgumentException if the provided argument is not a valid view area.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setViewArea($area)
    {
        if (!PageBoundariesValidator::isValid($area)) {
            throw new InvalidArgumentException("ViewArea is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $area);
        $this->setEntry('ViewArea', $value);
        return $this;
    }

    /**
     * Set view clip.
     *  
     * @param  string  $clip
     * @throws InvalidArgumentException if the provided argument is not a valid view clip.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setViewClip($clip)
    {
        if (!PageBoundariesValidator::isValid($clip)) {
            throw new InvalidArgumentException("ViewClip is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $clip);
        $this->setEntry('ViewClip', $value);
        return $this;
    }

    /**
     * Set print area.
     *  
     * @param  string  $area
     * @throws InvalidArgumentException if the provided argument is not a valid print area.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setPrintArea($area)
    {
        if (!PageBoundariesValidator::isValid($area)) {
            throw new InvalidArgumentException("PrintArea is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $area);
        $this->setEntry('PrintArea', $value);
        return $this;
    }

    /**
     * Set print clip.
     *  
     * @param  string  $clip
     * @throws InvalidArgumentException if the provided argument is not a valid print clip.
     * @return \Papier\Type\ViewerPreferencesDictionaryType
     */
    public function setPrintClip($clip)
    {
        if (!PageBoundariesValidator::isValid($clip)) {
            throw new InvalidArgumentException("PrintClip is incorrect. See ".__CLASS__." class's documentation for possible values.");
        }

        $value = Factory::create('Name', $clip);
        $this->setEntry('PrintClip', $value);
        return $this;
    }

}