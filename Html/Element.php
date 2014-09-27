<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Html;

use Visio;

/**
 * Creating HTML elements.
 *
 * @package Visio\Html
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Element extends Visio\Object implements \ArrayAccess {

    /**
     * Inject before index
     *
     * @const Visio\Html\Element::INJECT_BEFORE
     */
    const INJECT_BEFORE = 1;

    /**
     * Inject after index
     *
     * @const Visio\Html\Element::INJECT_AFTER
     */
    const INJECT_AFTER = 2;

    /**
     * Inject top index
     *
     * @const Visio\Html\Element::INJECT_TOP
     */
    const INJECT_TOP = 3;

    /**
     * Inject bottom index
     *
     * @const Visio\Html\Element::INJECT_BOTTOM
     */
    const INJECT_BOTTOM = 4;

    /**
     * @var integer $mode
     */
    public $mode = Visio\Html\Element\Renderer::MODE_XHTML;

    /**
     * @var string $name
     */
    public $name;

    /**
     * @var bool $isPair
     */
    private $isPair;

    /**
     * @var array $attributes
     */
    public $attributes = array();

    /**
     * @var array $injections
     */
    public $injections = array();

    /**
     * @var string $innerHTML
     */
    private $innerHTML = "";

    /**
     * @var array $unpairedTags
     */
    private static $unpairedTags = array("br",
                                         "hr",
                                         "br",
                                         "input",
                                         "meta",
                                         "area",
                                         "link",
                                         "frame",
                                         "param",
                                         "base",
                                         "embed",
                                         "wbr",
                                         "source",
                                         "col",
                                         "img");

    /**
     * Factory for creating HTML elements.
     *
     * @param string $element
     * @param array $attributes
     * @return Visio\Html\Element
     */
    public static function create($element, $attributes = array()) {
        $element = Visio\Utilities\String::lower($element);
        $isPair = in_array($element, self::$unpairedTags);
        $newElement = new static($element, !$isPair);
        $newElement->setAttributes($attributes);

        return $newElement;
    }

    /**
     * __construct()
     *
     * @param string $element
     * @param bool $isPair
     */
    public function __construct($element, $isPair = false) {
        $this->name = Visio\Utilities\String::lower($element);
        $this->isPair = (bool)$isPair;
    }

    /**
     * render()
     *
     * @return string
     */
    public function render() {
        $renderer = new Visio\Html\Element\Renderer($this, $this->mode);
        return $renderer->render();
    }

    /**
     * changeMode()
     *
     * @param int $mode
     */
    public function changeMode($mode) {
        $this->mode = (integer)$mode;
    }

    /**
     * changeElement()
     *
     * @param string $newElement
     * @param bool $reset
     * @return Visio\Html\Element
     */
    public function changeElement($newElement, $reset = false) {
        $newElement = self::create($newElement);

        if ($reset === false) {
            $newElement->setAttributes($this->attributes);
        }

        return $newElement;
    }

    /**
     * inject()
     *
     * @param Visio\Html\Element $element
     * @param integer $where
     */
    public function inject(Visio\Html\Element $element, $where = self::INJECT_AFTER) {
        $this->injections[$where][] = $element;
    }

    /**
     * setAttributes()
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes) {
        $this->attributes = $attributes;
    }

    /**
     * isPair()
     *
     * @return bool
     */
    public function isPair() {
        return $this->isPair;
    }

    /**
     * getInnerHTML()
     *
     * @return string
     */
    public function getInnerHTML() {
        return $this->innerHTML;
    }

    /**
     * setAttribute()
     *
     * @param string $name
     * @param mixed $value
     */
    private function setAttribute($name, $value) {
        if ($value === true) {
            $value = $name;
        }

        if ($value === null || $value === false) {
            $this->unsetAttribute($name);
            return;
        }

        if ($name != 'inner') {
            if (is_array($value)) {
                switch ($name) {
                    case 'style':
                        $temp = "";

                        foreach ($value as $style => $styleValue) {
                            $temp .= $style . ":" . $styleValue . ";";
                        }

                        $value = $temp;
                        break;

                    default:
                        $value = implode("", $value);
                        break;
                }
            }

            $value = htmlspecialchars($value, ENT_QUOTES, "utf-8");
            $name = htmlspecialchars($name, ENT_QUOTES, "utf-8");

            $this->attributes[$name] = $value;
        } else {
            $this->innerHTML = $value;
        }
    }

    /**
     * unsetAttribute()
     *
     * @param string $name
     */
    private function unsetAttribute($name) {
        unset($this->attributes[$name]);
    }

    /**
     * __call()
     *
     * @param string $key
     * @param array $val
     * @return Visio\Html\Element
     */
    public function __call($key, $val) {
        $this->setAttribute($key, $val[0]);
        return $this;
    }

    /**
     * __set()
     *
     * @param string $key
     * @param mixed $val
     */
    public function __set($key, $val) {
        $this->setAttribute($key, $val);
    }

    /**
     * __get()
     *
     * @param string $name
     * @return mixed
     */
    public function &__get($name) {
        return $this->attributes[$name];
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return (string)$this->render();
    }

    /**
     * offsetSet()
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (!is_null($offset)) {
            $this->setAttribute($offset, $value);
        }
    }

    /**
     * offsetExists()
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    /**
     * offsetUnset()
     *
     * @param string $offset
     */
    public function offsetUnset($offset) {
        $this->unsetAttribute($offset);
    }

    /**
     * offsetGet()
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return $this->__get($offset);
    }

}
