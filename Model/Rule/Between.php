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
 * Alpha numeric validation
 *
 * @package Visio\Model\Rule
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Between extends Model\Rule\BaseRule implements Model\IRule {

    /**
     * @var float $from
     */
    public $from;

    /**
     * @var float $to
     */
    public $to;

    /**
     * Process validation
     *
     * @return bool
     */
    public function test() {
        if (!isset($this->from) && isset($this->to)) {
            $rangeFrom = 0;
            $rangeTo = $this->to;
        } else {
            $rangeFrom = $this->from;
            $rangeTo = $this->to;
        }

        $length = floatval($this->fieldValue);

        return ($length >= $rangeFrom && $length <= $rangeTo);
    }
}