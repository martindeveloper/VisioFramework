<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Diagnostic\Logger;

use Visio;

/**
 * Main logger service.
 *
 * @package Visio\Diagnostic\Logger
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Service extends Visio\Object implements Visio\DependencyInjection\IService {

    /**
     * __construct()
     */
    public function __construct() {
    }

    /**
     * onRegister()
     */
    public function __invoke(Visio\DependencyInjection\IContainer $container) {
        return new Visio\Diagnostic\Logger($container->applicationConfig);
    }
}