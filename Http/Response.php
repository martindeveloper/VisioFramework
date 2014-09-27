<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Http;

use Visio;

/**
 * Class handling HTTP response.
 * 
 * @package Visio\Http
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
final class Response extends Visio\Object {

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $baseUrl = "";

    /**
     * __construct()
     */
    public function __construct() {
        foreach (headers_list() as $header) {
            $pos = strpos($header, ':');
            $this->headers[substr($header, 0, $pos)] = substr($header, $pos + 2);
        }
    }

    /**
     * addHeader()
     * 
     * @param string $name
     * @param string $value
     * @return Visio\Http\Response
     */
    public function addHeader($name, $value) {
        if (headers_sent($file, $line)) {
            throw new Visio\Exception\General("Cannot send header after HTTP headers have been sent (output started at " . $file . ":" . $line . ").");
        }

        header($name . ': ' . $value, false);

        return $this;
    }

    /**
     * addHeaderRaw()
     * 
     * @param string $header Raw header
     * @param bool $replace
     * @param mixed $code
     * @return Visio\Http\Response
     */
    public function addHeaderRaw($header, $replace = false, $code = null) {
        if (headers_sent($file, $line)) {
            throw new Visio\Exception\General("Cannot send header after HTTP headers have been sent (output started at " . $file . ":" . $line . ").");
        }

        header($header, (bool) $replace, $code);

        return $this;
    }

    /**
     * setContentType()
     * 
     * @param string $type
     * @param string $charset
     * @return Visio\Http\Response object
     */
    public function setContentType($type, $charset = null) {
        $this->addHeader('Content-Type', $type . ($charset ? ('; charset=' . $charset) : ''));

        return $this;
    }

    /**
     * getBaseUrl()
     * 
     * @return string
     */
    public function getBaseUrl() {
        if (empty($this->baseUrl)) {
            $dirName = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $_SERVER['SERVER_NAME'] .= ($_SERVER['SERVER_PORT'] != '80' ? (':' . $_SERVER['SERVER_PORT']) : '');
            if (isset($_SERVER["HTTPS"]) && Visio\Utilities\String::lower($_SERVER["HTTPS"]) == 'on') {
                $url = "https://" . $_SERVER['SERVER_NAME'] . $dirName . "/";
            } else {
                $url = "http://" . $_SERVER['SERVER_NAME'] . $dirName . "/";
            }
            $this->baseUrl = $url;
        }

        return $this->baseUrl;
    }

    /**
     * redirect()
     * 
     * @param string $url
     * @return null
     */
    public function redirect($url) {
        $this->addHeader('Location', $url);

        die("<h1>Redirecting ...</h1>\n\r\n\r<p>Please click <a href=\"" . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . "\">here</a> to continue.</p>");
    }

    /**
     * isSent()
     * @return bool
     */
    public function isSent() {
        return headers_sent();
    }

    /**
     * getHeaders()
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * getHeaders()
     * @param string $name
     * @return string
     */
    public function getHeader($name) {
        return (isset($this->headers[$name]) ? $this->headers[$name] : "");
    }

}