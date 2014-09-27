<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.5
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Http;

use Visio;

/**
 * Class handling HTTP request.
 *
 * @package Visio\Http
 * @author Martin Pernica
 * @version 3.5
 * @access public
 */
final class Request extends Visio\Object {

    /**
     * @var array
     */
    protected $query = [];

    /**
     * @var array
     */
    protected $post = [];

    /**
     * @var array
     */
    protected $put = [];

    /**
     * @var array
     */
    protected $delete = [];

    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var array
     */
    protected $cookies = [];

    /**
     * @var array
     */
    protected $headers = null;

    /**
     * __construct()
     */
    public function __construct($filter = true) {
        $method = $_SERVER["REQUEST_METHOD"];

        $this->getHeaders();
        $this->query = $filter ? filter_input_array(INPUT_GET, FILTER_UNSAFE_RAW) : (empty($_GET) ? array() : $_GET);

        switch (strtoupper($method)) {
            default:
            case "POST":
                $field = "post";
                break;
            case "PUT":
                $field = "put";
                break;
            case "DELETE":
                $field = "delete";
                break;
        }

        parse_str(file_get_contents("php://input"), $this->$field);

        $this->cookies = $filter ? filter_input_array(INPUT_COOKIE, FILTER_UNSAFE_RAW) : (empty($_COOKIE) ? array() : $_COOKIE);
        $this->files = $_FILES;
    }

    /**
     * getHeader()
     * @param string $header
     * @param string $failSafe
     * @return mixed
     */
    public function getHeader($header, $failSafe = "") {
        $header = Visio\Utilities\String::lower($header);
        if (isset($this->headers[$header])) {
            return $this->headers[$header];
        } else {
            return $failSafe;
        }
    }

    /**
     * getHeaders()
     * @return array
     */
    public function getHeaders() {
        if ($this->headers === null) {
            if (function_exists('apache_request_headers')) {
                $this->headers = array_change_key_case(apache_request_headers(), CASE_LOWER);
            } else {
                $this->headers = array();
                foreach ($_SERVER as $key => $val) {
                    if (strncmp($key, 'HTTP_', 5) == 0) {
                        $key = substr($key, 5);
                        $this->headers[Visio\Utilities\String::lower($key)] = $val;
                    }
                }
            }
        }

        return $this->headers;
    }

    /**
     * @param null $name
     * @return array|mixed|null
     */
    public function getQuery($name = null) {
        if (!is_null($name) && isset($this->query[$name])) {
            return $this->query[$name];
        }

        return $this->query;
    }

    /**
     * getMethod()
     * @return mixed
     */
    public function getMethod() {
        return (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null);
    }

    /**
     * isMethod()
     * @param  string
     * @return bool
     */
    public function isMethod($method) {
        return (isset($_SERVER['REQUEST_METHOD']) ? strcasecmp($_SERVER['REQUEST_METHOD'], $method) === 0 : false);
    }

    /**
     * getReferer()
     * @return string
     */
    public function getReferer() {
        $uri = $this->getHeader('referer');
        return $uri;
    }

    /**
     * isSecured()
     * @return bool
     */
    public function isSecured() {
        return (isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'off'));
    }

    /**
     * isAjax()
     * @return bool
     */
    public function isAjax() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && Visio\Utilities\String::lower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") {
            return true;
        }

        return false;
    }

    /**
     * getRemoteAddress
     * @return string
     */
    public function getRemoteAddress() {
        return (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null);
    }

    /**
     * getRemoteHost
     * @return string
     */
    public function getRemoteHost() {
        if (!isset($_SERVER['REMOTE_HOST'])) {
            if (!isset($_SERVER['REMOTE_ADDR'])) {
                return "";
            }

            $_SERVER['REMOTE_HOST'] = getHostByAddr($_SERVER['REMOTE_ADDR']);
        }

        return $_SERVER['REMOTE_HOST'];
    }

    /**
     * getQueryIndex
     * @param string $index
     * @return mixed
     */
    public function getQueryIndex($index) {
        return (isset($this->query[$index]) ? $this->query[$index] : null);
    }

    /**
     * @param $index
     * @param $value
     */
    public function setQueryIndex($index, $value) {
        $this->query[$index] = $value;
    }

    /**
     * setPostIndex
     * @param string $index
     * @param string $value
     * @return mixed
     */
    public function setPostIndex($index, $value) {
        $this->post[$index] = $value;
    }

    /**
     * getPostIndex
     * @param string $index
     * @return mixed
     */
    public function getPostIndex($index) {
        return (isset($this->post[$index]) ? $this->post[$index] : null);
    }

    /**
     * @return array|mixed|null
     */
    public function getPost() {
        return $this->post;
    }

    /**
     * getPutIndex
     * @param string $index
     * @return mixed
     */
    public function getPutIndex($index) {
        return (isset($this->put[$index]) ? $this->put[$index] : null);
    }

    /**
     * @return array|null
     */
    public function getPut() {
        return $this->put;
    }

    /**
     * getCookiesIndex
     * @param string $index
     * @return mixed
     */
    public function getCookiesIndex($index) {
        return (isset($this->cookies[$index]) ? $this->cookies[$index] : null);
    }

    /**
     * getCookies
     * @return mixed
     */
    public function getCookies() {
        return $this->cookies;
    }

    /**
     * getFilesIndex
     * @param string $index
     * @return mixed
     */
    public function getFilesIndex($index) {
        return (isset($this->files[$index]) ? $this->files[$index] : null);
    }

}