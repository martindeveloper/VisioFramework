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
 * Role entity class
 *
 * @package Visio\Acl
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Role extends Visio\Object {

    /**
     * @var string $roleName
     */
    private $roleName;

    /**
     * @var int $id
     */
    public $id = 0;

    /**
     * @var Visio\Acl\Role|null $parent
     */
    private $parent = null;

    /**
     * __construct()
     *
     * @param string $roleName
     * @param Visio\Acl\Role $parent
     */
    public function __construct($roleName, Visio\Acl\Role $parent = null) {
        $this->roleName = $roleName;
        $this->parent = $parent;
    }

    /**
     * Have role parent?
     *
     * @return bool
     */
    public function hasParent() {
        return ($this->parent != null);
    }

    /**
     * Set new parent to role
     *
     * @param Visio\Acl\Role $parent
     */
    public function setParent(Visio\Acl\Role $parent) {
        $this->parent = $parent;
    }

    /**
     * @return null|Visio\Acl\Role
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getRoleName() {
        return $this->roleName;
    }

    /**
     * Refresh ID
     *
     * @param Visio\Acl\IAdapter $adapter
     */
    public function refreshID(Visio\Acl\IAdapter $adapter) {
        $this->id = $adapter->getRoleID($this);
    }
}