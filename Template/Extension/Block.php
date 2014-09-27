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
 * Block system extension for Visio\Template.
 * Define block place {block #someIdHere}{/block}
 * And define block data {blockData #someIdHere} Some test data {/blockData}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Block extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    /**
     * @var string $content
     */
    private $content;

    const PRIORITY = Visio\Template::PRIORITY_MEDIUM;

    /**
     * @param $content
     * @throws Visio\Exception\Template
     */
    public function __construct($content) {
        $this->content = $content;

        $blockStart = array();
        $blockEnd = array();

        preg_match_all('/\{block +\#.*?\}/i', $this->content, $blockStart);
        preg_match_all('/\{\/block\}/i', $this->content, $blockEnd);

        $countOpenTag = sizeof($blockStart[0]);
        $countCloseTag = sizeof($blockEnd[0]);

        if ($countOpenTag != $countCloseTag) {
            throw new Visio\Exception\Template("You must close all 'block' statement!");
        }

        preg_match_all('/\{blockData \#.*?\}/i', $this->content, $blockStart);
        preg_match_all('/\{\/blockData\}/i', $this->content, $blockEnd);

        $countOpenTag = sizeof($blockStart[0]);
        $countCloseTag = sizeof($blockEnd[0]);

        if ($countOpenTag != $countCloseTag) {
            throw new Visio\Exception\Template("You must close all 'blockData' statement!");
        }
    }

    /**
     * onParse()
     */
    public function onParse() {
        $blocks = $this->getBlocks($this->content);
        $data = $this->getBlocksData($this->content);

        foreach ($blocks as $blockId) {
            $innerData = (isset($data[$blockId]) ? $data[$blockId] : false);

            if ($innerData !== false) {
                $this->content = preg_replace('/\{block \#' . preg_quote($blockId, '/') . '\}(.*?){\/block\}/sui', $innerData, $this->content);
            }
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        //$this->content = preg_replace('/\{block\|.*?\}(.*?){\/blockData\}/sui', '', $this->content);
        $this->content = preg_replace('/\{blockData \#.*?\}(.*?){\/blockData\}/sui', '', $this->content);
    }

    /**
     * getOutput()
     */
    public function getOutput() {
        return $this->content;
    }

    /**
     * getBlocks()
     *
     * @param mixed $html
     */
    private function getBlocks($html) {
        $matches = array();

        preg_match_all('/\{block \#(.*?)\}/sui', $html, $matches);

        return $matches[1];
    }

    /**
     * @param $html
     * @return array
     */
    private function getBlocksData($html) {
        $matches = array();

        preg_match_all('/\{blockData \#(.*?)\}(.*?)\{\/blockData\}/sui', $html, $matches);

        unset($matches[0]);

        $data = array();

        foreach ($matches[1] as $key => $val) {
            $data[$val] = $matches[2][$key];
        }

        return $data;
    }

    /**
     * setFiltersHandler()
     */
    public function setFiltersHandler($filtersHandler) {

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