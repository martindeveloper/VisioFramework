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
 * IP address validation rule
 *
 * @package Visio\Model\Rule
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class IpAddress extends Model\Rule\BaseRule implements Model\IRule {

    /**
     * Validate IPv6 format
     * @var bool
     */
    public $ipv6 = false;

    /**
     * @return bool|mixed
     */
    public function test() {
        $flag = FILTER_FLAG_IPV4;

        if ($this->ipv6 === true) {
            $flag = FILTER_FLAG_IPV6;
        }

        return filter_var($this->fieldValue, FILTER_VALIDATE_IP, $flag);
    }
}