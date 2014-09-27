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
 * Base rule
 *
 * @package Visio\Model\Rule
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class BaseRule extends Visio\Object implements Model\IRule {

    /**
     * @var string $fieldName
     */
    public $fieldName;

    /**
     * @var mixed $fieldValue
     */
    public $fieldValue = null;

    /**
     * @var string $message
     */
    public $message;

    /**
     * __construct()
     *
     * @param string $fieldName
     * @param mixed $fieldValue
     * @param mixed $message
     */
    public function __construct($fieldName, $fieldValue, $message) {
        $this->fieldName = $fieldName;
        $this->fieldValue = $fieldValue;
        $this->message = $message;
    }

    /**
     * Dump validation
     *
     * @throws Visio\Exception\Validator
     */
    public function test() {
        throw new Visio\Exception\Validator("Implement test() method or do not use BaseRule as rule!");
    }
}