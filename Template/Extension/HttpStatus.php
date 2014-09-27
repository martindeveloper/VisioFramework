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
 * Extension for setting HTTP response code for Visio\Template.
 * Usage: {httpStatus "code"}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class HttpStatus extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    private $vars = array();
    private $content;
    private $filtersHandler;

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
        $matches = array();

        preg_match_all('/\{httpStatus +(\"|\')(.*?)(\"|\') *\}/si', $this->content, $matches);

        foreach ($matches[2] as $status) {
            if (function_exists("http_response_code")) {
                http_response_code($status);
            } else {
                Visio\Http::getInstance()->response->addHeaderRaw(":", true, $status);
            }
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{httpStatus(.*?)\}/i', '', $this->content);
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