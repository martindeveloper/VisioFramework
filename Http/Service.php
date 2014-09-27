<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Http;

use Visio;

/**
 * HTTP service
 *
 * @package Visio\Http
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Service extends Visio\Object implements Visio\DependencyInjection\IService {

    /**
     * __construct
     */
    public function __construct() {
    }

    /**
     * @param \Visio\DependencyInjection\IContainer $container
     * @return \Visio\Http
     */
    public function __invoke(Visio\DependencyInjection\IContainer $container) {
        return new Visio\Http();
    }
}