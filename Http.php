<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Class handling all HTTP communication.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Http extends Visio\Object {

    /**
     * @var Visio\Http\Response
     */
    public $response = null;

    /**
     * @var Visio\Http\Request
     */
    public $request = null;

    /**
     * @var Visio\Http
     */
    public static $_instance;

    /**
     * __construct()
     */
    public function __construct() {
        $this->request = new Visio\Http\Request();
        $this->response = new Visio\Http\Response();
    }

    /**
     * Visio\Http::getInstance()
     *
     * @return Visio\Http object
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}