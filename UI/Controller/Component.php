<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\UI\Controller;

use Visio;

/**
 * Controller represents a static web page component
 *
 * @package Visio\Application
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Component extends Visio\UI\Controller implements Visio\UI\IControl {

    /**
     * @var bool $preventExecution
     */
    protected $preventExecution = true;

    /**
     * @var Visio\UI\Controller
     */
    protected $parent;

    /**
     * setTemplate
     *
     * @param string $controller
     * @param string $action
     * @return void
     */
    public function setTemplate($controller, $action) {
        parent::setTemplate($controller, $action);
    }

    /**
     * taskStartup
     */
    public function taskStartup() {

    }

    /**
     * taskBeforeRun
     */
    public function taskBeforeRun() {
        $parts = explode("\\", (string)$this);
        $this->name = $parts[count($parts) - 1];

        parent::taskBeforeRun();
    }

    /**
     * taskAfterRun
     */
    public function taskAfterRun() {
        if ($this->ajax === true) {
            if (is_array($this->json)) {
                // If is AJAX, send JSON respond
                return $this->execute($this->json, Visio\Application\ResponseType::JSON);
            }

            if ($this->template instanceof Visio\Template) {
                return $this->execute($this->template, Visio\Application\ResponseType::HTML);
            }
        }

        // Not AJAX
        if ($this->template instanceof Visio\Template) {
            // Request by Visio Components (lazy loading)
            if ($this->httpRequest->getHeader("X-Requested-With", "0") == "VisioComponentsAjax") {
                return $this->execute($this->template, Visio\Application\ResponseType::HTML);
            } else {
                // Visio Components (not lazy loading)
                return $this->execute($this->template, Visio\Application\ResponseType::HTML);
            }
        }
    }

    /**
     * @param Visio\UI\IControl $parent
     */
    public function setParent(Visio\UI\IControl $parent) {
        $this->parent = $parent;
    }

    /**
     * On attach event/task
     */
    public function taskOnAttach() {

    }
}