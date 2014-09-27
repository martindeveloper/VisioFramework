<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Template;

use Visio;

/**
 * Default extensions handler class.
 * Can run extension on template or register own extension on-the-fly.
 *
 * @package Visio\Template
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Extension extends Visio\Object {

    /**
     * @var array $extensions
     */
    private $extensions = array();

    /**
     * @var bool $silentMode
     */
    private $silentMode = false;

    /**
     * @var Visio\Template\Filter $filtersHandler
     */
    private $filtersHandler;

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    private $container;

    /**
     * @param $filtersHandler
     * @param array $extensions
     * @param \Visio\DependencyInjection\IContainer $container
     */
    public function __construct($filtersHandler, array $extensions, Visio\DependencyInjection\IContainer $container) {
        $this->container = $container;

        $this->filtersHandler = $filtersHandler;
        $this->registerDefaultExtensions($extensions);
    }

    /**
     * @param array $extensions
     * @return bool
     */
    protected function registerDefaultExtensions(array $extensions) {
        foreach ($extensions as $ext) {
            $priority = Visio\Template::PRIORITY_NORMAL;

            if (defined($ext . "::PRIORITY")) {
                $priority = $ext::PRIORITY;
            }

            $this->registerExtension($ext, $priority);
        }

        return true;
    }

    /**
     * @param $className
     * @param int $priority
     * @return bool
     */
    public function registerExtension($className, $priority = Visio\Template::PRIORITY_NORMAL) {
        if (class_exists($className, true)) {
            $this->extensions[$priority][] = $className;
            return true;
        } else {
            return false;
        }
    }

    /**
     * runExtensions()
     * Run registered extensions and parse content tags
     *
     * @param string $content
     * @param mixed $vars
     * @return string
     */
    public function runExtensions($content, $vars) {
        ksort($this->extensions);
        foreach ($this->extensions as $exts) {
            foreach ($exts as $ext) {
                $obj = new $ext($content, $vars);
                $obj->container = $this->container;
                $obj->filtersHandler = $this->filtersHandler;
                $obj->onParse();
                $obj->onClean();
                $content = $obj->getOutput();
            }
        }
        return $content;
    }

    /**
     * setFiltersHandler()
     *
     * @param object $handler
     */
    public function setFiltersHandler($handler) {
        $this->filtersHandler = $handler;
    }

    /**
     * setSilent()
     *
     * @param bool $silent
     */
    public function setSilentMode($silent = false) {
        $this->silentMode = (bool)$silent;
    }

}