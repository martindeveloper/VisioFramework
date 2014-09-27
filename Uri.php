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
 * Class for URI
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Uri extends Visio\Object {

    /**
     * @var array
     */
    private $parts = array();

    /**
     * @var string
     */
    private $fullUri = "";

    private $scheme;
    private $host;
    private $user;
    private $pass;
    private $path;
    private $fragment;
    private $port;
    private $query;

    /**
     * @param string $uri
     * @throws Visio\Exception\General
     */
    public function __construct($uri) {
        if (!isset($uri)) {
            throw new Visio\Exception\General("\$uri variable must be set!");
        } else {
            $urlParts = @parse_url($uri);
            if ($urlParts === false) {
                throw new Visio\Exception\General("Unsupported URI format : " . $uri);
            }

            $this->fullUri = $uri;

            foreach ($urlParts as $key => $val) {
                $this->$key = $val;
            }

            $this->parts = $urlParts;

            #$x = strrpos($_SERVER["PHP_SELF"], '/');
            #$this->seo = substr($this->path, $x);
        }
    }

    /**
     * getScheme()
     *
     * @return string
     */
    public function getScheme() {
        return (isset($this->scheme) ? $this->scheme : false);
    }

    /**
     * getUser()
     *
     * @return string
     */
    public function getUser() {
        return (isset($this->user) ? $this->user : false);
    }

    /**
     * getPassword()
     *
     * @return string
     */
    public function getPassword() {
        return (isset($this->pass) ? $this->pass : false);
    }

    /**
     * getHost()
     *
     * @return string
     */
    public function getHost() {
        return (isset($this->host) ? $this->host : false);
    }

    /**
     * getPort()
     *
     * @return string
     */
    public function getPort() {
        return (isset($this->port) ? $this->port : false);
    }

    /**
     * getPath()
     *
     * @return string
     */
    public function getPath() {
        return (isset($this->path) ? $this->path : false);
    }

    /**
     * getQuery()
     *
     * @return string
     */
    public function getQuery() {
        return (isset($this->query) ? $this->query : false);
    }

    /**
     * getFragment()
     *
     * @return string
     */
    public function getFragment() {
        return (isset($this->fragment) ? $this->fragment : false);
    }

    /**
     * isSecure()
     *
     * @return bool
     */
    public function isSecure() {
        return (Visio\Utilities\String::lower($this->scheme) == "https") ? true : false;
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return (string)$this->fullUri;
    }

}