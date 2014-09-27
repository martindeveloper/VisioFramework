<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Translate;

use Visio;

/**
 * Visio\Translate\IDriver
 *
 * @package Visio\Translate
 * @author Martin Pernica
 * @version 3.0
 */
interface IDriver {

    public function preLoad();

    public function getTranslate($name, $namespace);

    public function getNamespace($namespace);

    public function close();

    public function getAvailableLanguages();
}