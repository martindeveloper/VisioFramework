<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\UI\Controller;

use Visio;

/**
 * REST controller
 *
 * @package Visio\Application
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Rest extends Visio\UI\Controller implements Visio\UI\IControl {
    protected $method;

    /**
     * Shortcut to get PUT data by index
     *
     * @param string $key
     * @return mixed
     */
    public function getPut($key = null) {
        if (is_null($key)) {
            return $this->httpRequest->getPut();
        }

        return $this->httpRequest->getPutIndex($key);
    }

    /**
     * Task before run of controller
     */
    public function taskBeforeRun() {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            $this->method = Visio\Utilities\String::upper($_SERVER['REQUEST_METHOD']);
        } else {
            $this->method = "GET";
        }

        if ($this->method != "GET") {
            $this->preventExecution = true;
        }

        parent::taskBeforeRun();
    }
}