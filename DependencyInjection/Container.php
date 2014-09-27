<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\DependencyInjection;

use Visio;

/**
 * Dependency Injection container
 *
 * @package Visio\DI
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Container extends \ArrayObject implements Visio\DependencyInjection\IContainer {

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @var array
     */
    public $objects = array();

    /**
     * @var array
     */
    private $sharedMap = array();

    /**
     * @var array
     */
    private $typeMap = array();

    /**
     * Set service/callable
     *
     * @param string $key
     * @param $object
     */
    public function __set($key, $object) {
        $key = Visio\Utilities\String::ucfirst($key);

        $this->insert($key, $object);
    }

    /**
     * Get service
     *
     * @param $key
     * @return object
     */
    public function __get($key) {
        return $this->obtain($key);
    }

    /**
     * Insert service/callable to container
     *
     * @param $key
     * @param $object
     * @param bool $shared
     * @throws \Visio\Exception
     */
    public function insert($key, $object, $shared = false) {
        $this->objects[trim($key)] = $object;
        $this->typeMap[trim($key)] = get_class($object);

        if ($shared === true) {
            $this->sharedMap[$key] = "";
        }
    }

    /**
     * @param $type
     * @return $this|object
     * @throws \Visio\Exception\General
     */
    public function obtainByType($type) {
        $type = trim($type, "\\");
        $key = array_search($type, $this->typeMap);

        if($key !== false) {
            return $this->obtain($key);
        }

        throw new Visio\Exception\General("Type '" . $type . "' is not found in container!");
    }

    /**
     * Obtain service from container
     *
     * @param $key
     * @return $this|object
     * @throws \Visio\Exception
     */
    public function obtain($key) {
        $key = Visio\Utilities\String::ucfirst(trim($key));

        if ($key == "Container") {
            return $this;
        }

        if (isset($this->objects[$key])) {
            if (is_callable($this->objects[$key])) {
                if (isset($this->sharedMap[$key]) && !is_object($this->sharedMap[$key])) {
                    $object = $this->objects[$key]($this);

                    if (is_callable($object)) {
                        $object = $object($this);
                    }

                    $this->sharedMap[$key] = $object;
                }

                if (isset($this->sharedMap[$key]) && is_object($this->sharedMap[$key])) {
                    return $this->sharedMap[$key];
                }

                $object = $this->objects[$key]($this);

                if (is_callable($object)) {
                    $object = $object($this);
                }

                $object = $this->injectByDocComments($object);

                return $object;
            }

            return $this->objects[$key];
        }

        throw new Visio\Exception("Obtaining undefined object at key '" . $key . "'!");
    }

    /**
     * Inject dependencies by doc comments
     *
     * @param $object
     * @return mixed
     * @throws \Visio\Exception\General
     */
    public function injectByDocComments($object) {
        $reflection = new \ReflectionClass($object);
        $classProperties = $reflection->getProperties();
        foreach ($classProperties as $classProperty) {
            $docComment = $classProperty->getDocComment();
            preg_match('~\@inject(.*)~i', $docComment, $match);

            if (!empty($match) && isset($match[1])) {
                $match[1] = trim($match[1]);

                if(empty($match[1])) {
                    preg_match('~\@var (.*)~i', $docComment, $typeMatch);

                    if(!empty($typeMatch) && isset($typeMatch[1])) {
                        $object->{$classProperty->getName()} = $this->obtainByType($typeMatch[1]);

                        continue;
                    }else{
                        throw new Visio\Exception\General("Trying to inject property without specified type!");
                    }
                }

                if(isset($this->objects[$match[1]])) {
                    $object->{$classProperty->getName()} = $this->obtain($match[1]);

                    continue;
                }
            }
        }

        return $object;
    }

    /**
     * @param $key
     */
    public function remove($key) {
        unset($this->objects[$key]);
        unset($this->sharedMap[$key]);
        unset($this->typeMap[$key]);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->parameters[$key];
    }

    /**
     * Set parameter
     * @param mixed $key
     * @param mixed $value
     */
    public function offsetSet($key, $value) {
        $this->parameters[$key] = $value;
    }

    /**
     * Is parameter exist?
     *
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key) {
        return (isset($this->parameters[$key]));
    }

    /**
     * @param mixed $key
     */
    public function offsetUnset($key) {
        unset($this->parameters[$key]);
    }

    /**
     * Initialize services from default files
     */
    public function initialize() {
        $file = new Visio\FileSystem\File(CONFIG_DIR . "SystemServices.json");
        $adapter = new Visio\Config\Adapter\Json($file);
        $runtime = new Visio\Config($adapter);
        $services = $runtime->getNamespace("Services");

        $this->createServicesFromArray($services);

        if (Visio\FileSystem::fileExist(CONFIG_DIR . "Services.json")) {

            $file = new Visio\FileSystem\File(CONFIG_DIR . "Services.json");
            $adapter = new Visio\Config\Adapter\Json($file);
            $runtime = new Visio\Config($adapter);
            $services = $runtime->getNamespace("Services");

            $this->createServicesFromArray($services);
        }
    }

    /**
     * Create services from array and inject dependencies.
     *s
     * @param array $services
     */
    private function createServicesFromArray(array $services) {
        foreach ($services as $service) {
            $this->insert($service["key"], function () use ($service) {
                $reflection = new \ReflectionClass($service["class"]);

                $arguments = (isset($service["arguments"]) ? $service["arguments"] : array());
                $properties = array();

                foreach ($arguments as $key => &$value) {
                    if (is_string($value) && $value[0] == "@") {
                        $value = $this->obtain(substr($value, 1));
                    }

                    if (is_string($key) && $key[0] == "-") {
                        $properties[substr($key, 1)] = $value;
                    }
                }

                if (isset($service["shared"]) && $service["shared"] == true) {
                    $this->sharedMap[$service["key"]] = "";
                }

                $object = $reflection->newInstanceArgs($arguments);

                foreach ($properties as $key => $value) {
                    $object->$key = $value;
                }

                $object = $this->injectByDocComments($object);

                return $object;
            });

            $this->typeMap[$service["key"]] = $service["class"];
        }
    }

    /**
     * Is object already in container?
     *
     * @param $key
     * @return bool
     */
    public function isRegistered($key) {
        $key = Visio\Utilities\String::ucfirst($key);

        return (isset($this->objects[$key]));
    }
}