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
 * Class for callback creating
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
final class Callback extends Visio\Object {

    /**
     * @var callable
     */
    private $callback;

    /**
     * @param $callback
     * @param null $method
     * @throws Visio\Exception\InvalidArgument
     */
    public function __construct($callback, $method = null) {
        if ($method != null) {
            $callback = array($callback,
                              $method);
        }

        if (!is_callable($callback, true)) {
            throw new Visio\Exception\InvalidArgument("Not callable arguments passed!");
        }

        $this->callback = $callback;
    }

    /**
     * Invoke callback
     *
     * @return mixed
     */
    public function __invoke() {
        return call_user_func_array($this->callback, func_get_args());
    }
}