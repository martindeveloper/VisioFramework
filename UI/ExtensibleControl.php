<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\UI;

use Visio;

/**
 * Extensible Control
 *
 * @package Visio\UI
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class ExtensibleControl extends Control implements Visio\Extensibility\IExtensible, IControl {

    /**
     * Run controller extensions method by pattern
     *
     * @param string $pattern
     */
    public function runExtensions($pattern) {
        //Find after run methods of extensions (traits)
        $methods = get_class_methods($this);
        $methods = array_filter($methods, function ($var) use ($pattern) {
            return (bool)strstr($var, $pattern);
        });

        //Unset self
        unset($methods[array_search($pattern, $methods, true)]);

        foreach ($methods as $method) {
            $this->$method();
        }
    }

    /**
     * Run extensions handlers "before run"
     */
    public function taskBeforeRun() {
        $this->runExtensions("taskBeforeRun");
    }

    /**
     * Run extensions handlers "after run"
     */
    public function taskAfterRun() {
        $this->runExtensions("taskAfterRun");
    }
}