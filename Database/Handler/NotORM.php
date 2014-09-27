<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Database\Handler;

use Visio;

/**
 * NotORM handler to Visio\Database
 *
 * @package Visio\Database\Handler
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class NotORM extends Visio\Object implements Visio\Database\IHandler {

    /**
     * @var \NotORM $dbo
     */
    public $dbo;

    /**
     *
     */
    public function __construct() {
        if (!extension_loaded("pdo")) {
            throw new Visio\Exception\Database("Can not use NotORM service without PDO PHP extension!");
        }

        if (!class_exists("NotORM", true)) {
            throw new Visio\Exception\Database("NotORM class not found!");
        }
    }

    /**
     * Connect to database using PDO extension and with NotORM
     *
     * @param array $credentials
     * @throws \Visio\Exception\Database
     */
    public function connect(array $credentials) {
        try {
            $PDO = new Visio\Database\Handler\PDO();
            $PDO->connect($credentials);

            $this->dbo = new \NotORM($PDO->dbo);
        } catch (\PDOException $ex) {
            throw new Visio\Exception\Database("Can not connect to database server using PDO! Failed with message: " . $ex->getMessage());
        }
    }

    /**
     * Close connection
     *
     * @param \Visio\Callback $callback
     */
    public function close(Visio\Callback $callback = null) {
        unset($this->dbo);

        if ($callback !== null) {
            $callback();
        }
    }
}