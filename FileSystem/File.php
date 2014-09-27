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
 * File wrapper class for comfortable work with single file.
 *
 * @package Visio\FileSystem
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class File extends Visio\Object implements Visio\FileSystem\IFile {

    /**
     * Original path to file
     *
     * @var string $_path
     */
    protected $_path;

    /**
     * Resource pointer
     *
     * @var resource $pointer
     */
    protected $pointer;

    /**
     * Is file changed?
     *
     * @var bool $changed
     */
    protected $changed = false;

    /**
     * File extension
     *
     * @var string $extension
     */
    private $extension;

    /**
     * MIME type of file
     *
     * @var string $mimeType
     */
    private $mimeType;

    /**
     * Path to file
     *
     * @var string $path
     */
    private $path;

    /**
     * Full path to file
     *
     * @var string $fullPath
     */
    private $fullPath;

    /**
     * Folder name of file
     *
     * @var string $folder
     */
    private $folder;

    /**
     * Name of file
     *
     * @var string $name
     */
    private $name;

    /**
     * Size of file
     *
     * @var int $size
     */
    private $size;

    /**
     * Access time of file
     *
     * @var int $accessTime
     */
    private $accessTime;

    /**
     * Modification time of file
     *
     * @var int $modificationTime
     */
    private $modificationTime;

    /**
     * Current CHMOD of file
     *
     * @var int $chmod
     */
    protected $chmod;

    /**
     * Content of file
     *
     * @var string $content
     */
    protected $content;

    /**
     * __construct()
     *
     * @param string $path
     * @param bool $create
     * @param bool $silent
     * @throws Visio\Exception\FileNotFound if file not found or is not readable and creating is disabled
     * @throws Visio\Exception\File if can not create file
     */
    public function __construct($path, $create = false, $silent = false) {
        if ((!Visio\FileSystem::fileExist($path) || !Visio\FileSystem::isReadable($path)) && $create === false) {
            throw new Visio\Exception\FileNotFound("Path '" . $path . "' is invalid! Or file is not readable!");
        }

        if ($create === true && !Visio\FileSystem::fileExist($path)) {
            $fileResource = @fopen($path, "w");
            if ($fileResource !== false) {
                fclose($fileResource);
                unset($fileResource);
            } else {
                if ($silent !== true) {
                    throw new Visio\Exception\File("Cannot create file '" . $path . "'!");
                }
            }
        }

        $this->_path = $path;
        $this->parseFileInfo();
    }

    /**
     * Get content of file
     *
     * @return string
     */
    public function getContent() {
        if (!$this->content) {
            $this->refreshContent();
        }

        return $this->content;
    }

    /**
     * Refresh content of file
     *
     * @throws Visio\Exception\File if can not read file
     */
    public function refreshContent() {
        if (Visio\FileSystem::isReadable($this->_path)) {
            $this->content = file_get_contents($this->_path);
        } else {
            throw new Visio\Exception\File("Cannot read file '" . $this->_path . "'!");
        }
    }

    /**
     * Refresh size of file
     */
    public function refreshSize() {
        $this->size = filesize($this->_path);
    }

    /**
     * Set a new content of file
     *
     * @param string $content
     */
    public function setContent($content) {
        $this->changed = true;
        $this->content = $content;
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
     * getExtension()
     *
     * @return string
     */
    public function getExtension() {
        return $this->extension;
    }

    /**
     * getMimeType()
     *
     * @return string
     */
    public function getMimeType() {
        return $this->mimeType;
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
     * getFolder()
     *
     * @return string
     */
    public function getFolder() {
        return $this->folder;
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
     * getSize()
     *
     * @return int
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * getAccessTime()
     *
     * @return string
     */
    public function getAccessTime() {
        return $this->accessTime;
    }

    /**
     * getModificationTime()
     *
     * @return string
     */
    public function getModificationTime() {
        return $this->modificationTime;
    }

    /**
     * save()
     *
     * @param mixed $lock
     * @param bool $silent
     * @throws Visio\Exception\File if can not write to file
     * @throws Visio\Exception\File if can not set chmod of file
     * @return bool
     */
    public function save($flag = null, $silent = false) {
        if ($this->changed !== false) {
            if (!Visio\FileSystem::isWritable($this->_path)) {
                throw new Visio\Exception\File("File is not writeable!");
            }

            $write = @file_put_contents($this->_path, $this->content, $flag);
            $chmod = @chmod($this->_path, $this->chmod);

            if ($silent === false) {
                if ($write === false) {
                    throw new Visio\Exception\File("Cannot write to file '" . $this->_path . "'!");
                }
                if ($chmod === false) {
                    throw new Visio\Exception\File("Cannot set chmod '" . $this->chmod . "' to file '" . $this->_path . "'!");
                }
            }
        }

        return true;
    }

    /**
     * delete()
     *
     * @return bool
     */
    public function delete() {
        return (bool)Visio\FileSystem::deleteFile($this->_path);
    }

    /**
     * parseFileInfo()
     */
    private function parseFileInfo() {
        clearstatcache();
        $parts = pathinfo($this->_path);
        $this->extension = $parts['extension'];
        $this->path = $parts['dirname'];
        $this->fullPath = $this->_path;
        $this->name = $parts['basename'];
        $this->folder = dirname($this->_path);
        $this->chmod = @substr(sprintf('%o', fileperms($this->_path)), -4);
        $this->size = filesize($this->_path);
        $this->mimeType = Visio\Utilities::getMimeContentType($this->_path);
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return $this->getFullPath();
    }

    /**
     * __destroy()
     */
    public function __destroy() {
        if (is_resource($this->pointer)) {
            fclose($this->pointer);
        }
    }

}