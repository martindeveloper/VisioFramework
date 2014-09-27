<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Application\Response;

use Visio;

/**
 * Base response class.
 *
 * @package Visio\Application\Response
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class BaseResponse extends Visio\Object {

    /**
     * __construct()
     *
     * @param mixed $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string)$this->data;
    }

}