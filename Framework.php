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
 * Framework info class
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
final class Framework {

    /**
     * Name of framework
     *
     * @const Visio\Framework::NAME
     */
    const NAME = "Visio";

    /**
     * Full name of framework
     *
     * @const Visio\Framework::FULL_NAME
     */
    const FULL_NAME = "Visio framework 3.0.0 - The effiency of easyness";

    /**
     * Version of framework
     *
     * @const Visio\Framework::VERSION
     */
    const VERSION = "3.0.0 alpha";

    /**
     * Active build of framework
     *
     * @const Visio\Framework::BUILD
     */
    const BUILD = "2316-01/24/2012";

    /**
     * __construct()
     *
     * @throws Visio\Exception\Logical if this class tried to be instantiated
     */
    final public function __construct() {
        throw new Visio\Exception\Logical("Cannot instantiate static class " . get_class($this));
    }

}