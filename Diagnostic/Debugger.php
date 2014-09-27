<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Diagnostic;

use Visio;

/**
 * Debugger class for handling errors and exceptions.
 *
 * @package Visio\Diagnostic
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Debugger {

    /**
     * Visio\Diagnostic\Debugger::__construct()
     *
     * @throws Visio\Exception\Logical if this class tried to be instantiated
     */
    public function __construct() {
        throw new Visio\Exception\Logical("Cannot instantiate static class " . get_class($this));
    }

    /**
     * Register PHP error and exception handlers
     */
    public static function register() {
        set_error_handler(array("\\Visio\\Diagnostic\\Debugger",
                                "handleError"), E_ALL);
        set_exception_handler(array("\\Visio\\Diagnostic\\Debugger",
                                    "handleException"));

        register_shutdown_function(function () {
            $error = error_get_last();

            if ($error !== NULL) {
                $errorType = $error["type"];
                $errorFile = $error["file"];
                $errorLine = $error["line"];
                $errorMessage = $error["message"];

                $exception = new Visio\Exception\PhpError($errorMessage, $errorType, $errorFile, $errorLine);
                $exception->showErrorMessage();
            }
        });
    }

    /**
     * Unregister PHP error and exception handlers
     */
    public static function unregister() {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * Handle PHP error/warning/notice
     *
     * @param int $number
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public static function handleError($number, $message, $file, $line) {
        if (error_reporting() !== 0) { // Error is NOT silent @
            $error = new Visio\Exception\PhpError($message, $number, $file, $line);
            $error->showErrorMessage();
        }
    }

    /**
     * Handle PHP exception
     *
     * @param mixed $exception
     */
    public static function handleException($exception) {
        $exception = new Visio\Exception\Internal($exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine());
        $exception->showErrorMessage();
    }

    /**
     * Translate PHP error code to readable text
     *
     * @param int $code
     * @return string
     */
    public static function translateErroCode($code) {
        $types = array(E_WARNING => 'Warning',
                       E_COMPILE_WARNING => 'Warning',
                       E_USER_WARNING => 'Warning',
                       E_NOTICE => 'Notice',
                       E_USER_NOTICE => 'Notice',
                       E_STRICT => 'Strict standards',
                       @E_DEPRECATED => 'Deprecated',
            //since PHP 5.3.0
                       @E_USER_DEPRECATED => 'Deprecated'
            //since PHP 5.3.0
        );

        return (isset($types[$code]) ? $types[$code] : 0);
    }

    /**
     * Dump variable for debug purpose
     *
     * @param mixed $var
     * @param bool $die
     */
    public static function dumpVariable($var, $die = true) {
        echo "It is '" . gettype($var) . "' type and have folowing value:\n";
        var_dump($var);

        if ($die === true) {
            die();
        }
    }

}