<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Main loader class.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Loader {

    /**
     * @var Visio\Loader
     */
    public static $instance = null;

    /**
     * @var array $libraries
     */
    private $libraries = array();

    /**
     * @var array $priority
     */
    public static $priority = array("Object",
                                    "Events",
                                    "Exception",
                                    "Diagnostic",
                                    "FileSystem");

    /**
     * getInstance()
     *
     * @return self
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * __construct()
     */
    public function __construct() {
        #load priority class
        foreach (self::$priority as $class) {
            require VF_ROOT . $class . ".php";
        }

        $this->addPrefix(str_replace('\Loader', '', __CLASS__), __DIR__); // as of PHP 5.3

        $this->register();

        #start ScriptExecutionTime Visio\Diagnostic\Stopwatch
        Visio\Diagnostic::createStopwatch("ScriptExecutionTime");
    }

    /**
     * autoload()
     *
     * @param string $class
     * @return null
     */
    public function autoload($class) {
        foreach ($this->libraries as $item) {
            $prefix = explode("\\", $class, 2);

            if ($prefix[0] == $item['prefix']) {
                //$load = substr($class, $prefixLen - strlen($class) + 1);
                $this->load($prefix[1], $item['path']);
            } else {
                #throw new Visio\Exception\Loader('Prefix was not found');
                continue;
            }
        }
    }

    /**
     * Load class
     *
     * @param $load
     * @param $path
     * @throws Exception\Loader
     */
    public function load($load, $path) {
        $parts = explode('\\', trim($load));
        $file = implode(DS, $parts) . '.php';

        if (Visio\FileSystem::fileExist($path . DS . $file)) {
            include_once $path . DS . $file;
        } else {
            throw new Visio\Exception\Loader('Cant load \'' . $load . '\' class!');
        }
    }

    /**
     * Add prefix
     *
     * @param $prefix
     * @param $path
     * @return bool
     * @throws Exception\Loader
     */
    public function addPrefix($prefix, $path) {
        if (Visio\FileSystem::directoryExist($path)) {
            $this->libraries[] = array('prefix' => $prefix,
                                       'path' => $path);
        } else {
            throw new Visio\Exception\Loader('Variable $path must be an directory! Given \'' . (string)$path . '\'.');
        }

        return true;
    }

    /**
     * Get path by namespace prefix
     *
     * @param $prefix
     * @return bool
     */
    public function getPath($prefix) {
        foreach ($this->libraries as $lib) {
            if ($lib['prefix'] == $prefix) {
                return $lib;
            }
        }

        return false;
    }

    /**
     * @return bool
     * @throws Visio\Exception\General
     */
    public function register() {
        if (!function_exists('spl_autoload_register')) {
            throw new Visio\Exception\General('spl_autoload() function not found in this PHP installation!');
        }

        return spl_autoload_register(array($this,
                                           "autoload"));
    }

    /**
     * unregister()
     * @return bool
     */
    public function unregister() {
        return spl_autoload_unregister(array($this,
                                             "autoload"));
    }

}
