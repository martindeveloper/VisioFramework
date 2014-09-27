<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Template\Extension;

use Visio;

/**
 * Notify link extension for Visio\Template.
 * Usage: {notify ComponentAlias::action}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Notify extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    private $vars = array();
    private $content;
    private $filtersHandler;

    const PRIORITY = Visio\Template::PRIORITY_NORMAL;

    /**
     * __construct()
     *
     * @param string $content
     * @param mixed $vars
     */
    public function __construct($content, $vars) {
        $this->content = $content;
        $this->vars = $vars;
    }

    /**
     * onParse()
     */
    public function onParse() {
        $matches = array();

        preg_match_all('/\{notify (.*?) *\}/sui', $this->content, $matches);

        foreach ($matches[0] as $key => $value) {
            $parts = explode("::", $matches[1][$key], 2);
            $parts = join("/", $parts);
            $parts = "?notify=" . $parts;

            $this->content = str_replace($value, $parts, $this->content);
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{notify(.*?)\}/sui', '', $this->content);
    }

    /**
     * getOutput()
     */
    public function getOutput() {
        return $this->content;
    }

    /**
     * setFiltersHandler()
     */
    public function setFiltersHandler($filtersHandler) {
        $this->filtersHandler = $filtersHandler;
    }

    /**
     * getPriority()
     *
     * @return int
     */
    public static function getPriority() {
        return self::PRIORITY;
    }

}
