<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined("APP_DIR")) {
    define("APP_DIR", "/");
}

if (!defined("CONFIG_DIR")) {
    trigger_error("Visio Framework: Constant CONFIG_DIR needs to be defined!", E_USER_ERROR);
}

define('VF_ROOT', realpath(dirname(__FILE__)) . DS);

# Visio system check
error_reporting(E_ALL | E_STRICT);
iconv_set_encoding('internal_encoding', 'UTF-8');
extension_loaded('mbstring') && mb_internal_encoding('UTF-8');
@ini_set('zend.ze1_compatibility_mode', '0');

require VF_ROOT . 'Loader.php';

$loader = \Visio\Loader::getInstance();

# Visio header
if (!defined("STDIN")) {
    header('X-Powered-By: Visio ' . \Visio\Framework::VERSION);
}