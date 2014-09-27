<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio;

use Visio;

/**
 * File system handling class.
 * Can read folder, file, delete file, folder, etc.
 *
 * @package Visio
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class FileSystem extends Visio\Object {

    /**
     * __construct()
     *
     * @throws Visio\Exception\Logical if this class tried to be instantiated
     */
    final public function __construct() {
        throw new Visio\Exception\Logical("Cannot instantiate static class " . get_class($this));
    }

    /**
     * Read content of directory
     * 
     * @param string $directory
     * @param string $extension
     * @param bool $filter
     * @throws Visio\Exception\DirectoryNotFound if directory not found
     * @return array
     */
    public static function readContent($directory, $extension = '.php', $filter = false) {
        if (!is_dir($directory))
            throw new Visio\Exception\DirectoryNotFound("Path '" . $directory . "' is not valid directory!");

        $trueDirectory = $directory;

        $skip = array(".", "..", ".svn", ".htaccess", ".DS_Store");

        $dir = scandir($directory);

        foreach ($dir as $k => $v) {
            if ((is_dir($trueDirectory . $v)) || in_array($v, $skip)) {
                unset($dir[$k]);

                continue;
            }

            if ($filter === true) {
                $info = pathinfo($dir[$k]);
                if ("." . $info['extension'] != $extension) {
                    unset($dir[$k]);

                    continue;
                }
            }

            $dir[$k] = basename($dir[$k], $extension);
        }

        $dir = array_values($dir);

        return $dir;
    }

    /**
     * Read content of file
     * 
     * @param string $path
     * @throws Visio\Exception\FileNotFound if file not found
     * @return string
     */
    public static function readFile($path) {
        if (is_file($path)) {
            return file_get_contents($path);
        } else {
            throw new Visio\Exception\FileNotFound('File \'' . $path . '\' not found!');
        }
    }

    /**
     * Create a new file with specified content
     * 
     * @param string $path
     * @param string $content
     * @throws Visio\Exception\General if file is already exist
     * @return string
     */
    public static function createFile($path, $content) {
        if (is_file($path)) {
            return file_put_contents($path, $content);
        } else {
            throw new Visio\Exception\General('File \'' . $path . '\' is already exist!');
        }
    }

    /**
     * Delete file on path
     * 
     * @param string $path
     * @throws Visio\Exception\FileNotFound if file not found
     * @return bool
     */
    public static function deleteFile($path) {
        if (is_file($path)) {
            return unlink($path);
        } else {
            throw new Visio\Exception\FileNotFound('File \'' . $path . '\' not found!');
        }
    }

    /**
     * Delete folder on path
     * 
     * @param string $path
     * @throws Visio\Exception\FolderNotFound if folder not found
     * @return bool
     */
    public static function deleteFolder($path) {
        if (is_dir($path)) {
            return rmdir($path);
        } else {
            throw new Visio\Exception\FolderNotFound('Folder \'' . $path . '\' not found!');
        }
    }

    /**
     * Check if file exists
     * 
     * @param string $path
     * @return bool
     */
    public static function fileExist($path) {
        clearstatcache();
        return is_file($path);
    }

    /**
     * Check if directory exists
     * 
     * @param string $path
     * @return bool
     */
    public static function directoryExist($path) {
        clearstatcache();
        return is_dir($path);
    }

    /**
     * Check if path is writable
     * 
     * @param string $path
     * @return bool
     */
    public static function isWritable($path) {
        clearstatcache();
        return is_writable($path);
    }

    /**
     * Check if path is readable
     * 
     * @param string $path
     * @return bool
     */
    public static function isReadable($path) {
        clearstatcache();
        return is_readable($path);
    }

}