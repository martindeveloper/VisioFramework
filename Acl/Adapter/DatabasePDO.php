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
 * Adapter for ACL handling - database using PDO
 *
 * @package Visio\Acl\Adapter
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class DatabasePDO extends Visio\Object implements Visio\Acl\IAdapter {

    /**
     * @var \PDO $database
     */
    private $database;

    /**
     * @var array $tables
     */
    private $tables;

    /**
     * __construct()
     *
     * @param \PDO $database
     * @param array $tables
     */
    public function __construct(\PDO $database, array $tables) {
        $this->database = $database;
        $this->tables = $tables;
    }

    /**
     * @param Visio\Acl\Role $role
     */
    public function addRole(Visio\Acl\Role $role) {
        $check = $this->database->prepare("SELECT id FROM " . $this->tables["roles"] . " WHERE role = :role");
        $check->bindValue(":role", $role->roleName, \PDO::PARAM_STR);

        try {
            $check->execute();
        } catch (\PDOException $exception) {
            throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
        }

        if ($check->fetchColumn() == 0) {
            $insert = $this->database->prepare("INSERT INTO " . $this->tables["roles"] . " (role) VALUES (:role)");
            $insert->bindValue(":role", $role->roleName, \PDO::PARAM_STR);

            try {
                $insert->execute();
            } catch (\PDOException $exception) {
                throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
            }
        } else {
            throw new Visio\Exception\Acl("Can not add existing role!");
        }
    }

    /**
     * @param Visio\Acl\Role $role
     */
    public function deleteRole(Visio\Acl\Role $role) {
        $delete = $this->database->prepare("DELETE FROM " . $this->tables["roles"] . " WHERE role = :role");
        $delete->bindValue(":role", $role->roleName, \PDO::PARAM_STR);

        try {
            $delete->execute();
        } catch (\PDOException $exception) {
            throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
        }
    }

    /**
     * @param Visio\Acl\Role $role
     * @param Visio\Acl\Action $action
     */
    public function allow(Visio\Acl\Role $role, Visio\Acl\Action $action) {
        $check = $this->database->prepare("SELECT id FROM " . $this->tables["permissions"] . " WHERE action = :action AND role = :role AND namespace = :namespace");
        $check->bindValue(":action", $action->action, \PDO::PARAM_STR);
        $check->bindValue(":role", $role->id, \PDO::PARAM_INT);
        $check->bindValue(":namespace", $action->namespace, \PDO::PARAM_STR);

        try {
            $check->execute();
        } catch (\PDOException $exception) {
            throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
        }

        if ($check->fetchColumn() != 0) {
            throw new Visio\Exception\Acl("Action '" . $action->action . "' is already allowed for role '" . $role->roleName . "'!");
        }

        $insert = $this->database->prepare("INSERT INTO " . $this->tables["permissions"] . " (role, action, namespace) VALUES (:role, :action, :namespace)");
        $insert->bindValue(":role", $role->id, \PDO::PARAM_INT);
        $insert->bindValue(":action", $action->action, \PDO::PARAM_STR);
        $insert->bindValue(":namespace", $action->namespace, \PDO::PARAM_STR);

        try {
            $insert->execute();
        } catch (\PDOException $exception) {
            throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
        }
    }

    /**
     * @param Visio\Acl\Role $role
     * @param Visio\Acl\Action $action
     */
    public function deny(Visio\Acl\Role $role, Visio\Acl\Action $action) {
        $delete = $this->database->prepare("DELETE FROM " . $this->tables["permissions"] . " WHERE role = :role AND action = :action AND namespace = :namespace");
        $delete->bindValue(":role", $role->id, \PDO::PARAM_INT);
        $delete->bindValue(":action", $action->action, \PDO::PARAM_STR);
        $delete->bindValue(":namespace", $action->namespace, \PDO::PARAM_STR);

        try {
            $delete->execute();
        } catch (\PDOException $exception) {
            throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
        }
    }

    /**
     * Get role ID
     *
     * @param Visio\Acl\Role $role
     */
    public function getRoleID(Visio\Acl\Role $role) {
        $select = $this->database->prepare("SELECT id FROM " . $this->tables["roles"] . " WHERE role = :role");
        $select->bindValue(":role", $role->roleName, \PDO::PARAM_STR);

        try {
            $select->execute();
            $temp = $select->fetch(\PDO::FETCH_ASSOC);

            if ($temp === false) {
                throw new Visio\Exception\Acl("Trying to get ID of non exist role!");
            }

            $role->id = (int)$temp["id"];

            return $role->id;
        } catch (\PDOException $exception) {
            throw new Visio\Exception\Acl("Error in PDO layer! Description: " . $exception->getMessage());
        }
    }

    /**
     * @param \Visio\Acl\Role $role
     * @param \Visio\Acl\Action $action
     */
    public function isAllowed(Visio\Acl\Role $role, Visio\Acl\Action $action) {
        $select = $this->database->prepare("SELECT id FROM " . $this->tables["permissions"] . " WHERE action = :action AND namespace = :namespace AND role = :role");
        $select->bindValue(":role", $role->id, \PDO::PARAM_INT);
        $select->bindValue(":action", $action->action, \PDO::PARAM_STR);
        $select->bindValue(":namespace", $action->namespace, \PDO::PARAM_STR);

        $select->execute();

        return ($select->fetch(\PDO::FETCH_ASSOC) != false);
    }
}