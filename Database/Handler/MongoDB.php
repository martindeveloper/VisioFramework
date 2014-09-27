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
 * MongoDB handler for Visio\Database
 *
 * @package Visio\Database\Handler
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class MongoDB extends Visio\Object implements Visio\Database\IHandler {

    /**
     * @var \Mongo $dbo
     */
    public $dbo;

    /**
     *
     */
    public function __construct() {
        if (!extension_loaded("mongo")) {
            throw new Visio\Exception\Database("Can not use MongoDB service without MongoDB PHP extension!");
        }
    }

    /**
     * Connect to Mongo database using native MongoDB extension
     *
     * @param array $credentials
     * @throws \Visio\Exception\Database
     */
    public function connect(array $credentials) {
        $connectionString = $this->buildConnectionString($credentials["host"], $credentials["user"], $credentials["password"]);

        try {
            $this->tryConnect($connectionString, $credentials["database"]);
        } catch (\MongoException $ex) {
            $retries = 3;

            for ($i = 1; $i <= $retries; $i++) {
                try {
                    $this->tryConnect($connectionString, $credentials["database"]);
                } catch (\MongoException $ex) {
                    continue;
                }

                return;
            }
            throw new Visio\Exception\Database("Can not connect to MongoDB server or select collection! Failed with message: " . $ex->getMessage());
        }
    }

    private function tryConnect($connectionString, $database) {
        $class = '\MongoClient';

        if (!class_exists($class)) {
            $class = '\Mongo';
        }

        $this->dbo = new $class($connectionString);
        $this->dbo = $this->dbo->{$database};
    }

    /**
     * Build connection string
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @return string
     */
    private function buildConnectionString($host = "localhost", $user = "", $password = "") {
        $connectionString = "mongodb://";

        if (!empty($user)) {
            $connectionString .= $user;
        }

        if (!empty($password)) {
            $password = !empty($user) ? ":" . $password : $password;

            $connectionString .= $password;
        }

        if (!empty($host)) {
            $host = !empty($user) ? "@" . $host : $host;

            $connectionString .= $host;
        }

        return $connectionString;
    }

    /**
     * Close connection
     *
     * @param \Visio\Callback $callback
     */
    public function close(Visio\Callback $callback = null) {
        unset($this->pdo);

        if ($callback !== null) {
            $callback();
        }
    }
}