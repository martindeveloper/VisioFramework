<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Router\FileParser;

use Visio;

/**
 * JSON file parser for Router
 *
 * @package Visio\Router\FileParser
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Json implements IFileParser {

    /**
     * @var Visio\FileSystem\File $file
     */
    private $file;

    /**
     * __construct()
     *
     * @param \Visio\FileSystem\File $file
     */
    public function __construct(Visio\FileSystem\File $file) {
        if (!function_exists("json_decode")) {
            throw new Visio\Exception("Can not use router JSON file parser without JSON extension!");
        }

        $this->file = $file;
    }

    /**
     * Parse JSON file to routes array
     *
     * @return array
     */
    public function parse() {
        $routes = array();
        $json = json_decode($this->file->content, true);

        foreach ($json["Routes"] as $jsonArray) {
            $arguments = false;
            $universal = false;

            if (isset($jsonArray["arguments"])) {
                $arguments = ((string)$jsonArray["arguments"] == "1") ? true : false;
            }

            if (isset($jsonArray["universal"])) {
                $universal = ((string)$jsonArray["universal"] == "1") ? true : false;
            }

            $routes[] = array("mask" => trim($jsonArray["mask"]),
                              "metadata" => (array)$jsonArray["metadata"],
                              "arguments" => $arguments,
                              "universal" => $universal);
        }

        return $routes;
    }
}