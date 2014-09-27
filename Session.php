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
 * Session handling class.
 * Support switching storage by runtime config file.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Session extends Visio\Object {

    /**
     * Status of session
     * @var bool
     * */
    private $started = false;

    /**
     * Options
     * @var array
     * */
    private $options = array();

    /**
     * @var Visio\Session\IStorage object
     * */
    private $storage;

    /**
     * @param Session\IStorage $storage
     * @param DependencyInjection\IContainer $container
     */
    public function __construct(Visio\Session\IStorage $storage, Visio\DependencyInjection\IContainer $container) {
        if ($this->isStarted() === false) {
            //header('P3P: CP="NON DSP COR NOR"');

            $allowPhpStart = $container->applicationConfig->get('allowPhpStart', 'Session');

            /*
             * TODO: Multiple session creation while using Components!
            $session_id = session_id();
            if (!empty($session_id) && $allowPhpStart === false) {
                // Destroy any existing sessions started with session.auto_start
                session_unset();
                session_destroy();
            }
            */

            $this->storage = new Visio\Session\Storage($storage);
            $this->start();
        }
    }

    /**
     * start()
     */
    public function start() {
        $this->storage->start($this->options);
        $this->started = true;
    }

    /**
     * read()
     *
     * @param mixed $var
     * @param string $namespace
     */
    public function read($var, $namespace = 'Default') {
        return $this->storage->read($var, $namespace);
    }

    /**
     * getNamespace()
     *
     * @param string $namespace
     * @return mixed
     */
    public function getNamespace($namespace = 'Default') {
        return $this->storage->getNamespace($namespace);
    }

    /**
     * write()
     *
     * @param string $var
     * @param mixed $val
     * @param string $namespace
     */
    public function write($var, $val, $namespace = 'Default') {
        return $this->storage->write($var, $val, $namespace);
    }

    /**
     * Check if variable exist in session
     *
     * @param string $var
     * @param string $namespace
     * @return mixed
     */
    public function exist($var, $namespace = 'Default') {
        return $this->storage->exist($var, $namespace);
    }

    /**
     * delete()
     *
     * @param string $var
     * @param string $namespace
     */
    public function delete($var, $namespace = 'Default') {
        return $this->storage->delete($var, $namespace);
    }

    /**
     * destroy()
     *
     * @return bool
     */
    public function destroy() {
        $this->started = false;
        return $this->storage->destroy();
    }

    /**
     * close()
     *
     * @return bool
     */
    public function close() {
        if ($this->isStarted()) {
            return $this->storage->close();
        }

        return true;
    }

    /**
     * isStarted()
     *
     * @return bool
     */
    public function isStarted() {
        return (bool)$this->started;
    }

    /**
     * __destroy()
     */
    public function __destroy() {
        $this->close();
    }

    /**
     * __set()
     *
     * @param string $var
     * @param mixed $val
     */
    public function __set($var, $val) {
        $this->write($var, $val);
    }

    /**
     * __get()
     *
     * @param string $var
     * @return mixed
     */
    public function __get($var) {
        return $this->read($var);
    }

}