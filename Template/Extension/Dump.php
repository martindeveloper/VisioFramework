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
 * Extension for dumping variables for Visio\Template.
 * Usage: {dump $variable}
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Dump extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    private $vars = array();
    private $content;
    private $filtersHandler;

    const PRIORITY = Visio\Template::PRIORITY_NORMAL;

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
        $matches = array();

        preg_match_all('/\{dump +\$(.*?) *\}/siu', $this->content, $matches);

        foreach ($matches[1] as $key => $variable) {
            $keys = explode("-", $variable);

            if (isset($this->vars[$keys[0]])) {
                $var = $this->vars[$keys[0]];

                if (isset($this->vars[$keys[0]]) && is_array($this->vars[$keys[0]])) {
                    $var = $this->vars[$keys[0]];

                    for ($i = 1; $i < sizeof($keys); $i++) {
                        if (isset($var[$keys[$i]])) {
                            $var = $var[$keys[$i]];
                        } else {
                            throw new Visio\Exception\Template("Undefined index '" . $keys[$i] . "' of array variable '" . $keys[$i - 1] . "' for dumping!");
                        }
                    }
                }

                ob_start();
                var_dump($var);
                $dump = ob_get_clean();

                $dump = "<code><pre>" . $dump . "</pre></code>";

                $this->content = str_replace($matches[0][$key], $dump, $this->content);
            } else {
                throw new Visio\Exception\Template("Invalid variable '" . $keys[0] . "' for dumping!");
            }
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{dump(.*?)\}/i', '', $this->content);
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