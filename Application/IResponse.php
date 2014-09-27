<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Application;

use Visio;

/**
 * Visio\Application\IResponse
 * 
 * @package Visio\Application
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IResponse {

    /**
     * @param \Visio\Http\Response $httpResponse
     * @param \Visio\Http\Request $httpRequest
     * @param \Visio\DependencyInjection\IContainer $container
     * @return mixed
     */
    public function send(Visio\Http\Response $httpResponse, Visio\Http\Request $httpRequest, Visio\DependencyInjection\IContainer $container);
}