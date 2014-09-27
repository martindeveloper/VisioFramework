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
 * JSON Encoder
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Encoder extends Visio\Object {

    const HEX_QUOT = \JSON_HEX_QUOT;
    const HEX_TAG = \JSON_HEX_TAG;
    const HEX_AMP = \JSON_HEX_AMP;
    const HEX_APOS = \JSON_HEX_APOS;
    const NUMERIC_CHECK = \JSON_NUMERIC_CHECK;
    const FORCE_OBJECT = \JSON_FORCE_OBJECT;

    //PHP 5.4
    const PRETTY_PRINT = \JSON_PRETTY_PRINT;
    const UNESCAPED_SLASHES = \JSON_UNESCAPED_SLASHES;
    const UNESCAPED_UNICODE = \JSON_UNESCAPED_UNICODE;

    /**
     *
     */
    public function __construct() {
    }

    /**
     * @param mixed $value
     * @param int $options
     * @return Visio\Data\Json\Object
     * @throws Visio\Exception\Json
     */
    public function encode($value, $options = null) {
        $value = json_encode($value, $options);

        if (Visio\Data\Json\Error::check()) {
            return new Visio\Data\Json\Object($value);
        }
    }
}