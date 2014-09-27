<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Translate\Driver;

use Visio;

/**
 * Driver for translations stored in INI files.
 *
 * @package Visio\Translate\Driver
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Ini extends Visio\Object implements Visio\Translate\IDriver {

    public $lang;
    public $folder;
    private $isLoaded = false;
    private $data = array();

    /**
     * __construct
     */
    public function __construct() {

    }

    /**
     * Get translated text by name/key
     *
     * @param string $name
     * @param string $namespace
     * @return string
     */
    public function getTranslate($name, $namespace) {
        if ($this->isLoaded === false) {
            try {
                $this->preLoad();
            } catch (Visio\Exception $ex) {
                $ex->showErrorMessage();
            }

            $this->isLoaded = true;
        }

        if (!isset($this->data[$namespace][$name])) {
            return "#" . $name . "#";
        }

        return $this->data[$namespace][$name];
    }

    /**
     * @param $namespace
     */
    public function getNamespace($namespace) {
        if ($this->isLoaded === false) {
            try {
                $this->preLoad();
            } catch (Visio\Exception $ex) {
                $ex->showErrorMessage();
            }

            $this->isLoaded = true;
        }

        return $this->data[$namespace];
    }

    /**
     * Get available languages
     *
     * @return array $languages
     */
    public function getAvailableLanguages() {
        return Visio\FileSystem::readContent($this->folder, ".ini", true);
    }

    /**
     * Destroy data
     */
    public function close() {
        unset($this->data);
    }

    /**
     * Preload data
     *
     * @throws Visio\Exception\FileNotFound if translation file is not found
     */
    public function preLoad() {
        $path = $this->folder . DS . $this->lang . ".ini";

        if (!Visio\FileSystem::fileExist($path)) {
            throw new Visio\Exception\FileNotFound("Translation file '" . $path . "' not found!");
        }

        $this->data = \parse_ini_file($path, true);
    }

}