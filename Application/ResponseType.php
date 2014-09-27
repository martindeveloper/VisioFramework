<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Application;

use Visio;

/**
 * Response type class.
 *
 * @package Visio\Application
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class ResponseType {
    const HTML = "Visio\\Application\\Response\\Html";
    const CSS = "Visio\\Application\\Response\\CSS";
    const IMAGE = "Visio\\Application\\Response\\Image";
    const JS = "Visio\\Application\\Response\\JavaScript";
    const JSON = "Visio\\Application\\Response\\Json";
    const XML = "Visio\\Application\\Response\\Xml";
}