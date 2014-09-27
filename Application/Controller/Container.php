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
 * Container for controller
 *
 * @package Visio\Application\Controller
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Container extends Visio\Object {

    /**
     * @var Visio\UI\Controller $currentObject
     */
    public $currentObject;

    /**
     * @var \Visio\DependencyInjection\IContainer
     */
    private $di;

    /**
     * @param Visio\UI\Controller $controller
     * @param Visio\DependencyInjection\IContainer $di
     */
    public function __construct(Visio\UI\Controller $controller, Visio\DependencyInjection\IContainer $di) {
        $this->currentObject = $controller;
        $this->di = $di;
    }

    /**
     * Try to inject dependency data to current object
     *
     * @param mixed $dependency
     * @param bool $silent
     * @throws Visio\Exception if injecting will fails and $silent is set to false
     */
    public function inject(array $dependency, $silent = true) {
        foreach ($dependency as $property => $value) {
            try {
                $this->currentObject->$property = $value;
            } catch (Visio\Exception\MemberAccess $ex) {
                if ($silent === false) {
                    throw new Visio\Exception("Can not inject data to property '" . $property . "'!");
                }
            }
        }

        $this->di->injectByDocComments($this->currentObject);
    }

    /**
     * Start task on controller
     *
     * @param $taskName
     * @param array $arguments
     * @return mixed
     * @throws \Visio\Exception\Controller
     */
    public function startTask($taskName, $arguments = array()) {
        try {
            $result = $this->callMethod("task" . Visio\Utilities\String::ucfirst($taskName), $arguments);

            return $result;
        } catch (Visio\Exception\Task $ex) {
            throw new Visio\Exception\Controller("Can not start task '" . $ex->getErrorMessage() . "' on controller!");
        }
    }

    /**
     * Render action
     *
     * @param $actionName
     * @return mixed
     */
    public function renderAction($actionName) {
        try {
            $actionName = $this->buildActionName($actionName);
            $arguments = [];

            $method = new \ReflectionMethod($this->currentObject->getClassName(), $actionName);
            $parameters = $method->getParameters();

            foreach ($parameters as $parameter) {
                $paramType = $parameter->getClass()->name;

                $arguments[] = $this->di->obtainByType($paramType);
            }

            return $this->callMethod($actionName, $arguments);
        } catch (Visio\Exception $ex) {
            //throw new Visio\Exception\Controller("Internal controller error '" . $ex->getMessage() . "'!");
            return $ex->showErrorMessage();
        }
    }

    /**
     * @param $actionName
     * @param bool $ajax
     * @return bool
     */
    public function actionExist($actionName, $ajax = false) {
        $actionName = $this->buildActionName($actionName);

        if ($ajax === true) {
            $actionName .= "Ajax";
        }

        return is_callable(array($this->currentObject,
                                 $actionName));
    }

    /**
     * Call method on object
     *
     * @param $methodName
     * @param array $arguments
     * @return mixed
     */
    private function callMethod($methodName, $arguments = array()) {
        return call_user_func_array(array($this->currentObject,
                                          $methodName), $arguments);
    }

    /**
     * Build action name
     *
     * @param string $action
     * @return string
     */
    private function buildActionName($action) {
        if ($this->currentObject instanceof Visio\UI\Controller\Rest) {
            if (isset($_SERVER['REQUEST_METHOD'])) {
                $method = Visio\Utilities\String::upper($_SERVER['REQUEST_METHOD']);
            } else {
                $method = "Get";
            }

            $action = $action . $method;
        }

        return $action;
    }

}
