<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Router;

use Visio, Visio\Router;

/**
 * Router helper for parsing routes from file
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class FileParser extends Visio\Object {

    /**
     * @var Visio\FileSystem\File $file
     */
    private $file;

    /**
     * @var Visio\Router\FileParser\IFileParser
     */
    private $parser;

    /**
     * __construct()
     *
     * @param \Visio\FileSystem\File $file
     */
    public function __construct(Visio\FileSystem\File $file) {
        $this->file = $file;

        $this->detectParser();
    }

    /**
     * Parse file and return array with Visio\Route
     *
     * @return array
     */
    public function parse() {
        $routes = array();
        $parsedRoutes = $this->parser->parse();

        foreach ($parsedRoutes as $routeData) {
            $route = new Visio\Route($routeData["mask"], $routeData["metadata"], ($routeData["arguments"] == true) ? array(Visio\Route::ALLOW_ARGS) : array());
            $route->universal = $routeData["universal"];
            $routes[] = $route;
        }

        return $routes;
    }

    /**
     * Detect parser from file extension
     */
    private function detectParser() {
        switch ($this->file->extension) {
            case "xml":
                $this->parser = new Router\FileParser\Xml($this->file);
                break;

            case "json":
                $this->parser = new Router\FileParser\Json($this->file);
                break;
        }
    }
}