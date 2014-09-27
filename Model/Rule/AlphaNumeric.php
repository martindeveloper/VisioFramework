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
class AlphaNumeric extends Model\Rule\BaseRule implements Model\IRule {

    /**
     * Process validation
     *
     * @return bool
     */
    public function test() {
        return ctype_alnum($this->fieldValue);
    }
}