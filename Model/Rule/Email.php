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
 * Email validation rule
 *
 * @package Visio\Model\Rule
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Email extends Model\Rule\BaseRule implements Model\IRule {

    /**
     * @return bool|mixed
     */
    public function test() {
        return filter_var($this->fieldValue, FILTER_VALIDATE_EMAIL);
    }
}