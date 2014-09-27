<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Config;

/**
 * Visio\Config\IAdapter
 *
 * @package Visio\Config
 * @author Martin Pernica
 * @version 3.0
 */
interface IAdapter {

    /**
     * Get config variable from namespace by name
     * 
     * @param string $name
     * @param string $namespace
     */
    public function get($name, $namespace);

    /**
     * Get config namespace
     * 
     * @param string $name
     */
    public function getNamespace($name);
}