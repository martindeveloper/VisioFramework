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
 * XML file parser for Router
 *
 * @package Visio\Router\FileParser
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Xml implements IFileParser {

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
        if (!function_exists("simplexml_load_string")) {
            throw new Visio\Exception("Can not use router XML file parser without SimpleXML extension!");
        }

        $this->file = $file;
    }

    /**
     * Parse XML file to routes array
     *
     * @return array
     */
    public function parse() {
        $routes = array();
        $xmlObject = (array)simplexml_load_string($this->file->content);

        if (is_array($xmlObject["Route"])) {
            foreach ($xmlObject["Route"] as $node) {
                $routes[] = $this->parseNodeToArray($node);
            }
        } else {
            $routes[] = $this->parseNodeToArray($xmlObject["Route"]);
        }

        return $routes;
    }

    /**
     * Parse SimpleXMLElement node to array
     *
     * @param \SimpleXMLElement $node
     * @return array
     * @throws \Visio\Exception
     */
    private function parseNodeToArray(\SimpleXMLElement $node) {
        if (!isset($node->path)) {
            throw new Visio\Exception("Node 'path' at Route node is not specified!");
        }

        $arguments = false;
        $universal = false;

        if (isset($node["arguments"])) {
            $arguments = ((string)$node["arguments"] == "true") ? true : false;
        }

        if (isset($jsonArray["universal"])) {
            $universal = ((string)$jsonArray["universal"] == "1") ? true : false;
        }

        return array("mask" => trim((string)$node->mask),
                     "metadata" => (array)$node->metadata,
                     "arguments" => $arguments,
                     "universal" => $universal);
    }
}