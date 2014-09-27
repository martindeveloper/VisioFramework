<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Cache;

use Visio;

/**
 * Visio\Cache\IStorage
 *
 * @package Visio\Cache
 * @author Martin Pernica
 * @version 3.0
 */
interface IStorage {

    /**
     * Get cache item by key
     *
     * @param mixed $key
     * @param string $namespace
     * @param int $expiration
     */
    public function get($key, $namespace, $expiration);

    /**
     * Set cache item by key
     *
     * @param mixed $key
     * @param string $namespace
     * @param mixed $data
     */
    public function set($key, $namespace, $data);

    /**
     * Clear cache item by key
     *
     * @param mixed $key
     * @param string $namespace
     */
    public function clear($key, $namespace);

    /**
     * Purge whole cache
     */
    public function purge();

    /**
     * Check if item in cache is valid
     *
     * @param mixed $key
     * @param string $namespace
     * @param int $expiration
     */
    public function isValid($key, $namespace, $expiration);
}