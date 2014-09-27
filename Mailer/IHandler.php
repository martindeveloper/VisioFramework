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
 * Visio\Mailer\IHandler
 * 
 * @package Visio\Mailer
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IHandler {

    public function prepare($recipients, $from, $message);

    public function send();
}