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
 * Response class for JavaScript Object Nation
 *
 * @package Visio\Application\Response
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Json extends Visio\Application\Response\BaseResponse implements Visio\Application\IResponse {

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
        if ($this->data instanceof Visio\Template) {
            try {
                $respond = (string) $this->data;
            } catch (Visio\Exception\Template $ex) {
                $httpResponse->addHeaderRaw("HTTP/1.1 500 Internal Server Error");
                $respond = $ex->getErrorMessage();
            }
        } else {
            $respond = json_encode($this->data);
        }

        $httpResponse->setContentType("application/json");

        Visio\Events::dispatch("ResponseJson-onSend", array(&$this, &$respond));

        echo $respond;
    }

}
