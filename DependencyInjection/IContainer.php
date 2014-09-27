<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\DependencyInjection;

/**
 * Visio\DependencyInjection\IContainer
 *
 * @package Visio\DependencyInjection
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IContainer {
    public function insert($key, $object, $shared = false);

    public function obtain($key);

    public function remove($key);
}