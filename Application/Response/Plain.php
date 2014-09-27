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
 * Response class for plain text files.
 *
 * @package Visio\Application\Response
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Plain extends Visio\Application\Response\BaseResponse implements Visio\Application\IResponse {

    public $data;

    /**
     * __construct()
     *
     * @param mixed $data
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * @param \Visio\Http\Response $httpResponse
     * @param \Visio\Http\Request $httpRequest
     * @param \Visio\DependencyInjection\IContainer $container
     * @return mixed|void
     */
    public function send(Visio\Http\Response $httpResponse, Visio\Http\Request $httpRequest, Visio\DependencyInjection\IContainer $container) {
        $httpResponse->setContentType("text/plain", "utf-8");

        Visio\Events::dispatch("ResponseCss-onSend", array(&$this,
                                                           &$this->data));

        echo $this->data;
    }

}