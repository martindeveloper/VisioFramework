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
use Visio\FileSystem;

/**
 * Critical logger class. Logging only into file.
 * But you can attach your logger using 'Logger-onWrite' event.
 *
 * @package Visio\Diagnostic
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Logger extends Visio\Object {

    /**
     * Application config
     *
     * @var Visio\Config
     */
    protected $config = null;

    /**
     * __construct()
     */
    public function __construct(Visio\Config $config) {
        Visio\Events::addListener("Exception-onThrow", $this, "write");

        $this->config = $config;
    }

    /**
     * write()
     *
     * @param string $message
     * @param integer $level
     * @param string $type
     * @param int $lineNumber
     * @param string $file
     */
    public function write($message, $level = 1, $type = "Unknown", $lineNumber = 0, $file = "Unknown") {
        Visio\Events::dispatch("Logger-onWrite", array($message,
                                                       $level,
                                                       $type));

        $folder = str_replace(array('/',
                                    '\\'), array(DS,
                                                 DS), $this->config->get("logs", "Directories"));

        if ($folder !== false) {

            $fileName = $folder . DS . Visio\DateTime::getInstance()->format('Y-m-d') . '.log';
            $fileName2 = $folder . DS . 'last.tmp';
            $environment = $this->config->get('environment', 'Application');

            $line = "Visio application error";
            $line .= "\n--------------------------------------";
            $line .= "\nDate: " . Visio\DateTime::getInstance()->format("m/d/y H:i:s");
            $line .= "\nMessage: " . $message;
            $line .= "\nError type: " . $type;
            $line .= "\nError level: " . $level;
            $line .= "\nError file: " . $file;
            $line .= "\nError line: " . $lineNumber;

            if (isset($_SERVER['SERVER_NAME']) && isset($_SERVER['REQUEST_URI'])) {
                $line .= "\nRequest URL: //" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            }

            $line .= "\nApplication environment: " . $environment;
            $line .= "\nVisio version: " . Visio\Framework::VERSION;
            $line .= "\n--------------------------------------";

            //Try write to log file
            try {
                $file = new FileSystem\File($fileName, true, false);

                $content = "\r\n" . $line . "\r\n" . $file->getContent();
                $file->chmod = 0777;
                $file->content = $content;
                $file->save(null, true); //Silent
                unset($file);
            } catch (Visio\Exception $ex) {
                //Silent
            }

            //Send mail
            try {
                $hash = sha1($message);

                $hashFile = new FileSystem\File($fileName2, true, true);
                $hashFile->chmod = 0777;

                $lastHash = $hashFile->getContent();
                if ($lastHash != $hash) {
                    if ($this->config->get('emailEnabled', 'Logger') && $this->config->get('email', 'Logger')) {
                        @mail($this->email, 'VFramework Logger', $line, 'From: VFramework');
                    } //Do not use Visio\Mailer!

                    $hashFile->content = $hash;
                    $hashFile->save(null, true); //Silent
                }
                unset($hashFile);
            } catch (Visio\Exception $ex) {
                //Silent
            }
        }
    }

}