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
 * Class for sending cURL requests.
 *
 * @package Visio\Http
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class CurlRequest extends Visio\Object implements Visio\Http\IRequest {

    /**
     * @var null|resource
     */
    private $handle = null;

    /**
     * @param Visio\Uri $url
     */
    public function __construct(Visio\Uri $url) {
        $this->handle = curl_init((string)$url);
    }

    /**
     * Set option
     *
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
    }

    /**
     * Execute cURL request
     *
     * @return mixed
     */
    public function execute() {
        return curl_exec($this->handle);
    }

    /**
     * Get cURL info
     *
     * @return mixed
     */
    public function getInfo() {
        return curl_getinfo($this->handle);
    }

    /**
     * Close cURL handler
     */
    public function close() {
        curl_close($this->handle);
    }
}