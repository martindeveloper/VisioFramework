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
 * Link extension for Visio\Template.
 * Usage: {link Controller::action|arg1,arg2 #lang}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Link extends Visio\Object implements Visio\Template\IExtension {

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

        preg_match_all('/\{link (.*?)(\|(.*?))? *(\#(.*?))? *\}/sui', $this->content, $matches);

        unset($matches[2]);
        unset($matches[4]);

        foreach ($matches[0] as $key => $value) {
            $parts = explode("::", $matches[1][$key], 2);
            $lang = (isset($matches[5][$key]) && !empty($matches[5][$key])) ? $matches[5][$key] : $this->container->translate->lang;
            $link = Visio\Http::getInstance()->response->baseUrl;

            if (isset($parts[1])) {
                $action = str_replace("_", "-", $parts[1]);
            } else {
                $action = "index";
            }

            $args = explode(",", trim($matches[3][$key], ", "));

            array_walk($args, function ($value, $key) use (&$args) {
                if (empty($value)) {
                    unset($args[$key]);
                }

                if (isset($value[0]) && $value[0] == "\$") {
                    $val = substr($value, 1);

                    if (isset($this->vars[$val])) {
                        $args[$key] = $this->vars[$val];
                    }
                }
            });

            $args["lang"] = $lang;

            $link .= $this->container->router->createReverseRoute($parts[0], $action, $args);

            $this->content = str_replace($value, $link, $this->content);
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{link(.*?)\}/sui', '', $this->content);
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
