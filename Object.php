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
 * Parent of all object in Visio
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Object {

    /**
     * @var array
     */
    public $_methods;

    /**
     * @var Visio\Object
     */
    protected static $instance;

    /**
     * Get reflection object of current class
     *
     * @return \ReflectionClass
     */
    public function getReflection() {
        return new \ReflectionClass($this);
    }

    /**
     * Get current class name
     *
     * @return string
     */
    public function getClassName() {
        return get_class($this);
    }

    /**
     * Get class name using 'Late Static Binding'
     * @return string
     */
    public static function getCalledClass() {
        return get_called_class();
    }

    /**
     * Get property and try to call getters
     *
     * @param $name
     * @return mixed
     * @throws Exception\MemberAccess
     */
    public function __get($name) {
        $class = get_class($this);

        if ($name === '') {
            throw new Visio\Exception\MemberAccess("Invalid property name " . $class . "::\$" . $name . "!");
        }

        if (!isset($this->_methods)) {
            $this->_methods = array_flip(get_class_methods($class));
        }

        $method = 'get' . Visio\Utilities\String::ucfirst($name);

        if (isset($this->_methods[$method])) {
            $value = $this->$method();

            return $value;
        }

        $method = 'is' . Visio\Utilities\String::ucfirst($name);
        if (isset($this->_methods[$method])) {
            $value = $this->$method();

            return $value;
        }

        throw new Visio\Exception\MemberAccess("Cannot read an undeclared property " . $class . "::\$" . $name . "!");
    }

    /**
     * Set property and try to call setters
     *
     * @param $name
     * @param $value
     * @throws Exception\MemberAccess
     */
    public function __set($name, $value) {
        $class = get_class($this);

        if ($name === '') {
            throw new Visio\Exception\MemberAccess("Invalid property name " . $class . "::\$" . $name . "!");
        }

        if (!isset($this->_methods)) {
            $this->_methods = array_flip(get_class_methods($class));
        }

        $method = 'set' . Visio\Utilities\String::ucfirst($name);

        if (isset($this->_methods[$method])) {
            $value = $this->$method($value);
            return;
        }

        throw new Visio\Exception\MemberAccess("Cannot write to undeclared or read only property " . $class . "::\$" . $name . "!");
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return $this->getClassName();
    }

    /**
     * @return string
     */
    public function generateUniqueId() {
        return spl_object_hash($this);
    }

    /**
     * Get single instance of class (Singleton)
     *
     * @return Visio\Object
     */
    public static function getInstance() {
        if (self::$instance == null) {
            $class = get_called_class(); //only for PHP 5.3
            self::$instance = new $class();
        }
        return self::$instance;
    }

}