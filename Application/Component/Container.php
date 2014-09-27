<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Application\Component;

use Visio;

/**
 * Container for component
 *
 * @package Visio\Application\Component
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Container extends Visio\Object {

    /**
     * @var \Visio\UI\Controller\Component
     */
    private $component;

    /**
     * @var \Visio\Http\Request
     */
    private $httpRequest;

    /**
     * @var string
     */
    private $placeholder;

    /**
     * @param \Visio\UI\Controller\Component $component
     * @param \Visio\Http\Request $httpRequest
     * @param string $placeholder
     */
    public function __construct(Visio\UI\Controller\Component $component, Visio\Http\Request $httpRequest, $placeholder) {
        $this->component = $component;
        $this->httpRequest = $httpRequest;
        $this->placeholder = $placeholder;
    }

    /**
     * Run component and return output
     *
     * @param $method
     * @return string
     * @throws \Visio\Exception\Template
     */
    public function run($method) {
        $this->component->taskBeforeRun();

        if ($this->httpRequest->getQueryIndex("notify")) {
            $temp = explode("/", $this->httpRequest->getQueryIndex("notify"));

            $suffix = ($this->httpRequest->ajax === true) ? "Ajax" : "";

            if ($temp[0] == $this->placeholder) {
                if (isset($temp[1])) {
                    $method = $temp[1] . $suffix;
                } else {
                    $method = "renderIndex" . $suffix;
                }

                unset($temp[0]);
                unset($temp[1]);

                if (count($temp) != 0) {
                    $args = $this->component->getArgs();
                    $args = array_merge($args, $temp);
                    $this->component->args = $args;
                }
            }
        }

        if (method_exists($this->component, $method)) {
            $temp = call_user_func(array($this->component,
                                         $method));
        } else {
            throw new Visio\Exception\Template("Calling undefined method '" . $method . "' of component!");
        }

        if ($temp instanceof Visio\Application\Response\BaseResponse) {
            return $temp;
        }

        $respond = $this->component->taskAfterRun();

        if ($respond->response instanceof Visio\Application\IResponse) {
            return $respond->response;
        }
    }
}
