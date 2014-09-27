<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Diagnostic;

use Visio;

/**
 * Stopwatch class.
 *
 * @package Visio\Diagnostic
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Stopwatch extends Visio\Object implements Visio\Diagnostic\IStopwatch {

    /**
     * Name of stopwatch
     * 
     * @var string $name
     */
    public $name;

    /**
     * Start time
     * 
     * @var int $start
     */
    public $start;

    /**
     * Stop time
     * 
     * @var int $stop
     */
    public $stop;

    /**
     * Delta of times
     * 
     * @var int $delta
     */
    private $delta;

    /**
     * Is stopwatch started?
     * 
     * @var bool $started
     */
    protected $started = false;

    /**
     * __construct()
     * 
     * @param string $name
     */
    public function __construct($name) {
        $this->name = $name;
    }

    /**
     * Start stopwatch counter
     * 
     * @param mixed $start
     */
    public function start($start = null) {
        if (is_null($start)) {
            $this->start = microtime(true);
        } else {
            $this->start = $start;
        }

        $this->started = true;
    }

    /**
     * Stop stopwatch
     * 
     * @throws Visio\Exception\Logical if stopwatch is not started yet
     */
    public function stop() {
        if (!$this->started)
            throw new Visio\Exception\Logical("Cannot stop stopwatch, if it not started yet!");

        $this->stop = microtime(true);
    }

    /**
     * Return delta of start and end time
     * 
     * @param string $suffix
     * @throws Visio\Exception\Logical if stopwatch is not started yet
     * @return string
     */
    public function getDelta($suffix = "") {
        if (!$this->started)
            throw new Visio\Exception\Logical("Cannot get delta time, if it not started yet!");

        return (string) ($this->stop - $this->start) . $suffix;
    }

    /**
     * Reset stopwatch to default state
     * 
     * @return bool
     */
    public function reset() {
        $this->start = 0;
        $this->stop = 0;
        $this->delta = 0;
        $this->started = false;

        return true;
    }

    /**
     * __destroy()
     */
    public function __destroy() {
        $this->reset();
    }

}