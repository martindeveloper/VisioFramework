<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Data;

use Visio;

/**
 * JSON client class
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Json extends Visio\Object implements Visio\Data\IFormat {

    /**
     * Encode value to JSON format
     * @param mixed $value
     * @param int|null $options
     * @return Json\Object
     */
    public function encode($value, $options = null) {
        $encoder = new Visio\Data\Json\Encoder;
        return $encoder->encode($value, $options);
    }

    /**
     * Decode value from JSON format
     * @param mixed $value
     * @param int|null $options
     * @return mixed
     */
    public function decode($value, $forceArray = true, $depth = 512, $options = null) {
        $decoder = new Visio\Data\Json\Decoder;
        $decoder->depth = $depth;
        $decoder->forceArray = $forceArray;

        return $decoder->decode($value, $options);
    }
}