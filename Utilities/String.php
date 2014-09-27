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
 * Strings related tools.
 *
 * @package Visio\Utilities
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
final class String {

    /**
     * __construct()
     */
    final public function __construct() {
        throw new Visio\Exception\Logical("Cannot instantiate static class " . get_class($this));
    }

    /**
     * @param string $string
     * @return int
     */
    public static function length($string) {
        //return mb_strlen($string, 'UTF-8');
        $string = utf8_decode($string);
        return strlen($string);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function lower($string) {
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * @param string $string
     * @return string
     */
    public static function upper($string) {
        return mb_strtoupper($string, 'UTF-8');
    }

    /**
     * @param string $string
     * @return string
     */
    public static function capitalize($string) {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * @param string $string
     * @return string
     */
    public static function ucfirst($string) {
        return ucfirst($string);
    }

    /**
     * @param $string
     * @param bool $strict
     * @return string
     */
    public static function detectEncoding($string, $strict = false) {
        return mb_detect_encoding($string, null, $strict);
    }

}