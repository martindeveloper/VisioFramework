<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Exception;

use Visio;

/**
 * Visio\Exception\Template
 *
 * @package Visio\Exception
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Template extends Visio\Exception {
    public function __construct($message, $level = \E_USER_ERROR, $file = null, $line = null) {
        parent::__construct($message, \E_USER_ERROR);

        $this->file = ($file != null) ? $file : $this->file;
        $this->line = ($line != null) ? $line : $this->line;
    }
}