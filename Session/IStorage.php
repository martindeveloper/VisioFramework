<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Session;

/**
 * Visio\Session\IStorage
 *
 * @package Visio\Session
 * @author Martin Pernica
 * @version 3.0
 */
interface IStorage {

    public function start($options = array());

    public function read($var, $namespace = 'default');

    public function getNamespace($namespace = 'default');

    public function write($var, $val, $namespace = 'default');

    public function delete($var, $namespace = 'default');

    public function destroy();

    public function close();
}