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
 * Visio\Exception\ValidatorFailed
 *
 * @package Visio\Exception
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class ValidatorFailed extends Visio\Exception {

    /**
     * Error messages
     *
     * @var array
     */
    public $messages;

    /**
     * @param array $messages
     */
    public function __construct(array $messages) {
        $this->messages = $messages;

        parent::__construct("Validation failed!");
    }
}