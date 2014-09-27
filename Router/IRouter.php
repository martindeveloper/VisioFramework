<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Router;

use Visio;

/**
 * Visio\Router\IRouter
 *
 * @package Visio\Router
 * @author Martin Pernica
 * @version 3.0
 */
interface IRouter {

    const ALLOW_ARGS = "args";
    const K_FIXED = 0;
    const K_DEFAULT = 1;
    const K_VALUE = 2;
    const NAMES = 333;
    const LANG = "lang";
    const CONTROLLER = "controller";
    const DOACTION = "doAction";
    const ACTION = "action";
    const DEFAULT_LANG = "en";

    function connect(Visio\Http\Request $httpRequest);
}
