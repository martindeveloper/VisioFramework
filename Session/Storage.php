<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Session;

use Visio;

/**
 * Session storage class
 *
 * @package Visio\Session
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Storage extends Visio\Object {

    /**
     * @return Visio\Session\IStorage
     */
    private $handler;

    /**
     * @param $storage
     * @throws Visio\Exception\FileNotFound
     */
    public function __construct(Visio\Session\IStorage $storage) {
        $this->handler = $storage;
    }

    /**
     * start()
     *
     * @param array $options
     * @return object
     */
    public function start($options) {
        return $this->handler->start($options);
    }

    /**
     * read()
     *
     * @param string $var
     * @param string $namespace
     * @return string
     */
    public function read($var, $namespace = 'default') {
        return $this->handler->read($var, $namespace);
    }

    /**
     * getNamespace()
     *
     * @param string $namespace
     * @return mixed
     */
    public function getNamespace($namespace = 'default') {
        return $this->handler->getNamespace($namespace);
    }

    /**
     * write()
     *
     * @param string $var
     * @param mixed $val
     * @param string $namespace
     * @return bool
     */
    public function write($var, $val, $namespace = 'default') {
        return $this->handler->write($var, $val, $namespace);
    }

    public function exist($var, $namespace = 'default') {
        return $this->handler->exist($var, $namespace);
    }

    /**
     * delete()
     *
     * @param string $var
     * @param string $namespace
     * @return bool
     */
    public function delete($var, $namespace = 'default') {
        return $this->handler->delete($var, $namespace);
    }

    /**
     * destroy()
     *
     * @return bool
     */
    public function destroy() {
        return $this->handler->destroy();
    }

    /**
     * close()
     *
     * @return bool
     */
    public function close() {
        return $this->handler->close();
    }

}