<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Config;

use Visio;

/**
 * Config service
 *
 * @package Visio\Config
 * @author Martin Pernica
 * @version 3.0
 * @access public
 */
class Service extends Visio\Object {

    private $filename;

    /**
     * @param string $filename
     */
    public function __construct($filename) {
        $this->filename = CONFIG_DIR . $filename;
    }

    public function __invoke(Visio\DependencyInjection\Container $container) {
        $file = new Visio\FileSystem\File($this->filename);

        switch ($file->extension) {
            case "json":
                $adapter = new Visio\Config\Adapter\Json($file);
                break;

            case "xml":
                $adapter = new Visio\Config\Adapter\Xml($file);
                break;

            case "ini":
                $adapter = new Visio\Config\Adapter\Ini($file);
                break;
        }

        return new Visio\Config($adapter);
    }

}