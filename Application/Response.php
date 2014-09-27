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
 * Response class.
 * Set response handler and send data.
 *
 * @package Visio\Application
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Response extends Visio\Object implements Visio\Application\IResponse {

    public $response;

    /**
     * __construct()
     *
     * @param Visio\Application\IResponse $responseHandler
     */
    public function __construct(Visio\Application\IResponse $responseHandler) {
        $this->response = $responseHandler;
    }

    /**
     * @param \Visio\Http\Response $httpResponse
     * @param \Visio\Http\Request $httpRequest
     * @param \Visio\DependencyInjection\IContainer $container
     * @return mixed|void
     */
    public function send(Visio\Http\Response $httpResponse, Visio\Http\Request $httpRequest, Visio\DependencyInjection\IContainer $container) {
        Visio\Events::dispatch("Response-onSend", array(&$this));
        $this->response->send($httpResponse, $httpRequest, $container);
    }

}