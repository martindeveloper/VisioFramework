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
 * Foreach extension for Visio\Template.
 * Usage: {foreach $array} {$key} {$value} {/foreach}
 * Usage: {foreach $multiArray} {$multiArray->index} {/foreach}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class ForeachLoop extends Visio\Object implements Visio\Template\IExtension {

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
     * @var
     */
    private $filtersHandler;
    protected $maxIteration = 0;

    const PRIORITY = Visio\Template::PRIORITY_CRITICAL;

    /**
     * @param $content
     * @param $vars
     * @throws Visio\Exception\Template
     */
    public function __construct($content, array $vars) {
        $this->vars = $vars;
        $this->content = $content;

        preg_match_all('/\{foreach .*?\}/i', $this->content, $foreachStart);
        preg_match_all('/\{\/foreach\}/i', $this->content, $foreachEnd);

        $countOpenTag = sizeof($foreachStart[0]);
        $countCloseTag = sizeof($foreachEnd[0]);

        if ($countOpenTag != $countCloseTag) {
            throw new Visio\Exception\Template("You must close all 'foreach' statement! " . $countOpenTag . " " . $countCloseTag);
        }

        $this->maxIteration = $countOpenTag;
    }

    /**
     * onParse()
     */
    public function onParse() {
        for ($iteration = 1; $iteration <= $this->maxIteration; $iteration++) {
            $output = "";

            preg_match('/\{foreach +\$(.*?)\}(.*?){\/foreach\}/sui', $this->content, $code);
            $code[2] = ltrim($code[2]);
            $array = (isset($this->vars[$code[1]]) ? $this->vars[$code[1]] : null);

            if (!is_array($array) || is_null($array)) {
                throw new Visio\Exception\Template("Invalid variable '" . $code[1] . "' for foreach!");
            }

            $foreachCounter = 1;
            foreach ($array as $key => $val) {
                $temp = $code[2];

                if (is_array($val) || is_object($val)) {
                    preg_match_all('/\{\$' . preg_quote($code[1], "/") . '(.*?)(\|(.*?))?\}/i', $code[2], $indexes);

                    foreach ($indexes[1] as $keyIndex => $index) {
                        $index = $code[1] . $index;

                        $realIndex = explode("->", $index);
                        $value = $val;

                        for ($i = 1; $i < sizeof($realIndex); $i++) {
                            if (is_object($value)) {
                                if (property_exists($value, $realIndex[$i])) {
                                    $value = $value->{$realIndex[$i]};
                                    continue;
                                } else {
                                    $value = "-";
                                    continue;

                                    throw new Visio\Exception\Template("Undefined property '" . $realIndex[$i] . "' of object variable '" . $realIndex[$i - 1] . "'!");
                                }
                            }

                            if (is_array($value)) {
                                if (isset($value[$realIndex[$i]])) {
                                    $value = $value[$realIndex[$i]];
                                    continue;
                                } else {
                                    $value = "-";
                                    continue;

                                    throw new Visio\Exception\Template("Undefined index '" . $realIndex[$i] . "' of array variable '" . $realIndex[$i - 1] . "'!");
                                }
                            }

                            throw new Visio\Exception\Template("Invalid type for foreach!");
                        }

                        if (!empty($indexes[2][$keyIndex])) {
                            //Filter
                            $value = $this->filtersHandler->filterValue($value, $indexes[3][$keyIndex]);
                        }

                        $vars = $this->vars;
                        $vars[$realIndex[0]] = $array[$key];
                        $ifHandler = new Visio\Template\Extension\IfStatement($temp, $vars);
                        $ifHandler->onParse();
                        $ifHandler->onClean();

                        $temp = $ifHandler->getOutput();

                        if (preg_match("/\{break\}/i", $temp)) {
                            break;
                        }

                        $temp = preg_replace('/' . preg_quote($indexes[0][$keyIndex], '/') . '/iu', (string)$value, $temp);
                    }
                } else {
                    $temp = preg_replace('/\{\$key\}/iu', $key, $temp);
                    $temp = preg_replace('/\{\$value\}/iu', $val, $temp);
                }

                $temp = preg_replace("/\{foreachIteration\}/iu", $foreachCounter, $temp);

                $output .= $temp;
                $foreachCounter++;
            }

            $this->content = str_replace($code[0], $output, $this->content);
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{foreach(.*?)\}(.*?){\/foreach}/siu', '', $this->content);
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