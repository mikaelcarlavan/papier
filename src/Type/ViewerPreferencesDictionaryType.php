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
}