<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Mailer;

use Visio;

/**
 * Class for attachment at Visio\Mailer
 * 
 * @package Visio\Mailer
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Attachment extends Visio\Object {

    protected $name;
    protected $base64;
    protected $path;
    protected $boundary;

    /**
     * __construct()
     * 
     * @param mixed $filePath
     */
    public function __construct($filePath) {
        if (!Visio\FileSystem::fileExist($filePath)) {
            throw new Visio\Exception\FileNotFound("File for attachment not found at path '" . $filePath . "'!");
        }

        $this->path = $filePath;
        $this->name = basename($filePath);
    }

    /**
     * getBase64()
     * 
     * @return string
     */
    public function getBase64() {
        $this->base64 = chunk_split(base64_encode(Visio\FileSystem::readFile($this->path)));

        return $this->base64;
    }

    /**
     * getName()
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * setBoundary()
     * 
     * @param string $boundary
     */
    public function setBoundary($boundary) {
        $this->boundary = $boundary;
    }

    /**
     * __toString()
     * 
     * @return string
     */
    public function __toString() {
        $buffer = "";
        $buffer .= "Content-Type: " . Visio\Utilities::getMimeContentType($this->name) . "; name=\"" . $this->name . "\"\n";
        $buffer .= "Content-Transfer-Encoding: base64\n";
        $buffer .= "Content-Disposition: attachment; ";
        $buffer .= "filename=\"" . $this->name . "\"\r\n\n";
        $buffer .= $this->getBase64();
        $buffer .= "--" . $this->boundary . "--\n\r";

        return $buffer;
    }

}