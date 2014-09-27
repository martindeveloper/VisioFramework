<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Template;

use Visio;

/**
 * Visio Kiwi templates transpiler
 *
 * @package Visio\Template
 * @author Martin Pernica
 * @version 3.0
 * @access public
 * @todo Not completed yet!
 */
class Transpiler extends Visio\Object {

    /**
     * @var \Visio\Cache
     */
    private $cache;

    /**
     * @param \Visio\Cache $cache
     */
    public function __construct(Visio\Cache $cache) {
        $this->cache = $cache;
    }

    /**
     * @param \Visio\Template $template
     */
    public function isCacheValid(Visio\Template $template) {

    }
}