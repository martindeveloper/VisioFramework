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
 * Cache class.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Cache extends Visio\Object {

    /**
     * @var string
     */
    public static $defaultNamespace = "Default";

    /**
     * @var int
     */
    public static $defaultExpire = 3600;

    /**
     * @var Visio\Cache\IStorage object
     */
    private $storage;

    /**
     * @var bool $enabled
     */
    private $enabled;

    /**
     * @param Cache\IStorage $storage
     * @param bool $enabled
     */
    public function __construct(Visio\Cache\IStorage $storage, $enabled = true) {
        $this->storage = $storage;
        $this->enabled = $enabled;
    }

    /**
     * get()
     *
     * @param mixed $key
     * @param int $expiration
     * @param string $namespace
     * @return mixed
     */
    public function get($key, $expiration = null, $namespace = null) {
        if ($this->enabled == false) {
            return null;
        }

        $expiration = ($expiration == null) ? self::$defaultExpire : $expiration;
        $namespace = ($namespace == null) ? self::$defaultNamespace : $namespace;

        return $this->storage->get($key, $namespace, $expiration);
    }

    /**
     * set()
     *
     * @param mixed $key
     * @param mixed $data
     * @param string $namespace
     * @return bool
     */
    public function set($key, $data, $namespace = null) {
        if ($this->enabled == false) {
            return null;
        }

        $namespace = ($namespace == null) ? self::$defaultNamespace : $namespace;

        return $this->storage->set($key, $namespace, $data);
    }

    /**
     * clear()
     *
     * @param mixed $key
     * @param string $namespace
     * @return bool
     */
    public function clear($key, $namespace = null) {
        $namespace = ($namespace == null) ? self::$defaultNamespace : $namespace;

        return $this->storage->clear($key, $namespace);
    }

    /**
     * purge()
     *
     * @return bool
     */
    public function purge() {
        return $this->storage->purge();
    }

}