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
 * Variable extension for Visio\Template.
 * Usage: {$variableName|filterAlias}
 * The "filter" attribute is optional.
 * If variable name will start with ^ will be evaluated!
 *
 * @package Visio\Template\Extension
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Variable extends Visio\Object implements Visio\Template\IExtension {

    /**
     * $var Visio\DependencyInjection\IContainer $container
     */
    public $container;

    /**
     * @var array|mixed $vars
     */
    private $vars = array();

    /**
     * @var mixed $content
     */
    private $content;
    private $filtersHandler;

    const PRIORITY = Visio\Template::PRIORITY_MEDIUM;

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
        preg_match_all('/\{\$(.*?)(\|(.*?))? *\}/siu', $this->content, $matches);

        unset($matches[0], $matches[2]);
        $matches = array_values($matches);
        $count = (sizeof($matches[0]) - 1);

        for ($i = 0; $i <= $count; $i++) {
            $variableName = $matches[0][$i]; //Get variable name
            $filterName = !empty($matches[1][$i]) ? $matches[1][$i] : null; //Get optional filter

            $this->parse($variableName, $filterName);
        }
    }

    /**
     * onClean()
     */
    public function onClean() {
        $this->content = preg_replace('/\{\$(.*?)\}/i', '', $this->content);
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
     * parse()
     *
     * @param mixed $variable
     * @param mixed $filter
     *
     * @throws Visio\Exception\Template
     */
    private function parse($variable, $filter = null) {
        $stripedVariable = substr($variable, 0, strpos($variable, "->"));

        if (isset($this->vars[$stripedVariable]) && $this->vars[$stripedVariable] instanceof Visio\Object) {
            $value = $this->parseObject($this->vars[$stripedVariable], $variable);
        } else {

            $variable = trim($variable, ".");
            if (strpos($variable, "->") === false) {
                if (isset($this->vars[$variable])) {
                    $value = $this->vars[$variable];
                } else {
                    throw new Visio\Exception\Template("Undefined variable '" . $variable . "'!");
                }
            } else {

                if ($variable != null && $variable[0] == "^") {
                    $variable = substr($variable, 1, strlen($variable)); //Delete ^

                    $variableParts = explode("::", trim($variable), 2);

                    if ($variableParts[1] == "\$") {
                        $value = $variableParts[0]::$$variableParts[1]; //static variable
                    } else {
                        $value = constant($variableParts[0] . "::" . $variableParts[1]); //class constant
                    }
                } else {
                    $keys = explode("->", $variable);

                    if (isset($this->vars[$keys[0]]) && (is_array($this->vars[$keys[0]]) || $this->vars[$keys[0]] instanceof \ArrayObject)) {
                        $value = $this->vars[$keys[0]];

                        for ($i = 1; $i < sizeof($keys); $i++) {
                            if (isset($value[$keys[$i]])) {
                                $value = $value[$keys[$i]];
                            } else {
                                throw new Visio\Exception\Template("Undefined index '" . $keys[$i] . "' of array variable '" . $keys[$i - 1] . "'!");
                            }
                        }
                    } else {
                        throw new Visio\Exception\Template("Undefined array variable '" . $keys[0] . "'!");
                    }
                }
            }

            if (!is_null($filter)) {
                $value = $this->filtersHandler->filterValue($value, $filter);
            }
        }

        $this->content = preg_replace('/\{\$' . preg_quote($variable, '/') . '(.*?)\}/i', (string)$value, $this->content);
    }

    /**
     * Parse object
     *
     * @param \Visio\Object $object
     * @param $placeholder
     * @throws Visio\Exception\Template
     * @return string
     */
    public function parseObject(Visio\Object $object, $placeholder) {
        if ($object instanceof Visio\UI\Controller\Component) {
            if (strpos($placeholder, "->") !== false) {
                $parts = explode("->", $placeholder);

                $container = new Visio\Application\Component\Container($object, $this->container->http->request, $parts[0]);

                $method = substr($parts[1], 0, strpos($parts[1], "()"));
                $result = $container->run($method);

                return $result;
            } else {
                throw new Visio\Exception\Template("You must specified action or method name!");
            }
        } else {
            if (strpos($placeholder, "->") !== false) {
                $keys = explode("->", $placeholder);
                unset($keys[0]);

                foreach ($keys as $key) {
                    if (strpos($key, "()")) {
                        $method = substr($key, 0, strpos($key, "()"));
                        $object = call_user_func(array($object,
                                                       $method));

                        if ($object instanceof Visio\Application\Response\BaseResponse) {
                            return (string)$object;
                        }
                    } else {
                        $object = $object->$key;
                    }
                }
            }
        }

        return (string)$object;
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