<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Acl;

use Visio;

/**
 * Action entity class
 *
 * @package Visio\Acl
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Action extends Visio\Object {

    /**
     * @var string $action
     */
    private $action;

    /**
     * @var string $namespace
     */
    private $namespace;

    /**
     * __construct()
     *
     * @param string $action
     * @param string $namespace
     */
    public function __construct($action, $namespace = "Default") {
        $this->action = $action;
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }
}