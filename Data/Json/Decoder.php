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
 * JSON Decoder
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Decoder extends Visio\Object {

    const BIGINT_AS_STRING = JSON_BIGINT_AS_STRING;

    /**
     * @var bool
     */
    public $forceArray = true;

    /**
     * @var int
     */
    public $depth = 512;

    /**
     *
     */
    public function __construct() {
    }

    /**
     * @param string $value
     * @param int $options
     * @return mixed
     * @throws Visio\Exception\Json
     */
    public function decode($value, $options) {
        if (floatval(phpversion()) < 5.4) {
            $value = json_decode($value, (bool)$this->forceArray, $this->depth);
        }else{
            $value = json_decode($value, (bool)$this->forceArray, $this->depth, $options);
        }

        if (Visio\Data\Json\Error::check()) {
            return $value;
        }
    }
}