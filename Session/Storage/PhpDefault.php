<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Session\Storage;

use Visio;

/**
 * PHP default session storage for Visio\Session.
 *
 * @package Visio\Session
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class PhpDefault extends Visio\Object implements Visio\Session\IStorage {

    /**
     * start()
     * 
     * @param array $options
     * @return void
     */
    public function start($options = array()) {
        foreach ($options as $key => $val) {
            ini_set("session." . $key, $val);
        }

        @session_start();
    }

    /**
     * read()
     * 
     * @param string $var
     * @param string $namespace
     * @return mixed
     */
    public function read($var, $namespace = 'default') {
        return isset($_SESSION[$namespace][$var]) ? $_SESSION[$namespace][$var] : null;
    }

    /**
     * getNamespace()
     * 
     * @param string $namespace
     * @return mixed
     * @throws Visio\Exception\Session
     */
    public function getNamespace($namespace = 'default') {
        if (isset($_SESSION[$namespace]))
            return $_SESSION[$namespace];

        throw new Visio\Exception\Session("Can not found '" . $namespace . "' session namespace!");
    }

    /**
     * write()
     * 
     * @param string $var
     * @param mixed $val
     * @param string $namespace
     * @return void
     */
    public function write($var, $val, $namespace = 'default') {
        $_SESSION[$namespace][$var] = $val;
    }

    /**
     * exist()
     * 
     * @param string $var
     * @param string $namespace
     * @return bool
     */
    public function exist($var, $namespace = 'default') {
        return isset($_SESSION[$namespace][$var]);
    }

    /**
     * delete()
     * 
     * @param string $var
     * @param string $namespace
     * @return void
     */
    public function delete($var, $namespace = 'default') {
        unset($_SESSION[$namespace][$var]);
    }

    /**
     * destroy()
     * 
     * @return bool
     */
    public function destroy() {
        return session_destroy();
    }

    /**
     * close()
     * 
     * @return void
     */
    public function close() {
        session_write_close();
    }

}