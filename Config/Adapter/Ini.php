<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Config\Adapter;

use Visio;

/**
 * INI adapter for Visio\Config.
 *
 * @package Visio\Config
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Ini implements Visio\Config\IAdapter {

    /**
     * Array representation of INI config file
     *
     * @var array $obj
     */
    protected $obj = null;

    /**
     * __construct()
     *
     * @param Visio\FileSystem\File $file
     * @throws Visio\Exception\FileNotFound if config file not found
     */
    public function __construct(Visio\FileSystem\File $file) {
        $this->obj = parse_ini_file($file->fullPath, true);
    }

    /**
     * get()
     *
     * @param string $name
     * @param string $namespace
     * @return mixed
     */
    public function get($name, $namespace) {
        if (isset($this->obj[$namespace][$name])) {
            return $this->obj[$namespace][$name];
        } else {
            return false;
        }
    }

    /**
     * getNamespace()
     *
     * @param string $name
     * @return mixed
     */
    public function getNamespace($name) {
        if (isset($this->obj[$name])) {
            return (object)$this->obj[$name];
        } else {
            return false;
        }
    }

}