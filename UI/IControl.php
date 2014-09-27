<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\UI;

use Visio;

/**
 * Visio\UI\IControl
 *
 * @package Visio\Session
 * @author Martin Pernica
 * @version 3.0
 */
interface IControl {

    public function setArgs($args);

    public function setAjax($ajax);

    public function setHttpRequest(Visio\Http\Request $httpRequest);

    public function setHttpResponse(Visio\Http\Response $httpResponse);

    public function setTemplate($controller, $action);
}