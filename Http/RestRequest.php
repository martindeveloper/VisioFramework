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
 * Class for sending REST requests.
 *
 * @package Visio\Http
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class RestRequest extends Visio\Object implements Visio\Http\IRequest {

    /**
     * Requested URL
     *
     * @var Visio\Uri $url
     */
    private $url;

    /**
     * Method of request
     *
     * @var string $method
     */
    private $method;

    /**
     * Body of request
     *
     * @var string $requestBody
     */
    private $requestBody;

    /**
     * Length of request body
     *
     * @var int $requestLength
     */
    private $requestLength;

    /**
     * Username
     *
     * @var string $username
     */
    private $username;

    /**
     * Password
     *
     * @var string $password
     */
    private $password;

    /**
     * Accept type
     *
     * @var string $acceptType
     */
    private $acceptType;

    /**
     * Body of respond
     *
     * @var string $responseBody
     */
    private $responseBody;

    /**
     * Info about respond
     *
     * @var string $responseInfo
     */
    private $responseInfo;

    /**
     * Method constants
     */
    const METHOD_GET = "GET", METHOD_POST = "POST", METHOD_PUT = "PUT", METHOD_DELETE = "DELETE";

    /**
     * __construct()
     *
     * @param Visio\Uri $url
     * @param string $method
     * @param string $requestBody
     */
    public function __construct(Visio\Uri $url, $method = self::METHOD_GET, $requestBody = "") {
        $this->url = $url;
        $this->method = $method;
        $this->requestBody = $requestBody;
        $this->requestLength = 0;
        $this->username = "";
        $this->password = "";
        $this->acceptType = 'application/json';

        if ($this->requestBody !== "") {
            $this->buildPostBody();
        }
    }

    /**
     * Flush current buffer
     */
    public function flush() {
        $this->requestBody = "";
        $this->requestLength = 0;
        $this->method = self::METHOD_GET;
        $this->responseBody = "";
        $this->responseInfo = "";
    }

    /**
     * Execute request
     *
     * @throws Visio\Exception
     * @throws Visio\Exception\InvalidArgument
     *
     * @return string
     */
    public function execute() {
        $curl = new Visio\Http\CurlRequest($this->url);
        $this->setAuth($curl);

        try {
            switch (Visio\Utilities\String::upper($this->method)) {
                case self::METHOD_GET:
                    $this->executeGet($curl);
                    break;
                case self::METHOD_POST:
                    $this->executePost($curl);
                    break;
                case self::METHOD_PUT:
                    $this->executePut($curl);
                    break;
                case self::METHOD_DELETE:
                    $this->executeDelete($curl);
                    break;
                default:
                    throw new Visio\Exception\InvalidArgument('Invalid REST method, given ' . $this->method . '.');
            }

            return $this->responseBody;
        } catch (Visio\Exception $ex) {
            $curl->close();

            throw $ex;
        }
    }

    /**
     * Build body of post
     *
     * @param array $data
     */
    public function buildPostBody(array $data = array()) {
        $data = (!empty($data)) ? $data : $this->requestBody;

        $data = http_build_query($data, '', '&');
        $this->requestBody = $data;
    }

    /**
     * Execute request using GET method
     *
     * @param Visio\Http\CurlRequest $curl
     */
    private function executeGet(Visio\Http\CurlRequest $curl) {
        $this->doExecute($curl);
    }

    /**
     * Execute request using POST method
     *
     * @param Visio\Http\CurlRequest $curl
     */
    private function executePost(Visio\Http\CurlRequest $curl) {
        if (!is_string($this->requestBody)) {
            $this->buildPostBody();
        }

        $curl->setOption(CURLOPT_POSTFIELDS, $this->requestBody);
        $curl->setOption(CURLOPT_POST, 1);

        $this->doExecute($curl);
    }

    /**
     * Execute request using PUT method
     *
     * @param Visio\Http\CurlRequest $curl
     */
    private function executePut(Visio\Http\CurlRequest $curl) {
        if (!is_string($this->requestBody)) {
            $this->buildPostBody();
        }

        $this->requestLength = strlen($this->requestBody);

        $phpMemory = fopen('php://memory', 'rw');
        fwrite($phpMemory, $this->requestBody);
        rewind($phpMemory);

        $curl->setOption(CURLOPT_INFILE, $phpMemory);
        $curl->setOption(CURLOPT_INFILESIZE, $this->requestLength);
        $curl->setOption(CURLOPT_PUT, true);

        $this->doExecute($curl);

        fclose($phpMemory);
    }

    /**
     * Execute request using DELETE method
     *
     * @param Visio\Http\CurlRequest $curl
     */
    private function executeDelete(Visio\Http\CurlRequest $curl) {
        $curl->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

        $this->doExecute($curl);
    }

    /**
     * Execute request
     *
     * @param Visio\Http\CurlRequest $curlHandler
     */
    private function doExecute(Visio\Http\CurlRequest $curlHandler) {
        $this->setCurlOpts($curlHandler);
        $this->responseBody = $curlHandler->execute();
        $this->responseInfo = $curlHandler->getInfo();

        $curlHandler->close();
    }

    /**
     * Set cURL options
     *
     * @param Visio\Http\CurlRequest $curlHandler
     * @return Visio\Http\CurlRequest
     */
    private function setCurlOpts(Visio\Http\CurlRequest $curlHandler) {
        $curlHandler->setOption(CURLOPT_TIMEOUT, 10);
        $curlHandler->setOption(CURLOPT_URL, (string)$this->url);
        $curlHandler->setOption(CURLOPT_RETURNTRANSFER, true);
        $curlHandler->setOption(CURLOPT_HTTPHEADER, array('Accept: ' . $this->acceptType));

        return $curlHandler;
    }

    /**
     * Set authentication
     *
     * @param Visio\Http\CurlRequest $curlHandler
     * @return Visio\Http\CurlRequest
     */
    private function setAuth(Visio\Http\CurlRequest $curlHandler) {
        if ($this->username !== "" && $this->password !== "") {
            $curlHandler->setOption(CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
            $curlHandler->setOption(CURLOPT_USERPWD, $this->username . ':' . $this->password);
        }

        return $curlHandler;
    }

    /**
     * @return string
     */
    public function getAcceptType() {
        return $this->acceptType;
    }

    /**
     * @param string $acceptType
     */
    public function setAcceptType($acceptType) {
        $this->acceptType = $acceptType;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getResponseBody() {
        return $this->responseBody;
    }

    /**
     * @return string
     */
    public function getResponseInfo() {
        return $this->responseInfo;
    }

    /**
     * @return string
     */
    public function getUrl() {
        return (string)$this->url;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }
}
