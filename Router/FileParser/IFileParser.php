<?php

/**
 * @author Martin Pernica
 * @copyright 2008 - 2013
 * @version 3.0
 * @license {linkPending} Proprietary software, unauthorized copying of this file is strictly prohibited!
 * @package Visio
 */

namespace Visio\Router\FileParser;

use Visio;

/**
 * Visio\Router\FileParser\IFileParser
 *
 * @package Visio\Router\FileParser
 * @author Martin Pernica
 * @version 3.0
 */
interface IFileParser {
    public function __construct(Visio\FileSystem\File $file);
    public function parse();
}