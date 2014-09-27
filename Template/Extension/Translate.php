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
 * Extension for translating directly in Views.
 * Usage: {translate #variable} or simplified version {#variable#}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Translate extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    private $vars = array();
    private $content;
    private $filtersHandler;
    private $translate;

    const PRIORITY = Visio\Template::PRIORITY_CRITICAL;

    /**
     * __construct()
     *
     * @param mixed $content
     * @param mixed $vars
     */
    public function __construct($content, $vars) {
        $this->vars = $vars;
        $this->content = $content;
    }

    /**
     * onParse()
     */
    public function onParse() {
        $this->translate = $this->container->translate;

        $matches = array();

        preg_match_all('/\{translate \#(.*?) *\}/siu', $this->content, $matches);

        foreach ($matches[1] as $key => $name) {
            if (strpos($name, "->") !== false) {
                $name = explode("->", $name, 2);

                $translateText = $this->translate->get($name[1], $name[0]);
            } else {
                $translateText = $this->translate->get($name);
            }

            $this->content = str_replace($matches[0][$key], $translateText, $this->content);
        }

        preg_match_all('/\{\#(.*?)\#\}/siu', $this->content, $matches);

        foreach ($matches[1] as $key => $name) {
            if (strpos($name, "->") !== false) {
                $name = explode("->", $name, 2);

                $translateText = $this->translate->get($name[1], $name[0]);
            } else {
                $translateText = $this->translate->get($name);
            }

            $this->content = str_replace($matches[0][$key], $translateText, $this->content);
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{translate(.*?)\}/i', '', $this->content);
    }

    /**
     * getOutput()
     *
     * @return string
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