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
 * Minimal length rule for data validation
 *
 * @package Visio\Model\Rule
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class MinLength extends Model\Rule\BaseRule implements Model\IRule {

    /**
     * @var int $length
     */
    public $length;

    /**
     * Process validation
     *
     * @return bool
     * @throws Visio\Exception\Validator
     */
    public function test() {
        if (!isset($this->length)) {
            throw new Visio\Exception\Validator("Missing first argument for minLength rule!");
        }
        return (Visio\Utilities\String::length($this->fieldValue) >= $this->length);
    }
}