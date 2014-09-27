<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Extensibility;

/**
 * Visio\Extensibility\IExtensible
 *
 * @package Visio\Extensibility
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IExtensible {
    public function runExtensions($pattern);
}