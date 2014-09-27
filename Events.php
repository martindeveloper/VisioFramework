<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * Events for internal hooks in classes.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Events extends Visio\Object {

    private static $events = array();

    /**
     * Visio\Events::dispatch()
     * 
     * @param string $event
     * @param mixed $args
     */
    public static function dispatch($event, $args = array()) {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $class) {
                //call_user_func($func, $args);
                $obj = $class[0];
                $method = $class[1];

                call_user_func_array(array($obj, $method), $args);

                //$obj->$method($args);
            }
        }
    }

    /**
     * Visio\Events::addListener()
     * 
     * @param string $event
     * @param mixed $obj
     * @param string $method
     */
    public static function addListener($event, $obj, $method) {
        $class = get_class($obj);
        self::$events[$event][$class][0] = $obj;
        self::$events[$event][$class][1] = $method;
    }

}