<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Cache\Storage;

use Visio;

/**
 * File based cache storage
 *
 * @package Visio\Cache
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class File extends Visio\Object implements Visio\Cache\IStorage {

    /**
     * @var string $directory
     */
    private $directory;

    /**
     * __construct()
     *
     * @param Visio\Config $config
     * @throws Visio\Exception\DirectoryNotFound if directory, specified at config file, is not found
     */
    public function __construct(Visio\Config $config) {
        $this->directory = str_replace(array('/',
                                             '%app%',
                                             '\\'), array(DS,
                                                          APP_DIR,
                                                          DS), $config->get("cache", "Directories"));

        $this->directory = realpath($this->directory);

        if (!Visio\FileSystem::directoryExist($this->directory)) {
            throw new Visio\Exception\DirectoryNotFound("Cache directory '" . $this->directory . "' not found!");
        }

        if (!Visio\FileSystem::isWritable($this->directory)) {
            throw new Visio\Exception\DirectoryNotFound("Cache directory '" . $this->directory . "' is not writable!");
        }
    }

    /**
     * get()
     *
     * @param mixed $key
     * @param string $namespace
     * @param integer $expiration
     * @return mixed
     */
    public function get($key, $namespace, $expiration) {
        if (!Visio\FileSystem::directoryExist($this->directory) || !Visio\FileSystem::isWritable($this->directory)) {
            return null;
        }

        $cachePath = $this->createName($key, $namespace);

        if (!Visio\FileSystem::fileExist($cachePath)) {
            return null;
        }

        if (!$this->isValid($key, $namespace, $expiration)) {
            $this->clear($key, $namespace);
            return null;
        }

        if (!$fileResource = @fopen($cachePath, "rb")) {
            return null;
        }

        flock($fileResource, LOCK_SH);

        if (filesize($cachePath) > 0) {
            $cache = unserialize(fread($fileResource, filesize($cachePath)));
        } else {
            $cache = null;
        }

        flock($fileResource, LOCK_UN);
        fclose($fileResource);

        return $cache;
    }

    /**
     * set()
     *
     * @param mixed $key
     * @param string $namespace
     * @param mixed $data
     * @return bool
     */
    public function set($key, $namespace, $data) {
        if (!Visio\FileSystem::directoryExist($this->directory) || !Visio\FileSystem::isWritable($this->directory)) {
            return null;
        }

        $cachePath = $this->createName($key, $namespace);

        if (!$fileResource = fopen($cachePath, 'wb')) {
            return null;
        }

        if (flock($fileResource, LOCK_EX)) {
            fwrite($fileResource, serialize($data));
            flock($fileResource, LOCK_UN);
        } else {
            return null;
        }

        fclose($fileResource);
        @umask(0000);
        @chmod($cachePath, 0777);

        return true;
    }

    /**
     * isValid()
     *
     * @param string $key
     * @param string $namespace
     * @param integer $expiration
     * @return bool
     */
    public function isValid($key, $namespace, $expiration) {
        $cachePath = $this->createName($key, $namespace);
        if (filemtime($cachePath) < (time() - $expiration)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * clear()
     *
     * @param string $key
     * @param string $namespace
     * @return bool
     */
    public function clear($key, $namespace) {
        $cachePath = $this->createName($key, $namespace);

        if (Visio\FileSystem::fileExist($cachePath)) {
            @umask(0000);
            Visio\FileSystem::deleteFile($cachePath);

            return true;
        }

        return false;
    }

    /**
     * purge()
     *
     * @return bool
     */
    public function purge() {
        $folder = new Visio\FileSystem\Folder($this->directory);
        return $folder->delete(true);
    }

    /**
     * createName()
     *
     * @param string $key
     * @param string $namespace
     * @return string
     */
    private function createName($key, $namespace) {
        $name = $this->directory . DS . sha1($namespace) . "_" . sha1($key) . ".cache";
        return addslashes($name);
    }

}