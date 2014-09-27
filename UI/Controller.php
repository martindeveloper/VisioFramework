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
 * Controller represents a web page.
 *
 * @package Visio\Application
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Controller extends Visio\UI\ExtensibleControl implements Visio\UI\IControl {

    /**
     * Current controller language
     * @var string $lang
     */
    public $lang;

    /**
     * List of attached components
     * @var array
     */
    private $components = array();

    /**
     * Storage array for AJAX - JSON response ONLY!
     * @var array
     */
    protected $json = array();

    /**
     * Instance of Visio\Translate for translating content
     * @var Visio\Translate
     */
    protected $translate;

    /**
     * Prevent for automatic execution of controller
     * @var bool $preventExecution
     */
    protected $preventExecution = false;

    /**
     * Shortcut to get POST index
     *
     * @param string $key
     * @return mixed
     */
    public function getPost($key = null) {
        if (is_null($key)) {
            return $this->httpRequest->getPost();
        }

        return $this->httpRequest->getPostIndex($key);
    }

    /**
     * Shortcut to get QUERY/GET index
     *
     * @param string $key
     * @return mixed
     */
    public function getQuery($key = null) {
        if (is_null($key)) {
            return $this->httpRequest->getQuery();
        }

        return $this->httpRequest->getQueryIndex($key);
    }

    /**
     * Shortcut to get FILES index
     *
     * @param string $key
     * @return mixed
     */
    public function getFile($key) {
        return $this->httpRequest->getFilesIndex($key);
    }

    /**
     * Set a new language
     *
     * @param string $lang
     * @return void
     */
    public function setLanguage($lang) {
        $this->lang = $lang;
    }

    /**
     * Get current language
     *
     * @return string
     */
    public function getLanguage() {
        return $this->lang;
    }

    /**
     * @param string $controller
     * @param string $action
     * @param array $args
     *
     * @return string
     */
    public function createLink($controller, $action, array $args = array()) {
        return $this->container->router->createReverseRoute($controller, $action, $args);
    }

    /**
     * Redirect
     *
     * @param Visio\Uri $uri
     * @param bool $sendStatusCode
     * @return void
     */
    public function redirect(Visio\Uri $uri, $sendStatusCode = false) {
        if ($sendStatusCode === true) {
            $this->httpResponse->addHeaderRaw('HTTP/1.0 301 Moved Permanently');
        }

        if ($uri->host == false) {
            $location = $this->httpResponse->baseUrl . $uri->path . "?" . $uri->query;
            $this->httpResponse->redirect(trim($location, "?"));
        }

        $this->httpResponse->redirect((string)$uri);
    }

    /**
     * Redirect back
     *
     * @throws Visio\Exception\General
     * @return void
     */
    public function redirectBack() {
        if (isset($_SERVER["HTTP_REFERER"])) {
            $this->httpResponse->redirect((string)$_SERVER["HTTP_REFERER"]);
        } else {
            throw new Visio\Exception\General("Can not redirect back! Empty refer.", E_USER_NOTICE);
        }
    }

    /**
     * Execute controller
     *
     * @param mixed $data
     * @param string $responseClass
     * @throws Visio\Exception\Response if invalid response type is given
     * @return Visio\Application\Response
     */
    public function execute($data, $responseClass = Visio\Application\ResponseType::HTML) {
        Visio\Events::dispatch("Controller-onExecute", array(&$this));

        if ($this->ajax === true) {
            $this->httpResponse->addHeader("Expires", "0");
            $this->httpResponse->addHeader("Cache-Control", "no-cache, must-revalidate, post-check=0, pre-check=0");
            $this->httpResponse->addHeader("Pragma", "no-cache");
        }

        if ($data instanceof Visio\Template) {
            $this->template->url = $this->httpResponse->getBaseUrl();
            $this->template->lang = $this->lang;
            $this->template->randomToken = Visio\Utilities::createToken();

            $controller = new \stdClass();
            $controller->args = $this->args;
            $controller->lang = $this->lang;

            $this->template->_controller = $controller;
            $this->template->_parent = $this;

            foreach ($this->components as $identifier => $component) {
                $this->template->$identifier = $component;
            }

            $data = $this->template->getOutput();
        }

        if (class_exists($responseClass, true)) {
            $respondHandler = new $responseClass($data);

            if ($respondHandler instanceof Visio\Application\IResponse) {
                return new Visio\Application\Response($respondHandler);
            } else {
                throw new Visio\Exception\Response("Invalid respond class '" . $responseClass . "'! Respond class must implements IResponse.", E_USER_ERROR);
            }
        } else {
            throw new Visio\Exception\Response("Invalid respond type '" . $responseClass . "'!", E_USER_ERROR);
        }
    }

    /**
     * taskBeforeRun
     */
    public function taskBeforeRun() {
        parent::taskBeforeRun();

        $action = Visio\Utilities\String::ucfirst($this->args['action']);

        $this->setTemplate($this->name, $action);

        // Default variables
        $this->template->messages = array();

        if ($this->container->isRegistered("Translate")) {
            $enableSwitching = $this->container->applicationConfig->get("enableSwitching", "Translate");
            $this->translate = $this->container->translate;
            $this->translate->driverName = $this->container->applicationConfig->get("driver", "Translate");

            if ($enableSwitching == true) {
                $this->translate->lang = $this->lang;
            }

            $this->container->translate = $this->translate;
        }

        if ($this->container->isRegistered("Session")) {
            $this->session = $this->container->session;
        }

        $this->initializeMessages();
    }

    /**
     * Render controller and return respond
     *
     * @return Visio\Application\IResponse
     * @throws Visio\Exception\Controller if template for controller not found
     */
    public function taskAfterRun() {
        parent::taskAfterRun();

        if ($this->outputSend !== true && $this->preventExecution !== true) {
            if ($this->ajax === true) {

                if ($this->getQuery("notify")) {
                    $parts = explode("/", $this->getQuery("notify"));

                    $object = $this->getComponent($parts[0]);
                    $container = new Visio\Application\Component\Container($object, $this->container->http->request, $parts[0]);
                    $result = $container->run((isset($parts[1]) ? $parts[1] : "renderIndex"));

                    return $this->execute($result, Visio\Application\ResponseType::HTML);
                }

                if ($this->template instanceof Visio\Template && empty($this->json)) {
                    return $this->execute($this->template, Visio\Application\ResponseType::HTML);
                }

                if (!empty($this->json)) {
                    return $this->execute($this->json, Visio\Application\ResponseType::JSON);
                }

                if (!empty($this->template) && is_string($this->template)) {
                    return $this->execute($this->template, Visio\Application\ResponseType::HTML);
                }

                if (!$this->template instanceof Visio\Template && empty($this->json)) {
                    throw new Visio\Exception\Controller("Can not load template and JSON respond is empty!");
                }
            } else {
                if (empty($this->template)) {
                    throw new Visio\Exception\Controller('Undefined template of controller!');
                }

                return $this->execute($this->template, Visio\Application\ResponseType::HTML);
            }
        }
    }

    /**
     * Attach component to current control
     *
     * @param Controller\Component $component
     * @param $identifier
     * @param bool $override
     * @throws \Visio\Exception\Controller
     */
    public function attachComponent(Visio\UI\Controller\Component $component, $identifier, $override = false) {
        $component->lang = $this->lang;
        $component->args = $this->args;
        $component->container = $this->container;
        $component->parent = $this;
        $component->httpRequest = $this->httpRequest;
        $component->httpResponse = $this->httpResponse;

        if (isset($this->components[$identifier]) && $override === false) {
            throw new Visio\Exception\Controller("Can not attach component! Identifier in use!");
        }

        $component->taskOnAttach();

        $this->components[$identifier] = $component;
    }

    /**
     * Detach component by identifier
     *
     * @param string $identifier
     */
    public function detachComponent($identifier) {
        unset($this->components[$identifier]);
    }

    /**
     * Get component by identifier
     *
     * @param $identifier
     * @return \Visio\UI\Controller\Component
     * @throws \Visio\Exception\Controller
     */
    public function getComponent($identifier) {
        if (isset($this->components[$identifier])) {
            return $this->components[$identifier];
        } else {
            throw new Visio\Exception\Controller("Can not get undefined component!");
        }
    }

    /**
     * Check if component (by identifier) is attached to controller
     *
     * @param $identifier
     * @return bool
     */
    public function isComponentAttached($identifier) {
        return (isset($this->components[$identifier]));
    }
}
