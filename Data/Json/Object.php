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
 * JSON object class
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Object extends Visio\Object {

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->value;
    }
}