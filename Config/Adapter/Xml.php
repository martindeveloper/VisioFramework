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
 * XML adapter for Visio\Config.
 *
 * @package Visio\Config
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Xml implements Visio\Config\IAdapter {

    /**
     * SimpleXMLElement object of XML config file
     *
     * @var \SimpleXMLElement
     */
    protected $obj = null;

    /**
     * __construct()
     *
     * @param Visio\FileSystem\File $file
     * @throws Visio\Exception\FileNotFound if config file not found
     */
    public function __construct(Visio\FileSystem\File $file) {
        $this->obj = simplexml_load_string($file->content);
    }

    /**
     * get()
     *
     * @param string $name
     * @param string $namespace
     * @return mixed
     */
    public function get($name, $namespace) {
        if (isset($this->obj->$namespace->$name)) {
            return $this->obj->$namespace->$name;
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
        if (isset($this->obj->$name)) {
            return $this->obj->$name;
        } else {
            return false;
        }
    }

}