<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\FileSystem;

use Visio;

/**
 * Folder wrapper class for comfortable work with single folder.
 *
 * @package Visio\FileSystem
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Folder extends Visio\Object implements Visio\FileSystem\IFolder {

    /**
     * Original path to folder
     *
     * @var string $_path
     */
    protected $_path;

    /**
     * Resource pointer to folder
     *
     * @var resource $pointer
     */
    protected $pointer;

    /**
     * Array of content of folder
     *
     * @var array $content
     */
    private $content = array();

    /**
     * Path to file
     *
     * @var string $path
     */
    private $path;

    /**
     * Full path of file
     *
     * @var string $fullPath
     */
    private $fullPath;

    /**
     * CHMOD of file
     * @var int $chmod
     */
    public $chmod;

    /**
     * __construct()
     *
     * @param mixed $path
     * @param bool $create
     * @throws Visio\Exception\FolderNotFound if path is invalid and create is false
     * @throws Visio\Exception if can not create folder
     */
    public function __construct($path, $create = false) {
        $path = realpath($path);
        if (!Visio\FileSystem::directoryExist($path) && $create === false) {
            throw new Visio\Exception\FolderNotFound("Path '" . $path . "' is invalid!");
        }

        if ($create === true) {
            if (@mkdir($path, 0666) === false) {
                throw new Visio\Exception("Cannot create folder '" . $path . "'!");
            }
        }

        $this->_path = $path;
        $this->pointer = dir($path);
        $this->parseFolderInfo();
    }

    /**
     * Refresh content of folder
     */
    public function refreshContent() {
        while ($entry = $this->pointer->read()) {
            $path = $this->_path . DS . $entry;

            if (Visio\FileSystem::fileExist($path)) {
                $this->content[$entry] = new Visio\FileSystem\File($path, false);
            } else if (Visio\FileSystem::directoryExist($path)) {
                $this->content[$entry] = new Visio\FileSystem\Folder($path, false);
            } else {
                $this->content[$entry] = $entry;
            }
        }
    }

    /**
     * Save attributes of folder
     *
     * @param bool $silent
     * @throws Visio\Exception when can not set new CHMOD
     * @return bool
     */
    public function save($silent = false) {
        $chmodResult = @chmod($this->_path, $this->chmod);

        if ($chmodResult === false && $silent === false) {
            throw new Visio\Exception("Cannot set chmod '" . $this->chmod . "' to folder '" . $this->_path . "'!");
        }

        return true;
    }

    /**
     * Delete folder
     *
     * @param bool $recursive
     * @return bool
     */
    public function delete($recursive = false) {
        if ($recursive === true) {
            $directoryI = new \DirectoryIterator($this->fullPath);
            foreach ($directoryI as $file) {
                if ($file->isDot()) {
                    continue;
                }

                if ($file->isDir()) {
                    $this->delete($file->getPathname(), true);
                }

                unlink($file->getPathname());
            }

            return true;
        } else {
            return (bool)Visio\FileSystem::deleteFolder($this->path);
        }
    }

    /**
     * Get content of folder
     *
     * @return array
     */
    public function getContent() {
        if (empty($this->content)) {
            $this->refreshContent();
        }

        return $this->content;
    }

    /**
     * getPath()
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * getFullPath()
     *
     * @return string
     */
    public function getFullPath() {
        return $this->fullPath;
    }

    /**
     * getChmod()
     *
     * @return int
     */
    public function getChmod() {
        return $this->chmod;
    }

    /**
     * setChmod()
     *
     * @param int $chmod
     */
    public function setChmod($chmod) {
        $this->chmod = $chmod;
    }

    /**
     * parseFolderInfo()
     */
    private function parseFolderInfo() {
        $parts = pathinfo($this->_path);
        $this->path = $parts['dirname'];
        $this->fullPath = $this->_path;
        $this->chmod = substr(sprintf('%o', fileperms($this->_path)), -4);
    }

    /**
     * __destroy()
     */
    public function __destroy() {
        $this->pointer->close();
    }

}