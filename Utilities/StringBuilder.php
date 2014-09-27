<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Utilities;

use Visio;

/**
 * Simple string builder.
 *
 * @package Visio\Utilities
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class StringBuilder extends Visio\Object {

    /**
     * @var string $buffer
     */
    private $buffer = "";

    /**
     * append()
     * 
     * @param string $string
     * @return Visio\Utilities\StringBuilder
     */
    public function append($string) {
        $this->buffer .= (string) $string;
        return $this;
    }

    /**
     * appendLine()
     * 
     * @param string $string
     * @return Visio\Utilities\StringBuilder
     */
    public function appendLine($string) {
        $this->buffer .= (string) $string . "\n";
        return $this;
    }

    /**
     * delete()
     * 
     * @param string $string
     * @param integer $limit
     * @return Visio\Utilities\StringBuilder
     */
    public function delete($string, $caseIntensive = false, $limit = null) {
        $this->buffer = preg_replace("/" . preg_quote($string) . "/s" . ($caseIntensive === true ? "i" : "") . "u", "", $this->buffer, $limit);
        return $this;
    }

    /**
     * replace()
     * 
     * @param string $string
     * @param string $replacement
     * @param integer $limit
     * @return Visio\Utilities\StringBuilder
     */
    public function replace($string, $replacement, $caseIntensive = false, $limit = null) {
        $this->buffer = preg_replace("/" . preg_quote($string) . "/s" . ($caseIntensive === true ? "i" : "") . "u", $replacement, $this->buffer, $limit);
        return $this;
    }

    /**
     * rtrim()
     * 
     * @param string $chars
     * @return Visio\Utilities\StringBuilder
     */
    public function rtrim($chars) {
        $this->buffer = rtrim($this->buffer, $chars);
        return $this;
    }

    /**
     * ltrim()
     * 
     * @param string $chars
     * @return Visio\Utilities\StringBuilder
     */
    public function ltrim($chars) {
        $this->buffer = ltrim($this->buffer, $chars);
        return $this;
    }

    /**
     * __toString()
     * 
     * @return string
     */
    public function __toString() {
        return $this->buffer;
    }

}
