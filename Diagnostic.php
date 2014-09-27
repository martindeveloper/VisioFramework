<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Diagnostic class.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
final class Diagnostic {

    /**
     * @var array $stopwatches
     */
    private static $stopwatches = array();

    /**
     * __construct()
     *
     * @throws Visio\Exception\Logical if this class tried to be instantiated
     */
    final public function __construct() {
        throw new Visio\Exception\Logical("Cannot instantiate static class " . get_class($this));
    }

    /**
     * Visio\Diagnostic::getMemoryUsage()
     *
     * @return string
     */
    public static function getMemoryUsage() {
        $size = memory_get_usage(true);
        $unit = array('b',
                      'kb',
                      'mb',
                      'gb',
                      'tb',
                      'pb');

        return @round($size / pow(1024, ($unitKey = floor(log($size, 1024)))), 2) . ' ' . $unit[(int)$unitKey];
    }

    /**
     * Visio\Diagnostic::getMemoryLimit()
     *
     * @return float
     */
    public static function getMemoryLimit() {
        if (function_exists("ini_get")) {
            return floatval(ini_get('memory_limit'));
        }
        return 0.0;
    }

    /**
     * Visio\Diagnostic::createStopwatch()
     *
     * @param string $identifier
     * @throws Visio\Exception if can not start stopwatch
     * @return bool
     */
    public static function createStopwatch($identifier) {
        try {
            $stopwatch = new Visio\Diagnostic\Stopwatch($identifier);
            $stopwatch->start();
            self::$stopwatches[$identifier] = $stopwatch;
        } catch (Visio\Exception $ex) {
            throw new Visio\Exception("Cannot start stopwatch! Visio\\Diagnostic\\Stopwatch returned '" . $ex->getMessage() . "'");
        }

        return true;
    }

    /**
     * Visio\Diagnostic::getStopwatchDelta()
     *
     * @param string $identifier
     * @throws Visio\Exception if tried to stop undefined stopwatch
     * @return float
     */
    public static function getStopwatchDelta($identifier) {
        if (isset(self::$stopwatches[$identifier])) {
            self::$stopwatches[$identifier]->stop();
            return self::$stopwatches[$identifier]->getDelta();
        } else {
            throw new Visio\Exception("Cannot stop undefined stopwatch!");
        }
    }

    /**
     * Visio\Diagnostic::getBacktraceArray()
     *
     * @return array
     */
    public static function getBacktraceArray() {
        $callStack = debug_backtrace();

        return array_reverse($callStack);
    }

}