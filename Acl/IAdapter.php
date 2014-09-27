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
 * Visio\Acl\IAdapter
 *
 * @package Visio\Acl
 * @author Martin Pernica
 * @version 3.0
 */
interface IAdapter {
    public function addRole(Visio\Acl\Role $role);

    public function deleteRole(Visio\Acl\Role $role);

    public function allow(Visio\Acl\Role $role, Visio\Acl\Action $action);

    public function deny(Visio\Acl\Role $role, Visio\Acl\Action $action);

    public function isAllowed(Visio\Acl\Role $role, Visio\Acl\Action $action);

    public function getRoleID(Visio\Acl\Role $role);
}