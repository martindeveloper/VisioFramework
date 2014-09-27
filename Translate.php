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
 * System for translating application.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Translate extends Visio\Object {

    /**
     * @var Visio\DependencyInjection\IContainer
     */
    public $container;

    /**
     * Current language for getting translation
     * @var string $lang
     * */
    private $lang = "en";

    /**
     * Instance of driver
     * @var Visio\Translate\IDriver $driver
     */
    private $driver;

    /**
     * @var bool $isChecked
     */
    private $isChecked = false;

    /**
     * @param $lang
     * @param Translate\IDriver $driver
     */
    public function __construct($lang, Visio\Translate\IDriver $driver) {
        $this->lang = $lang;
        $this->driver = $driver;
    }

    /**
     * Get translation by pseudo language identifier
     *
     * @param string $name
     * @param string $namespace
     * @return string
     */
    public function get($name, $namespace = "Default") {
        $text = "";

        if ($this->isChecked === false) {
            try {
                $this->preCheck();
            } catch (Visio\Exception $ex) {
                $ex->showErrorMessage();
            }

            $this->isChecked = true;
        }

        try {
            $text = $this->driver->getTranslate($name, $namespace);
        } catch (Visio\Exception $ex) {
            $ex->showErrorMessage();
        }

        return $text;
    }

    /**
     * @param string $namespace
     */
    public function getNamespace($namespace = "Default") {
        if ($this->isChecked === false) {
            try {
                $this->preCheck();
            } catch (Visio\Exception $ex) {
                $ex->showErrorMessage();
            }

            $this->isChecked = true;
        }

        try {
            return $this->driver->getNamespace($namespace);
        } catch (Visio\Exception $ex) {
            $ex->showErrorMessage();
        }
    }

    /**
     * Get available languages
     *
     * @return array $languages
     */
    public function getAvailableLanguages() {
        return $this->driver->getAvailableLanguages();
    }

    /**
     * Translation system pre-checking
     *
     * @throws Visio\Exception\DirectoryNotFound
     * @throws Visio\Exception
     */
    private function preCheck() {
        $translationFolder = $this->container->applicationConfig->get('translate', 'Directories');
        $this->driver->lang = $this->lang;
        $this->driver->folder = $translationFolder;

        $languages = $this->getAvailableLanguages();

        if (!in_array($this->lang, $languages)) {
            if (isset($languages[0])) {
                $this->lang = $languages[0];
            } else {
                throw new Visio\Exception("Invalid translation '" . $this->lang . "'!");
            }
        }

        if (!Visio\FileSystem::directoryExist($translationFolder)) {
            throw new Visio\Exception\DirectoryNotFound("Directory '" . $translationFolder . "' for translations not found!");
        }
    }

    /**
     * Force new language to translator
     *
     * @param string $lang
     */
    public function setLang($lang) {
        $this->lang = $lang;
        $this->isChecked = false;
    }

    /**
     * Return current active language
     *
     * @return string $lang
     */
    public function getLang() {
        return $this->lang;
    }

    /**
     * Force new driver
     *
     * @param string $driverName
     */
    public function setDriverName($driverName) {
        $this->driverName = $driverName;
        $this->isChecked = false;
    }

    /**
     * Clean up
     */
    public function __destruct() {
        if ($this->driver instanceof Visio\Translate\IDriver) {
            $this->driver->close();
        }
    }

}