<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

/**
 * Visio\ArrayList
 * 
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class ArrayList implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * @var array
     */
    private $arrayList = array();

    /**
     * @var bool
     */
    protected $special;

    /**
     * __construct
     *
     * @param bool
     */
    public function __construct($special = false) {
        $this->special = $special;
    }

    /**
     * Returns an iterator over all items.
     * @return \ArrayIterator
     */
    public function getIterator() {
        return new \ArrayIterator($this->arrayList);
    }

    /**
     * count
     * @return int
     */
    public function count() {
        return (int) count($this->arrayList);
    }

    /**
     * offsetSet
     * 
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value) {
        if ($this->special === true && is_array($value)) {
            $this->specialOffsetSet($value);
        } elseif (is_null($offset)) {
            $this->arrayList[] = $value;
        } else {
            $this->arrayList[$offset] = $value;
        }
    }

    /**
     * offsetExists
     * 
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->arrayList[$offset]);
    }

    /**
     * offsetUnset
     * 
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset) {
        unset($this->arrayList[$offset]);
    }

    /**
     * offsetGet
     * 
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        return isset($this->arrayList[$offset]) ? $this->arrayList[$offset] : null;
    }

    /**
     * specialOffsetSet
     *
     * @param array
     * @return void
     */
    public function specialOffsetSet(array $value) {
        foreach ($value as $val) {
            $this->arrayList[] = $val;
        }
    }

}