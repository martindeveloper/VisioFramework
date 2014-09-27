<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\UI;

use Visio;

/**
 * Control
 *
 * @package Visio\UI
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Control extends Visio\Object implements IControl {
    /**
     * @var Visio\Http\Request $httpRequest
     */
    protected $httpRequest;

    /**
     * @var Visio\Http\Response $httpResponse
     */
    protected $httpResponse;

    /**
     * Session instance
     * @var Visio\Session $session
     */
    protected $session;

    /**
     * Arguments send by URL to controller
     * @var array $args
     */
    protected $args;

    /**
     * Is current request from AJAX?
     * @var bool $ajax
     */
    protected $ajax = false;

    /**
     * Automatic instance of Visio\Template based on Controller name
     * @var Visio\Template
     */
    protected $template;

    /**
     * Controller output is send?
     * @var bool $outputSend
     */
    protected $outputSend = false;

    /**
     * Model of current controller
     * @var Visio\Model\IDataAccess $model
     */
    public $model;

    /**
     * Controller name
     * @var string $name
     */
    protected $name;

    /**
     * @var Visio\DependencyInjection\IContainer
     */
    public $container;

    /**
     * Initialize messages
     */
    public function initializeMessages() {
        if ($this->session instanceof Visio\Session && $this->template instanceof Visio\Template) {
            $this->template->messages = array();

            $messages = $this->session->read("messages", "Visio\\Components\\" . $this->name);

            if (is_array($messages)) {
                foreach ($messages as $message) {
                    $this->template->messages[] = $message;
                }
            }

            $this->session->write("messages", array(), "Visio\\Components\\" . $this->name);
        }
    }

    /**
     * Add message
     *
     * @param string $message
     */
    public function addMessage($message) {
        if ($this->template instanceof Visio\Template && is_array($this->template->messages)) {
            $this->template->messages[] = $message;

            $this->session->write("messages", $this->template->messages, "Visio\\Components\\" . $this->name);
        } else {
            if ($this->session->exist("messages", "Visio\\Components\\" . $this->name)) {
                $messages = $this->session->read("messages", "Visio\\Components\\" . $this->name);
                $messages[] = $message;

                $this->session->write("messages", $messages, "Visio\\Components\\" . $this->name);
            }
        }
    }

    /**
     * Add messages
     *
     * @param array $messages
     */
    public function addMessages(array $messages) {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    /**
     * setArgs
     *
     * @param array $args
     * @return void
     */
    public function setArgs($args) {
        $this->args = $args;

        $controller = explode("\\", $this->args['controller'], 2);
        $controller = $controller[1];
        $this->name = $controller;
        $actionParts = explode('_', $this->args['action']);

        foreach ($actionParts as & $part) {
            $part = Visio\Utilities\String::ucfirst($part);
        }

        $this->args['action'] = join('_', $actionParts);
    }

    /**
     * getArgs
     *
     * @return array
     */
    public function getArgs() {
        return $this->args;
    }

    /**
     * setAjax
     *
     * @param bool $ajax
     * @return void
     */
    public function setAjax($ajax) {
        $this->ajax = (bool)$ajax;
    }

    /**
     * setHttpRequest
     *
     * @param Visio\Http\Request $httpRequest
     * @return void
     */
    public function setHttpRequest(Visio\Http\Request $httpRequest) {
        $this->httpRequest = $httpRequest;
    }

    /**
     * setHttpResponse
     *
     * @param Visio\Http\Response $httpResponse
     * @return void
     */
    public function setHttpResponse(Visio\Http\Response $httpResponse) {
        $this->httpResponse = $httpResponse;
    }

    /**
     * Set template to control
     *
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function setTemplate($controller, $action) {
        $viewsPath = $this->container->applicationConfig->get("views", "Directories");

        $suffix = ($this->ajax === true) ? ".ajax" : "";

        $paths = array();
        $paths[0] = $viewsPath . DS . $controller . DS . $action . $suffix . ".kiwi"; // Controller/Action{.ajax}.kiwi
        $paths[1] = $viewsPath . DS . $controller . $action . $suffix . ".kiwi"; // ControllerAction{.ajax}.kiwi
        $paths[2] = $viewsPath . DS . $controller . DS . $action . $suffix . ".phtml"; // Controller/Action{.ajax}.phtml
        $paths[3] = $viewsPath . DS . $controller . $action . $suffix . ".phtml"; // ControllerAction{.ajax}.phtml

        foreach ($paths as $path) {
            if (Visio\FileSystem::fileExist($path) && Visio\FileSystem::isReadable($path)) {
                $this->template = new Visio\Template($path, false, $this->container);

                break;
            }
        }

        if (!$this->template instanceof Visio\Template) {
            $this->template = new \stdClass();
        }
    }

}