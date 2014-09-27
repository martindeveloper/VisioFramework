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
 * Visio\Exception\Internal
 * 
 * @package Visio\Exception
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Internal extends Visio\Exception {

    /**
     * __construct()
     * 
     * @param string $message
     * @param int $number
     * @param string $file
     * @param int $line
     */
    public function __construct($message, $number, $file, $line) {
        $this->file = $file;
        $this->line = $line;
        parent::__construct($message, $number);
    }

}