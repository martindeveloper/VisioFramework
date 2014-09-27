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
 * Class for handling with image.
 * Resize, scale, send, etc.
 *
 * @package Visio\Utilities
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Image extends Visio\Object {

    /**
     * @var resource
     */
    private $pointer;

    /**
     * @var array
     */
    private static $types = array("jpeg" => IMAGETYPE_JPEG,
                                  "png" => IMAGETYPE_PNG,
                                  "gif" => IMAGETYPE_GIF);

    /**
     * @var array
     */
    private $info = array();

    /**
     * @param resource $resource
     * @param mixed $path
     */
    public function __construct($resource, $path = null) {
        $this->setResource($resource);
        imagesavealpha($this->pointer, true);

        if ($path != null) {
            $this->info = getimagesize($path);
        }

        return $this;
    }

    /**
     * Create image object from existing image file
     *
     * @param $file
     * @return Image
     * @throws \Visio\Exception\FileNotFound
     * @throws \Visio\Exception\InvalidFormat
     */
    public static function createFromFile($file) {
        $file = ($file instanceof Visio\FileSystem\File) ? $file->fullPath : $file;

        if (Visio\FileSystem::fileExist($file) && Visio\FileSystem::isReadable($file)) {
            $info = getimagesize($file);
            switch ($info[2]) {
                case self::$types['jpeg']:
                    return (new self(imagecreatefromjpeg($file), $file));
                    break;

                case self::$types['png']:
                    return (new self(imagecreatefrompng($file), $file));
                    break;

                case self::$types['gif']:
                    return (new self(imagecreatefromgif($file), $file));
                    break;

                default:
                    throw new Visio\Exception\InvalidFormat('Unsupported image type!');
                    break;
            }
        } else {
            throw new Visio\Exception\FileNotFound("Image file '" . $file . "' not found or is not readable!");
        }
    }

    /**
     * Create image from scratch
     *
     * @param $width
     * @param $height
     * @return Image
     */
    public static function createFromBlank($width, $height) {
        $width = intval($width);
        $height = intval($height);

        //BUG: No $path provided to constructor!
        return (new self(imagecreatetruecolor($width, $height)));
    }

    /**
     * Scale image
     *
     * @param int $scale
     * @return Visio\Utilities\Image
     */
    public function scale($scale) {
        $width = ($this->getWidth() * $scale / 100);
        $height = ($this->getHeight() * $scale / 100);
        return $this->resize($width, $height);
    }

    /**
     * Resize image to required width and height
     *
     * @param null $width
     * @param null $height
     * @param bool $keepAspectRatio
     * @return $this
     */
    public function resize($width = null, $height = null, $keepAspectRatio = false) {
        if ($keepAspectRatio === true) {
            if (!is_null($width)) {
                $factor = (float)$width / (float)$this->getWidth();
                $height = $factor * $this->getHeight();
            }
            if (!is_null($height)) {
                $factor = (float)$height / (float)$this->getHeight();
                $width = $factor * $this->getWidth();
            }
        }

        $holder = imagecreatetruecolor($width, $height);
        imagecopyresampled($holder, $this->getResource(), 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->setResource($holder);
        $this->info[0] = $width;
        $this->info[1] = $height;

        return $this;
    }

    /**
     * Set new resource
     *
     * @param resource $resource
     * @return $this
     * @throws \Visio\Exception\InvalidFormat
     */
    private function setResource($resource) {
        if (is_resource($resource) && (get_resource_type($resource) == 'gd')) {
            $this->pointer = $resource;
            return $this;
        }

        throw new Visio\Exception\InvalidFormat('Invalid image resource!');
    }

    /**
     * Get string representation of file
     *
     * @param string $type
     * @param int $quality
     * @return string
     */
    public function getString($type, $quality = null) {
        ob_start();
        $this->send($type, $quality);
        return ob_get_clean();
    }

    /**
     * Send image to browser
     *
     * @param string $type
     * @param int $quality
     * @throws Visio\Exception\InvalidFormat if invalid type is given
     * @return bool
     */
    public function send($type, $quality = null) {
        $type = Visio\Utilities\String::lower($type);
        if (!isset(self::$types[$type])) {
            throw new Visio\Exception\InvalidFormat("Invalid image type, '" . $type . "' given.");
        }

        switch ($type) {
            case 'jpg':
                return $this->saveJpegFile(null, $quality);
                break;
            case 'png':
                return $this->savePngFile(null, $quality);
                break;
            case 'gif':
                return $this->saveGifFile(null);
        }
    }

    /**
     * save()
     * Save image to file
     *
     * @param string $path
     * @param int $quality
     * @return bool
     */
    public function save($path, $quality = null) {
        switch (Visio\Utilities\String::lower(pathinfo($path, PATHINFO_EXTENSION))) {
            case 'jpg':
            case 'jpeg':
                return $this->saveJpegFile($path, $quality);
                break;
            case 'png':
                return $this->savePngFile($path, $quality);
                break;
            case 'gif':
                return $this->saveGifFile($path);
        }
    }

    /**
     * saveJpegFile()
     *
     * @param string $path
     * @param int $quality
     * @return bool
     */
    private function saveJpegFile($path, $quality) {
        $quality = (is_null($quality)) ? 85 : max(0, min(100, intval($quality)));
        return imagejpeg($this->getResource(), $path, $quality);
    }

    /**
     * savePngFile()
     *
     * @param string $path
     * @param int $quality
     * @return bool
     */
    private function savePngFile($path, $quality) {
        $quality = (is_null($quality)) ? 8 : max(0, min(9, intval($quality)));
        return imagepng($this->getResource(), $path, $quality);
    }

    /**
     * saveGifFile()
     *
     * @param string $path
     * @return bool
     */
    private function saveGifFile($path) {
        return imagegif($this->getResource(), $path);
    }

    /**
     * getResource()
     * Get current resource of image
     *
     * @return resource
     */
    public function getResource() {
        return $this->pointer;
    }

    /**
     * getSupportedTypes()
     * Get array of supported image types
     *
     * @return array
     */
    public function getSupportedTypes() {
        return self::$types;
    }

    /**
     * getInfo()
     *
     * @return array
     */
    public function getInfo() {
        return $this->info;
    }

    /**
     * getWidth()
     *
     * @return int
     */
    public function getWidth() {
        return $this->info[0];
    }

    /**
     * getHeight()
     *
     * @return int
     */
    public function getHeight() {
        return $this->info[1];
    }

    /**
     * getMimeType()
     *
     * @return string
     */
    public function getMimeType() {
        return $this->info['mime'];
    }

    /**
     * __toString()
     *
     * @return string
     */
    public function __toString() {
        return $this->getString(self::$types['jpeg']);
    }

    /**
     * __destroy()
     */
    public function __destroy() {
        if (is_resource($this->pointer)) {
            imagedestroy($this->pointer);
        }
    }

}