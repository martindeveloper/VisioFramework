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
 * Condition extension for Visio\Template.
 * Usage: {if $foo == $bar} show some stuff {/if}
 * Available operands: eq !eq lt gt regex !regex
 * If variable name will start with ^ will be evaluated!
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class IfStatement extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    private $vars = array();
    private $content;
    protected $maxIteration = 0;

    const PRIORITY = Visio\Template::PRIORITY_MEDIUM;

    /**
     * @param $content
     * @param $vars
     * @throws Visio\Exception\Template
     */
    public function __construct($content, $vars) {
        $this->vars = $vars;
        $this->content = $content;

        preg_match_all('/\{if(.*?)\}/i', $this->content, $ifStart);
        preg_match_all('/\{\/if\}/i', $this->content, $ifEnd);

        $countOpenTag = sizeof($ifStart[0]);
        $countCloseTag = sizeof($ifEnd[0]);

        if ($countOpenTag != $countCloseTag) {
            throw new Visio\Exception\Template("You must close all 'if' statement!");
        }
        $this->maxIteration = $countOpenTag;
    }

    /**
     * onParse()
     */
    public function onParse() {
        for ($i = 1; $i <= $this->maxIteration; $i++) {
            preg_match('/\{if (.*?)\}(.*?)\{\/if\}/sui', $this->content, $code);

            if (empty($code)) {
                continue;
            }

            //Delete $ marks form condition
            $code[1] = str_replace("\$", "", $code[1]);

            $condition = explode(" ", trim($code[1]));

            $keys = explode("->", $condition[0]);

            if (isset($this->vars[$keys[0]])) {
                $variable = $this->vars[$keys[0]];

                if (is_array($variable) || $variable instanceof \ArrayObject || is_object($variable)) {
                    for ($i = 1; $i < sizeof($keys); $i++) {

                        //Variable is object
                        if (is_object($variable)) {

                            //Calling method?
                            if (strpos($keys[$i], "()") !== false) {
                                $method = substr($keys[$i], 0, strpos($keys[$i], "()"));
                                $variable = $variable->$method();

                                continue;
                            }

                            //Search for property
                            if (property_exists($variable, $keys[$i])) {
                                $property = $keys[$i];
                                $variable = $variable->$property;
                            } else {
                                throw new Visio\Exception\Template("Undefined property '" . $keys[$i] . "' of object '" . $keys[$i - 1] . "'!");
                            }
                        } else {
                            //Array or ArrayObject
                            if (isset($variable[$keys[$i]])) {
                                $variable = $variable[$keys[$i]];
                            } else {
                                throw new Visio\Exception\Template("Undefined index '" . $keys[$i] . "' of array variable '" . $keys[$i - 1] . "'!");
                            }
                        }
                    }
                }
            } else {
                $variable = null;
            }

            $operation = Visio\Utilities\String::lower($condition[1]);

            $keys = (isset($condition[2])) ? explode("->", $condition[2]) : null;

            if (isset($this->vars[$keys[0]])) {
                $variable2 = $this->vars[$keys[0]];

                if (isset($this->vars[$keys[0]]) && is_array($this->vars[$keys[0]])) {
                    $variable2 = $this->vars[$keys[0]];

                    for ($i = 1; $i < sizeof($keys); $i++) {
                        if (is_object($variable2) && property_exists($variable2, $keys[$i])) {
                            $variable2 = $variable2->{$keys[$i]};
                        } else if (is_array($variable2) && isset($variable2[$keys[$i]])) {
                            $variable2 = $variable2[$keys[$i]];
                        } else {
                            throw new Visio\Exception\Template("Undefined index '" . $keys[$i] . "' of array variable '" . $keys[$i - 1] . "'!");
                        }
                    }
                }
            } else {
                $variable2 = (isset($condition[2]) ? $condition[2] : null);
            }

            if ($variable2 != null && $variable2[0] == "^") {
                $variable2 = substr($variable2, 1, strlen($variable2)); //Delete ^

                $variableParts = explode("::", trim($variable2), 2);

                if ($variableParts[1] == "\$") {
                    $variable2 = $variableParts[0]::$$variableParts[1]; //static variable
                } else {
                    $variable2 = constant($variableParts[0] . "::" . $variableParts[1]); //class constant
                }
            }

            $variable2 = trim($variable2);
            if (strpos($variable2, '"') !== false || strpos($variable2, "'") !== false) {
                $variable2 = str_replace(array("'",
                                               '"'), array("",
                                                           ""), $variable2);
            }

            $delete = $this->decideDeletion($operation, $variable, $variable2, $condition);

            if ($delete == false) {
                $this->content = str_replace($code[0], $code[2], $this->content);
            } else {
                $this->content = str_replace($code[0], "", $this->content);
            }
        }
    }

    /**
     * @param $operation
     * @param $variable
     * @param $variable2
     * @param $condition
     * @return bool
     * @throws \Visio\Exception\Template
     */
    public function decideDeletion($operation, $variable, $variable2, $condition) {
        if (is_bool($variable)) {
            $variable2 = ($variable2 == "true") ? true : false;
        }

        switch ($operation) {
            case 'isset':
                if (!is_null($variable)) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case '!isset':
                if (is_null($variable)) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case 'empty':
                if (empty($variable)) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case '!empty':
                if (!empty($variable)) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case 'eq':
            case '==':
                if (is_null($variable)) {
                    $variable = $condition[0];
                }

                if ($variable == $variable2) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case '!eq':
            case '!=':
                if ($variable != $variable2) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case 'lt':
            case '<':
                if ($variable < $variable2) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case 'gt':
            case '>':
                if ($variable > $variable2) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case 'regex':
                if (preg_match($variable2, $variable) > 0) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            case '!regex':
                if (!preg_match($variable2, $variable) > 0) {
                    $delete = false;
                } else {
                    $delete = true;
                }
                break;

            default:
                throw new Visio\Exception\Template("Invalid logic operation '" . $operation . "' in if statement!");
                break;
        }

        return $delete;
    }

    /**
     * onClean()
     */
    public function onClean() {

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

    }

    /**
     * setVars()
     */
    public function setVars(array $vars) {
        $this->vars = $vars;
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
