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
 * Extension for breakpoint support with XDebug extension for Visio\Template.
 * Usage: {breakpoint}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Breakpoint extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    /**
     * @var array|mixed $vars
     */
    private $vars = array();

    /**
     * @var string $content
     */
    private $content;

    /**
     * @var Visio\Template\Filter
     */
    private $filtersHandler;

    const PRIORITY = Visio\Template::PRIORITY_NORMAL;

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

        preg_match_all('/\{breakpoint *\}/siu', $this->content, $matches);

        $iterations = count($matches[0]);

        for (; $iterations > 0; $iterations--) {
            if (function_exists("xdebug_break")) {
                xdebug_break();
            } else {
                throw new Visio\Exception\Template("Can not use 'breakpoint' without XDebug extension!");
            }
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{breakpoint(.*?)\}/i', '', $this->content);
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