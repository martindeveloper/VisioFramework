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
 * Wrapper for work with cookies.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Cookie extends Visio\Object {

    /**
     * @var Visio\DependencyInjection\IContainer $container
     */
    private $container;

    /**
     * __construct()
     *
     * @param Visio\DependencyInjection\IContainer $container
     */
    public function __construct(Visio\DependencyInjection\IContainer $container) {
        $this->container = $container;
    }

    /**
     * Get cookie by name
     *
     * @param mixed $name
     * @return mixed
     */
    public function get($name) {
        $cookie = Visio\Http::getInstance()->request->getCookiesIndex($name);
        if (!is_null($cookie)) {
            return $cookie;
        }

        return false;
    }

    /**
     * Set a new cookie
     *
     * @param mixed $var
     * @param mixed $val
     * @param mixed $path
     * @param mixed $domain
     * @param mixed $expires
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function set($var, $val, $path = null, $domain = null, $expires = null, $secure = false, $httpOnly = false) {
        if ($expires === null) {
            $expires = ($this->container->applicationConfig->get("lifetime", "Cookie") + time());
        }

        return setcookie($var, $val, $expires, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Check if cookie exists
     *
     * @param mixed $var
     * @return bool
     */
    public function exists($var) {
        $cookie = Visio\Http::getInstance()->request->getCookiesIndex($var);
        if (!is_null($cookie)) {
            return true;
        }

        return false;
    }

    /**
     * Delete specified cookie
     *
     * @param mixed $var
     * @param mixed $path
     * @param mixed $domain
     * @return bool
     */
    public function delete($var, $path = null, $domain = null) {
        return setcookie($var, false, time() - 60000, $path, $domain);
    }

}