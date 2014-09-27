<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Application\Controller;

use Visio;

/**
 * Controllers locator
 *
 * @package Visio\Application\Controller
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Locator extends Visio\Object {

    /**
     * @var Visio\Application\Controller\Container $controllerContainer
     */
    private $controllerContainer;

    /**
     * @var string $action
     */
    private $action;

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    private $container;

    /**
     * __construct()
     *
     * @param string $controllerName
     * @param string $methodName
     * @param Visio\DependencyInjection\IContainer $container
     * @throws Visio\Exception\Application if can not locate specified controller
     */
    public function __construct($controllerName, $methodName, Visio\DependencyInjection\IContainer $container) {
        $controllerName = "\\Application\\" . $controllerName;
        $this->container = $container;

        if (class_exists($controllerName, true)) {
            $controller = new $controllerName();
            $controller->container = $this->container;

            $this->controllerContainer = new Visio\Application\Controller\Container($controller, $container);
            $this->action = $methodName;
        } else {
            throw new Visio\Exception\Application("Can not locate controller '" . $controllerName . "'!");
        }
    }

    /**
     * @param $actionName
     * @return bool
     */
    public function actionExist($actionName) {
        if ($this->container->http->request->getQueryIndex("notify") && $this->container->http->request->isAjax()) {
            return $this->controllerContainer->actionExist($actionName, false);
        }

        return $this->controllerContainer->actionExist($actionName, $this->container->http->request->isAjax());
    }

    /**
     * @param array $additionalDependency
     * @return mixed
     */
    public function execute(array $additionalDependency = array()) {
        $http = $this->container->http;

        $dependency = array("httpRequest" => $http->request,
                            "httpResponse" => $http->response);
        $dependency = array_merge($dependency, $additionalDependency);
        $dependency["ajax"] = $http->request->isAjax();

        $this->controllerContainer->inject($dependency);

        $this->controllerContainer->startTask("beforeRun");

        if ($this->controllerContainer->actionExist("taskStartup")) {
            $this->controllerContainer->startTask("startup");
        }

        //Notify by AJAX to component, without rendering controller
        if ($http->request->getQueryIndex("notify") && $http->request->isAjax()) {
            try {
                $respond = $this->controllerContainer->renderAction($this->action);

                $parts = explode("/", $http->request->getQueryIndex("notify"));

                $object = $this->controllerContainer->currentObject->getComponent($parts[0]);
                $container = new Visio\Application\Component\Container($object, $http->request, $parts[0]);
                $result = $container->run((isset($parts[1]) ? $parts[1] : "renderIndex"));

                return $result->send($http->response, $http->request, $this->container);
            } catch (Visio\Exception $ex) {
                $ex->showErrorMessage();
            }
        }

        //Render controller action as AJAX action
        if ($http->request->isAjax()) {
            $action = $this->action . "Ajax";
            if ($this->controllerContainer->actionExist($action)) {
                try {
                    $respond = $this->controllerContainer->renderAction($action);

                    if ($respond instanceof Visio\Application\IResponse) {
                        return $respond->send($http->response, $http->request, $this->container);
                    }
                } catch (Visio\Exception $ex) {
                    echo $ex->getErrorMessage();
                }
            }
        } else {
            try {
                $respond = $this->controllerContainer->renderAction($this->action);

                if ($respond instanceof Visio\Application\IResponse) {
                    return $respond->send($http->response, $http->request, $this->container);
                }
            } catch (Visio\Exception $ex) {
                $ex->showErrorMessage();
            }
        }

        $respond = $this->controllerContainer->startTask("afterRun");
        if ($respond instanceof Visio\Application\IResponse) {
            return $respond->send($http->response, $http->request, $this->container);
        }

        return $respond;
    }

    /**
     * Get current container
     *
     * @return Visio\Application\Controller\Container
     */
    public function getContainer() {
        return $this->controllerContainer;
    }

}
