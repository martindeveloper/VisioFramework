<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Main ACL class
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Acl extends Visio\Object {

    /**
     * @var array $rolesCache
     */
    private $rolesCache = array();

    /**
     * @var Visio\Acl\IAdapter $adapter
     */
    private $adapter;

    /**
     * @param Acl\IAdapter $adapter
     */
    public function __construct(Visio\Acl\IAdapter $adapter) {
        $this->adapter = $adapter;
    }

    /**
     * @param Visio\Acl\Role $role
     * @return Visio\Acl
     */
    public function addRole(Visio\Acl\Role $role) {
        $this->adapter->addRole($role);

        return $this;
    }

    /**
     * @param Visio\Acl\Role $role
     * @return Visio\Acl
     */
    public function deleteRole(Visio\Acl\Role $role) {
        $this->adapter->deleteRole($role);

        return $this;
    }

    /**
     * @param string $role
     * @param string|array $action
     * @param string $namespace
     * @return Visio\Acl
     */
    public function allow($role, $action, $namespace = "Default") {
        $role = $this->getRole($role);

        if (is_array($action)) {
            foreach ($action as $singleAction) {
                $this->adapter->allow($role, new Visio\Acl\Action($singleAction, $namespace));
            }
        } else {
            $this->adapter->allow($role, new Visio\Acl\Action($action, $namespace));
        }

        return $this;
    }

    /**
     * @param string $role
     * @param string|array  $action
     * @param string $namespace
     * @return Visio\Acl
     */
    public function deny($role, $action, $namespace = "Default") {
        $role = $this->getRole($role);

        if (is_array($action)) {
            foreach ($action as $singleAction) {
                $this->adapter->deny($role, new Visio\Acl\Action($singleAction, $namespace));
            }
        } else {
            $this->adapter->deny($role, new Visio\Acl\Action($action, $namespace));
        }

        return $this;
    }

    /**
     * @param string $role
     * @param string $action
     * @param string $namespace
     * @return bool
     */
    public function isAllowed($role, $action, $namespace = "Default") {
        $role = $this->getRole($role);

        if (is_array($action)) {
            $checksum = count($action);
            $current = 0;

            foreach ($action as $singleAction) {
                $current += (int)$this->adapter->isAllowed($role, new Visio\Acl\Action($singleAction, $namespace));
            }

            return ($checksum == $current);
        } else {
            return $this->adapter->isAllowed($role, new Visio\Acl\Action($action, $namespace));
        }
    }

    /**
     * @param string $role
     */
    private function getRole($role) {
        if (!isset($this->rolesCache[$role])) {
            $roleObject = new Visio\Acl\Role($role);
            $roleObject->id = $this->adapter->getRoleID($roleObject);

            $this->rolesCache[$roleObject->roleName] = $roleObject;
        }

        return $this->rolesCache[$role];
    }
}