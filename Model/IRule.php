<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Model;

use Visio;

/**
 * Rules interface
 *
 * @package Visio\Model
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IRule {
    public function __construct($fieldName, $fieldValue, $message);

    public function test();
}