<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Template;

/**
 * Visio\Template\IExtension
 *
 * @package Visio\Template
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
interface IExtension {

    public function onParse();

    public function onClean();

    public function getOutput();

    public static function getPriority(); //For PHP less than 5.3

    public function setFiltersHandler($filtersHandler);
}