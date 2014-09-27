<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\DependencyInjection;

use Visio;

/**
 * Interface for services. Every service needs to implement this interface!
 *
 * @package Visio\DependencyInjection
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IService {
    public function __invoke(Visio\DependencyInjection\IContainer $container);
}