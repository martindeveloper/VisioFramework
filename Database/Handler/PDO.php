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
 * PDO handler to Visio\Database
 *
 * @package Visio\Database\Handler
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class PDO extends Visio\Object implements Visio\Database\IHandler {

    /**
     * @var \PDO $dbo
     */
    public $dbo;

    /**
     *
     */
    public function __construct() {
        if (!extension_loaded("pdo")) {
            throw new Visio\Exception\Database("Can not use PDO service without PDO PHP extension!");
        }
    }

    /**
     * Connect to database using PDO extension
     *
     * @param array $credentials
     * @throws \Visio\Exception\Database
     *
     * @return \PDO
     */
    public function connect(array $credentials) {
        $connectionString = $credentials["driver"] . ":host=" . $credentials["host"] . ";";

        if (!empty($credentials["database"])) {
            $connectionString .= "dbname=" . $credentials["database"] . ";";
        }

        try {
            $this->dbo = new \PDO($connectionString, $credentials["user"], $credentials["password"]);
            $this->dbo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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