<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Database;

use Visio;

/**
 * Visio\Database\IHandler
 *
 * @package Visio\FileSystem
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IHandler {
    public function connect(array $credentials);

    public function close(Visio\Callback $callback = null);
}