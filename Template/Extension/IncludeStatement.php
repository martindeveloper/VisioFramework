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
 * Include extension for Visio\Template.
 * Usage: {include "path/to/file/template.phtml"}
 * For template extending use Layout extension!
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class IncludeStatement extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    private $vars = array();
    private $content;
    private $filtersHandler;

    const PRIORITY = Visio\Template::PRIORITY_MEDIUM;

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
        $code = array();

        preg_match_all('/\{include +(\"|\')(.*?)(\"|\') *\}/sui', $this->content, $code);

        unset($code[0]);
        $paths = $code[2];

        foreach ($paths as $path) {
            $originalPath = $path;

            $path = APP_DIR . $path;
            $path = str_replace(array('/',
                                      '\\'), array(DS,
                                                   DS), $path);

            if (Visio\FileSystem::fileExist($path) && Visio\FileSystem::isReadable($path)) {
                $tpl = new Visio\Template($path, false, $this->container);

                $tpl->setArray($this->vars);
                //$tpl->setBlockArray($this->blocks);
                $tpl->setFiltersHandler($this->filtersHandler);

                $out = $tpl->getOutput();

                $this->content = preg_replace('/\{include +(\"|\')' . preg_quote($originalPath, '/') . '(\"|\') *\}/sui', $out, $this->content);
            } else {
                throw new Visio\Exception\Template("Invalid file path '" . $path . "' for including!");
            }
        }
        unset($tpl, $out);
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{include(.*?)}/sui', '', $this->content);
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
