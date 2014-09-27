<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Data\Json;

use Visio;

/**
 * JSON Error
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Error extends Visio\Object {

    /**
     * Check for last JSON error
     * @return bool
     * @throws \Visio\Exception\Json
     */
    public static function check() {
        switch (json_last_error()) {
            default:
                throw new Visio\Exception\Json("Unknown JSON error!");
                break;
            case JSON_ERROR_NONE:
                return true;
                break;
            case JSON_ERROR_DEPTH:
                throw new Visio\Exception\Json("Maximum stack depth exceeded!");
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new Visio\Exception\Json("Underflow or the modes mismatch!");
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new Visio\Exception\Json("Unexpected control character found!");
                break;
            case JSON_ERROR_SYNTAX:
                throw new Visio\Exception\Json("Syntax error, malformed JSON!");

                break;
            case JSON_ERROR_UTF8:
                throw new Visio\Exception\Json("Malformed UTF-8 characters, possibly incorrectly encoded!");
                break;
        }
    }
}