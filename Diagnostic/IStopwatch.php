<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Diagnostic;

/**
 * Visio\Diagnostic\IStopwatch
 *
 * @package Visio\Diagnostic
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IStopwatch {

    /**
     * Start stopwatch
     */
    public function start();

    /**
     * Stop stopwatch
     */
    public function stop();
}