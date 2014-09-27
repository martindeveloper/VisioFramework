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
 * Class for reading config files.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Config extends Visio\Object {

    /**
     * @var Visio\Config\IAdapter object
     */
    protected $adapter;

    /**
     * @var array Array of variables to replace in config
     */
    protected $variables = array("%app%" => APP_DIR,
                                 "%root%" => VF_ROOT);

    /**
     * @param Config\IAdapter $adapter
     */
    public function __construct(Visio\Config\IAdapter $adapter) {
        $this->adapter = $adapter;
    }

    /**
     * get()
     *
     * @param string $name
     * @param string $namespace
     * @return mixed
     */
    public function get($name, $namespace = "General") {
        $value = $this->adapter->get($name, $namespace);

        if (is_string($value) || is_array($value)) {
            switch ($value) {
                case 'true':
                    return true;
                    break;

                case 'false':
                    return false;
                    break;

                default:
                    return $this->replaceVariables($value);
                    break;
            }
        } else {
            return $value;
        }
    }

    /**
     * getNamespace()
     *
     * @param string $name
     * @return mixed
     */
    public function getNamespace($name = "General") {
        return (array)$this->adapter->getNamespace($name);
    }

    /**
     * __get()
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }

    /**
     * replaceVariables()
     *
     * @param string $string
     * @return string
     */
    private function replaceVariables($string) {
        return str_replace(array_keys($this->variables), array_values($this->variables), $string);
    }
}