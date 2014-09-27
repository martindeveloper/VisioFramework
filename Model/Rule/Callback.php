<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Model\Rule;

use Visio, Visio\Model;

/**
 * Validation with callback
 *
 * @package Visio\Model\Rule
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Callback extends Model\Rule\BaseRule implements Model\IRule {

    /**
     * @var Visio\Callback $callback
     */
    private $callback;

    /**
     * Set callback
     *
     * @param Visio\Callback $callback
     */
    public function setCallback(Visio\Callback $callback) {
        $this->callback = $callback;
    }

    /**
     * @return Visio\Callback
     */
    public function getCallback() {
        return $this->callback;
    }

    /**
     * @return bool|mixed
     */
    public function test() {
        $callback = $this->callback;
        return (bool)$callback($this->fieldValue);
    }
}