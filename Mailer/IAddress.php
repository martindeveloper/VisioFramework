<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Mailer;

/**
 * Visio\Mailer\IAddress
 * 
 * @package Visio\Mailer
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IAddress {

    public function getUsername();

    public function getHostname();

    public function __toString();
}