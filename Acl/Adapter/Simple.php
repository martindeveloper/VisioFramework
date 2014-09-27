<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Acl\Adapter;

use Visio;

/**
 * Adapter for ACL handling - using PHP hardcoded rules
 *
 * @package Visio\Acl\Adapter
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Simple extends Visio\Object implements Visio\Acl\IAdapter {

    /**
     * @var array $permissions
     */
    private $permissions;

    /**
     * @param Visio\Acl\Role $role
     */
    public function addRole(Visio\Acl\Role $role) {
        $this->permissions[$role->roleName] = array();
    }

    /**
     * @param Visio\Acl\Role $role
     */
    public function deleteRole(Visio\Acl\Role $role) {
        unset($this->permissions[$role->roleName]);
    }

    /**
     * @param Visio\Acl\Role $role
     * @param Visio\Acl\Action $action
     */
    public function allow(Visio\Acl\Role $role, Visio\Acl\Action $action) {
        $this->permissions[$role->roleName][$action->namespace][$action->action] = true;
    }

    /**
     * @param Visio\Acl\Role $role
     * @param Visio\Acl\Action $action
     */
    public function deny(Visio\Acl\Role $role, Visio\Acl\Action $action) {
        $this->permissions[$role->roleName][$action->namespace][$action->action] = false;
    }

    /**
     * Get role ID
     *
     * @param Visio\Acl\Role $role
     */
    public function getRoleID(Visio\Acl\Role $role) {
        return 0;
    }

    /**
     * @param \Visio\Acl\Role $role
     * @param \Visio\Acl\Action $action
     */
    public function isAllowed(Visio\Acl\Role $role, Visio\Acl\Action $action) {
        if (isset($this->permissions[$role->roleName][$action->namespace][$action->action])) {
            return $this->permissions[$role->roleName][$action->namespace][$action->action];
        } else {
            return false;
        }
    }
}