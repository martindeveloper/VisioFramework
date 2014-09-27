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
 * Parent file extension for Visio\Template.
 * Usage: {layout "Template1.parent.phtml"}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Layout extends Visio\Object implements Visio\Template\IExtension {

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
        preg_match('/\{layout (\"|\')(.*?)(\"|\') *\}/siu', $this->content, $matches);

        if (!empty($matches)) {
            $parent = $matches[2];

            $parentPath = $this->container->applicationConfig->get("layout", "Directories");

            $originalParent = $parent;

            if (pathinfo($parent, PATHINFO_EXTENSION) == "") {
                $parent .= ".kiwi";
            }

            if ($parentPath !== false && Visio\FileSystem::fileExist($parentPath . DS . $parent)) {
                try {
                    ob_start();
                    include $parentPath . DS . $parent;
                    $templateOut = (string)ob_get_clean();

                    $this->content = preg_replace('/\{layout (\"|\')' . preg_quote($originalParent, "/") . '(\"|\') *\}/siu', $templateOut, $this->content, 1);
                } catch (Visio\Exception\Template $ex) {
                    throw new Visio\Exception\Template("Error in parent file '" . $parent . "'. " . $ex->getMessage(), $parentPath . DS . $parent, 1);
                }
            } else {
                throw new Visio\Exception\Template("Layout file '" . $parent . "' not found!");
            }
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{layout(.*?)\}/isu', '', $this->content);
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
