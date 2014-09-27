<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Router;

use Visio, Visio\Router;

/**
 * Router helper for parsing routes from file
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 *
 * TODO: Complete this class
 */
class ReverseRoute extends Visio\Object {
    private $controller;
    private $action;
    private $args;

    public function __construct($controller, $action, array $args = array()) {
        $controller = Visio\Utilities\String::ucfirst($controller);

    }
}