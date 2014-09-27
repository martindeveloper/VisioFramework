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
 * Kiwi templating system.
 * Support extensions and filters.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Template extends Visio\Object {

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    protected $container;

    /**
     * Template variables
     * @var array $vars
     */
    protected $vars = array();

    /**
     * @var mixed|string $content
     */
    protected $content;

    /**
     * @var Template\Extension $extensionsHandler
     */
    protected $extensionsHandler;

    /**
     * @var Template\Filter $filtersHandler
     */
    protected $filtersHandler;

    /**
     * @var mixed|string $filename
     */
    protected $fileName;

    /**
     * Set silent option. If is set to true, return white page and log exception
     * @var bool $silent
     */
    protected $silent = false;

    /**
     * Set cache option
     * @var bool $cache
     */
    protected $cache = false;
    protected $cacheKey = 0;
    protected $cacheTime = 3600;

    const PRIORITY_CRITICAL = 0, PRIORITY_MEDIUM = 1, PRIORITY_NORMAL = 2;

    /**
     * @param $file
     * @param bool $raw
     * @param DependencyInjection\IContainer $container
     */
    public function __construct($file, $raw = false, Visio\DependencyInjection\IContainer $container) {
        if ($raw === false) {
            $this->content = Visio\FileSystem::readFile($file);

            $this->fileName = $file;
        } else {
            $this->content = $file;

            $this->fileName = "**RAW INPUT**";
        }

        $container->templateConfig = new Visio\Callback(function () {
            $file = new Visio\FileSystem\File(CONFIG_DIR . "Template.json");
            $adapter = new Visio\Config\Adapter\Json($file);

            return new Visio\Config($adapter);
        });

        $this->filtersHandler = new Visio\Template\Filter($container->templateConfig->getNamespace("Filters"));
        $this->extensionsHandler = new Visio\Template\Extension($this->filtersHandler, $container->templateConfig->getNamespace("Extensions"), $container);
        $this->container = $container;
    }

    /**
     * set()
     *
     * @param string $name
     * @param mixed $content
     */
    public function set($name, $content) {
        $this->vars[$name] = $content;
    }

    /**
     * __set()
     *
     * @param string $name
     * @param mixed $content
     */
    public function __set($name, $content) {
        $this->set($name, $content);
    }

    /**
     * __get()
     *
     * @param $name
     * @return mixed
     */
    public function &__get($name) {
        return $this->vars[$name];
    }

    /**
     * Set variables by array
     * @param array $array
     */
    public function setArray(array $array) {
        foreach ($array as $key => $val) {
            $this->vars[$key] = $val;
        }
    }

    /**
     * getOutput()
     *
     * @return string
     */
    public function getOutput() {
        $this->extensionsHandler->silentMode = $this->silent;
        $output = "";

        if ($this->cache === true) {
            $cacheStorage = new Visio\Cache\Storage\File($this->container->applicationConfig);
            $cache = new Visio\Cache($cacheStorage);
            $cacheData = $cache->get($this->cacheKey, $this->cacheTime);

            if ($cacheData === false) {
                $output = $this->extensionsHandler->runExtensions($this->content, $this->vars);

                $cache->set($this->cacheKey, $output);

                return $output;
            } else {
                return $cacheData;
            }
        } else {
            try {
                $output = $this->extensionsHandler->runExtensions($this->content, $this->vars);
            } catch (Visio\Exception $ex) {
                if ($this->silent !== true) {

                    //Get relative path of template file
                    $fileName = substr($this->fileName, strlen(realpath(APP_DIR . ".." . DS)));
                    $ex->setMessage("Error in template file '" . $fileName . "'. " . $ex->getMessage());

                    $ex->showErrorMessage();
                }
            }

            if (empty($output)) {
                $this->container->http->response->addHeader("X-Visio-Empty", "1");
            }

            return $output;
        }
    }

    /**
     * Set error showing
     *
     * @param bool $silent
     */
    public function setSilent($silent = false) {
        $this->silent = (bool)$silent;
    }

    /**
     * @param $cache
     * @param $cacheKey
     * @param $cacheTime
     * @return bool
     */
    public function setCache($cache, $cacheKey, $cacheTime) {
        $this->cache = (bool)$cache;
        $this->cacheKey = (string)$cacheKey;
        $this->cacheTime = (int)$cacheTime;

        return true;
    }

    /**
     * Register a new extension
     *
     * @param $className
     * @param int $priority
     * @return bool
     */
    public function registerExtension($className, $priority = self::PRIORITY_NORMAL) {
        return $this->extensionsHandler->registerExtension($className, $priority);
    }

    /**
     * setFiltersHandler()
     *
     * @param Visio\Template\IFilter $handler
     */
    public function setFiltersHandler($handler) {
        $this->filtersHandler = $handler;
        $this->extensionsHandler->filtersHandler = $handler;
    }

    /**
     * setExtensionsHandler()
     *
     * @param Visio\Template\IExtension $handler
     */
    public function setExtensionsHandler($handler) {
        $this->extensionsHandler = $handler;
    }

    /**
     * Rehi
     *
     * @param mixed $filter
     * @param string $alias
     */
    public function registerFilter($filter, $alias) {
        $this->filtersHandler->registerFilter($filter, $alias);
    }

    /**
     * getFilter()
     */
    public function getFilter() {
        return $this->filtersHandler;
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return $this->getOutput();
    }

}
