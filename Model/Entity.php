<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Model;

use Visio;

/**
 * Entity
 *
 * @package Visio\Model
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
abstract class Entity extends Visio\Object implements \JsonSerializable, \ArrayAccess {

    /**
     * Create a new instance of entity
     *
     * @param array $data
     */
    public function __construct(array $data = array()) {
        $this->inject($data);
    }

    /**
     * Set data property to entity
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Get data property to entity
     *
     * @param string $name
     * @throws Visio\Exception\Model if accessing to undefined property
     * @return mixed
     */
    public function &__get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }

        throw new Visio\Exception\Model("Can not access to undefined property '" . $name . "' of entity!");
    }

    /**
     * Inject array of data into entity
     *
     * @param array $data
     */
    public function inject(array $data) {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /* JsonSerializable implementation */

    /**
     * @return mixed
     */
    public function jsonSerialize() {
        $array = array();

        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }

    /* ArrayAccess implementation */

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \Visio\Exception\InvalidArgument
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            throw new Visio\Exception\InvalidArgument("You must specify \$offset!");
        } else {
            $this->$offset = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->$offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->$offset);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {
        return isset($this->$offset) ? $this->$offset : null;
    }
}
